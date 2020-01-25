<?php
	require "APIAuthenticate.php";
	
	$metaData = array(
		"compression " => $CONFIG["API"]["imageCompression"],
		"chunkSize" => $CONFIG["world"]["mapTileSize"],
		"miniMapList" => createMiniMapMetaData()
	);

	echo json_encode($metaData);  	


	function createMiniMapMetaData() {
		$projectList = json_decode(file_get_contents("../../" . $GLOBALS["CONFIG"]["overworldData-url"], true), true);

		$metaDataList = [];
		foreach ($projectList as $project)
		{
			if (
				!$project["type"] || 
				!$project["type"]["genMiniMap"] ||
				!$project["type"]["radius"]
			) continue;

			$metaData = array(
				"x" => $project["coords"]["x"] - $project["type"]["radius"] / 2,
				"z" => $project["coords"]["z"] - $project["type"]["radius"] / 2,
				"size" => $project["type"]["radius"]
			);
			array_push($metaDataList, $metaData);
		}
		
		return $metaDataList;
	}
?>