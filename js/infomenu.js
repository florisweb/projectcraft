function _InfoMenu() {
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
        coordinatesHolder.classList.add("infoMenuHidden");
		mapCanvas.classList.add("infoMenuHidden");
		infoMenu.classList.add("hide");
		$(".button.infoMenuIcon").show();
	}

	this.open = function() {
		this.openState = true;
		
		zoomButtonHolder.classList.remove("infoMenuHidden");
        coordinatesHolder.classList.remove("infoMenuHidden");
		mapCanvas.classList.remove("infoMenuHidden");
		infoMenu.classList.remove("hide");
		
		$(".button.infoMenuIcon").hide();
	}


	this.createItemsByList = function(_array) {
		HTML.projectList.innerHTML = "";
		for (let i = 0; i < _array.length; i++)
		{
			_createItem(_array[i]);
		}
	}

	function _createItem(_info) {
		let startIndex = HTML.projectList.children.length;
		let html = '<div class="projectItem">' + 
			'<img class="headHolder">' +
			'<div class="headerText titleHolder preventTextOverflow"></div>' +
		'</div>';
		HTML.projectList.insertAdjacentHTML("beforeend", html);
		html = HTML.projectList.children[startIndex];

		setTextToElement(html.children[1], _info.title);

		html.children[0].setAttribute("src", _getHeadUrl(_info));
		
		html.onclick = function () {App.openProject(_info.title);};

		if (!_info.type.typeName) return;
		html.insertAdjacentHTML("beforeend", '<div class="typeName headerText preventTextOverflow"></div>');
		setTextToElement(html.children[2], _info.type.typeName);
	}
		function _getHeadUrl(_info) {
			let builderName = _info.builders[0];
			if (_info.builders.length > 1) builderName = "MHF_Exclamation";
			if (_info.customHead) builderName = _info.customHead;
			return "heads.php?type=avatar&username=" + builderName;
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


	this.openProjectPageByTitle = function(_title) {
		let item = Server.getItemByTitle(_title);
		if (!item) return;
		if (!this.openState) this.open();

		_updatedHeaderVisibility(item);
		
		setTextToElement(projectPage_titleHolder, item.title);
		setTextToElement(projectPage_coordHolder, "Coords: (" + item.coords.x + ", " + item.coords.z + ")");
		setTextToElement(projectPage_builderNames, item.builders.join(", "));
		setDescriptionText(item.description);
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

		function _updatedHeaderVisibility(_item) {
			HTML.builderHeader.style.display = "block";
			if (!_item.builders[0]) HTML.builderHeader.style.display = "none";

			HTML.descriptionHeader.style.display = "block";
			if (!_item.description) HTML.descriptionHeader.style.display = "none";

			HTML.imagesHeader.style.display = "block";
			if (!_item.images[0]) HTML.imagesHeader.style.display = "none";
		}

		function setDescriptionText(_text) {
			projectPage_description.innerHTML = "";
			
			let descriptionTextLines = _text.split("\n");
			for (line of descriptionTextLines)
			{	
				let lineHolder = document.createElement("div");
				setTextToElement(lineHolder, line);
				lineHolder.innerHTML += "<br>";
				projectPage_description.append(lineHolder);
			}
		}




}

