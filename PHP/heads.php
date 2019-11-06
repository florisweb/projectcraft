<?php
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    include "$root/git/projectcraft/PHP/config.php";

	if(!isset($_GET))                  die("No data supplied.");
	if(!isset($_GET["username"]))      die("No username supplied.");
	if(!isset($_GET["type"]))          die("No type supplied.");

	$username  = filter_var($_GET["username"], FILTER_SANITIZE_STRING);
	$type      = filter_var($_GET["type"], FILTER_SANITIZE_STRING);
   

    $uuids     = $CONFIG["server"]["members"];
    $uuid_found = false;
    $uuid = "";
    
    foreach($uuids as $u)
    {   
        if ($u[0] !== $username || empty($u[1])) continue;
        $uuid = $u[1];
        $uuid_found = true;
        break;
    }

    
    if (!$uuid_found)
    {
        $json = file_get_contents("https://api.mojang.com/users/profiles/minecraft/" . $username, true);
        if ($json === false) 
        {
            error_log("101");
            echo json_encode(["error" => 101]);
            exit;
        }
        
        $uuid = json_decode($json, true)["id"];
        
        if (is_array($uuids) || is_object($uuids))
        {
            foreach($uuids as &$u)
            {
                if ($u[0] === $username && empty($u[1])) $u[1] = $uuid;
            }
        }

        $CONFIG["server"]["members"] = $uuids;
        WriteConfig($CONFIG);
    }

    
    header("content-type: image/png");
    
    $avatar = null;
    
    $imageUrl = "images/headsCache/" . $uuid . "_" . $type . ".png";
    if (file_exists($imageUrl)) {
        $avatar = file_get_contents($imageUrl, true);
    } else
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
        else die("Invalid type.");   
    }

	echo $avatar;
    
    if(!file_exists($imageUrl) || hash_file("md5", $imageUrl) !== hash("md5", $avatar))
    {
        file_put_contents($imageUrl, $avatar);
    }
?>