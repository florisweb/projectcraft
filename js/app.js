var App = new _App();
var Server = new _server();
var Map = new _map();


function _App() {
  this.infoMenu = new _App_infoMenu();
  this.homeScreen = new _App_homeScreen();


  this.update = function() {
    Server.getData().then(function () {
      App.infoMenu.createItemsByList(Server.items);
      Map.init();
      if (executeUrlCommands) executeUrlCommands()
    }, function () {});
  }


  this.openProject = function(_title) {
    App.infoMenu.openProjectPageByTitle(_title);
    Map.focusItem(_title);
  }
}






function _App_homeScreen() {
  let HTML = {
    homeScreen: homeScreen,
  }

  this.open = function() {
    HTML.homeScreen.classList.remove("hide");
  }

  function close() {
    HTML.homeScreen.classList.add("hide");
  }
  
  this.openMap = function() {
    close();
  }
}












App.setup = function() {
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


  App.update();
}



function setTextToElement(_element, _text) {
  if (!_element) return console.error("- setTextToElement: the element (", _element, ") doesn't exist.");
  _element.innerHTML = "";
  let a = document.createElement('a');
  a.text = String(_text);
  _element.append(a);
}







App.setup();
