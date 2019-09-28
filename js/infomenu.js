// DESCRIPTION
/*
	- Create the infomenu object:
	const InfoMenu = new _InfoMenu();

	- Functions:
	InfoMenu.close()
	InfoMenu.open()
	InfoMenu.openPageByIndex(index)
	

	InfoMenu.createitemsByList(array of items \/)	
	InfoMenu.addItem({
		title: "Title",
		imageUrl: "imageUrl",
		onclick: functionHandler(this),
		typeName: "typeName"
	})

*/




function _InfoMenu() {
	let HTML = {
		projectList: 			projectListHolder,
		pages: 					$(".infoMenuPage")
	}

	this.openState = true;
	this.pageIndex = 0;

	this.close = function() {
		this.openState = false;

		document.body.classList.add("infoMenuHidden");

		infoMenu.classList.add("hide");
		$(".button.infoMenuIcon").show();
	}

	this.open = function() {
		this.openState = true;

		document.body.classList.remove("infoMenuHidden");
		infoMenu.classList.remove("hide");
		
		$(".button.infoMenuIcon").hide();
	}
		
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


	this.createItemsByList = function(_array) {
		HTML.projectList.innerHTML = "";
		for (let i = 0; i < _array.length; i++)
		{
			this.addItem(_array[i]);
		}
	}
	
	this.addItem = function(_info) {
		let startIndex = HTML.projectList.children.length;
		let html = '<div class="projectItem">' + 
			'<img class="headHolder">' +
			'<div class="headerText titleHolder preventTextOverflow"></div>' +
		'</div>';
		HTML.projectList.insertAdjacentHTML("beforeend", html);
		
		html = HTML.projectList.children[startIndex];


		setTextToElement(html.children[1], _info.title);
		html.children[0].setAttribute("src", _info.imageUrl);
		
		html.onclick = function () {_info.onclick(_info);}
		html.addEventListener("click", function () {InfoMenu.onItemClick(_info);})
		
		if (!_info.typeName) return;
		html.insertAdjacentHTML("beforeend", '<div class="typeName headerText preventTextOverflow"></div>');
		setTextToElement(html.children[2], _info.typeName);
	}


	this.onItemClick = function() {};
	


	document.onkeyup = function(_e) {
		if (_e.key != "Escape") return;
		
		_e.preventDefault();
		if (InfoMenu.pageIndex == 1) 	return InfoMenu.openPageByIndex(0);
		if (InfoMenu.openState) 		return InfoMenu.close();
	}
}







function _InfoMenu_mapJsExtender() {
	_InfoMenu.call(this);
	let Inheriter = new _InfoMenu();
	let HTML = {
		builderHeader: 			$(".infoMenuPage .text.subHeader")[0],
		descriptionHeader: 		$(".infoMenuPage .text.subHeader")[1],
		miniMapHeader: 			$(".infoMenuPage .text.subHeader")[2],
		imagesHeader: 			$(".infoMenuPage .text.subHeader")[3],
		netherPortalButton: 	$(".infoMenuPage .netherPortalButton")[0],
		
		miniMapImg: 			$(".infoMenuPage .miniMapHolder .miniMapImg")[0],
		miniMapHolder: 			$(".infoMenuPage .miniMapHolder")[0],
	}



	this.addItem = function (_info) {
		if (_info.displayInList === false) return;
		_info.imageUrl 	= getHeadUrl(_info);
		_info.typeName 	= _info.type ? _info.type.name : "";
		_info.onclick 	= function () {
			InfoMenu.openProjectPageByTitle(_info.title);
		}
		
		return Inheriter.addItem(_info);
	}


	this.openProjectPageByTitle = function(_title) {
		let item = Server.getItemByTitle(_title);
		if (!item) return;
		if (!this.openState) this.open();

		updateHeaderVisibility(item);
		
		setTextToElement(projectPage_titleHolder, item.title);
		setTextToElement(projectPage_coordHolder, "Coords: (" + item.coords.x + ", " + item.coords.z + ")");
		if (item.builders) setTextToElement(projectPage_builderNames, item.builders.join(", "));
		
		setDescriptionText(item.description);
		addMiniMap(item);
		addImagesToPage(item.images);

		this.openPageByIndex(1);
	}

	this.goThroughPortal = function(_title) {
		window.location.replace("nether.php?project=" + _title);
	}

	function updateHeaderVisibility(_item) {
		HTML.builderHeader.style.display = "block";
		if (!_item.builders || _item.builders.length == 0) HTML.builderHeader.style.display = "none";

		HTML.descriptionHeader.style.display = "block";
		if (!_item.description) HTML.descriptionHeader.style.display = "none";

		HTML.imagesHeader.style.display = "block";
		if (!_item.images || _item.images.length == 0) HTML.imagesHeader.style.display = "none";


		HTML.netherPortalButton.style.display = "block";
		if (!_item.dimensionLink) HTML.netherPortalButton.style.display = "none";
		HTML.netherPortalButton.onclick = function() {
			InfoMenu.goThroughPortal(_item.dimensionLink);
		};

		HTML.miniMapHeader.style.display = "block";
		HTML.miniMapHolder.style.display = "block";
		if (!_item.type.genMiniMap) HTML.miniMapHeader.style.display = "none";
		if (!_item.type.genMiniMap) HTML.miniMapHolder.style.display = "none";
	}


	function setDescriptionText(_text) {
		projectPage_description.innerHTML = "";
		if (!_text) return false;
		
		let descriptionTextLines = _text.split("\n");
		for (line of descriptionTextLines)
		{	
			let lineHolder = document.createElement("div");
			setTextToElement(lineHolder, line);
			lineHolder.innerHTML += "<br>";
			projectPage_description.append(lineHolder);
		}
	}


	function addMiniMap(_project) {
		HTML.miniMapImg.setAttribute("src", "");
		if (!_project || !_project.type.genMiniMap) return false;
		let size = _project.type.radius * 2; // In MC-blocks
		let scalar = size / HTML.miniMapHolder.offsetWidth; // Makes sure every real pixel is 1 mc block
		let mcPxPerRealPixel = 1;

		HTML.miniMapImg.setAttribute("src", "images/miniMap.png");
		HTML.miniMapImg.style.width = 100 * scalar / mcPxPerRealPixel + "%";
	}


	function addImagesToPage(_imageUrls) {
		projectPage_imageHolder.innerHTML = "";
		if (!_imageUrls) return false;

		for (let i = 0; i < _imageUrls.length; i++)
		{
			projectPage_imageHolder.innerHTML += "<img class='infoImage'>";
			let image = projectPage_imageHolder.children[i];
			image.setAttribute("src", _imageUrls[i]);
		}
	}



	function getHeadUrl(_info) {
		let headName = "";
		if (_info.builders) headName = _info.builders[0]; 
		if (_info.builders && _info.builders.length > 1) headName = "MHF_Exclamation";
		if (_info.customHead) headName = _info.customHead;
		
		return "heads.php?type=avatar&username=" + headName;
	}
}


