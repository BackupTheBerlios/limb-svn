function PopupURL(caller, url, handler, initFunction, windowName, windowParams) {
	this.caller = caller;
	this.url = url;
	this.handler = handler;
	
	if (!windowName)
		windowName = "__ha_dialog";
	
	if (typeof(window.popups) != 'array')
		window.popups = new Array();
	
	if (typeof(window.popups[windowName]) != 'array')
		window.popups[windowName] = new Array();
	
	window.popups[windowName]['init_popup'] = initFunction;
	window.popups[windowName]['process_popup'] = handler;
	
	var dlg = popup(url, windowName, windowParams);
	this.wnd = dlg;
	var doc = dlg.document;
	this.doc = doc;
	var self = this;

	var base = document.baseURI || document.URL;
	if (base && base.match(/(.*)\/([^\/]+)/)) {
		base = RegExp.$1 + "/";
	}
	this.baseURL = base;
};

PopupURL.prototype.callHandler = function() {
	var tags = ["input", "textarea", "select"];
	var params = new Object();
	for (var ti in tags) {
		var tag = tags[ti];
		var els = this.content.getElementsByTagName(tag);
		for (var j = 0; j < els.length; ++j) {
			var el = els[j];
			var val = el.value;
			if (el.tagName.toLowerCase() == "input") {
				if (el.type == "checkbox") {
					val = el.checked;
				}
			}
			params[el.name] = val;
		}
	}
	this.handler(this, params);
	return false;
};

PopupURL.prototype.close = function() {
	this.wnd.close();
};

PopupURL.prototype.addButtons = function() {
	var self = this;
	var div = this.doc.createElement("div");
	this.content.appendChild(div);
	div.className = "buttons";
	for (var i = 0; i < arguments.length; ++i) {
		var btn = arguments[i];
		var button = this.doc.createElement("button");
		div.appendChild(button);
		button.innerHTML = HTMLArea.I18N.buttons[btn];
		switch (btn) {
		    case "ok":
			button.onclick = function() {
				self.callHandler();
				self.close();
				return false;
			};
			break;
		    case "cancel":
			button.onclick = function() {
				self.close();
				return false;
			};
			break;
		}
	}
};

PopupURL.prototype.showAtElement = function() {
	var self = this;
	// Mozilla needs some time to realize what's goin' on..
	setTimeout(function() {
		var w = self.content.offsetWidth + 4;
		var h = self.content.offsetHeight + 4;
		// size to content -- that's fuckin' buggy in all fuckin' browsers!!!
		// so that we set a larger size for the dialog window and then center
		// the element inside... phuck!

		// center...
		var el = self.content;
		var s = el.style;
		// s.width = el.offsetWidth + "px";
		// s.height = el.offsetHeight + "px";
		s.position = "absolute";
		s.left = (w - el.offsetWidth) / 2 + "px";
		s.top = (h - el.offsetHeight) / 2 + "px";
		if (HTMLArea.is_gecko) {
			self.wnd.innerWidth = w;
			self.wnd.innerHeight = h;
		} else {
			self.wnd.resizeTo(w + 8, h + 35);
		}
	}, 25);
};
