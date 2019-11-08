<?php
    include "PHP/config.php";
?>

<!DOCTYPE html>
<html>
	<head>
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0' name='viewport'/>
		<link rel="stylesheet" type="text/css" href="css/main.css?x=2">
        <link rel="stylesheet" type="text/css" href="css/nether.css">
		<title><?php echo $CONFIG["server"]["name"] . " | Nether Map"; ?></title>
	</head>

	<body style="overflow: hidden;" class="noselect">
        <div id="topBar">
			<img src="images/homeIcon.svg" class="button" onclick="window.location.replace('../')">
			<img src="images/menuIcon.png" class="button infoMenuIcon" style="display: none" onclick="InfoMenu.open()">
			<div class="shadowBackground"></div>
		</div>

		<div id="mapHolder">
			<canvas id="mapCanvas" width="3062" height="3062"></canvas>
		</div>
        
        <div class="coordinatesHolder" id="coordinatesHolder">
            <span id="current_x">0</span>
            <span id="current_z">0</span>
        </div>
        
        <div class="buttonHolder" id="dimensionButtonHolder">
			<div class="text netherPortalButton" onclick="window.location.replace('map.php')">
				OVERWORLD
			</div>
		</div>

		<div id="infoMenu" class="h ide">
			<div class="infoMenuPage">
				<div class="headerText preventTextOverflow">PORTALS</div>
				<img class="icon" src="images/exitIcon.png" onclick="InfoMenu.close()">
				<img class="icon searchIcon" src="images/searchIcon.png" onclick="InfoMenu.search.open()">
				<div id="projectListHolder"></div>
			</div>


			<div class="infoMenuPage hide" style="color: white">
				<div class="headerText preventTextOverflow" id="projectPage_titleHolder">PORTAL</div>
				<img class="exitIcon" src="images/exitIcon.png" onclick="InfoMenu.openPageByIndex(0)">

				<div class="text" id="projectPage_coordHolder"></div>
				<div class="text subHeader"><br>BUILDERS</div>
				<div class="text" id="projectPage_builderNames"></div>
				
				<div class="text subHeader"><br>DESCRIPTION</div>
				<div class="text" id="projectPage_description"></div>
                
                <br>
                <div class="text netherPortalButton">TO THE OVERWORLD</div>

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

		<script>
			// temporarily so things don't get cached
			let antiCache = Math.random() * 100000000;
			$.getScript("js/handyFunctions.js?antiCache=" 	+ antiCache, function() {});
            $.getScript("js/chat.js?antiCache=" 			+ antiCache, function() {});
            $.getScript("js/map.js?antiCache=" 				+ antiCache, function() {});
            $.getScript("js/server.js?antiCache=" 			+ antiCache, function() {});
			$.getScript("js/infomenu.js?antiCache=" 		+ antiCache, function() {
				setup();
			});
			
			document.body.onload = function() {
				// setup();
			}
            
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

		

            var Server;
			var Map;
			var InfoMenu;

			function setup() {
				Server 		= new _server();
				Map 		= new _map();
		 		InfoMenu 	= new _InfoMenu_mapJsExtender();
				
				Map.onItemClick 		= function(_item) {InfoMenu.openProjectPageByTitle(_item.title)}
				InfoMenu.onItemClick 	= function(_item) {Map.panToItem(_item)}

				Server.getData("uploads/nether.txt").then(function (_data) {		
					InfoMenu.createItemsByList(_data);
                    
                    drawLines(_data);

					Map.init(_data, 1);
                    
					if (executeUrlCommands) executeUrlCommands();
				});
				InfoMenu.goThroughPortal = function(_title) {
					window.location.replace("map.php?project=" + _title);
				}
			}
            
            function drawLines(data) {
            	for (let i = 0; i < data.length; i++) {
            		if (data[i].updated)
            			continue;

            		data[i].updated = false;

            		for (let j = 0; j < data[i].neighbours.length; j++) {
            			let neighbour = Server.getItemByTitle(data[i].neighbours[j]["name"]);

            			if (neighbour.updated || !neighbour)
            				continue;
                        
                        let colour = "#777"
                        
                        switch(data[i].neighbours[j]["type"].toLowerCase()) {
                            case "iceway":
                                colour = "#748ebe";
                                break;
                            default:
                                colour = "#777";
                                break;
                        }

            			Map.drawLine(data[i].coords.x, data[i].coords.z, neighbour.coords.x, neighbour.coords.z, colour);
                        console.log(data[i].coords.x + " " + data[i].coords.z + " " + neighbour.coords.x + " " +  neighbour.coords.z)
            		}

            		data[i].updated = true;
            	}
            }
   		</script>
	</body>
</html>