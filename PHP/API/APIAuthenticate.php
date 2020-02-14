<?php
  	require __DIR__ . "/../config.php";

  	$APIKey = (string)$_GET["API-key"];
  	if (!$APIKey || $APIKey != $CONFIG["API"]["API-key"]) die("Access refused: API-key invalid or missing");
?>