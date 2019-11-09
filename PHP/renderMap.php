<?php
    require "config.php";

	if (
		!isset($_GET) || 
		empty($_GET) || 
		!isset($_GET["world"]) || 
		!isset($_GET["x"]) ||
		!isset($_GET["z"]) ||
		!isset($_GET["width"]) || 
		!isset($_GET["height"])
	) die("Parameters missing");

	if (!$CONFIG || !$CONFIG["world"]["mapTileWidth"]) die("Internal problem");

	$world 	= $_GET["world"] == "overworld" ? "overworld" : "nether";
	$width 	= (int)$_GET["width"];
	$height = (int)$_GET["height"];
	$startX = round((int)$_GET["x"] / $CONFIG["world"]["mapTileWidth"]) * $CONFIG["world"]["mapTileWidth"];
	$startZ = round((int)$_GET["z"] / $CONFIG["world"]["mapTileWidth"]) * $CONFIG["world"]["mapTileWidth"];

	// echo "<pre>";

	$SCALAR = 10; // x MC blocks / px
	$pxWidth = ceil($width / $SCALAR);
	$pxHeight = ceil($height / $SCALAR);
	$areamap = @imagecreatetruecolor($pxWidth, $pxHeight);

	for ($z = $startZ; $z < $startZ + $height; $z += $CONFIG["world"]["mapTileWidth"]) 
	{
	  
	  for ($x = $startX; $x < $startX + $width; $x += $CONFIG["world"]["mapTileWidth"]) 
	  {	
	  	$url = "API/generalMap/" . $world . "_" . $x . "_" . $z . "_" . $CONFIG["world"]["mapTileWidth"] . ".png";
	   	if (!file_exists($url)) continue;

	    $file = file_get_contents($url);
	    if ($file == null) continue;

	    $map = imagecreatefromstring($file);
	    if ($map == null) continue;

	    $newWidth = ceil($CONFIG["world"]["mapTileWidth"] / $SCALAR);
	    $scaledMap = imagescale(
	    	$map, 
	    	$newWidth,
	    	$newWidth,
	    );

	    imagecopymerge(
	    	$areamap, 
	    	$scaledMap, 
	    	($x - $startX) / $SCALAR, 
	    	($z - $startZ) / $SCALAR, 
	    	0, 0, 
	    	$newWidth,
	    	$newWidth,
	    	100
	    );
	    
	  }
	}

	header("Content-Type: image/png");
	imagepng($areamap);
	imagedestroy($areamap);
?>