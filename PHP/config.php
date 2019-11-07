<?php
    global $CONFIGPath;
    $root       = realpath($_SERVER["DOCUMENT_ROOT"]);
    $CONFIGPath = "$root/config.json";
    // $CONFIGPath = "$root/git/projectcraft/config.json";

    global $CONFIG;
	$CONFIG     = json_decode(file_get_contents($CONFIGPath), true);

    function WriteConfig($_newConfig) {
        file_put_contents($GLOBALS["CONFIGPath"], json_encode($_newConfig));
    }
?>