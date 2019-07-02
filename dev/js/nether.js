var clickboxes = [];

function Clickbox(root, x, y, rx, ry) {
    this.root = root;
	this.x = x;
	this.y = y;
	this.rx = rx;
	this.ry = ry;
}

function _map() {
	let canvas = document.getElementById("mapCanvas");
	let ctx = canvas.getContext("2d");
    let mapHolder = document.getElementById("mapHolder");
   
    this.settings = {
    	maxZoom: 10,
    	zoomStepSize: 1,
    	animationSpeed: 0
    }
    
	//Initialize function.
	this.init = function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height)
        ctx.fillStyle = "#261011";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
                
        Server.junctions[0].update();
        this.focusSpawn()
        
        for(let i = 0; i < Server.junctions.length; i++) 
		{ 
            this.registerPoint(Server.junctions[i]);
        }
	}
    
	this.registerPoint = function(_point) {
        let x = this.MCToDOM(_point.coords.x);
		let z = this.MCToDOM(_point.coords.z);
        
        let username = "ddrl46";
        if(_point.builders && _point.builders.length == 1) username = _point.builders;
        if(_point.builders && _point.builders.length > 1) username = "MHF_Chest";
        if(_point.customHead) username = _point.customHead;
        	
		if (_point.displayPoint || _point.displayPin) this.drawJunctionPoint(x, z, "#5d09c6", _point.displayPoint);
		if (_point.displayPin) this.drawPoint(x,z, username, "#5d09c6");
        
        console.log(username + " " + _point.id + " " + _point.clickable);
		
        if (_point.clickable) clickboxes.push(new Clickbox(_point.id,x - 24, z - 60, x + 24, z));
   	}



	this.drawPoint = function(x, y, username, colour) {
		let r = 20;
		ctx.fillStyle = "white";
		ctx.strokeStyle = "white";
		ctx.lineWidth = 2;

		ctx.fillStyle = "white";
		ctx.beginPath();
		ctx.moveTo(x,y);
		ctx.lineTo(x-r,y-2*r-1);
		ctx.lineTo(x,y-2*r-1);
		ctx.fill();

		let grd = ctx.createLinearGradient(x, y - 2 * r - 1, x + 0.5 * r, y);
		grd.addColorStop(0, "white")
		grd.addColorStop(1, "#aaa");
		ctx.fillStyle = grd;
		ctx.beginPath();
		ctx.moveTo(x,y);
		ctx.lineTo(x+r,y-2*r-1);
		ctx.lineTo(x,y-2*r-1);
		ctx.fill();

		let grd2 = ctx.createLinearGradient(x - r, y - r, x + r, y + r);

		grd2.addColorStop(0, colour);

		grd2.addColorStop(1, "#030303");
		ctx.fillStyle = grd2;
		ctx.beginPath();
		ctx.arc(x,y - 2 * r, r, 0, 2 * Math.PI);
		ctx.fill();
		ctx.fillStyle = "#ffffff";
		ctx.textAlign = "center"; 

		let img = new Image();

		img.onload = function() {
		ctx.drawImage(img, x-16, y-56, 32, 32);
		};

		img.src = "heads.php?type=head&scale=2&username=" + username;  
	}
    
    this.drawLine = function(startX, startZ, endX, endZ, displayPoint, colour) {
      ctx.fillStyle = "white";
      ctx.strokeStyle = "white";
      if(colour) ctx.strokeStyle = colour;
	  ctx.lineWidth = 3;
      
      ctx.beginPath();
      ctx.moveTo(this.MCToDOM(startX), this.MCToDOM(startZ));
      ctx.lineTo(this.MCToDOM(endX), this.MCToDOM(endZ));
      ctx.stroke();
    }
    
    this.drawJunctionPoint = function(x, z, _color, _large) {
      let r = 3;
      if(_large) r = 5;
      ctx.lineWidth = 2;
	  ctx.strokeStyle = "white";
	  ctx.fillStyle = "white";
	  if (_color) ctx.fillStyle = _color;
      
      ctx.beginPath();
      ctx.arc(x, z, r, 0, 2 * Math.PI);
      ctx.fill();
      ctx.stroke();
    }
    
   	this.MCToDOM = function(_mc) {
        return _mc + 1531;
    }
    
    this.DOMToMC = function(_dom) {
    	return _dom - 1531;
    }


	this.handleClick = function(_x, _y) {
        for(c in clickboxes) {
            if(_x >= clickboxes[c].x && _y >= clickboxes[c].y && _x <= clickboxes[c].rx && _y <= clickboxes[c].ry) {
                App.infoMenu.openProjectPageById(clickboxes[c].root);
                this.focusItem(clickboxes[c].root);
                break;
            }
        }
    }


    this.focusSpawn = function() {
    	for (let i = 0; i < Server.junctions.length; i++)
    	{
    		if(Server.junctions[i].title == "Spawn + JarPlayGo portal") return this.focusItem(Server.junctions[i].id);
    	}
    }

	this.focusItem = function(_id) {
		let item = Server.getItemById(_id);
		if (!item) return;
		if (this.zoomPercentage <= 1.5) this.zoom(2);
		this.panToMCCoords(item.coords.x, item.coords.z);
	}

	this.panToMCCoords = function(_x, _z) {//pixels on the canvas
		let x = this.MCToDOM(_x);
		let y = this.MCToDOM(_z);
		this.panToXY(x, y);
	}

	this.panToXY = function(_x, _y) {//pixels on the canvas
		let x = _x / canvas.width * canvas.offsetWidth - $("#mapHolder")[0].offsetWidth / 2;
		let y = _y / canvas.height * canvas.offsetHeight - $("#mapHolder")[0].offsetHeight / 2;

		$("#mapHolder").animate({
			scrollLeft: x + "px",
			scrollTop: y + "px"
		}, 500);
	}

	this.zoomPercentage = 1;
	this.zoom = function(_percentage = 1) {
		const screenWidth = document.body.offsetWidth;
		const screenHeight = document.body.offsetHeight;
	
		let startMapSize = $("#mapCanvas")[0].offsetWidth;
		
		let viewPosX = mapHolder.scrollLeft + (screenWidth / 2);
		let viewPosY = mapHolder.scrollTop + (screenHeight / 2);
		let percViewPosXMap = viewPosX / startMapSize; // %;
		let percViewPosYMap = viewPosY / startMapSize; // %;

		let endMapSize = startMapSize / this.zoomPercentage * _percentage;
        
   
		$("#mapHolder").animate({
			scrollLeft: percViewPosXMap * endMapSize - (screenWidth / 2) + "px",
			scrollTop: percViewPosYMap * endMapSize - (screenHeight / 2) + "px"
		},  this.settings.animationSpeed);


		$("#backgroundImage").animate({width: parseFloat(_percentage) * 100 + "vw"}, this.settings.animationSpeed);
		// height is auto so that one doesn't have to be set
		$("#mapCanvas")[0].style.width 			= parseFloat(_percentage) * 100 + "vw";
		$("#mapCanvas")[0].style.height 		= parseFloat(_percentage) * 100 + "vw";

		this.zoomPercentage = parseFloat(_percentage);
	}
	
	this.zoomIn = function() {
		if (this.zoomPercentage + this.settings.zoomStepSize > this.settings.maxZoom) return this.zoom(this.settings.maxZoom);
		this.zoom(this.zoomPercentage +  this.settings.zoomStepSize);
	}
    
	this.zoomOut = function() {
		if (this.zoomPercentage -  this.settings.zoomStepSize < 1) return this.zoom(1);
		this.zoom(this.zoomPercentage -  this.settings.zoomStepSize);
	}
}










function _server() {
  this.junctions = [];
  
  this.getJunction = function(_junctionTitle) {
	for (let i = 0; i < this.junctions.length; i++)
	{
		if (this.junctions[i].title == _junctionTitle) return this.junctions[i];
	}
    return false;
  }
  
  this.getPortals = function() {
      let portals = [];
      for(let i = 0; i < this.junctions.length; i++)
      {
          if (this.junctions[i].displayPin == true) portals.push(this.junctions[i]);
      }
      
      return portals;
  }
  
  this.getData = function() {
    return new Promise(function (resolve, fail) {
      REQUEST.send("uploads/nether.txt", "").then(

        function (_data) {
          if (typeof _data != "object") return console.error("nether.txt: there's a problem in your json syntax");
          
          Server.junctions = [];
          for (let i = 0; i < _data.length; i++)
          {
            let newJunction = new _server_junction(_data[i]);
            Server.junctions.push(newJunction);
          }
        
          resolve(Server.junctions);
        }, function (_e) {console.error("An error accured while trying to get nether.txt:", _e);}
      );

    });
  }

  this.getItemById = function(_id) {
    for (let i = 0; i < this.junctions.length; i++)
    {
      if (this.junctions[i].id == _id) return this.junctions[i];
    }
    return false;
  }

  this.getItemByTitle = function(_title) {
    _title = _title.toLowerCase();
    for (let i = 0; i < this.junctions.length; i++)
    {
      if (this.junctions[i].title.toLowerCase() == _title) return this.junctions[i];
    }
    return false;
  }
   

   
  this.getData();
}





function _server_junction(_junctionInfo) {
	let This = this;

	for (let i = 0; i < Object.keys(_junctionInfo).length; i++)
	{
		let curKey = Object.keys(_junctionInfo)[i];
		let curValue = _junctionInfo[curKey];
		this[curKey] = curValue;
	}

	this.updated = false;
	this.id = newId();

	this.update = function() {
		if (this.updated) return false;
		this.updated = true;
		updatedNeighbours();
	}

	function updatedNeighbours() {
		let neighbours = This.getNeighbours();

		for (let i = 0; i < neighbours.length; i++)
		{
			let neighbour = neighbours[i];
			neighbour.update();
            
            let colour = "#777777";
            if (This.neighbours[i][1] == 1) colour = "#4b69a1";
            
			Map.drawLine(This.coords.x, This.coords.z, neighbour.coords.x, neighbour.coords.z, This.displayPoint, colour);
		}
	}


	this.getNeighbours = function() {
		let neighbours = [];
		for (let i = 0; i < this.neighbours.length; i++)
		{
			let neighbour = Server.getJunction(this.neighbours[i][0]);
			if (!neighbour) continue;
			neighbours.push(neighbour);
		}
		return neighbours;
	}
}



























function _App_infoMenu() {
	let HTML = {
		projectList: projectListHolder,
		pages: $(".infoMenuPage"),
		builderHeader: $(".infoMenuPage .text.subHeader")[0],
		descriptionHeader: $(".infoMenuPage .text.subHeader")[1],
		imagesHeader: $(".infoMenuPage .text.subHeader")[2],
		netherPortalButton: $(".infoMenuPage .netherPortalButton")[0],
	}

	this.openState = true;
	this.close = function() {
		this.openState = false;
		zoomButtonHolder.classList.add("infoMenuHidden");
		mapCanvas.classList.add("infoMenuHidden");
		infoMenu.classList.add("hide");
		$(".button.infoMenuIcon").show();
	}

	this.open = function() {
		this.openState = true;
		zoomButtonHolder.classList.remove("infoMenuHidden");
		mapCanvas.classList.remove("infoMenuHidden");
		infoMenu.classList.remove("hide");
		$(".button.infoMenuIcon").hide();
	}


	this.createItemsByList = function(_array) {
		HTML.projectList.innerHTML = "";
		for (let i = 0; i < _array.length; i++)
		{
			this.createItem(_array[i]);
		}
	}

	this.createItem = function(_info) {
		let startIndex = HTML.projectList.children.length;
		let html = '<div class="projectItem">' + 
			'<img class="headHolder">' +
			'<div class="headerText titleHolder preventTextOverflow"></div>' +
		'</div>';
		HTML.projectList.insertAdjacentHTML("beforeend", html);
		html = HTML.projectList.children[startIndex];

		setTextToElement(html.children[1], _info.title);

		let builderName = _info.builders[0];
		if (_info.builders.length > 1) builderName = "chest";
		if (_info.customHead) builderName = _info.customHead;
		let imageUrl = "heads.php?type=avatar&username=" + builderName;
		html.children[0].setAttribute("src", imageUrl);
		
		html.onclick = function () {
			App.infoMenu.openProjectPageByTitle(_info.title);
			Map.focusItem(_info.id);
		};
	}

	this.pageIndex = 0;
	this.openPageByIndex = function(_index) {
		let curPage = HTML.pages[parseInt(_index)];
		if (!curPage) return;
		this.pageIndex = parseInt(_index);

		for (let i = 0; i < HTML.pages.length; i++)
		{
			HTML.pages[i].classList.add("hide");
		}
		curPage.classList.remove("hide");
	}


	this.openProjectPageById = function(_id) {
		let item = Server.getItemById(_id);
		if (!item) return;
		if (!this.openState) this.open();

		HTML.builderHeader.style.display = "block";
		if (!item.builders[0]) HTML.builderHeader.style.display = "none";

		HTML.netherPortalButton.style.display = "block";
		if (!item.overworldProjectTitle) HTML.netherPortalButton.style.display = "none";
		HTML.netherPortalButton.onclick = function() {
			App.infoMenu.goThroughPortal(item.overworldProjectTitle);
		};


		setTextToElement(projectPage_titleHolder, item.title);
		setTextToElement(projectPage_coordHolder, "Coords: (" + Math.round(item.coords.x/8) + ", " + Math.round(item.coords.z/8) + ")");
		setTextToElement(projectPage_builderNames, item.builders.join(", "));
		

		this.openPageByIndex(1);
	}

	this.openProjectPageByTitle = function(_title) {
		let item = Server.getItemByTitle(_title);
		if (!item) return;
		if (!this.openState) this.open();

		HTML.builderHeader.style.display = "block";
		if (!item.builders[0]) HTML.builderHeader.style.display = "none";

		HTML.netherPortalButton.style.display = "block";
		if (!item.overworldProjectTitle) HTML.netherPortalButton.style.display = "none";
		HTML.netherPortalButton.onclick = function() {
			App.infoMenu.goThroughPortal(item.overworldProjectTitle);
		};


		setTextToElement(projectPage_titleHolder, item.title);
		setTextToElement(projectPage_coordHolder, "Coords: (" + Math.round(item.coords.x/8) + ", " + Math.round(item.coords.z/8) + ")");
		setTextToElement(projectPage_builderNames, item.builders.join(", "));
		

		this.openPageByIndex(1);
	}

	this.goThroughPortal = function(_title) {
		window.location.replace("index.php?project=" + _title);
	}
}




















var App = new _App();
var Server = new _server();
var Map = new _map();

function _App() {
  this.infoMenu = new _App_infoMenu();

  this.update = function() {
    Server.getData().then(function () {
      App.infoMenu.createItemsByList(Server.getPortals());
      Map.init();
      if (executeUrlCommands) executeUrlCommands();
    }, function () {});
  }

  this.openProject = function(_title) {
    _title = _title.toLowerCase();
    App.infoMenu.openProjectPageByTitle(_title);
    let item = Server.getItemByTitle(_title);
    if (item.id) Map.focusItem(item.id);
  }


	this.setup = function() {
	  document.getElementById("mapCanvas").addEventListener("click", function(e) {
	    let mapCanvas = document.getElementById("mapCanvas");
	    let mapHolder = document.getElementById("mapHolder");

	    let mouseX = (e.x + mapHolder.scrollLeft) / (mapHolder.scrollWidth - 390 * App.infoMenu.openState);
	    let mouseY = (e.y + mapHolder.scrollTop) / mapHolder.scrollHeight;
	    let x = mouseX * mapCanvas.width;
	    let y = mouseY * mapCanvas.height;

	    Map.handleClick(x, y);
	  });


	  document.body.addEventListener("keydown", function(_e) {
	    if (_e.key == "Escape")
	    {
	      if (App.infoMenu.pageIndex == 1) return App.infoMenu.openPageByIndex(0);
	      if (App.infoMenu.openState) return App.infoMenu.close();
	    }
	    if (_e.key == "+") Map.zoomIn(); 
	    if (_e.key == "-") Map.zoomOut();
	    if (_e.key == "+" || _e.key == "-" || _e.key == "Escape") _e.preventDefault();
	  });

	  this.update();
	}
}





function setTextToElement(_element, _text) {
  if (!_element) return console.error("- setTextToElement: the element (", _element, ") doesn't exist.");
  _element.innerHTML = "";
  let a = document.createElement('a');
  a.text = String(_text);
  _element.append(a);
}

function newId() {return parseInt(Math.round(Math.random() * 100000000) + "" + Math.round(Math.random() * 100000000));}