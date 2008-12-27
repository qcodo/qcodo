///////////////////////////////////////////////////
// The Qcodo Object is used for everything in Qcodo
///////////////////////////////////////////////////

	var qcodo = {
		initialize: function() {

		////////////////////////////////
		// Browser-related functionality
		////////////////////////////////

			this.isBrowser = function(intBrowserType) {
				return (intBrowserType & qcodo._intBrowserType);
			};
			this.IE = 1;
			this.IE_6_0 = 2;
			this.IE_7_0 = 4;

			this.FIREFOX = 8;
			this.FIREFOX_1_0 = 16;
			this.FIREFOX_1_5 = 32;
			this.FIREFOX_2_0 = 64;

			this.SAFARI = 128;
			this.SAFARI_2_0 = 256;
			this.SAFARI_3_0 = 512;

			this.MACINTOSH = 1024;

			this.UNSUPPORTED = 2048;

			// INTERNET EXPLORER (supporting versions 6.0 and 7.0)
			if (navigator.userAgent.toLowerCase().indexOf("msie") >= 0) {
				this._intBrowserType = this.IE;

				if (navigator.userAgent.toLowerCase().indexOf("msie 6.0") >= 0)
					this._intBrowserType = this._intBrowserType | this.IE_6_0;
				else if (navigator.userAgent.toLowerCase().indexOf("msie 7.0") >= 0)
					this._intBrowserType = this._intBrowserType | this.IE_7_0;
				else
					this._intBrowserType = this._intBrowserType | this.UNSUPPORTED;

			// FIREFOX (supporting versions 1.0, 1.5 and 2.0)
			} else if ((navigator.userAgent.toLowerCase().indexOf("firefox") >= 0) || (navigator.userAgent.toLowerCase().indexOf("iceweasel") >= 0)) {
				this._intBrowserType = this.FIREFOX;
				var strUserAgent = navigator.userAgent.toLowerCase();
				strUserAgent = strUserAgent.replace('iceweasel/', 'firefox/');

				if (strUserAgent.indexOf("firefox/1.0") >= 0)
					this._intBrowserType = this._intBrowserType | this.FIREFOX_1_0;
				else if (strUserAgent.indexOf("firefox/1.5") >= 0)
					this._intBrowserType = this._intBrowserType | this.FIREFOX_1_5;
				else if (strUserAgent.indexOf("firefox/2.0") >= 0)
					this._intBrowserType = this._intBrowserType | this.FIREFOX_2_0;
				else
					this._intBrowserType = this._intBrowserType | this.UNSUPPORTED;

			// SAFARI (supporting version 2.0 and eventually 3.0)
			} else if (navigator.userAgent.toLowerCase().indexOf("safari") >= 0) {
				this._intBrowserType = this.SAFARI;
				
				if (navigator.userAgent.toLowerCase().indexOf("safari/41") >= 0)
					this._intBrowserType = this._intBrowserType | this.SAFARI_2_0;
				else if (navigator.userAgent.toLowerCase().indexOf("safari/52") >= 0)
					this._intBrowserType = this._intBrowserType | this.SAFARI_3_0;
				else
					this._intBrowserType = this._intBrowserType | this.UNSUPPORTED;

			// COMPLETELY UNSUPPORTED
			} else
				this._intBrowserType = this.UNSUPPORTED;

			// MACINTOSH?
			if (navigator.userAgent.toLowerCase().indexOf("macintosh") >= 0)
				this._intBrowserType = this._intBrowserType | this.MACINTOSH;



		////////////////////////////////
		// Browser-related functionality
		////////////////////////////////

			this.loadJavaScriptFile = function(strScript, objCallback) {
				strScript = qc.jsAssets + "/" + strScript;
				var objNewScriptInclude = document.createElement("script");
				objNewScriptInclude.setAttribute("type", "text/javascript");
				objNewScriptInclude.setAttribute("src", strScript);
				document.getElementById(document.getElementById("Qform__FormId").value).appendChild(objNewScriptInclude);

				// IE does things differently...
				if (qc.isBrowser(qcodo.IE)) {
					objNewScriptInclude.callOnLoad = objCallback;
					objNewScriptInclude.onreadystatechange = function() {
						if ((this.readyState == "complete") || (this.readyState == "loaded"))
							if (this.callOnLoad)
								this.callOnLoad();
					};

				// ... than everyone else
				} else {
					objNewScriptInclude.onload = objCallback;
				};
			};

			this.loadStyleSheetFile = function(strStyleSheetFile, strMediaType) {
				strStyleSheetFile = qc.cssAssets + "/" + strStyleSheetFile;

				// IE does things differently...
				if (qc.isBrowser(qcodo.IE)) {
					var objNewScriptInclude = document.createStyleSheet(strStyleSheetFile);

				// ...than everyone else
				} else {
					var objNewScriptInclude = document.createElement("style");
					objNewScriptInclude.setAttribute("type", "text/css");
					objNewScriptInclude.setAttribute("media", strMediaType);
					objNewScriptInclude.innerHTML = '@import "' + strStyleSheetFile + '";';
					document.body.appendChild(objNewScriptInclude);
				};
			};



		/////////////////////////////
		// QForm-related functionality
		/////////////////////////////

			this.registerForm = function() {
				// "Lookup" the QForm's FormId
				var strFormId = document.getElementById("Qform__FormId").value;

				// Register the Various Hidden Form Elements needed for QForms
				this.registerFormHiddenElement("Qform__FormControl", strFormId);
				this.registerFormHiddenElement("Qform__FormEvent", strFormId);
				this.registerFormHiddenElement("Qform__FormParameter", strFormId);
				this.registerFormHiddenElement("Qform__FormCallType", strFormId);
				this.registerFormHiddenElement("Qform__FormUpdates", strFormId);
				this.registerFormHiddenElement("Qform__FormCheckableControls", strFormId);
			};

			this.registerFormHiddenElement = function(strId, strFormId) {
				var objHiddenElement = document.createElement("input");
				objHiddenElement.type = "hidden";
				objHiddenElement.id = strId;
				objHiddenElement.name = strId;
				document.getElementById(strFormId).appendChild(objHiddenElement);
			};

			this.wrappers = new Array();



		////////////////////////////////////
		// Mouse Drag Handling Functionality
		////////////////////////////////////

			this.enableMouseDrag = function() {
				document.onmousedown = qcodo.handleMouseDown;
				document.onmousemove = qcodo.handleMouseMove;
				document.onmouseup = qcodo.handleMouseUp;
			};

			this.handleMouseDown = function(objEvent) {
				objEvent = qcodo.handleEvent(objEvent);

				var objHandle = qcodo.target;
				if (!objHandle) return true;

				var objWrapper = objHandle.wrapper;
				if (!objWrapper) return true;

				// Qcodo-Wide Mouse Handling Functions only operate on the Left Mouse Button
				// (Control-specific events can respond to QRightMouse-based Events)
				if (qcodo.mouse.left) {
					if (objWrapper.handleMouseDown) {
						// Specifically for Microsoft IE
						if (objHandle.setCapture)
							objHandle.setCapture();

						// Ensure the Cleanliness of Dragging
						objHandle.onmouseout = null;
						if (document.selection)
							document.selection.empty();

						qcodo.currentMouseHandleControl = objWrapper;
						return objWrapper.handleMouseDown(objEvent, objHandle);
					};
				};

				qcodo.currentMouseHandleControl = null;
				return true;
			};

			this.handleMouseMove = function(objEvent) {
				objEvent = qcodo.handleEvent(objEvent);

				if (qcodo.currentMouseHandleControl) {
					var objWrapper = qcodo.currentMouseHandleControl;
					var objHandle = objWrapper.handle;

					// In case IE accidentally marks a selection...
					if (document.selection)
						document.selection.empty();

					if (objWrapper.handleMouseMove)
						return objWrapper.handleMouseMove(objEvent, objHandle);
				};

				return true;
			};

			this.handleMouseUp = function(objEvent) {
				objEvent = qcodo.handleEvent(objEvent);

				if (qcodo.currentMouseHandleControl) {
					var objWrapper = qcodo.currentMouseHandleControl;
					var objHandle = objWrapper.handle;

					// In case IE accidentally marks a selection...
					if (document.selection)
						document.selection.empty();

					// For IE to release release/setCapture
					if (objHandle.releaseCapture) {
						objHandle.releaseCapture();
						objHandle.onmouseout = function() {this.releaseCapture()};
					};

					qcodo.currentMouseHandleControl = null;

					if (objWrapper.handleMouseUp)
						return objWrapper.handleMouseUp(objEvent, objHandle);
				};

				return true;
			};



		////////////////////////////////////
		// Window Unloading
		////////////////////////////////////

			this.unloadFlag = false;
			this.handleUnload = function() {
				qcodo.unloadFlag = true;
			};
			window.onunload= this.handleUnload;

			this.beforeUnloadFlag = false;
			this.handleBeforeUnload = function() {
				qcodo.beforeUnloadFlag = true;
			};
			window.onbeforeunload= this.handleBeforeUnload;



		////////////////////////////////////
		// Color Handling Functionality
		////////////////////////////////////

			this.colorRgbValues = function(strColor) {
				strColor = strColor.replace("#", "");

				try {
					if (strColor.length == 3)
						return new Array(
							eval("0x" + strColor.substring(0, 1)),
							eval("0x" + strColor.substring(1, 2)),
							eval("0x" + strColor.substring(2, 3))
						);
					else if (strColor.length == 6)
						return new Array(
							eval("0x" + strColor.substring(0, 2)),
							eval("0x" + strColor.substring(2, 4)),
							eval("0x" + strColor.substring(4, 6))
						);
				} catch (Exception) {};

				return new Array(0, 0, 0);
			};

			this.hexFromInt = function(intNumber) {
				intNumber = (intNumber > 255) ? 255 : ((intNumber < 0) ? 0 : intNumber);
				intFirst = Math.floor(intNumber / 16);
				intSecond = intNumber % 16;
				return intFirst.toString(16) + intSecond.toString(16);
			};

			this.colorRgbString = function(intRgbArray) {
				return "#" + qcodo.hexFromInt(intRgbArray[0]) + qcodo.hexFromInt(intRgbArray[1]) + qcodo.hexFromInt(intRgbArray[2]);
			};
		}
	};



////////////////////////////////
// Qcodo Shortcut and Initialize
////////////////////////////////

	var qc = qcodo;
	qc.initialize();
