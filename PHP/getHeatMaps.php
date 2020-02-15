<?php
    require_once __DIR__ . "/API/heatMap.php";
    echo json_encode($HEATMAP->getHeatMap());
?>