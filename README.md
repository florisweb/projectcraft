# Guidelines
  <h3>Global Parameters</h3>
  
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
    
    - name STRING REQUIRED
    - radius INT
    
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





