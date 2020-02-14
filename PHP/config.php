<?php
    global $CONFIGPath;
    $CONFIGPath = __DIR__ . "/../config.json";

    global $CONFIG;
	$CONFIG     = json_decode(file_get_contents($CONFIGPath), true);

    function WriteConfig($_newConfig) {
        file_put_contents($GLOBALS["CONFIGPath"], json_encode($_newConfig));
    }
?>