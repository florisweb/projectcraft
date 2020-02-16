<?php
  	require_once __DIR__ . "/../config.php";
  	require_once __DIR__ . "/../log/log.php";

  	AddLog("[Garbage Collector] --------------- Start running ---------------");
  	$miniMapRemovals 	= removeNonExistentMiniMaps();
  	$mapRemovals 		= removeNonExistenMapTiles();

  	AddLog("[Garbage Collector] Removed " . $miniMapRemovals . " unlinked minimaps.");
  	AddLog("[Garbage Collector] Removed " . $mapRemovals . " maptiles.");



  	function removeNonExistenMapTiles() {
  		$dirPath = __DIR__ . '/map/overworld/map/';
  		$filePaths = glob($dirPath . "*.png");

  		$filesRemoved = 0;
  		foreach ($filePaths as $path) 
  		{
  			$fileName = explode($dirPath, $path)[1];
  			$parts = explode("_", $fileName);
  			$x = (int)$parts[0];
  			$z = (int)$parts[1];
  			$size = (int)$parts[2];
  			
  			if (
  				$x >= $GLOBALS["CONFIG"]["world"]["minX"] &&
  				$x <= $GLOBALS["CONFIG"]["world"]["maxX"] &&
  				$z >= $GLOBALS["CONFIG"]["world"]["minZ"] &&
  				$z <= $GLOBALS["CONFIG"]["world"]["maxZ"] &&
  				$size == $GLOBALS["CONFIG"]["world"]["mapTileSize"]
  			) continue;
  			$filesRemoved++;
  			
  			unlink($path);
  		}

  		return $filesRemoved;
  	}



  	function removeNonExistentMiniMaps() {
  		$dirPath = __DIR__ . '/map/overworld/miniMap/';
  		$filePaths = glob($dirPath . "*.png");

  		$filesRemoved = 0;
  		foreach ($filePaths as $path) 
  		{
  			$fileName = explode($dirPath, $path)[1];
  			$parts = explode("_", $fileName);
  			$x = (int)$parts[0];
  			$z = (int)$parts[1];
  			$size = (int)$parts[2];
  			
  			$project = fetchProject($x, $z, $size);
  			if ($project) continue;
  			$filesRemoved++;

  			unlink($path);
  		}

  		return $filesRemoved;
  	}


  	function fetchProject($_x, $_z, $_size) {
		$radius = $_size / 2;
		$centerX = $_x + $radius;
		$centerZ = $_z + $radius;

		$projectList = json_decode(file_get_contents("../../" . $GLOBALS["CONFIG"]["overworldData-url"], true), true);

		foreach ($projectList as $project)
		{
			if (
				!$project["type"] || 
				!$project["type"]["genMiniMap"] ||
				!$project["type"]["radius"]
			) continue;

			if (
				$project["type"]["radius"] != $radius ||
				$project["coords"]["x"] != $centerX || 
				$project["coords"]["z"] != $centerZ
			) continue;

			return $project;
		}

		return false;
	}

?>