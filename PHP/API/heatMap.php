<?php
	require_once "../config.php";
    
    global $HEATMAP;
    $HEATMAP = new HeatMap();

    class HeatMap {
    	private $path;

    	public function __construct() {
    		$this->path = "map/overworld/heatMap.json";
    	}

    	public function updateChunk($_chunkX, $_chunkZ, $_chunkSize) {
    		$chunks = $this->readData();
    		$chunkIndex = $this->getChunkIndexByData($_chunkX, $_chunkZ, $_chunkSize);

    		$chunk = $chunks[(int)$chunkIndex];
    		if (!is_int($chunkIndex)) 
    		{
    			$chunk = array(
	    			"x" => (int)$_chunkX,
	    			"z" => (int)$_chunkZ,
	    			"size" => (int)$_chunkSize,
	    			"lastUpdate" => time(),
	    			"updates" => 0
	    		);
	    		$chunkIndex = sizeof($chunks);
    		}

    		$chunk["updates"]++;
      		$chunk["lastUpdate"] = time();

    		$chunks[$chunkIndex] = $chunk;
    		$this->writeData($chunks);
    	}

    	public function getHeatMap() {
    		$maxUpdates 	= $this->getHighestChunkUpdates();

    		$chunks = $this->readData();
    		for ($i = 0; $i < sizeof($chunks); $i++)
    		{
    			$chunks[$i]["relativeHeat"] = $chunks[$i]["updates"] / $maxUpdates;
    		}

    		return $chunks;
    	}
    	
    
    	private function getHighestChunkUpdates() {
    		$data = $this->readData();
    		$maxUpdates = 0;
    		for ($i = 0; $i < sizeof($data); $i++) if ($maxUpdates < $data[$i]["updates"]) $maxUpdates = $data[$i]["updates"];
    		return $maxUpdates;
    	}



    	public function getChunkIndexByData($_chunkX, $_chunkZ, $_chunkSize) {
    		$data = $this->readData();

    		for ($i = 0; $i < sizeof($data); $i++)
    		{
    			if ($data[$i]["x"] 		!= (int)$_chunkX) continue;
    			if ($data[$i]["z"] 		!= (int)$_chunkZ) continue;
    			if ($data[$i]["size"] 	!= (int)$_chunkSize) continue;
    			return $i;
    		}

    		return false;
    	}

 


    	private function writeData($_data) {
        	file_put_contents($this->path, json_encode($_data));
    	}
    	private function readData() {
    		if (!file_exists($this->path)) return array();
        	$data = file_get_contents($this->path);
        	return json_decode($data, true);
    	}
    }


    
    // $HEATMAP->updateChunk(0, 0, 128);

    // echo "<pre>";
    // var_dump($HEATMAP->getHeatMap());
?>