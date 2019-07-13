package tk.projectcraftmc.updater;

import java.io.IOException;
import java.net.URL;

import javax.net.ssl.HttpsURLConnection;

import org.bukkit.entity.Player;
import org.bukkit.event.EventHandler;
import org.bukkit.event.Listener;
import org.bukkit.event.player.AsyncPlayerChatEvent;
import org.bukkit.event.player.PlayerJoinEvent;
import org.bukkit.event.player.PlayerQuitEvent;
import org.bukkit.plugin.java.JavaPlugin;
import org.json.simple.JSONArray;
import org.json.simple.JSONObject;

@SuppressWarnings("unchecked") //JSONSimple error messages
public class PCMCUpdaterMain extends JavaPlugin implements Listener {
	
	private String lastData;

	private Runnable runnable;
	private JSONArray left;
	private JSONArray joined;
	private JSONArray chat;
	
	private int rateLimitCheck;
	private boolean rateLimited;

	@Override
	public void onEnable() {
		saveDefaultConfig(); //Create the config.yml file
		getConfig().options().copyDefaults(true);
		
		rateLimitCheck = 0;
		rateLimited = false;

		getServer().getPluginManager().registerEvents(this, this); //Setup PluginManager

		left = new JSONArray();
		joined = new JSONArray();
		chat = new JSONArray();
		
		lastData = "";  //Data last sent to the server

		runnable = new Runnable() { //Runnable for JSONObject creation
			public void run() {
				JSONObject data = new JSONObject();
				JSONArray players = new JSONArray();
				
				JSONArray currentLeft = left;
				JSONArray currentJoined = joined;
				JSONArray currentChat = chat;

				for (Player p : getServer().getOnlinePlayers()) {
					JSONObject player = new JSONObject();
					player.put("username", p.getPlayerListName());
					player.put("uuid", p.getUniqueId().toString());
					player.put("x", p.getLocation().getBlockX());
					player.put("y", p.getLocation().getBlockY());
					player.put("z", p.getLocation().getBlockZ());
					player.put("dim", p.getWorld().getEnvironment().toString());
					players.add(player);
				}

				data.put("players", players);
				data.put("left", currentLeft);
				data.put("joined", currentJoined);
				if(getConfig().getBoolean("send-chat")) data.put("chat", currentChat);
				
				try {
					if(!rateLimited) {
						sendData(data);
					} else {
						if(getConfig().getBoolean("logging")) {
							getLogger().info("Rate-limit mode active, not sending data. (Rate-limit counter: " + rateLimitCheck + ").");
						}
						
						rateLimitCheck--;
						
						if(rateLimitCheck == 0) {
							rateLimited = false;
						}
					}
				} catch (IOException e) {
					e.printStackTrace();
				}
				
				left.removeAll(currentLeft);
				joined.removeAll(currentJoined);
				chat.removeAll(currentChat);
			}
		};
	}

	@Override
	public void onDisable() {
		getServer().getScheduler().cancelTasks(this); //Disable all running tasks
	}

	public void sendData(JSONObject data) throws IOException {
		if(data.toJSONString().equals(lastData)) return; //To limit requests when a player is afk.
		
		lastData = data.toJSONString(); //Create JSON String
		
		data.put("current-time", System.currentTimeMillis()); //For sync purposes
		data.put("refresh-time", getConfig().getInt("delay-time"));
		
		/**
		 * HTTP Request
		 */
		
		URL serverURL = new URL(getConfig().getString("api-url") + "?data=" + data.toJSONString());
		HttpsURLConnection conn = (HttpsURLConnection) serverURL.openConnection();

		conn.setRequestMethod("GET");

		conn.setRequestProperty("User-Agent", "Mozilla/5.0");
		
		if(conn.getResponseCode() > 200) { //Unsuccesful request
			getLogger().warning("Couldn't send data to the webserver. (" + conn.getResponseCode() + " " + conn.getResponseMessage() + ")");
			
			rateLimitCheck++;
			
			if(rateLimitCheck > 5) {
				rateLimited = true;
			}
		}
		
		if(getConfig().getBoolean("logging")) getLogger().info(conn.getResponseCode() + " " + conn.getResponseMessage() + "\n" + data.toJSONString());
		
		conn.disconnect();
	}

	@EventHandler
	public void onJoin(PlayerJoinEvent e) {
		JSONObject player = new JSONObject();
		player.put("username", e.getPlayer().getPlayerListName());
		player.put("uuid", e.getPlayer().getUniqueId().toString());
		joined.add(player);
		
		if (getServer().getOnlinePlayers().size() == 1) { //Start runnable when the first player joins
			getServer().getScheduler().cancelTasks(this);
			
			getServer().getScheduler().scheduleSyncRepeatingTask(this, runnable, 5 * 20L, getConfig().getInt("delay-time") * 20L);
		}
	}

	@EventHandler
	public void onLeave(PlayerQuitEvent e) {
		JSONObject player = new JSONObject();
		player.put("username", e.getPlayer().getPlayerListName());
		player.put("uuid", e.getPlayer().getUniqueId().toString());
		left.add(player);
		
		if (getServer().getOnlinePlayers().size() == 1) { //Stop runnable when the last player leaves, send last data
			getServer().getScheduler().cancelTasks(this);
			
			JSONObject data = new JSONObject();
			data.put("left", left);
			data.put("joined", new JSONArray());
			data.put("players", new JSONArray());
			
			try {
				sendData(data);
			} catch (IOException e1) {
				e1.printStackTrace();
			}
			
			left.clear();
		}
	}
	
	@EventHandler
	public void onChat(AsyncPlayerChatEvent e) { //For chat logging
		JSONObject chatMessage = new JSONObject();
		chatMessage.put("sender", e.getPlayer().getPlayerListName());
		chatMessage.put("time", System.currentTimeMillis());
		chatMessage.put("message", e.getMessage());
		chat.add(chatMessage);
	}

}
