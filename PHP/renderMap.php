<?php
    require_once "config.php";

	if (
		!isset($_GET) || 
		empty($_GET) || 
		!isset($_GET["world"]) || 
		!isset($_GET["x"]) ||
		!isset($_GET["z"]) ||
		!isset($_GET["width"]) || 
		!isset($_GET["height"])
	) die("Parameters missing");

	if (!$CONFIG || !$CONFIG["world"]["mapTileSize"]) die("Internal problem");
	$mapTileSize = (int)$CONFIG["world"]["mapTileSize"];


	$world 	= $_GET["world"] == "overworld" ? "overworld" : "nether";
	$width 	= (int)$_GET["width"];
	$height = (int)$_GET["height"];
	$startX = (int)$_GET["x"];
	$startZ = (int)$_GET["z"];

	if (
		$startX < $CONFIG["world"]["minX"] || 
		$startZ < $CONFIG["world"]["minZ"] || 
		$startX + $width > $CONFIG["world"]["maxX"] ||
		$startZ + $height > $CONFIG["world"]["maxZ"] 
	) die("Invalid coordinates");



	$SCALAR 		= 2 / $CONFIG["API"]["imageCompression"]; // x MC blocks / px
	$newTileSize 	= ceil($mapTileSize / $SCALAR);
	$pxWidth 		= ceil($width / $SCALAR);
	$pxHeight	 	= ceil($height / $SCALAR);
	$areamap 		= @imagecreatetruecolor($pxWidth, $pxHeight);

	for ($z = $startZ; $z < $startZ + $height; $z += $mapTileSize) 
	{
	  for ($x = $startX; $x < $startX + $width; $x += $mapTileSize) 
	  {	
		$curX = round($x / $mapTileSize) * $mapTileSize; // snap the coords to the chunkgrid
		$curZ = round($z / $mapTileSize) * $mapTileSize;

	  	$url = "API/map/" . $world . "/map/" . $curX . "_" . $curZ . "_" . $mapTileSize . ".png";
	   	if (!file_exists($url)) continue;
	    
	    $file = file_get_contents($url);
	    if ($file == null) continue;

	    $map = imagecreatefromstring($file);
	    if ($map == null) continue;

	    $scaledMap = imagescale(
	    	$map, 
	    	$newTileSize,
	    	$newTileSize
	    );

	    imagecopymerge(
	    	$areamap, 
	    	$scaledMap, 
	    	($curX - $startX) / $SCALAR, 
	    	($curZ - $startZ) / $SCALAR, 
	    	0, 0, 
	    	$newTileSize,
	    	$newTileSize,
	    	100
	    );
	  }
	}

	header("Content-Type: image/png");
	imagepng($areamap);
	imagedestroy($areamap);
?>