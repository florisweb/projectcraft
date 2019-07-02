<!DOCTYPE html>
<html>
	<head>
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0' name='viewport'/>
		<link rel="stylesheet" type="text/css" href="css/main.css">
		<title>ProjectCraftMC | Nether Map.</title>
	</head>
	<body style="overflow: hidden;" class="noselect">
        <div id="topBar">
			<img src="images/homeIcon.svg" class="button" onclick="window.location.replace('../')">
			<img src="images/menuIcon.png" class="button infoMenuIcon" style="display: none" onclick="App.infoMenu.open()">
			<div class="shadowBackground"></div>
		</div>

		<div id="mapHolder">
			<canvas id="mapCanvas" width="3062" height="3062"></canvas>
		</div>
		
		<div id="zoomButtonHolder">
			<div class="zoomButton" onclick="Map.zoomIn()" style="border-right: 1px solid rgba(255, 255, 255, 0.2)">+</div>
			<div class="zoomButton" onclick="Map.zoomOut()" style="font-size: 55px; line-height: 24px">-</div>
		</div>
        
        <div class="buttonHolder" id="dimensionButtonHolder">
			<div class="text netherPortalButton" onclick="window.location.replace('../index.php')">
				OVERWORLD
			</div>
		</div>

		<div id="infoMenu" class="h ide">
			<div class="infoMenuPage">
				<div class="headerText preventTextOverflow">PORTALS</div>
				<img class="exitIcon" src="images/exitIcon.png" onclick="App.infoMenu.close()">
				<div id="projectListHolder"></div>
			</div>

			<div class="infoMenuPage hide" style="color: white">
				<div class="headerText preventTextOverflow" id="projectPage_titleHolder">PORTAL</div>
				<img class="exitIcon" src="images/exitIcon.png" onclick="App.infoMenu.openPageByIndex(0)">
                
				<div class="text" id="projectPage_coordHolder"></div>
				<div class="text subHeader"><br>BUILDERS</div>
				<div class="text" id="projectPage_builderNames"></div>

				<div class="text netherPortalButton">TO THE OVERWORLD</div>
			</div>
		</div>

		<script type="text/javascript" src="https://florisweb.tk/JS/jQuery.js"></script>
		<script type="text/javascript" src="https://florisweb.tk/JS/request2.js"></script>
		<script type="text/javascript" src="js/nether.js?antiCache=3"></script>

		<script>
			// temporarily so things don't get cached

			let antiCache = Math.random() * 100000000;
			// $.getScript("js/map.js?antiCache=" 		+ antiCache, function() {});
			// $.getScript("js/server.js?antiCache=" 	+ antiCache, function() {});
			// $.getScript("js/infomenu.js?antiCache=" 	+ antiCache, function() {});
			// $.getScript("js/app.js?antiCache=" 		+ antiCache, function() {});
			// $.getScript("js/nether.js?antiCache=" 			+ antiCache, function() {});
            
            function executeUrlCommands() {
                <?php
                    $project = $_GET["project"];
                    $project = explode('"', $project);
                    $project = implode("", $project);

                    if (!empty($project))
                    {
                        echo "App.openProject(\"" . $project . "\");";
                    }
                ?>
                executeUrlCommands = null;
            }

            document.body.onload = function() {
                App = new _App();
                App.setup();
            }
   		</script>
	</body>
</html>