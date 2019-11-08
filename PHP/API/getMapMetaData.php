<?php
	require "../config.php";


	$metaDataList = createMiniMapMetaData();

	for ($x = $CONFIG["world"]["minX"]; $x < $CONFIG["world"]["maxX"]; $x += $CONFIG["world"]["mapTileRadius"])
	{
		for ($z = $CONFIG["world"]["minZ"]; $z < $CONFIG["world"]["maxZ"]; $z += $CONFIG["world"]["mapTileRadius"])
		{
			$metaData = array(
  				"centerX" => $x + $CONFIG["world"]["mapTileRadius"] / 2,
  				"centerZ" => $z + $CONFIG["world"]["mapTileRadius"] / 2,
  				"radius" => $CONFIG["world"]["mapTileRadius"],
  				"highQuality" => false
  			);

			array_push($metaDataList, $metaData);
		}
	}

	echo json_encode($metaDataList);  	




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
  				"centerX" => $project["coords"]["x"],
  				"centerZ" => $project["coords"]["z"],
  				"radius" => $project["type"]["radius"],
  				"highQuality" => true
  			);
  			array_push($metaDataList, $metaData);
  		}
  		
  		return $metaDataList;
  	}

?>