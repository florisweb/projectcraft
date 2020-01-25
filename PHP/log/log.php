<?php
	global $LOGPath;
	$root       = realpath($_SERVER["DOCUMENT_ROOT"]);
    $LOGPath 	= "$root/PHP/log/log.json";
	
	$CONFIG     = json_decode(file_get_contents($CONFIGPath), true);

    function AddLog($_text) {
    	$timeStamp = date("Y-m-d H:i:s");
    	$string = $timeStamp . ": " . $_text;

    	$content = file_get_contents($GLOBALS["LOGPath"]);
    	$content = $content . $string . "\n";

        file_put_contents($GLOBALS["LOGPath"], $content);
    }
?>