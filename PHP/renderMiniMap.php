<?php
    require_once "config.php";

	if (
		!isset($_GET) || 
		!isset($_GET["projectTitle"])
	) die("Parameters missing");

	if (!$CONFIG || !$CONFIG["world"]["mapTileSize"]) die("Internal problem");
	$mapTileSize = (int)$CONFIG["world"]["mapTileSize"];


	$world 			= "overworld";
	$projectTitle 	= (string)$_GET["projectTitle"];


	$projectList = json_decode(file_get_contents("../" . $GLOBALS["CONFIG"]["overworldData-url"], true), true);

	foreach ($projectList as $project)
	{
		if ($project["title"] != $projectTitle) continue;

		$size = (int)$project["type"]["radius"] * 2;
		if (!$size) continue;

		$startX = $project["coords"]["x"] - $size / 2;
		$startZ = $project["coords"]["z"] - $size / 2;

		$url = "$root/PHP/API/map/" . $world . "/miniMap/" . $startX . "_" . $startZ . "_" . $size . ".png";
		
		if (!file_exists($url)) continue;

		$file = file_get_contents($url);
	    if ($file == null) continue;

	    $map = imagecreatefromstring($file);
	    if ($map == null) continue;

	    header("Content-Type: image/png");
		imagepng($map);
		imagedestroy($map);
		die();
	}

	die("MiniMap not found");
?>