function _server() {
  this.items = [];

   this.getData = function(_filePath) {
      return new Promise(function (resolve, fail) {
          REQUEST.send(_filePath, "").then(function (_data) {  
              if (typeof _data != "object") return console.error(_filePath + ": there's a problem in your json syntax");
              Server.items = _data;
              resolve(_data);
          }, function (_e) {
              console.error("An error accured while trying to get " + _filePath, _e);
          });
      });
  }


  this.getItemByTitle = function(_title) {
    _title = _title.toLowerCase();
    for (let i = 0; i < this.items.length; i++)
    {
      if (this.items[i].title.toLowerCase() == _title) return this.items[i];
    }
    return false;
  }
   
}