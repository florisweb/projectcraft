# ProjectCraft Code Documentation

# API guidelines
  
<h2>Protocol description</h2>
    MC: MC-server
    WEB: Web-server

    1. MC: Fetch map-metadata from WEB: /PHP/API/getMapMetaData.php - JSON: See -mapMetaData (Array of metaDataObjects)
    2. loop:
      2a. MC: Generate map-data from map-metaData
      2b. MC: Send mapData to WEB: /PHP/API/uploadMap.php
          paramaters:
          - API-key
          - mapData: JSON mapData object
          
          returns:
          - boolean: success



<h2>Data-structure description</h2>

    - mapMetaData: JSON-Object
      {
        centerX: MC coord,
        centerZ: '',
        radius: MC-pixels (a mapObject is a square not a circle)
        highQuality: boolean (true for miniMaps, false for general maps)
      }


      - mapData: JSON-Object Extends mapMetaData
      {
        data: [ rgb values ]
      }





<h2>File storage</h2>

  <h4>General map</h4>
    
    Access: -
    ImageStoreUrl: /PHP/API/generalMap
    FileName: worldName + "X" + coordX + "Z" + coordZ + "R" + radius
    Size: 2 * radius x 2 * radius (See config.json)


  <h4>Mini map</h4>
    
    Access: -
    ImageStoreUrl: /PHP/API/miniMap
    FileName: worldName + "X" + coordX + "Z" + coordY + "R" + radius
    Size: Radius x Radius




