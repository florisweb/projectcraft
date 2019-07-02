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

  let lastUpdatedData = "";
  this.getUpdaterData = function() {
    REQUEST.send("api/updaterlog.txt").then(this.parseUpdaterData);
  }
  
 
  this.parseUpdaterData = function(_data) {
    var loopTimer = setTimeout("Server.getUpdaterData()", _data["refresh-time"] * 1000 + 100);
    if (JSON.stringify(_data) == lastUpdatedData) return;
    if (Date.now() - _data["current-time"] > 2 * _data["refresh-time"] * 1000) return;
    console.warn("New Data", _data);

    if (_data.chat && typeof _data.chat == 'object')
    {
      for (message of _data.chat)
      {
        let username = message.sender.substr(0, 1).toUpperCase() + message.sender.substr(1, message.sender.length);
        Chat.addMessage(username, message.message);
      }
    }
    
    if (typeof _data.joined == 'object') 
    {
      for (user of _data.joined)
      {
        console.warn("join:", user);
        let username = user.username.substr(0, 1).toUpperCase() + user.username.substr(1, user.username.length);
        Chat.addMessage("", username + " joined the game!", "leaveJoinEvent");
      }
    }
     if (typeof _data.left == 'object') 
    {
      for (user of _data.left)
      {
        console.warn("left:", user);
        let username = user.username.substr(0, 1).toUpperCase() + user.username.substr(1, user.username.length);
        Chat.addMessage("", username + " left the game!", "leaveJoinEvent");
      }
    }
    
    lastUpdatedData = JSON.stringify(_data);
  }
   
  this.getData();
  this.getUpdaterData();
}