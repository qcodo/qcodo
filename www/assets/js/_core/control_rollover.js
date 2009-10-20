/////////////////////////////////////////////
// Control: Dialog Box functionality
/////////////////////////////////////////////

	qcodo.registerImageRollover = function(mixControl, strStandardImageSource, strHoverImageSource, blnLinkFlag) {
		// Initialize the Event Handler
		qcodo.handleEvent();

		// Get Control/Wrapper
		var objControl; if (!(objControl = qcodo.getControl(mixControl))) return;
		var objWrapper = objControl.wrapper;

		objWrapper.standardImageSource = strStandardImageSource;
		objWrapper.hoverImageSource = strHoverImageSource;

		// Pull out the Image Element
		if (blnLinkFlag)
			objWrapper.imageElement = qcodo.getControl(objControl.id + "_img");
		else
			objWrapper.imageElement = objControl;

		// Setup the DialogBoxBackground (DbBg) if applicable
		objWrapper.handleMouseOver = function(objEvent) {
			objEvent = qcodo.handleEvent(objEvent);
			var objControl = this;
			var objWrapper = objControl.wrapper;
			var objImage = objWrapper.imageElement;

			var intWidth = objImage.width;
			var intHeight = objImage.height;

			objImage.src = objWrapper.hoverImageSource;
			objImage.width = intWidth;
			objImage.height = intHeight;
		};

		objWrapper.handleMouseOut = function(objEvent) {
			objEvent = qcodo.handleEvent(objEvent);
			var objControl = this;
			var objWrapper = objControl.wrapper;
			var objImage = objWrapper.imageElement;

			objImage.src = objWrapper.standardImageSource;
		};

		// Preload
		var objHoverImage = document.createElement("img");
		objHoverImage.src = strHoverImageSource;

		// Setup Event Handlers
		objControl.onmouseover = objWrapper.handleMouseOver;
		objControl.onmouseout = objWrapper.handleMouseOut;
	};



//////////////////
// Qcodo Shortcuts
//////////////////

	qc.regIR = qcodo.registerImageRollover;