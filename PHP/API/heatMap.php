<?php
	require_once __DIR__ . "/../config.php";
    
    global $HEATMAP;
    $HEATMAP = new HeatMap();

    class HeatMap {
    	private $path;

    	public function __construct() {
    		$this->path = __DIR__ . "/map/overworld/heatMap.json";
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
	    			"updates" => array()
	    		);
	    		$chunkIndex = sizeof($chunks);
    		}

    		array_push($chunk["updates"], time());
    		$chunks[$chunkIndex] = $chunk;
    		$this->writeData($chunks);
    	}

    	public function getHeatMap() {
            $chunks = $this->readData();
           
            for ($i = 0; $i < sizeof($chunks); $i++)
            {
                $curChunk = $chunks[$i];
                for ($u = 0; $u < sizeof($curChunk["updates"]); $u++)
                {
                    if (time() - $curChunk["updates"][$u] < $GLOBALS["CONFIG"]["API"]["heatMap"]["updateLifeTime"]) continue;
                    array_splice($chunks[$i]["updates"], $u, 1);
                }

                $timeSinceLastUpdate = time() - $curChunk["updates"][sizeof($curChunk["updates"]) - 1];
                $recensyLength = 60 * 60 * 2; // seconds;
                $recensyScore = ($recensyLength - $timeSinceLastUpdate) / $recensyLength * .2; 
                                // extra score when the last update is in the last hour
                if ($recensyScore < 0) $recensyScore = 0;

                $chunks[$i]["relativeHeat"] = sizeof($curChunk["updates"]) / 144 * 5 + $recensyScore; // max updates
                if ($chunks[$i]["relativeHeat"] > 1) $chunks[$i]["relativeHeat"] = 1;
            }

            $this->writeData($chunks);

            return $chunks;
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
?>