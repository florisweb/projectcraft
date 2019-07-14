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
    Server = new _server();
    Map = new _map();
    Chat = new _chat();
    

    document.getElementById("mapCanvas").addEventListener("click", function(e) {
      let mapCanvas = document.getElementById("mapCanvas");
      let mapHolder = document.getElementById("mapHolder");

      let mouseX = (e.x + mapHolder.scrollLeft) / (mapHolder.scrollWidth - 390 * App.infoMenu.openState);
      let mouseY = (e.y + mapHolder.scrollTop) / mapHolder.scrollHeight;
      let x = mouseX * mapCanvas.width;
      let y = mouseY * mapCanvas.height;

      Map.handleClick(x, y);
    });
    


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
    

    this.update();
  }
}


