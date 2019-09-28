<?php
    $_projectId = (string)$_GET["projectId"];
	if(!isset($_projectId)) die("No projectId supplied.");
	

    $project = getProject($_projectId);
    if (!$project) die("Couldn't find project.");
    $miniMapName = getMiniMapName($project);
    
    $url = "images/maps/" . $miniMapName . ".png";
    
    if (!file_exists($url)) die();
    echo file_get_contents($url, true);



    function getProject($_projectId) {
        $projects = json_decode(file_get_contents("uploads/data.txt"), true);
        for ($i = 0; $i < sizeof($projects); $i++)
        {
            $project = $projects[$i];
            if ($project["id"] != $_projectId) continue;
            if (!$project["type"]["genMiniMap"]) continue;

            return $project;
        }
        return false;
    }

    function getMiniMapName($_project) {
        return "miniMapX" . $_project["coords"]["x"] . "Z" . $_project["coords"]["z"] . "R" . $_project["type"]["range"];
    }
?>