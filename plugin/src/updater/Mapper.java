package plugin.src.updater;

import java.awt.Color;
import java.io.FileReader;
import java.io.IOException;
import java.util.ArrayList;
import java.util.Collections;
import java.util.HashMap;

import org.bukkit.ChunkSnapshot;
import org.bukkit.Material;
import org.bukkit.World;
import org.json.simple.JSONArray;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;

import net.md_5.bungee.api.ChatColor;

@SuppressWarnings("unchecked")
public class Mapper {
	
	private UpdaterMain plugin;
	private Runnable mapper;
	
	private ArrayList<Color> colorIndex;
	private HashMap<Material, Integer> materialIndex;

	public Mapper(UpdaterMain instance) {
		plugin = instance;
		
		try {
			loadColors();
		} catch (Exception e) {
			plugin.getLogger().severe("An error occured whilst loading map color palette.");
			e.printStackTrace();
		}
		
		this.mapper = new Runnable() {
			
			@Override
			public void run() {
				if(plugin.updating) return;
				try {
					updateMap();
				} catch(Exception e) {
					plugin.getLogger().severe("An error occured whilst updating the map.");
					e.printStackTrace();
				}
			}
		};
		
		long delaytime = plugin.getConfig().getInt("render-update-time") * 20L;
		plugin.getServer().getScheduler().runTaskTimerAsynchronously(plugin, mapper, delaytime, delaytime);
	};
	
	private void loadColors() throws IOException, ParseException {
		FileReader fileReader = new FileReader(plugin.getDataFolder() + "/BlockMapColors.json");
		
		StringBuilder content = new StringBuilder();
		
		int c;
		while((c = fileReader.read()) != -1) {
			content.append((char) c);
		}
			
		fileReader.close();
			
		colorIndex = new ArrayList<Color>();
		materialIndex = new HashMap<Material, Integer>();

		JSONParser parser = new JSONParser();
		Object object = parser.parse(content.toString());
		JSONArray ids = (JSONArray) object;

		for (int i = 0; i < ids.size(); i++) {
			JSONObject id = (JSONObject) ids.get(i);
			JSONArray materials = (JSONArray) id.get("materials");
			JSONObject colorObj = (JSONObject) id.get("color");

			for (int m = 0; m < materials.size(); m++) {
				Material material = Material.getMaterial(materials.get(m).toString().toUpperCase());
				
				if (material == null) {
					throw new IllegalArgumentException(materials.get(m).toString().toUpperCase() + " is not a valid material.");
				}
					
				materialIndex.put(material, i);				
			}
			
			Color color = new Color(
					Integer.parseInt(colorObj.get("r").toString()), 
	                Integer.parseInt(colorObj.get("g").toString()), 
	                Integer.parseInt(colorObj.get("b").toString()), 
	                Integer.parseInt(colorObj.get("a").toString()));
			
			colorIndex.add(new Color(
					Math.floorDiv(color.getRed() * 180, 255),
					Math.floorDiv(color.getGreen() * 180, 255),
					Math.floorDiv(color.getBlue() * 180, 255),
					color.getAlpha()));
			
			colorIndex.add(new Color(
					Math.floorDiv(color.getRed() * 220, 255),
					Math.floorDiv(color.getGreen() * 220, 255),
					Math.floorDiv(color.getBlue() * 220, 255),
					color.getAlpha()));
			
			colorIndex.add(color);
			
			colorIndex.add(new Color(
					Math.floorDiv(color.getRed() * 135, 255),
					Math.floorDiv(color.getGreen() * 135, 255),
					Math.floorDiv(color.getBlue() * 135, 255),
					color.getAlpha()));
		}
	}
	
	public void updateMap() throws IOException, ParseException {
		plugin.updating = true;
		
		JSONParser parser = new JSONParser();
		
		ArrayList<SuperChunk> current = plugin.watchdog.getEditedChunks();
		JSONObject apidata = (JSONObject) parser.parse(plugin.getDataFromWebserver(plugin.getConfig().getString("api-fetch-url")));
		JSONArray minimaps = (JSONArray) apidata.get("miniMapList");
		
		if(!current.isEmpty()) {
			plugin.getServer().broadcastMessage("" + ChatColor.DARK_BLUE + ChatColor.BOLD + "[Map Updater] " + ChatColor.RED + "Updating map, this may be laggy.");
			
			int totalWorkload = minimaps.size() + current.size();
			double done = 0.0;
			
	    	for (SuperChunk c : current) {
				JSONArray data = new JSONArray();
				data.addAll(preGenerateMap(c.getWorld(), c.getX(), c.getZ(), plugin.CHUNKSIZE));	    		
				
				JSONObject metaData = new JSONObject();
				metaData.put("x", c.getX());
				metaData.put("z", c.getZ());
				metaData.put("size", plugin.CHUNKSIZE);
				metaData.put("world", c.getWorld().getEnvironment().toString().toLowerCase());
				metaData.put("isMiniMap", false);
				
				JSONObject complete = new JSONObject();
				complete.put("data", data);
				complete.put("metaData", metaData);
				
				plugin.sendDataToWebserver(complete.toJSONString(), plugin.getConfig().getString("api-upload-url"));
				
				done++;
				if(plugin.debugLogging) plugin.getLogger().info("Updating: " + Math.round((done / totalWorkload) * 100) + "%");
			}
	    	
			plugin.watchdog.clearChunkCache();
			
			plugin.getServer().getWorlds().get(0).setAutoSave(false);
			for(int m = 0; m < minimaps.size(); m++) {
				JSONObject minimapObj = (JSONObject) minimaps.get(m);
				int sideLength = Integer.parseInt(minimapObj.get("size").toString());
				int x = Integer.parseInt(minimapObj.get("x").toString());
				int z = Integer.parseInt(minimapObj.get("z").toString());
				JSONArray data = new JSONArray();

				data.addAll(generateMap(plugin.getServer().getWorlds().get(0), x, z, sideLength));
				
				JSONObject metaData = new JSONObject();
				metaData.put("x", x);
				metaData.put("z", z);
				metaData.put("size", sideLength);
				metaData.put("world", "normal");
				metaData.put("isMiniMap", true);
				
				JSONObject complete = new JSONObject();
				complete.put("data", data);
				complete.put("metaData", metaData);
				
				plugin.sendDataToWebserver(complete.toJSONString(), plugin.getConfig().getString("api-upload-url"));
				done++;
				if(plugin.debugLogging) plugin.getLogger().info("Updating: " + Math.round((done / totalWorkload) * 100) + "%");
			}
			plugin.getServer().getWorlds().get(0).setAutoSave(false);
			
			plugin.getServer().broadcastMessage("" + ChatColor.DARK_BLUE + ChatColor.BOLD + "[Map Updater]" + ChatColor.GREEN + " Map updated.");
		}
		
		plugin.updating = false;
		System.gc();
	}
	
	private ArrayList<Integer> preGenerateMap(World w, int startX, int startZ, int size) {
		ArrayList<Integer> img = new ArrayList<Integer>();
		for(int z = 0; z < size; z += 128) {
			for(int x = 0; x < size; x += 128) {
				img.addAll(z * 128 + x, generateMap(w, startX + x, startZ + z, 128));
			}
		}
		
		return img;
	}
	
	private ArrayList<Integer> generateMap(World w, int startX, int startZ, int size) {
		int arraySize = (int) (Math.ceil(size / 16) * 16);
		ArrayList<Integer> img = new ArrayList<Integer>(Collections.nCopies(arraySize * arraySize * 3, null));
		
		int minX = Math.floorDiv(startX, 16) * 16;
		int minZ = Math.floorDiv(startZ, 16) * 16;
		
		ArrayList<ChunkSnapshot> cache = new ArrayList<ChunkSnapshot>();
		
		int chunkSides = (int) Math.ceil(size / 16);
		int totalChunks = chunkSides * chunkSides;
		
		for(int nz = -1; nz < chunkSides; nz++) {
			for(int nx = 0; nx < chunkSides; nx++) {
				int xOffset = Math.floorDiv(minX + nx * 16, 16);
				int zOffset = Math.floorDiv(minZ + nz * 16, 16);
				cache.add(w.getChunkAt(xOffset, zOffset).getChunkSnapshot());
			}
		}

		for(int c = 0; c < totalChunks; c++) {
			ChunkSnapshot chunk = cache.get(chunkSides);
			ChunkSnapshot north = null;
			
			for(int rz = 0; rz < 16; rz++) {
				int northOffset = rz - 1;
				if(rz == 0) {
					northOffset = 15;
					north = cache.get(0);
				} else if (rz == 1) {
					north = chunk;
					cache.remove(0);
				}
				
				for(int rx = 0; rx < 16; rx++) {
					int y = getHighestSolidAt(chunk, rx, rz, -1, true);
					int m = materialIndex.get(chunk.getBlockType(rx, y, rz));
					int northY = 0;
					
					if(m == 12) { //Water: the water color is depth dependant, not dependant of the block north of it.
						northY = getHighestSolidAt(chunk, rx, rz, -1, false);
					} else {
						northY = getHighestSolidAt(north, rx, northOffset, -1, true);
					}
					
					Color mColor = getBlockColor(m, northY - y);
					
					int xOffset = (c % chunkSides) * 16 + rx;
					int zOffset = 16 * 16 * Math.floorDiv(c, chunkSides) * chunkSides;
					int chunkOffset = rz * chunkSides * 16;
					int pixelOffset = 3 * ( xOffset + zOffset + chunkOffset);
					img.set(pixelOffset    , mColor.getRed());
					img.set(pixelOffset + 1, mColor.getGreen());
					img.set(pixelOffset + 2, mColor.getBlue());
				}
			}
		} 
		
		return img;
	}

	/**
	 * Get the block color based on the height difference of itself with the block above it
	 * @param mIndex The block's material index.
	 * @param dY The Y of the block north of it - the block's own Y value.
	 * @return The color as a Color object.
	 */
	private Color getBlockColor(int mIndex, int dY) {
        if (mIndex == 12) {
			if (dY > 4) 			return colorIndex.get(mIndex * 4);
			if (dY <= 4 && dY > 2)	return colorIndex.get(mIndex * 4 + 1);
									return colorIndex.get(mIndex * 4 + 2);
		}

		if (dY > 0) 	return colorIndex.get(mIndex * 4);
		if (dY == 0) 	return colorIndex.get(mIndex * 4 + 1);
						return colorIndex.get(mIndex * 4 + 2);
	}
		
	/**
	 * Get the highest solid excluding transparent blocks at the given coordinates.
	 * @param chunk The chunk that is being scanned.
	 * @param x The block X coordinate.
	 * @param z The block Z coordinate.
	 * @param start A start coordinate, from which to start checking.
	 * @param waterIsTransparent Count water as transparent blocks.
	 * @return the Y coordinate of the heightest non-transparent block.
	 */
	private int getHighestSolidAt(ChunkSnapshot chunk, int x, int z, int start, boolean waterIsTransparent) {
		int y = 255;
		if(start == -1) {
			y = chunk.getHighestBlockYAt(x, z);
		} else {
			y = start;
		}
		
		while(materialIndex.get(chunk.getBlockType(x, y, z)) == 0 || (materialIndex.get(chunk.getBlockType(x, y, z)) == 12 && waterIsTransparent) ) { //Skip transparent blocks
			y--;
		}
		
		return (materialIndex.get(chunk.getBlockType(x, y + 1, z)) == 12 && waterIsTransparent) ? y + 1 : y;
	}
}
