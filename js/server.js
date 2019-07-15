function _server() {
  this.items = [];

  this.getData = function() {
    return new Promise(function (resolve, fail) {
      REQUEST.send("uploads/data.txt", "").then(
        function (_data) {  
          if (typeof _data != "object") return console.error("data.txt: there's a problem in your json syntax");
          Server.items = _data;
          resolve(Server.items);
        }, function (_e) {console.error("An error accured while trying to get data.txt:", _e);}
      );
      
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
   
  this.getData();
}