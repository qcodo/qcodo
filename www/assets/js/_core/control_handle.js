///////////////////////////////
// Control Handle Functionality
///////////////////////////////

	qcodo.registerControlHandle = function(mixControl, strCursor) {
		var objControl; if (!(objControl = qcodo.getControl(mixControl))) return;
		var objWrapper = objControl.wrapper;

		if (!objWrapper.handle) {
			var objHandle = document.createElement("span");
			objHandle.id = objWrapper.id + "handle";
			objWrapper.parentNode.appendChild(objHandle);

			objWrapper.handle = objHandle;
			objHandle.wrapper = objWrapper;

			if (!objWrapper.style.position) {
				// The Wrapper is not defined as Positioned Relatively or Absolutely
				// Therefore, no offsetTop/Left/Width/Height values are available on the wrapper itself
				objHandle.style.width = objWrapper.control.style.width;
				objHandle.style.height = objWrapper.control.style.height;
				objHandle.style.top = objWrapper.control.offsetTop + "px";
				objHandle.style.left = objWrapper.control.offsetLeft + "px";
			} else {
				objHandle.style.width = objWrapper.offsetWidth + "px";
				objHandle.style.height = objWrapper.offsetHeight + "px";
				objHandle.style.top = objWrapper.offsetTop + "px";
				objHandle.style.left = objWrapper.offsetLeft + "px";
			};

			objHandle.style.cursor = strCursor;
			objHandle.style.zIndex = 999;
			objHandle.style.backgroundColor = "white";
			if (qcodo.isBrowser(qcodo.IE))
				objHandle.style.filter = "alpha(opacity=0)";
			else
				objHandle.style.opacity = 0.0;
			objHandle.style.position = "absolute";
			objHandle.style.fontSize = "1px";
			objHandle.innerHTML = ".";
		};

		objWrapper.updateHandle = function(blnUpdateParent, strCursor) {
			var objHandle = this.handle;

			// Make Sure the Wrapper's Parent owns this Handle
			if (blnUpdateParent)
				this.parentNode.appendChild(objHandle);

			// Fixup Size and Positioning
			objHandle.style.top = this.offsetTop + "px";
			objHandle.style.left = this.offsetLeft + "px";
			objHandle.style.width = this.offsetWidth + "px";
			objHandle.style.height = this.offsetHeight + "px";
			
			// Update the Cursor
			if (strCursor)
				objHandle.style.cursor = strCursor;
		};
	};



//////////////////
// Qcodo Shortcuts
//////////////////

	qc.regCH = qcodo.registerControlHandle;
