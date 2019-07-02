





function _map() {
	let canvas = document.getElementById("mapCanvas");
	let ctx = canvas.getContext("2d");
    let mapHolder = document.getElementById("mapHolder");
    var clickboxes = [];

   

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
        this.focusItem("Spawn");
	}
	
	this.registerPoint = function(_point) {
        let x = this.MCToDOM(_point.coords.x);
		let z = this.MCToDOM(_point.coords.z);
        
        let username = "ddrl46";
        if(_point.builders.length == 1) username = _point.builders;
        if(_point.builders.length > 1) username = "MHF_Exclamation";
        if(_point.customHead) username = _point.customHead;
        
        let colour = "#006bed";
        if(_point.type.typeName == "Farm") colour = "#8200ed";
        if(_point.customPin) colour = _point.customPin;
        
		this.drawPoint(x, z, _point.type.radius, username, colour, _point.displayPin);
        

        if (_point.clickable == false) return;
        clickboxes.push({
			root: _point.title,
			x: x - 24,
			y: z - 60,
			rx: x + 24,
			ry: z
        });
   	}
	

	this.drawPoint = function(x, y, radius, username, colour, displayPin) {
		let r = 20;
		ctx.fillStyle = "white";
		ctx.strokeStyle = "white";
		ctx.lineWidth = 2;

		if (radius) {
			ctx.beginPath();
			ctx.strokeStyle = colour;
			ctx.fillStyle = colour;
			ctx.arc(x, y, radius/4,0,2*Math.PI);
			ctx.stroke();
			ctx.globalAlpha = 0.2;
			ctx.fill();
			ctx.globalAlpha = 1;
		}
      

		if (!displayPin) return;
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
		img.onload = function() {ctx.drawImage(img, x - 16, y - 56, 32, 32);};
		img.src = "heads.php?type=head&scale=2&username=" + username;  
	}

    
   	this.MCToDOM = function(_mc) {
        return _mc / 4 + 1531;
    }
    
    this.DOMToMC = function(_dom) {
    	return (_dom - 1531) * 4;
    }

	this.handleClick = function(_x, _y) {
        for (c in clickboxes) 
        {
            if (_x < clickboxes[c].x || _y < clickboxes[c].y || _x > clickboxes[c].rx || _y > clickboxes[c].ry) continue;
           
            App.infoMenu.openProjectPageByTitle(clickboxes[c].root);
            this.focusItem(clickboxes[c].root);
            break;
        }
    }


	this.focusItem = function(_title) {
		let item = Server.getItemByTitle(_title);
		if (!item) return;
		if (this.zoomPercentage <= 1.5) this.zoom(2);
		this.panToMCCoords(item.coords.x, item.coords.z);
	}

	this.panToMCCoords = function(_x, _z) {// minecraft coords
		let x = this.MCToDOM(_x);
		let y = this.MCToDOM(_z);
		this.panToXY(x, y);
	}

	this.panToXY = function(_x, _y) { // canvas coords
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
		const startMapSize = $("#mapCanvas")[0].offsetWidth;
		
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
		$("#mapCanvas")[0].style.width 	= parseFloat(_percentage) * 100 + "vw";
		$("#mapCanvas")[0].style.height = parseFloat(_percentage) * 100 + "vw";

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
