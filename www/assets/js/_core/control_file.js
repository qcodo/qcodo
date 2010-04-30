/////////////////////////////////////////////
// Control: Dialog Box functionality
/////////////////////////////////////////////

	qcodo.registerFileUploaderControl = function(mixControl, strPostBack, strUniqueHash) {
		// Initialize the Event Handler
		qcodo.handleEvent();

		// Get Control/Wrapper
		var objControl; if (!(objControl = qcodo.getControl(mixControl))) return;
		var objWrapper = objControl.wrapper;

		var strFileControlId = objWrapper.id + "flc";
		var objForm = document.getElementById(document.getElementById("Qform__FormId").value);

		objWrapper.uniqueHash = strUniqueHash;
		objWrapper.button = document.getElementById(objControl.id + "_button");
		objWrapper.progress = document.getElementById(objControl.id + "_progress");
		objWrapper.progress.size = document.getElementById(objControl.id + "_size");
		objWrapper.progress.status = document.getElementById(objControl.id + "_status");
		objWrapper.progress.fill = document.getElementById(objControl.id + "_fill");
		objWrapper.iframe = document.getElementById(objControl.id + "_iframe");


		// Setup the Outer Span
		var objOuterSpan = document.getElementById(objControl.id + "_ospan");
		objOuterSpan.style.width = "0px";
		objOuterSpan.style.height = "0px";
		objOuterSpan.style.position = "absolute";
		objOuterSpan.style.display = "inline";
		objOuterSpan.style.overflow = "visible";
		objOuterSpan.style.margin = 0;
		objOuterSpan.style.padding = 0;
		objOuterSpan.style.border = 0;
		
		// Setup the iFrame
		var objFupIframe = objWrapper.iframe;
		objFupIframe.style.width = objWrapper.button.offsetWidth + "px";
		objFupIframe.style.height = objWrapper.button.offsetHeight + "px";
		objFupIframe.style.border = 0;
		objFupIframe.style.padding = 0;
		objFupIframe.style.margin = 0;
		
		objFupIframe.style.display = "inline";
		objFupIframe.style.position = "relative";
		objFupIframe.style.left = "-" + objWrapper.button.offsetWidth + "px";
		objFupIframe.style.opacity = 0;
		objFupIframe.style.filter = "alpha(opacity=0)";
		objFupIframe.style.overflow = "hidden";

		var objFrameDoc = objFupIframe.contentDocument;
		if ((objFrameDoc == undefined) || (!objFrameDoc))
			objFrameDoc = objFupIframe.contentWindow.document;
		objWrapper.frameDoc = objFrameDoc;

		var strFormId = objWrapper.id + "form";
		var strFileControlId = objWrapper.id + "flc";
		objFrameDoc.open();
		objFrameDoc.writeln('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
		objFrameDoc.writeln('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"><head>');
		objFrameDoc.writeln('<body style="margin: 0; padding: 0;"><form method="post" action="' + strPostBack + '" enctype="multipart/form-data" id="' + strFormId + '">');
		objFrameDoc.writeln('<div id="' + objControl.id + '_iframediv"></div>');
		objFrameDoc.writeln('<input type="hidden" name="APC_UPLOAD_PROGRESS" id="APC_UPLOAD_PROGRESS" value="' + objWrapper.uniqueHash + '" />');
		objFrameDoc.writeln('<input type="hidden" name="Qform__FormState" id="Qform__FormState" value="" />');			
		objFrameDoc.writeln('<input type="hidden" name="Qform__FormId" id="Qform__FormId" value="' + objForm.id + '" />');
		objFrameDoc.writeln('</form></body></html>');
		objFrameDoc.close();

		var objFrameForm = objFrameDoc.getElementById(strFormId);
		qcodo.registerFormHiddenElement("Qform__FormControl", objFrameForm, objFrameDoc);
		qcodo.registerFormHiddenElement("Qform__FormEvent", objFrameForm, objFrameDoc);
		qcodo.registerFormHiddenElement("Qform__FormParameter", objFrameForm, objFrameDoc);
		qcodo.registerFormHiddenElement("Qform__FormCallType", objFrameForm, objFrameDoc);
		qcodo.registerFormHiddenElement("Qform__FormUpdates", objFrameForm, objFrameDoc);
		qcodo.registerFormHiddenElement("Qform__FormCheckableControls", objFrameForm, objFrameDoc);
	
		// Setup the Nested DIV and INPUT FILE form element
		var objIframeDiv = objFrameDoc.getElementById(objControl.id + "_iframediv");
		var objFileControl = objFrameDoc.createElement("input");
		objFileControl.type = "file";
		objIframeDiv.appendChild(objFileControl);

		objIframeDiv.style.width = objWrapper.button.offsetWidth + "px";
		objIframeDiv.style.height = objWrapper.button.offsetHeight + "px";
		objIframeDiv.style.overflow = "hidden";

		objFileControl.style.position = "relative";
		objFileControl.id = strFileControlId;
		objFileControl.name = strFileControlId;
		objFileControl.style.left = "-155px";

		// We need to take into account the "textbox by the browse button" in a <input type="file"> control
		// basically we need to shift everything over to the left so that we're only looking at the button and not the textbox
		if (qcodo.isBrowser(qcodo.IE)) {
			// IE
			objFileControl.style.left = "-158px";
			objFileControl.style.top = "-2px";
		} else if (qcodo.isBrowser(qcodo.FIREFOX)) {
			if (qcodo.isBrowser(qcodo.MACINTOSH)) {
				// Firefox for Mac
				objFileControl.style.left = "-155px";
				objFileControl.style.top = "-2px";
			} else {
				// Firefox for Windows
				objFileControl.style.left = "-146px";
			};
		} else {
			// Safari doesn't need any adjustments
		};

		// Save References to Objects
		objWrapper.iframe.wrapper = objWrapper;
		objWrapper.frameForm = objFrameForm;
		objWrapper.fileControl = objFileControl;
		objWrapper.fileControl.wrapper = objWrapper;

		objWrapper.executeSubmit = function() {
			var objWrapper = this.wrapper;
			var objForm = document.getElementById(document.getElementById("Qform__FormId").value);

			objWrapper.iframe.style.display = "none";
			objWrapper.button.style.display = "none";
			objWrapper.progress.style.display = "block";

			// Get the FrameDoc and FrameForm
			var objFrameDoc = objWrapper.frameDoc;
			var objFrameForm = objWrapper.frameForm;

			objFrameForm.Qform__FormState.value = objForm.Qform__FormState.value;
			objFrameForm.Qform__FormControl.value = objWrapper.control.id;
			objFrameForm.Qform__FormControl.value = objWrapper.control.id;
			objFrameForm.Qform__FormEvent.value = 'QFileUploadedEvent';
			objFrameForm.Qform__FormParameter.value = 'foobar';
			objFrameForm.Qform__FormCallType.value = "Ajax";
			objFrameForm.Qform__FormUpdates.value = qcodo.formUpdates();

			if (qcodo.isBrowser(qcodo.IE)) {
				objWrapper.iframe.onreadystatechange = function() { if (this.readyState == "complete") this.wrapper.response(); };
			} else {
				objWrapper.iframe.onload = objWrapper.response;
			};

			objWrapper.isUploading = true;
			setTimeout('document.getElementById("' + objWrapper.id + '").getStatus();', 1000);
			objFrameForm.submit();
			return;
		};

		objWrapper.response = function() {
			if (!objWrapper.isUploading) return;

			objWrapper.isUploading = false;
			var  objFupIframe = objWrapper.iframe;
			var objFrameDoc = objFupIframe.contentDocument;
			if ((objFrameDoc == undefined) || (!objFrameDoc))
				objFrameDoc = objFupIframe.contentWindow.document;
			var objIframeResponse = new Object();

			if (objFrameDoc.XMLDocument)
				objIframeResponse.responseXML = objFrameDoc.XMLDocument;
			else
				objIframeResponse.responseXML = objFrameDoc;

			qcodo.handleAjaxResponse(null, objIframeResponse);
		};

		objWrapper.fileControl.onchange = objWrapper.executeSubmit;
		
		objWrapper.getStatus = function() {
			var objRequest;
			if (window.XMLHttpRequest) {
				objRequest = new XMLHttpRequest();
			} else if (typeof ActiveXObject != "undefined") {
				objRequest = new ActiveXObject("Microsoft.XMLHTTP");
			};

			objWrapper.getStatusRequest = objRequest;

			var strUri = qcodo.phpAssets + "/_core/file_uploader.php/" + this.uniqueHash;
			if (objRequest) {
				objRequest.open("GET", strUri, true);
				objRequest.onreadystatechange = objWrapper.handleGetStatusResponse;
				objRequest.send(null);
			};
		};

		objWrapper.handleGetStatusResponse = function(objEvent) {
			if (objWrapper.getStatusRequest.readyState == 4) {
				if (objWrapper.isUploading) {
					var objXmlDoc = objWrapper.getStatusRequest.responseXML;
					if (objXmlDoc && objXmlDoc.getElementsByTagName('uploadData') && objXmlDoc.getElementsByTagName('uploadData').length) {
						var objUploadData = objXmlDoc.getElementsByTagName('uploadData')[0];

						objWrapper.progress.size.innerHTML = objUploadData.getAttribute('total');
						objWrapper.progress.status.innerHTML = "Uploading... <strong>" + objUploadData.getAttribute('percent') + "</strong>";
						objWrapper.progress.fill.style.width = objUploadData.getAttribute('percentFloor') + "px";
					};

					setTimeout('document.getElementById("' + objWrapper.id + '").getStatus();', 1000);
				};
			};
		};
	};



//////////////////
// Qcodo Shortcuts
//////////////////

		qc.regFUP = qcodo.registerFileUploaderControl;