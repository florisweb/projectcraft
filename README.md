# ProjectCraft Code Documentation

# Guidelines
  <h3>Global Parameters</h3>
  - title* STRING
  - builders* JSON_ARRAY default: [].
  - coords* JSON_OBJECT
  - type* JSON_OBJECT
  
  - clickable BOOLEAN default: true
  - customHead STRING
  - customPin HEX_COLOR
  - description STRING
  - dimensionLink STRING (Title of point in other dimension).
  - images JSON_ARRAY
  
  {coords}:
    - x* INT
    - z* INT
    
  {type}:
    - name* STRING
    - radius INT
    
  [images]:
    - PATH_STRING
  
  <h4>data.txt specific:</h4>
  - None.
  
  <h4>nether.txt specific:</h4>
  - neighbours JSON_ARRAY
  
  [neighbours]:
    - neighbour JSON_OBJECT
    
 {neighbour}:
    - name* STRING
    - type* STRING