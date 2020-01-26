<?php
	require "APIAuthenticate.php"; // Will check if the connecting server is authenticated
	require_once "../log/log.php";

	$postData = file_get_contents("php://input");
	if (!$postData) die("Parameters missing");
	
	$data = json_decode($postData, true);
	if (
		!$data["data"] || 
		!$data["metaData"] ||
		!isset($data["metaData"]["x"]) ||
		!isset($data["metaData"]["z"])
	) die("Parameters missing");

	$metaData = $data["metaData"];
	$imgData = $data["data"];

	$world 			= $metaData["world"] == "nether" ? "nether" : "overworld";
	$mapTileSize 	= (int)$CONFIG["world"]["mapTileSize"];
	$size			= (int)$metaData["size"];
	$startX 		= (int)$metaData["x"];
	$startZ 		= (int)$metaData["z"];

	
	if ($size <= 0) die("Invalid size");
	if (
		$startX				< $CONFIG["world"]["minX"] ||
		$startZ 			< $CONFIG["world"]["minZ"] ||
		$startX + $size 	> $CONFIG["world"]["maxX"] ||
		$startZ + $size 	> $CONFIG["world"]["maxZ"]
	) die("Invalid coordinates");


	if (sqrt(sizeof($imgData) / 3) > $CONFIG["API"]["maxImageWidth"])
	{
		AddLog(
			"[UploadMap.php]: Upload rejected, file too big: " . sqrt(sizeof($imgData) / 3) . " pixels wide, " . 
			"max " . $CONFIG["API"]["maxImageWidth"] . "px wide");

		die("File too big. Max " . $CONFIG["API"]["maxImageWidth"] . "px wide");
	}



	if (!$metaData["isMiniMap"])
	{
		$x = round($startX / $mapTileSize) * $mapTileSize; // snap the coords to the chunkgrid
		$z = round($startZ / $mapTileSize) * $mapTileSize;

		$url = "$root/PHP/API/map/" . $world . "/map/" . $x . "_" . $z . "_" . $mapTileSize . ".png";
		AddLog("[UploadMap.php]: Uploaded map: " . $_url);

		echo uploadFile($imgData, $url);
	} else {
		$project = fetchProject($x, $z, $size);
		if (!$project)
		{
			AddLog("[UploadMap.php]: MiniMap upload rejected, no project at specified location (x: " . $x . " y: " . $y . " size: " . $size . ")");
			die("MiniMap upload rejected, no project at specified location");
		}

		$url = "$root/PHP/API/map/" . $world . "/miniMap/" . $x . "_" . $z . "_" . $size . ".png";
		AddLog("[UploadMap.php]: Uploaded miniMap: " . $project["title"]);
		echo uploadFile($imgData, $url);
	}



	function uploadFile($_data, $_url) {
		$fileData = convertDataToImage($_data);

	  	$file = fopen($_url, "w");
	  	fwrite($file, $fileData);
	  	fclose($file);

		return true;
	}

	function convertDataToImage($_data) {
		$channels = 3;
		$size = sqrt(sizeof($_data) / $channels);
		$image = @imagecreatetruecolor($size, $size);

	  	for ($i = 0; $i < sizeof($_data); $i += $channels)
	  	{
	  		$x = $i / $channels;
	  		$y = floor($x / $size);
	  		$x -= $y * $size;

	  		$color = imagecolorallocate($image, $_data[$i], $_data[$i + 1], $_data[$i + 2]);
	  		imagesetpixel($image, $x, $y, $color);
	  	}

	  	ob_start();
		imagepng($image);
		$contents =  ob_get_contents();
		ob_end_clean();


	  	return $contents;
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