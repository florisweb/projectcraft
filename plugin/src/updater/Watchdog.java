package plugin.src.updater;

import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

import org.bukkit.World;
import org.bukkit.block.Block;
import org.bukkit.event.EventHandler;
import org.bukkit.event.EventPriority;
import org.bukkit.event.Listener;
import org.bukkit.event.block.BlockBreakEvent;
import org.bukkit.event.block.BlockPlaceEvent;
import org.json.simple.JSONArray;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;

import net.md_5.bungee.api.ChatColor;

public class Watchdog implements Listener {
	
	private UpdaterMain plugin;
	private ArrayList<SuperChunk> chunks;
	private Runnable watchdog;
	
	public Watchdog(UpdaterMain instance) {
		this.plugin = instance;
		this.chunks = new ArrayList<SuperChunk>();
		this.watchdog = new Runnable() {
			
			@Override
			public void run() {
				if(plugin.updating) return;
				try {
					saveChunkCache();
				} catch(Exception e) {
					plugin.getLogger().severe("An error occured whilst trying to update chunk cache.");
					e.printStackTrace();
				}
			}
		};
		
		long delaytime = plugin.getConfig().getInt("memory-clean-update-time") * 20L;
		plugin.getServer().getScheduler().runTaskTimerAsynchronously(plugin, watchdog, delaytime / 2L, delaytime);
	}
	
	@EventHandler (priority = EventPriority.MONITOR)
	public void onBlockBreak(BlockBreakEvent e) {
		registerChunk(e.getBlock());
	}
	
	@EventHandler (priority = EventPriority.MONITOR)
	public void onBlockPlace(BlockPlaceEvent e) {
		registerChunk(e.getBlock());
	}
	
	public void registerChunk(Block b) {
		SuperChunk c = getSuperChunk(b.getWorld(), b.getX(), b.getZ());
		if (!containsSuperChunk(chunks, c)) {
			chunks.add(c);
			if(plugin.debugLogging) plugin.getLogger().info("Registered (" + c.getX() + ", " + c.getZ() + ").");
		}
	}
	
	public void registerChunk(World w, int x, int z) {
		SuperChunk c = getSuperChunk(w, x, z);
		if (!containsSuperChunk(chunks, c)) {
			chunks.add(c);
			if(plugin.debugLogging) plugin.getLogger().info("Registered (" + c.getX() + ", " + c.getZ() + ").");
		}
	}
	
	public ArrayList<SuperChunk> getEditedChunks() throws FileNotFoundException, IOException, ParseException {
		ArrayList<SuperChunk> editedChunks = new ArrayList<SuperChunk>();
		
		JSONArray cacheJSON = getChunkCache();
		for(int c = 0; c < cacheJSON.size(); c++) {
			JSONObject chunk = (JSONObject) cacheJSON.get(c);
			editedChunks.add(SuperChunk.deserialize(chunk));
		}
		
		ArrayList<SuperChunk> copy = chunks;
		for(SuperChunk c : copy) {
			if(containsSuperChunk(editedChunks, c)) continue;
			
			editedChunks.add(c);
		}
		
		return editedChunks;
	}
	
	@SuppressWarnings("unchecked")
	public void saveChunkCache() throws IOException, ParseException {
		if(chunks.isEmpty()) return;
		
		plugin.getServer().broadcastMessage("" + ChatColor.DARK_BLUE + ChatColor.BOLD + "[Map Updater]" + ChatColor.RED + " Writing memory to file. This may be laggy.");

		JSONArray cacheJSON = getChunkCache();
		ArrayList<SuperChunk> copy = chunks;

		for (SuperChunk c : copy) {
		    JSONObject chunk = c.serialize();
		    
		    if(cacheJSON.contains(c)) return;
		    
		    cacheJSON.add(chunk);
		}

		writeChunkCache(cacheJSON, false);

		chunks.removeAll(copy);
		
		System.gc();
		
		plugin.getServer().broadcastMessage("" + ChatColor.DARK_BLUE + ChatColor.BOLD + "[Map Updater]" + ChatColor.GREEN + " Memory clear complete.");
	}
	
	public void clearChunkCache() throws IOException {
		writeChunkCache(new JSONArray(), false);
		chunks.clear();
	}
	
	private JSONArray getChunkCache() throws FileNotFoundException, IOException, ParseException {
		JSONParser parser = new JSONParser();

		Object file = parser.parse(new FileReader(plugin.getDataFolder() + "/chunkCache.json"));
		
		return (JSONArray) file;
	}
	
	private void writeChunkCache(JSONArray data, boolean append) throws IOException {
		FileWriter cacheFile = new FileWriter(plugin.getDataFolder() + "/chunkCache.json", append);
		cacheFile.write(data.toJSONString());
		cacheFile.close();
	}
	
	private SuperChunk getSuperChunk(World w, int x, int z) {
		x = Math.floorDiv(x, plugin.CHUNKSIZE) * plugin.CHUNKSIZE;
		z = Math.floorDiv(z, plugin.CHUNKSIZE) * plugin.CHUNKSIZE;
		
		return new SuperChunk(w, x, z);
	}
	
	private boolean containsSuperChunk(List<SuperChunk> l, World w, int x, int z) {
		boolean found = false;
		
		for(SuperChunk c : l) {
			if(c.getWorld() == w && c.getX() == x && c.getZ() == z) {
				found = true;
				break;
			}
		}
		
		return found;
	}
	
	private boolean containsSuperChunk(List<SuperChunk> l, SuperChunk s) {
		return containsSuperChunk(l, s.getWorld(), s.getX(), s.getZ());
	}
	
}