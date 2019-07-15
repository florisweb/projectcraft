# ProjectCraft Code Documentation

# Module guidelines
    - Title [global object's name, for example: Map]
    - Description
    - Requires
    - Functions
    - Variables

<h2>InfoMenu [InfoMenu]</h2>
  InfoMenu is the window on the right side of the screen, responsible for showing a list of all projects and their information.

<h3>Functions</h3>
    
    - InfoMenu.open
    - InfoMenu.close
    - InfoMenu.createItemsByList(infoItem array)
    - InfoMenu.addItem(infoItem)
    - InfoMenu.openPageByIndex(PageIndex INT)



{infoItem}
  
    - title     STRING REQUIRED
    - imageUrl  PATH_STRING
    - onclick   FUNCTION(this)
    - typeName  STRING


<h3>Variables</h3>

    - InfoMenu.openState BOOLEAN
    - InfoMenu.pageIndex INT
    


EXTENDERS

<h2>InfoMenu_mapJsExtender [InfoMenu] - EXTENDS InfoMenu</h2>
InfoMenu is the window on the right side of the screen, responsible for showing a list of all projects and their information.


<h3>Functions</h3>

    - InfoMenu.openProjectPageByTitle(infoItem)
    - InfoMenu.goThroughPortal(ProjectTitle STRING)


{infoItem}: 
Documentation at README.md

<h2>Map [Map]</h2>
map.js is the drawing engine behind the website.<br><br>
Requires: nothing.

<h3>Functions</h3>

    - Map.init(points, factor):
      Call Map.init with an array of points which need to be drawn and a displacement factor. (4 for overworld, 1 for nether)
    
    - Map.registerPoint(point):
      Call to register a point.
    
    - Map.drawPoint(x, z, radius, username, colour):
      Call to draw a point.
    
    - Map.drawLine(startX, startZ, endX, endZ, colour):
      Call to draw a line.
    
    - Map.MCToDOM(x):
      Convert Minecraft coördinates to DOM coördinates
    
    - Map.DOMToMC(x):
      Convert DOM coördinates to Minecraft coördinates
    
    - Map.DOMPanTo(x, z):
      Pan to specified DOM coördinates
    
    - Map.panToItem(item):
      Pan to the position of the specified item.
    
    - Map.handleClick(x, z):
      Handle a click on the map at the specified clicking positions
      
    - Map.onItemClick(point): HANDLER
      This is a handler for page-specific code execution.
    
    - Map.findClickbox(x, z):
      Check if there's a clickbox at the specified DOM coördinates
    
    - Map.zoom(percentage):
      Zoom to the specified percentage
    
    - Map.zoomIn():
      Call to zoom in.
    
    - Map.zoomOut():
      Call to zoom out.

<h3>Variables</h3>
    
    - Settings:
      Contains these settings:
        - Max zoom (as maxZoom)
        - Zoom step size (as zoomStepSize)
        - Animation speed (as animationSpeed)