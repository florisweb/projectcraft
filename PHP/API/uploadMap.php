<?php
	require_once __DIR__ . "/APIAuthenticate.php"; // Will check if the connecting server is authenticated
	require_once __DIR__ . "/../log/log.php";
	require_once __DIR__ . "/heatMap.php";
	require_once __DIR__ . "/../config.php";

	$postData = file_get_contents("php://input");
	if (!$postData) 
	{
		AddLog("[UploadMap.php] Upload rejected due to lack of data. (BJSON)");
		die("Parameters missing");
	}
	$data = json_decode($postData, true);

	if (!$data)
	{
		AddLog("[UploadMap.php] Upload rejected due to lack of data. (AJSON)");
		die("Parameters missing");
	}
	if (sizeof($data) % 3 != 0) 
	{
		AddLog("[UploadMap.php] Upload rejected because of invalid length: " . sizeof($data));
		die("Invalid length");
	}

	uploadFile($data, __DIR__ . "/../../" . $GLOBALS["mapImage-url"]);

	function uploadFile($_data, $_url) {
		$fileData = convertDataToImage($_data);

	  	$file = fopen($_url, "w");
	  	fwrite($file, $fileData);
	  	fclose($file);

		return true;
	}

	function convertDataToImage($_data) {
	    $map = false;
	   	if (file_exists(__DIR__ . "/../../" . $GLOBALS["mapImage-url"])) 
   		{
   			$file = file_get_contents(__DIR__ . "/../../" . $GLOBALS["mapImage-url"]);
    		if (!is_null($file)) 
    		{
    			$map = imagecreatefromstring($file);
    		}
   		}

   		$mapWidth = $GLOBALS["CONFIG"]["world"]["maxX"] - $GLOBALS["CONFIG"]["world"]["minX"];
	    $mapHeight = $GLOBALS["CONFIG"]["world"]["maxZ"] - $GLOBALS["CONFIG"]["world"]["minZ"];
	    if ($map == null || $map == false) 
	    {
	    	$map = @imagecreatetruecolor($mapWidth, $mapHeight); // temp
	    }

	  	for ($i = 0; $i < sizeof($_data); $i += 3)
	  	{
	  		$colorObj = sRGBToRGBA($_data[$i + 2]);
	  		$color = imagecolorallocatealpha($map, $colorObj[0], $colorObj[1], $colorObj[2], 1); //alphachannel werkt niet?!

	  		$x = $_data[$i] - $GLOBALS["CONFIG"]["world"]["minX"];
	  		$z = $_data[$i + 1] - $GLOBALS["CONFIG"]["world"]["minZ"];

	  		if ($x < 0 || $x >= $mapWidth) continue;
	  		if ($z < 0 || $z >= $mapHeight) continue;
	  		imagesetpixel($map, $x, $z, $color);
	  	}

	  	ob_start();
		imagepng($map);
		$contents =  ob_get_contents();
		ob_end_clean();

	  	return $contents;
	}


	function sRGBToRGBA($obj)
	{
		$color = [];
		$color[] = ($obj >> 16) & 0xFF; // R
		$color[] = ($obj >> 8)  & 0xFF; // G
		$color[] = $obj         & 0xFF; // B
		$color[] = ($obj >> 24) & 0xFF; // A

		return $color;
	}
?>