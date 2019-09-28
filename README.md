# Guidelines

  <h3>Project & Portal Parameters</h3>
  
    - title STRING REQUIRED
    - builders JSON_ARRAY default: []. REQUIRED
    - coords JSON_OBJECT REQUIRED
    - type JSON_OBJECT REQUIRED
    
    - displayPoint BOOLEAN default: true
    - displayInList BOOLEAN default: true
    - clickable BOOLEAN default: true
    - customHead STRING
    - customPin HEX_COLOR
    - description STRING
    - dimensionLink STRING (Title of point in other dimension).
    - images JSON_ARRAY
  
  {coords}:
    
    - x INT REQUIRED
    - z INT REQUIRED
    
  {type}:
    
    - name ENUM REQUIRED
      ENUM options: BASE, AREA, BUILD
    - radius INT
    - genMiniMap BOOLEAN default: false
    - drawOnMap BOOLEAN default: false
    
  [images]:
    
    - PATH_STRING
  
  <h4>data.txt specific:</h4>
  
    - None.
  
  <h4>nether.txt specific:</h4>
  
    - neighbours JSON_ARRAY REQUIRED
  
  [neighbours]:
    
    - neighbour JSON_OBJECT
    
 {neighbour}:
    
    - name STRING REQUIRED
    - type STRING REQUIRED












  <h3>Map generation [data.txt only]</h3>
     For project-flags see: <i>Project & Portal Paramaters - Type</i>


  <h4>Main map</h4>
    
    Access: images/map/mainMap.php? --- ?? ---
    ImageStoreUrl: images/map/main
    FileName: worldName + "X" + coordX + "Z" + coordZ
    DefaultSize: 128 x 128


  <h4>Mini map</h4>
    
    Access: images/map/miniMap.php?projectId
    ImageStoreUrl: images/map/mini
    FileName: worldName + "X" + coordX + "Z" + coordY + "radius" + radius
    Size: Radius x Radius










