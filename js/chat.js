function _chat() {
	let This = this;
	let HTML = {
		chatHolder: $("#chatHolder")[0],
		openButton: $("#chatHolder .chatOpener")
	}

	let Messages = [];


	this.addMessage = function(_senderName, _message, _type = "normal") {
		let messageHTML = _createMessageHTML(_senderName, _message, _type);
		HTML.chatHolder.append(messageHTML);

		let message = {
			html: messageHTML,
			sender: _senderName,
			message: _message,
			type: _type,
			
			fadeOut: function() {
				message.html.classList.add("fadeOut");
				var loopTimer = setTimeout(message.remove, 500);
			},
			remove: function () {
				message.html.parentNode.removeChild(message.html);
				Messages.splice(0, 1);

				if (!Messages[0]) return;
				setTimeout(Messages[0].fadeOut, 5 * 1000 + 50 * Messages[0].message.length);
			}
		}
		
		Messages.push(message);
		
		if (Messages.length != 1) return;
		setTimeout(message.fadeOut, 20 * 1000 + 50 * _message.length);
	}

		function _createMessageHTML(_senderName, _message, _type) {
			let html = document.createElement("div");
			html.className = "messageHolder minecraftFont " + _type;
			html.innerHTML = "<div class='messageText'></div>";

			setTextToElement(html.children[0], _message);

			html.children[0].innerHTML = "<div class='messageText senderName'></div>" + html.children[0].innerHTML;
			
			if (_senderName) _senderName += ":";
			setTextToElement(html.children[0].children[0], _senderName);
			
			return html;
		}



	this.openState = true;
	this.hide = function() {
		this.openState = false;
		HTML.chatHolder.classList.add("hide");
	}
	
	this.show = function() {
		this.openState = true;
		HTML.chatHolder.classList.remove("hide");
	}


	this.toggle = function() {
		switch (this.openState)
		{
			case true: this.hide(); break;
			default: this.show(); break;
		}
	}

}
