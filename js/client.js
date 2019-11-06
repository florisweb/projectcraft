function _client() {
  let dataLoop = null;
  const canvas = document.getElementById("mapCanvas");
	const ctx = canvas.getContext("2d");
  
  this.init = function(_data) {
    let refresh_time = parseInt(_data["refresh-time"]);
    let diff = Date.now() - _data["current-time"];
    setTimeout(function() {
      dataLoop = setInterval(function() {
        Server.getData("api/updaterlog.txt").then(function(_data) {
          Client.update(_data);
        });
      }, refresh_time * 1000 + 500);
    }, refresh_time * 1000 - diff);
  }
  
  this.update = function(_data) {
    let radius = 10;
    console.log(_data);
    
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    Map.redrawPoints();
    
    ctx.fillStyle = "red";
    _data["players"].forEach(function(item, index) {
      let img = new Image();
      img.onload = function() {
        ctx.drawImage(img, Map.MCToDOM(item.x) - 16, Map.MCToDOM(item.z) - 16, 32, 32);
      };

      img.src = "PHP/heads.php?type=avatar&username=" + item.username;
      
      console.log("Drawn " + item.username + " at " + item.x + ", " + item.z);
    });
  }
}