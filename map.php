<?php
    include "PHP/config.php";
?>

<!DOCTYPE html>
<html>
	<head>
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0' name='viewport'/>
		<link rel="stylesheet" type="text/css" href="css/main.css?antiCache=4">
        <link rel="stylesheet" type="text/css" href="css/chat.css?antiCache=1">
		<title><?php echo $CONFIG["server"]["name"] . " | World Map"; ?></title>
	</head>

	<body style="overflow: hidden;" class="noselect">
		<div id="topBar">
			<img src="images/homeIcon.svg" class="button" onclick="window.location.replace('index.php')">
			<img src="images/menuIcon.png" class="button infoMenuIcon" style="display: none" onclick="InfoMenu.open()">
			<div class="shadowBackground"></div>
		</div>
        
        <div id="chatHolder" class="hide" onclick="Chat.toggle();">
			<div class="shadowHolder"></div>
		</div>


		<div id="mapHolder">
			<?php
				$width = (int)abs($CONFIG["world"]["maxX"] - $CONFIG["world"]["minX"]);
				$height = (int)abs($CONFIG["world"]["maxZ"] - $CONFIG["world"]["minZ"]);

				echo 	'<img src="PHP/renderMap.php?world=overworld' . 
						'&x=' . (int)$CONFIG["world"]["minX"] . 
						'&z=' . (int)$CONFIG["world"]["minZ"] . 
						'&width=' . $width . 
						'&height=' . $height . '" id="mapImage">' . 
						'<canvas id="mapCanvas" width="' . $width . '" height="' . $height . '"></canvas>';
			?>
            <div id="chatlog"></div>
		</div>
	
        
        <div class="coordinatesHolder" id="coordinatesHolder">
            <span id="current_x">0</span>
            <span id="current_z">0</span>
        </div>

		<div class="buttonHolder" id="dimensionButtonHolder">
			<div class="text netherPortalButton" onclick="window.location.replace('nether.php')">
				NETHER
			</div>
		</div>



		<!-- InfoMenu html includecode -->
		<div id="infoMenu">
			<div class="infoMenuPage">
				<div class="headerText preventTextOverflow">PROJECTS</div>
				<img class="icon" src="images/exitIcon.png" onclick="InfoMenu.close()">
				<img class="icon searchIcon" src="images/searchIcon.png" onclick="InfoMenu.search.open()">
				<div id="projectListHolder"></div>
			</div>

			<div class="infoMenuPage hide">
				<div class="headerText preventTextOverflow" id="projectPage_titleHolder">PROJECTS</div>
				<img class="icon" src="images/exitIcon.png" onclick="InfoMenu.openPageByIndex(0)">

				<div class="text" id="projectPage_coordHolder"></div>
				<div class="text subHeader"><br>BUILDERS</div>
				<div class="text" id="projectPage_builderNames"></div>
				
				<div class="text subHeader"><br>DESCRIPTION</div>
				<div class="text" id="projectPage_description"></div>
                
                <br>
                <div class="text netherPortalButton">TO THE NETHER</div>
                <br>

				<div class="text subHeader"><br>MINIMAP</div>
				<div class="miniMapHolder">
					<img class="miniMapImg">
				</div>


				<div class="text subHeader"><br>IMAGES</div>
				<div id="projectPage_imageHolder"></div>
			</div>

			<div class="infoMenuPage hide">
				<input class="headerText preventTextOverflow searchInput" placeholder="Search">
				<img class="icon exitIcon" src="images/exitIcon.png" onclick="InfoMenu.openPageByIndex(0)">
				<div id="projectSearchListHolder"></div>
			</div>
		</div>





		<script type="text/javascript" src="https://florisweb.tk/JS/jQuery.js"></script>
		<script type="text/javascript" src="https://florisweb.tk/JS/request2.js"></script>
		<!-- <script type="text/javascript" src="js/main_min.js?ac=1"></script> -->
    <script type="text/javascript" src="js/client.js?ac=1"></script>

		<script>
			// temperarelly so things don't get cached
			let antiCache = Math.random() * 100000000;
			$.getScript("js/handyFunctions.js?antiCache=" 	+ antiCache, function() {});
            $.getScript("js/chat.js?antiCache=" 			+ antiCache, function() {});
			$.getScript("js/map.js?antiCache=" 				+ antiCache, function() {});
			$.getScript("js/server.js?antiCache=" 			+ antiCache, function() {});
			$.getScript("js/infomenu.js?antiCache=" 		+ antiCache, function() {
				setup()
			});
   		</script>
   		<!-- <script type="text/javascript" src="js/server.js?antiCache=4"></script> -->
   		<!-- <script type="text/javascript" src="js/infomenu.js?antiCache=2"></script> -->
	</body>
</html>





<script>
	function executeUrlCommands() {
		<?php
            $project = $_GET["project"];
            $project = explode('"', $project);
            $project = implode("", $project);

            if (!empty($project))
            {
                echo "InfoMenu.openProjectPageByTitle(\"" . (string)$project . "\");";
                echo "Map.panToItem(Server.getItemByTitle(\"" . (string)$project . "\"));";
            }
        ?>
		executeUrlCommands = null;
	}
	document.body.onload = function() {
		setup();
	}

	var Server;
	var Map;
	var InfoMenu;
  var Client;

	function setup() {
		Server 		= new _server();
		Map 		= new _map();
 		InfoMenu 	= new _InfoMenu_mapJsExtender();
    Client = new _client();
		
		Map.onItemClick 		= function(_item) {InfoMenu.openProjectPageByTitle(_item.title)}
		InfoMenu.onItemClick 	= function(_item) {Map.panToItem(_item)}

		Server.getData("uploads/data.txt").then(function (_data) {
			InfoMenu.createItemsByList(_data);
			Map.init(_data, 4);
			if (executeUrlCommands) executeUrlCommands();
		});
    
    Server.getData("api/updaterlog.txt").then(function(_data) {
      Client.init(_data);
    });
	}

</script>
