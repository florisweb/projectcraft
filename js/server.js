function _server() {
  this.items = [];
  this.heatMaps = [];

  this.getData = async function(_url) {
      let data = await fetchData(_url);
      Server.items = data;
      return data;
  }

  this.getHeatMaps = async function() {
      let data = await fetchData("PHP/getHeatMaps.php");
      Server.heatMaps = data;
      return data;
  }


  this.getItemByTitle = function(_title) {
    _title = _title.toLowerCase();
    for (let i = 0; i < this.items.length; i++)
    {
      if (this.items[i].title.toLowerCase() == _title) return this.items[i];
    }
    return false;
  }

  function fetchData(_url) {
     return new Promise(function (resolve, fail) {
        REQUEST.send(_url, "").then(function (_data) {  
            if (typeof _data != "object") return console.error(_url + ": there's a problem in your json syntax");
            resolve(_data);
        }, function (_e) {
            console.error("An error accured while trying to get " + _url, _e);
        });
    });
  }
   
}