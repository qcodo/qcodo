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
			this.IE_8_0 = 8;
			this.IE_9_0 = 16;
		
			this.FIREFOX = 32;
			this.FIREFOX_1_0 = 64;
			this.FIREFOX_1_5 = 128;
			this.FIREFOX_2_0 = 256;
			this.FIREFOX_3_0 = 512;
			this.FIREFOX_3_5 = 1024;
			this.FIREFOX_4   = 2048;
		
			this.SAFARI = 4096;
			this.SAFARI_2_0 = 8192;
			this.SAFARI_3_0 = 16384;
			this.SAFARI_4_0 = 32768;
			this.SAFARI_5_0 = 65536;
		
			this.CHROME     = 131072;
			this.CHROME_2_0 = 262144;
			this.CHROME_3_0 = 524288;
			this.CHROME_4_0 = 1048576;
			this.CHROME_5_0 = 2097152;

			this.MACINTOSH = 4194304;
			this.IPHONE = 8388608;

			this.UNSUPPORTED = 16777216;

			// INTERNET EXPLORER (supporting versions 6.0, 7.0 and 8.0)
			if (navigator.userAgent.toLowerCase().indexOf("msie") >= 0) {
				this._intBrowserType = this.IE;

				if (navigator.userAgent.toLowerCase().indexOf("msie 6.0") >= 0)
					this._intBrowserType = this._intBrowserType | this.IE_6_0;
				else if (navigator.userAgent.toLowerCase().indexOf("msie 7.0") >= 0)
					this._intBrowserType = this._intBrowserType | this.IE_7_0;
				else if (navigator.userAgent.toLowerCase().indexOf("msie 8.0") >= 0)
					this._intBrowserType = this._intBrowserType | this.IE_8_0;
				else if (navigator.userAgent.toLowerCase().indexOf("msie 9.0") >= 0)
					this._intBrowserType = this._intBrowserType | this.IE_9_0;
				else
					this._intBrowserType = this._intBrowserType | this.UNSUPPORTED;

			// FIREFOX (supporting versions 1.0, 1.5, 2.0, 3.0 and 3.5)
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
				else if (strUserAgent.indexOf("firefox/3.0") >= 0)
					this._intBrowserType = this._intBrowserType | this.FIREFOX_3_0;
				else if (strUserAgent.indexOf("firefox/3.5") >= 0)
					this._intBrowserType = this._intBrowserType | this.FIREFOX_3_5;
				else if (strUserAgent.indexOf("firefox/4.0") >= 0)
					this._intBrowserType = this._intBrowserType | this.FIREFOX_4_0;
				else
					this._intBrowserType = this._intBrowserType | this.UNSUPPORTED;

			// CHROME (not yet supported)
			} else if (navigator.userAgent.toLowerCase().indexOf("chrome") >= 0) {
				this._intBrowserType = this.CHROME;
				this._intBrowserType = this._intBrowserType | this.UNSUPPORTED;

				if (navigator.userAgent.toLowerCase().indexOf("chrome/2.") >= 0)
					this._intBrowserType = this._intBrowserType | this.CHROME_2_0;
				else if (navigator.userAgent.toLowerCase().indexOf("chrome/3.") >= 0)
					this._intBrowserType = this._intBrowserType | this.CHROME_3_0;
				else if (navigator.userAgent.toLowerCase().indexOf("chrome/4.") >= 0)
					this._intBrowserType = this._intBrowserType | this.CHROME_4_0;
				else if (navigator.userAgent.toLowerCase().indexOf("chrome/5.") >= 0)
					this._intBrowserType = this._intBrowserType | this.CHROME_5_0;
				else
					this._intBrowserType = this._intBrowserType | this.UNSUPPORTED;

			// SAFARI (supporting version 2.0, 3.0 and 4.0)
			} else if (navigator.userAgent.toLowerCase().indexOf("safari") >= 0) {
				this._intBrowserType = this.SAFARI;
				
				if (navigator.userAgent.toLowerCase().indexOf("safari/41") >= 0)
					this._intBrowserType = this._intBrowserType | this.SAFARI_2_0;
				else if (navigator.userAgent.toLowerCase().indexOf("version/3.") >= 0)
					this._intBrowserType = this._intBrowserType | this.SAFARI_3_0;
				else if (navigator.userAgent.toLowerCase().indexOf("version/4.") >= 0)
					this._intBrowserType = this._intBrowserType | this.SAFARI_4_0;
				else if (navigator.userAgent.toLowerCase().indexOf("version/5.") >= 0)
					this._intBrowserType = this._intBrowserType | this.SAFARI_5_0;
				else
					this._intBrowserType = this._intBrowserType | this.UNSUPPORTED;

			// COMPLETELY UNSUPPORTED
			} else
				this._intBrowserType = this.UNSUPPORTED;

			// MACINTOSH?
			if (navigator.userAgent.toLowerCase().indexOf("macintosh") >= 0)
				this._intBrowserType = this._intBrowserType | this.MACINTOSH;

			// IPHONE?
			if (navigator.userAgent.toLowerCase().indexOf("iphone") >= 0)
				this._intBrowserType = this._intBrowserType | this.IPHONE;



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

			this.registerForm = function(strFormId, strFormState) {
				var objForm = document.getElementById(strFormId);

				// Register the Various Hidden Form Elements needed for QForms
				this.registerFormHiddenElement("Qform__FormId", objForm, document);
				this.registerFormHiddenElement("Qform__FormState", objForm, document);
				this.registerFormHiddenElement("Qform__FormControl", objForm, document);
				this.registerFormHiddenElement("Qform__FormEvent", objForm, document);
				this.registerFormHiddenElement("Qform__FormParameter", objForm, document);
				this.registerFormHiddenElement("Qform__FormCallType", objForm, document);
				this.registerFormHiddenElement("Qform__FormUpdates", objForm, document);
				this.registerFormHiddenElement("Qform__FormCheckableControls", objForm, document);
				
				// Set the QForm's FormId and FormState
				document.getElementById("Qform__FormId").value = strFormId;
				document.getElementById("Qform__FormState").value = strFormState;
			};

			this.registerFormHiddenElement = function(strId, objForm, objDocument) {
				var objHiddenElement = objDocument.createElement("input");
				objHiddenElement.type = "hidden";
				objHiddenElement.id = strId;
				objHiddenElement.name = strId;
				objForm.appendChild(objHiddenElement);
			};

			this.wrappers = new Array();

			this.registerAssetLocations = function(strJsAssets, strPhpAssets, strCssAssets, strImageAssets) {
				qc.jsAssets = strJsAssets;
				qc.phpAssets = strPhpAssets;
				qc.cssAssets = strCssAssets;
				qc.imageAssets = strImageAssets;
			};



		////////////////////////////////////
		// URL Hash Processing
		////////////////////////////////////
			this.processHashCurrent = null;
			this.processHashIntervalId = null;
			this.processHashControlId = null;

			this.registerHashProcessor = function(strControlId, intPollingInterval) {
				qc.processHashCurrent = null;
				qc.processHashControlId = strControlId;

				// Use native event for IE8/IE9 while NOT in IE7 CompatabilityMode
				if (('onhashchange' in window) && (document.documentMode != 7))
					window.onhashchange = this.processHash;
				else
					this.processHashIntervalId = setInterval("qc.processHash();", intPollingInterval);

				// Fire processor once to process hash on load instantly not waiting for interval
				this.processHash();
			};

			this.processHash = function() {
				// Get the Hash Value
				var strUrl = new String(document.location);

				// Only Proceed if it's different than before
				if (qc.processHashCurrent != strUrl.toString()) {
					// Update the stored current hash stuff
					qc.processHashCurrent = strUrl.toString();

					// Get Info Needed for the Control Proxy call
					var strFormId = document.getElementById("Qform__FormId").value;

					// Figure out the Hash data
					var strHashData = qc.getHashContent();

					// Make the callback
					qc.pA(strFormId, qc.processHashControlId, 'QClickEvent', strHashData, null);
				};
			};

			this.getHashContent = function() {
				var intPosition = qc.processHashCurrent.indexOf('#');
				var strHashData = "";

				if (intPosition > 0) strHashData = qc.processHashCurrent.substring(intPosition + 1);
				return strHashData;
			};

			this.clearHashProcessor = function() {
				//clear native event
				if ( 'onhashchange' in window )
					window.onhashchange = null;

				if (this.processHashIntervalId)
					clearInterval(this.processHashIntervalId);
			};

		////////////////////////////////////
		// Polling Processing
		////////////////////////////////////
			this.registerPollingProcessor = function(strControlId, intPollingInterval) {
				setTimeout("qc.processPolling('" + strControlId + "');", intPollingInterval);
			};

			this.processPolling = function(strControlId) {
				// Get Info Needed for the Control Proxy call
				var strFormId = document.getElementById("Qform__FormId").value;

				// Make the callback
				qc.pA(strFormId, strControlId, 'QClickEvent');
			};

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
	qc.regAL = qcodo.registerAssetLocations;
	qc.regHP = qcodo.registerHashProcessor;
	qc.clrHP = qcodo.clearHashProcessor;
	qc.regPP = qcodo.registerPollingProcessor;
