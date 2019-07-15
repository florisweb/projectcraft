<!--
	CREDITS:
	- Thanks to Crafatar.com for the avatars and heads.
-->
<!DOCTYPE html>
<html>
	<head>
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0' name='viewport'/>
		<link rel="stylesheet" type="text/css" href="css/main.css?antiCache=1">
        <link rel="stylesheet" type="text/css" href="css/chat.css?antiCache=1">
		<title>ProjectCraftMC | World Map</title>
	</head>

	<body style="overflow: hidden;" class="noselect">
		<div id="topBar">
			<img src="images/homeIcon.svg" class="button" onclick="window.location.replace('index.php')">
			<img src="images/menuIcon.png" class="button infoMenuIcon" style="display: none" onclick="InfoMenu.open()">
			<div class="shadowBackground"></div>
		</div>
        
        <div id="chatHolder" class="hi de" onclick="Chat.toggle();">
			<div class="shadowHolder"></div>
		</div>


		<div id="mapHolder">
			<img src="images/map.png" id="backgroundImage">
			<canvas id="mapCanvas" width="3062" height="3062"></canvas>
            <div id="chatlog"></div>
		</div>
	
        
        <div class="coordinatesHolder" id="coordinatesHolder">
            <span id="current_x"></span>
            <span id="current_z"></span>
        </div>

		<div class="buttonHolder" id="dimensionButtonHolder">
			<div class="text" onclick="window.location.replace('nether.php')">
				NETHER
			</div>
		</div>



		<!-- InfoMenu html includecode -->
		<div id="infoMenu">
			<div class="infoMenuPage">
				<div class="headerText preventTextOverflow">PROJECTS</div>
				<img class="exitIcon" src="images/exitIcon.png" onclick="InfoMenu.close()">
				<div id="projectListHolder"></div>
			</div>

			<div class="infoMenuPage hide" style="color: white">
				<div class="headerText preventTextOverflow" id="projectPage_titleHolder">PROJECTS</div>
				<img class="exitIcon" src="images/exitIcon.png" onclick="InfoMenu.openPageByIndex(0)">

				<div class="text" id="projectPage_coordHolder"></div>
				<div class="text subHeader"><br>BUILDERS</div>
				<div class="text" id="projectPage_builderNames"></div>
				
				<div class="text subHeader"><br>DESCRIPTION</div>
				<div class="text" id="projectPage_description"></div>
                
                <div class="text netherPortalButton">TO THE NETHER</div>

				<div class="text subHeader"><br>IMAGES</div>
				<div id="projectPage_imageHolder"></div>
			</div>
		</div>





		<script type="text/javascript" src="https://florisweb.tk/JS/jQuery.js"></script>
		<script type="text/javascript" src="https://florisweb.tk/JS/request2.js"></script>
		<!-- <script type="text/javascript" src="js/main_min.js?antiCache=2"></script> -->

		<script>
			// temperarelly so things don't get cached
			let antiCache = Math.random() * 100000000;
			$.getScript("js/handyFunctions.js?antiCache=" 	+ antiCache, function() {});
            $.getScript("js/chat.js?antiCache=" 			+ antiCache, function() {});
			$.getScript("js/map.js?antiCache=" 				+ antiCache, function() {});
			$.getScript("js/server.js?antiCache=" 			+ antiCache, function() {});
			$.getScript("js/infomenu.js?antiCache=" 		+ antiCache, function() {
				App.setup()
			});
   		</script>
	</body>
</html>





<script>
	function executeUrlCommands() {
		<?php
			$_openProjectByTitle = $_GET["openProjectByTitle"];

			if ($_openProjectByTitle)
			{
				echo "App.openProject(\"" . (string)$_openProjectByTitle . "\");";				
				$commandFound = true;
			}

		?>
		executeUrlCommands = null;
	}



	var Server;
	var Map;
	var Chat;
	var InfoMenu;
	var App = new _App();



	function setup() {
		Server 		= new _server();
		Map 		= new _map();
		Chat 		= new _chat();
 		InfoMenu 	= new _InfoMenu_mapJsExtender();
		
		Map.onItemClick 		= function(_item) {InfoMenu.openProjectPageByTitle(_item.title)}
		InfoMenu.onItemClick 	= function(_item) {
			let item = Server.getItemById(_item);
			Map.panToItem(_item)
		}

		Server.

	}



	function _App() {
	  	this.update = function() {
		    Server.getData().then(function () {
		      InfoMenu.createItemsByList(Server.items);
		      Map.init();
		      if (executeUrlCommands) executeUrlCommands()
		    }, function () {});
		 }

	  	this.setup = function() {
		    Server 		= new _server();
		    Map 		= new _map();
		    Chat 		= new _chat();
		    InfoMenu 	= new _InfoMenu_mapJsExtender();
		    


		  	this.update();


		  	// assign the eventhandlers
		    document.getElementById("mapCanvas").addEventListener("click", function(e) {
				let mapCanvas = document.getElementById("mapCanvas");
				let mapHolder = document.getElementById("mapHolder");

				let mouseX = (e.x + mapHolder.scrollLeft) / (mapHolder.scrollWidth - 390 * InfoMenu.openState);
				let mouseY = (e.y + mapHolder.scrollTop) / mapHolder.scrollHeight;
				let x = mouseX * mapCanvas.width;
				let y = mouseY * mapCanvas.height;

				Map.handleClick(x, y);
		    });


		    document.onmousemove = function(e) {
				let mapCanvas = document.getElementById("mapCanvas");
				let mapHolder = document.getElementById("mapHolder");

				let mouseX = (e.x + mapHolder.scrollLeft) / (mapHolder.scrollWidth - 390 * InfoMenu.openState);
				let mouseY = (e.y + mapHolder.scrollTop) / mapHolder.scrollHeight;
				let x = Map.DOMToMC(mouseX * mapCanvas.width);
				let y = Map.DOMToMC(mouseY * mapCanvas.height);

				document.getElementById("current_x").innerHTML = Math.round(x);
				document.getElementById("current_z").innerHTML = Math.round(y);
		    }
		  

		    document.onkeydown = function(_e) {
		      	if (_e.key == "Escape")
		      	{
		        	if (InfoMenu.pageIndex == 1) return InfoMenu.openPageByIndex(0);
		        	if (InfoMenu.openState) return InfoMenu.close();
		      	}
		      	if (_e.key == "+") Map.zoomIn(); 
		      	if (_e.key == "-") Map.zoomOut();
		    	if (_e.key == "+" || _e.key == "-" || _e.key == "Escape") _e.preventDefault();
		    };
		 }
	}

</script>
