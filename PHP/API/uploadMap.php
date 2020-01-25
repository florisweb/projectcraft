<?php
	require "APIAuthenticate.php"; // Will check if the connecting server is authenticated


	$imgData = $_GET["data"];
	$metaData = $_GET["metaData"];

	if (
		!$imgData ||
		!$metaData ||
		!$metaData["world"] ||
		!$metaData["centerX"] ||
		!$metaData["centerZ"] ||
		!$metaData["radius"]
	) die("Parameters missing");


	$world 			= $metaData["world"] == "overworld" ? "overworld" : "nether";
	$mapTileWidth 	= (int)$CONFIG["world"]["mapTileWidth"];
	$radius			= (int)$metaData["radius"];
	$centerX 		= (int)$metaData["centerX"];
	$centerZ 		= (int)$metaData["centerZ"];

	
	if ($radius <= 0) die("Invalid radius");
	if (
		$centerX - $radius < $CONFIG["world"]["minX"] || 
		$centerZ - $radius < $CONFIG["world"]["minZ"] || 
		$centerX + $radius > $CONFIG["world"]["maxX"] || 
		$centerZ + $radius > $CONFIG["world"]["maxZ"]
	) die("Invalid coordinates");



	$isMiniMap = (boolean)$metaData["highQuality"];

	if ($isMiniMap === false)
	{
		$x = round($centerX / $mapTileWidth) * $mapTileWidth; // snap the coords to the chunkgrid
		$z = round($centerZ / $mapTileWidth) * $mapTileWidth;

		$url = "API/map/" . $world . "/map/" . $x . "_" . $z . "_" . $mapTileWidth . ".png";

		file_put_contents($url, $imgData);
	} else {
		$url = "API/map/" . $world . "/miniMap/" . $x . "_" . $z . "_" . $radius . ".png";
		file_put_contents($url, $imgData);
	}

  	die("succesfully stored image");


?>