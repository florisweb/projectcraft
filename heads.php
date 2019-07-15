<?php
	if(!isset($_GET)) die("No data supplied.");
	if(!isset($_GET["username"])) die("No username supplied.");
	if(!isset($_GET["type"])) die("No type supplied.");
	$username = filter_var($_GET["username"], FILTER_SANITIZE_STRING);
	$type = filter_var($_GET["type"], FILTER_SANITIZE_STRING);
    
    $uuids = json_decode(file_get_contents("uploads/players.txt"), true);
    $uuid_found = false;
    $uuid = "";
    
    foreach($uuids as $u)
    {
        if($u[0] === $username && !empty($u[1]))
        {
            $uuid = $u[1];
            $uuid_found = true;
        }
    }
    
    if(!$uuid_found)
    {
        $json = file_get_contents("https://api.mojang.com/users/profiles/minecraft/".$username, true);
        if($json === false) {
            error_log("101");
            echo json_encode(["error" => 101]);
            exit;
        }
        
        $uuid = json_decode($json, true)["id"];
        $uuid_found = true;
        
        if(is_array($uuids) || is_object($uuids))
        {
            foreach($uuids as &$u)
            {
                if($u[0] === $username && empty($u[1])) $u[1] = $uuid;
            }
        }
        
        file_put_contents("uploads/players.txt", json_encode($uuids));
    }
    
    header("content-type: image/png");
    
    $avatar = null;
    
    if(file_exists("images/headsCache/".$uuid."_".$type.".png")) {
        $avatar = file_get_contents("images/headsCache/".$uuid."_".$type.".png", true);
    }
    else
    {
        $scale = (isset($_GET["scale"]) ? $_GET["scale"] : 2);
        if ($type == "avatar")
        {
            $avatar = file_get_contents("https://crafatar.com/avatars/" . $uuid . "?overlay&size=32", true);
        } 
        else if ($type == "head")
        {
            $avatar = file_get_contents("https://crafatar.com/renders/head/" . $uuid . "?overlay&scale=" . $scale, true);
        }
        else if ($type == "body")
        {
            $avatar = file_get_contents("https://crafatar.com/renders/body/" . $uuid . "?overlay&scale=" . $scale, true);
        }
        else
        {
            die("Invalid type.");   
        }
    }
	echo $avatar;
    
    if(!file_exists("images/headsCache/".$uuid."_".$type.".png") || hash_file("md5", "images/headsCache/".$uuid."_".$type.".png") !== hash("md5", $avatar))
    {
        file_put_contents("images/headsCache/".$uuid."_".$type.".png", $avatar);
    }
?>