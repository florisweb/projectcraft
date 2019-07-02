<!--
	CREDITS:
	- Thanks to Crafatar.com for the avatars and heads.
-->
<!DOCTYPE html>
<html>
	<head>
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0' name='viewport'/>
		<link rel="stylesheet" type="text/css" href="main.css?antiCache=1">
		<title>ProjectCraftMC | World Map</title>
	</head>

	<body style="overflow: hidden;" class="noselect">
		<div id="topBar">
			<img src="images/homeIcon.svg" class="button" onclick="App.homeScreen.open()">
			<img src="images/menuIcon.png" class="button infoMenuIcon" style="display: none" onclick="App.infoMenu.open()">
			<div class="shadowBackground"></div>
		</div>


		<div id="mapHolder">
			<img src="images/map.png" id="backgroundImage">
			<canvas id="mapCanvas" width="3062" height="3062"></canvas>
		</div>
		
		<div class="buttonHolder" id="zoomButtonHolder">
			<div class="zoomButton" onclick="Map.zoomIn()" style="border-right: 1px solid rgba(255, 255, 255, 0.2)">+</div>
			<div class="zoomButton" onclick="Map.zoomOut()" style="font-size: 55px; line-height: 24px">-</div>
		</div>

		<div class="buttonHolder" id="dimensionButtonHolder">
			<div class="text netherPortalButton" onclick="window.location.replace('nether/nether_test.php')">
				NETHER
			</div>
		</div>


		<div id="infoMenu">
			<div class="infoMenuPage">
				<div class="headerText preventTextOverflow">PROJECTS</div>
				<img class="exitIcon" src="images/exitIcon.png" onclick="App.infoMenu.close()">
				<div id="projectListHolder"></div>
			</div>

			<div class="infoMenuPage hide" style="color: white">

				<div class="headerText preventTextOverflow" id="projectPage_titleHolder">PROJECTS</div>
				<img class="exitIcon" src="images/exitIcon.png" onclick="App.infoMenu.openPageByIndex(0)">

				<div class="text" id="projectPage_coordHolder"></div>
				<div class="text subHeader"><br>BUILDERS</div>
				<div class="text" id="projectPage_builderNames"></div>
				
				<div class="text subHeader"><br>DESCRIPTION</div>
				<div class="text" id="projectPage_description"></div>

				<div class="text subHeader"><br>IMAGES</div>
				<div id="projectPage_imageHolder"></div>
			</div>
		</div>



		<div id="homeScreen" class="hide">
			<img style="animation-fill-mode: forwards;" id="homeScreen_backgroundImage" src="<?php
                    $files = glob("uploads/images/homescreen/*.png");
					$length = sizeof($files);
					$index = rand(0, $length-1);
                    echo $files[$index];
				?>">
			<div class="text" id="homescreen_projectCraftLogo">
				PROJECTCRAFT
				<div style="font-size: 15px; opacity: 0.5">
					Written and maintained by 
					<a href="https://eelekweb.tk" style="color: #fff">eelekweb.tk</a> 
					and 
					<a href="https://florisweb.tk" style="color: #fff">florisweb.tk</a>
				</div>
			</div>

			<div id="homeScreen_navigationHolder"> 
				<div class="navigationItem" onclick="window.location.replace('info.php')">
					<img src="heads.php?username=MHF_Question&type=head&scale=10" class="iconHolder">
					<div class="text itemTitle">INFO</div>
				</div>
			<!-- 	<div class="navigationItem" onclick="window.location.replace('dev/nether/nether_test.php')">
					<img src="heads.php?username=Numba_one_Stunna&type=head&scale=10" class="iconHolder">
					<div class="text itemTitle">NETHER</div>
				</div> -->
				<div class="navigationItem" onclick="App.homeScreen.openMap()">
					<img src="heads.php?username=0qt&type=head&scale=10" class="iconHolder">
					<div class="text itemTitle">MAP</div>
				</div>
			</div>
		</div>



		<script type="text/javascript" src="https://florisweb.tk/JS/jQuery.js"></script>
		<script type="text/javascript" src="https://florisweb.tk/JS/request2.js"></script>
		<!-- <script type="text/javascript" src="js/main_min.js?antiCache=2"></script> -->

		<script>
			// temperarelly so things don't get cached
			let antiCache = Math.random() * 100000000;
			$.getScript("js/map.js?antiCache=" 		+ antiCache, function() {});
			$.getScript("js/server.js?antiCache=" 	+ antiCache, function() {});
			$.getScript("js/infomenu.js?antiCache=" + antiCache, function() {});
			$.getScript("js/app.js?antiCache=" 		+ antiCache, function() {});
   		</script>
	</body>
</html>


<script>
	function executeUrlCommands() {
		<?php
			$_openProjectByTitle = $_GET["openProjectByTitle"];
			$commandFound = false;

			if ($_openProjectByTitle)
			{
				echo "App.openProject(\"" . (string)$_openProjectByTitle . "\");";				
				$commandFound = true;
			}

			if ($commandFound) echo "\nhomeScreen.classList.add('hide');";

		?>
		executeUrlCommands = null;
	}
</script>
