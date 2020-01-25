<?php
	require "APIAuthenticate.php"; // Will check if the connecting server is authenticated

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



	$world 			= $metaData["world"] == "overworld" ? "overworld" : "nether";
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


	if (!$metaData["isMiniMap"])
	{
		$x = round($startX / $mapTileSize) * $mapTileSize; // snap the coords to the chunkgrid
		$z = round($startZ / $mapTileSize) * $mapTileSize;

		$url = "$root/PHP/API/map/" . $world . "/map/" . $x . "_" . $z . "_" . $mapTileSize . ".png";
		uploadFile($imgData, $url);
	} else {
		$url = "$root/PHP/API/map/" . $world . "/miniMap/" . $x . "_" . $z . "_" . $size . ".png";
		uploadFile($imgData, $url);
	}



	function uploadFile($_data, $_url) {
		$fileData = convertDataToImage($_data);

	  	$file = fopen($_url, "w");
	  	fwrite($file, $fileData);
	  	fclose($file);
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
?>