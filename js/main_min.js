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
        ctx.clearRect(0, 0, canvas.width, canvas.height);
		for(i = 0; i < Server.items.length; i++) 
		{ 
            this.registerPoint(Server.items[i]);
        }
	}
	
	this.registerPoint = function(_point) {
        let x = this.MCToDOM(_point.coords.x);
		let z = this.MCToDOM(_point.coords.z);
        
        let username = "ddrl46";
        if(_point.builders.length == 1) username = _point.builders;
        if(_point.builders.length > 1) username = "MHF_Chest";
        if(_point.customHead) username = _point.customHead;
        
        let colour = "#006bed";
        if(_point.type.typeName == "Farm") colour = "#8200ed";
        if(_point.customPin) colour = _point.customPin;
        
		this.drawPoint(x,z,_point.type.radius, username, colour, _point.displayPin);
        if(_point.clickable !== false) clickboxes.push(new Clickbox(_point.id,x-24,z-60,x+24,z));
        
        if(_point.title == "Spawn") this.focusItem(_point.id);
	}
	
	//1 = Gouden Snede, 2 = Symmetrie
	this.drawPoint = function(x, y, radius, username, colour, displayPin) {
      let r = 20;
	  ctx.fillStyle = "white";
	  ctx.strokeStyle = "white";
	  ctx.lineWidth = 2;
	  
      if(radius != 0) {
        ctx.beginPath();
        ctx.strokeStyle = colour;
        ctx.fillStyle = colour;
        ctx.arc(x, y, radius/4,0,2*Math.PI);
        ctx.stroke();
        ctx.globalAlpha = 0.2;
        ctx.fill();
        ctx.globalAlpha = 1;
      }
      
      if(displayPin) {
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
      
        img.src = "heads.php?type=head&scale=2&username="+username;  
      }
	}

    
   	this.MCToDOM = function(_mc) {
        return _mc / 4 + 1531;
    }
    
    this.DOMToMC = function(_dom) {
    	return (_dom - 1531) * 4;
    }

	//open menu's with: Server.open(object);
	this.handleClick = function(_x, _y) {
        for(c in clickboxes) {
            if(_x >= clickboxes[c].x && _y >= clickboxes[c].y && _x <= clickboxes[c].rx && _y <= clickboxes[c].ry) {
                App.infoMenu.openProjectPageByTitle(clickboxes[c].root);
                this.focusItem(clickboxes[c].root);
                break;
            }
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
  this.items = [];

  this.openItem = function(_index) {
    //item = clicked menu item.
    //App.infoMenu.open(item);
    //Map.panToCoords(item.coords.x, item.coords.y);
  }

  this.getData = function() {
    return new Promise(function (resolve, fail) {
      
      REQUEST.send("uploads/data.txt", "").then(
        function (_data) {
          if (typeof _data != "object") return console.error("data.txt: there's a problem in your json syntax");
          
          Server.items = [];
          for (let i = 0; i < _data.length; i++)
          {
            _data[i].id = newId();
            Server.items.push(_data[i]);
          }

          resolve(Server.items);
        }, function (_e) {console.error("An error accured while trying to get data.txt:", _e);}
      );
      
    });
  }

  this.getItemById = function(_id) {
    for (let i = 0; i < this.items.length; i++)
    {
      if (this.items[i].id == _id) return this.items[i];
    }
    return false;
  }
  
   
  this.getData();
}
 
function _App_infoMenu() {
	let HTML = {
		projectList: projectListHolder,
		pages: $(".infoMenuPage"),
		builderHeader: $(".infoMenuPage .text.subHeader")[0],
		descriptionHeader: $(".infoMenuPage .text.subHeader")[1],
		imagesHeader: $(".infoMenuPage .text.subHeader")[2],
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
			App.infoMenu.openProjectPageByTitle(_info.id);
			Map.focusItem(_info.id);
		};


		if (_info.type.typeName)
		{
			html.insertAdjacentHTML("beforeend", '<div class="typeName headerText preventTextOverflow"></div>');
			setTextToElement(html.children[2], _info.type.typeName);
		}
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


	this.openProjectPageByTitle = function(_id) {
		let item = Server.getItemById(_id);
		if (!item) return;
		if (!this.openState) this.open();

		HTML.builderHeader.style.display = "block";
		if (!item.builders[0]) HTML.builderHeader.style.display = "none";

		HTML.descriptionHeader.style.display = "block";
		if (!item.description) HTML.descriptionHeader.style.display = "none";

		HTML.imagesHeader.style.display = "block";
		if (!item.images[0]) HTML.imagesHeader.style.display = "none";


		setTextToElement(projectPage_titleHolder, item.title);
		setTextToElement(projectPage_coordHolder, "Coords: (" + item.coords.x + ", " + item.coords.z + ")");
		setTextToElement(projectPage_builderNames, item.builders.join(", "));
		
		setTextToElement(projectPage_description, item.description);
		
		_addImagesToPage(item.images);

		this.openPageByIndex(1);
	}

	function _addImagesToPage(_imageUrls) {
		projectPage_imageHolder.innerHTML = "";
		for (let i = 0; i < _imageUrls.length; i++)
		{
			projectPage_imageHolder.innerHTML += "<img class='infoImage'>";
			let image = projectPage_imageHolder.children[i];
			image.setAttribute("src", _imageUrls[i]);
		}
	}
}









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
  // document.body.onclick = toggleFullScreen;

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


  App.update();
}















function toggleFullScreen() {
  if ((window.fullScreen) || (window.innerWidth == screen.width && window.innerHeight == screen.height)) return;
  
  if ((document.fullScreenElement && document.fullScreenElement !== null) ||    
   (!document.mozFullScreen && !document.webkitIsFullScreen)) {
    if (document.documentElement.requestFullScreen) {  
      document.documentElement.requestFullScreen();  
    } else if (document.documentElement.mozRequestFullScreen) {  
      document.documentElement.mozRequestFullScreen();  
    } else if (document.documentElement.webkitRequestFullScreen) {  
      document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);  
    }  
  } else {  
    if (document.cancelFullScreen) {  
      document.cancelFullScreen();  
    } else if (document.mozCancelFullScreen) {  
      document.mozCancelFullScreen();  
    } else if (document.webkitCancelFullScreen) {  
      document.webkitCancelFullScreen();  
    }  
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



App.setup();