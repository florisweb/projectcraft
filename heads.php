<?php
	if(!isset($_GET)) die("No data supplied.");
	if(!isset($_GET["username"])) die("No username supplied.");
	if(!isset($_GET["type"])) die("No type supplied.");

	$username = filter_var($_GET["username"], FILTER_SANITIZE_STRING);
	$type = filter_var($_GET["type"], FILTER_SANITIZE_STRING);

	$uuid = json_decode(file_get_contents("https://api.mojang.com/users/profiles/minecraft/".$username, true), true)["id"];

	header("content-type: image/png");

	$avatar = null;
	$scale = (isset($_GET["scale"]) ? $_GET["scale"] : 2);
	if($type == "avatar")
	{
	    $avatar = file_get_contents("https://crafatar.com/avatars/".$uuid."?overlay&size=32", true);
	} 
	else if($type == "head")
	{
	    $avatar = file_get_contents("https://crafatar.com/renders/head/".$uuid."?overlay&scale=".$scale, true);
	}
	else if($type == "body")
	{
	    $avatar = file_get_contents("https://crafatar.com/renders/body/".$uuid."?overlay&scale=".$scale, true);
	}
	else
	{
	    die("Invalid type.");   
	}

	echo $avatar;
?>