var App;
var Server;
var Map;
var Chat;

function _App() {
  this.infoMenu = new _App_infoMenu();

  this.update = function() {
    Server.getData().then(function () {
      App.infoMenu.createItemsByList(Server.items);
      Map.init();
      if (executeUrlCommands) executeUrlCommands()
    }, function () {});
  }


  this.openProject = function(_title) {
    _title = _title.toLowerCase();
    App.infoMenu.openProjectPageByTitle(_title);
    Map.focusItem(_title);
  }

  this.setup = function() {
    console.log("0");
    Server = new _server();
    Map = new _map();
    Chat = new _chat();
    
    console.log("1");

    document.getElementById("mapCanvas").addEventListener("click", function(e) {
      console.log("test0");
      let mapCanvas = document.getElementById("mapCanvas");
      let mapHolder = document.getElementById("mapHolder");

      let mouseX = (e.x + mapHolder.scrollLeft) / (mapHolder.scrollWidth - 390 * App.infoMenu.openState);
      let mouseY = (e.y + mapHolder.scrollTop) / mapHolder.scrollHeight;
      let x = mouseX * mapCanvas.width;
      let y = mouseY * mapCanvas.height;

      Map.handleClick(x, y);
    });
    
    document.onmousemove = function(e) {
      let mapCanvas = document.getElementById("mapCanvas");
      let mapHolder = document.getElementById("mapHolder");

      let mouseX = (e.x + mapHolder.scrollLeft) / (mapHolder.scrollWidth - 390 * App.infoMenu.openState);
      let mouseY = (e.y + mapHolder.scrollTop) / mapHolder.scrollHeight;
      let x = Map.DOMToMC(mouseX * mapCanvas.width);
      let y = Map.DOMToMC(mouseY * mapCanvas.height);
      
      document.getElementById("current_x").innerHTML = Math.round(x);
      document.getElementById("current_z").innerHTML = Math.round(y);
    }
    
    console.log("2");


    document.onkeydown = function(_e) {
      if (_e.key == "Escape")
      {
        if (App.infoMenu.pageIndex == 1) return App.infoMenu.openPageByIndex(0);
        if (App.infoMenu.openState) return App.infoMenu.close();
      }
      if (_e.key == "+") Map.zoomIn(); 
      if (_e.key == "-") Map.zoomOut();
      if (_e.key == "+" || _e.key == "-" || _e.key == "Escape") _e.preventDefault();
    };
    
    console.log("3");


    this.update();
    console.log("4");
  }
}









function setTextToElement(_element, _text) {
  if (!_element) return console.error("- setTextToElement: the element (", _element, ") doesn't exist.");
  _element.innerHTML = "";
  let a = document.createElement('a');
  a.text = String(_text);
  _element.append(a);
}





