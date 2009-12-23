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

		objWrapper.uniqueHash = strUniqueHash;
		objWrapper.button = document.getElementById(objControl.id + "_button");
		objWrapper.progress = document.getElementById(objControl.id + "_progress");
		objWrapper.progress.size = document.getElementById(objControl.id + "_size");
		objWrapper.progress.status = document.getElementById(objControl.id + "_status");
		objWrapper.progress.fill = document.getElementById(objControl.id + "_fill");

		// Setup the Nested DIV and INPUT FILE form element
		var objOuterDiv = document.createElement("div");
		objOuterDiv.style.position = 'absolute';
		objOuterDiv.style.overflow = 'hidden';
		objOuterDiv.style.top = objWrapper.button.offsetTop + "px";
		objOuterDiv.style.left = objWrapper.button.offsetLeft + "px";
		objOuterDiv.style.height = objWrapper.button.offsetHeight + "px";
		objOuterDiv.style.width = objWrapper.button.offsetWidth + "px";
		objOuterDiv.style.opacity = 0;
		objWrapper.appendChild(objOuterDiv);

		var objInnerDiv = document.createElement("div");
		objInnerDiv.style.cssFloat = "left";
		objInnerDiv.style.overflow = "hidden";
		objInnerDiv.style.width = "70px";
		objInnerDiv.style.height = objOuterDiv.style.height;

		var objFileControl = document.createElement("input");
		objFileControl.id = strFileControlId;
		objFileControl.name = strFileControlId;
		objFileControl.type = "file";
		objFileControl.style.height = objOuterDiv.style.height;
		objFileControl.style.position = 'relative';
		objFileControl.style.left = '-155px';
		objInnerDiv.appendChild(objFileControl);
		objOuterDiv.appendChild(objInnerDiv);

		objWrapper.fileControl = objFileControl;
		objWrapper.fileControl.wrapper = objWrapper;
		objWrapper.fileControlParent = objInnerDiv;

		objWrapper.executeSubmit = function() {
			var objWrapper = this.wrapper;
			var objForm = document.getElementById(document.getElementById("Qform__FormId").value);
			var objFupIframe = document.createElement("iframe");
			objFupIframe.id = objWrapper.id + "ifrm";
//			objFupIframe.style.border = '5px solid black';
			objFupIframe.style.display = "none";
			objForm.appendChild(objFupIframe);
			objWrapper.frame = objFupIframe;

			objWrapper.button.style.display = "none";
			objWrapper.progress.style.display = "block";

			var objFrameDoc = objFupIframe.contentDocument;
			if ((objFrameDoc == undefined) || (!objFrameDoc))
				objFrameDoc = objFupIframe.contentWindow.document;
			objWrapper.frameDoc = objFrameDoc;

			var strFormId = objWrapper.id + "form";
			var strFileControlId = objWrapper.id + "flc";
			objFrameDoc.open();
			objFrameDoc.writeln('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
			objFrameDoc.writeln('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"><head>');
			objFrameDoc.writeln('<body><form method="post" action="' + strPostBack + '" enctype="multipart/form-data" id="' + strFormId + '">');
			objFrameDoc.writeln('<input type="hidden" name="APC_UPLOAD_PROGRESS" value="' + objWrapper.uniqueHash + '" />');
			objFrameDoc.writeln('<input type="hidden" name="Qform__FormState" id="Qform__FormState" value="" />');			
			objFrameDoc.writeln('<input type="hidden" name="Qform__FormId" id="Qform__FormId" value="' + objForm.id + '" />');
			objFrameDoc.writeln('</form></body></html>');
			objFrameDoc.close();

			var objFrameForm = objFrameDoc.getElementById(strFormId);
			qcodo.registerFormHiddenElement("Qform__FormControl", objFrameForm);
			qcodo.registerFormHiddenElement("Qform__FormEvent", objFrameForm);
			qcodo.registerFormHiddenElement("Qform__FormParameter", objFrameForm);
			qcodo.registerFormHiddenElement("Qform__FormCallType", objFrameForm);
			qcodo.registerFormHiddenElement("Qform__FormUpdates", objFrameForm);
			qcodo.registerFormHiddenElement("Qform__FormCheckableControls", objFrameForm);

			objWrapper.fileControlParent.removeChild(objWrapper.fileControl);
			objFrameForm.appendChild(objWrapper.fileControl);
			objWrapper.fileControl.style.position = null;

			objFrameForm.Qform__FormState.value = objForm.Qform__FormState.value;
			objFrameForm.Qform__FormControl.value = objWrapper.control.id;
			objFrameForm.Qform__FormControl.value = objWrapper.control.id;
			objFrameForm.Qform__FormEvent.value = 'QFileUploadedEvent';
			objFrameForm.Qform__FormParameter.value = 'foobar';
			objFrameForm.Qform__FormCallType.value = "Ajax";
			objFrameForm.Qform__FormUpdates.value = qcodo.formUpdates();
			objFrameForm.Qform__FormCheckableControls.value = qcodo.formCheckableControls(objForm.id, "Ajax");

			objWrapper.frame.onload = objWrapper.response;
			objWrapper.isUploading = true;
			setTimeout('document.getElementById("' + objWrapper.id + '").getStatus();', 1000);
			objFrameForm.submit();
			return;
		};

		objWrapper.response = function() {
			objWrapper.isUploading = false;
			var  objFupIframe = document.getElementById(objWrapper.id + "ifrm");
			var objFrameDoc = objFupIframe.contentDocument;
			if ((objFrameDoc == undefined) || (!objFrameDoc))
				objFrameDoc = objFupIframe.contentWindow.document;
			var objIframeResponse = new Object();
			objIframeResponse.responseXML = objFrameDoc;
			qcodo.handleAjaxResponse(null, objIframeResponse);

			var objForm = document.getElementById(document.getElementById("Qform__FormId").value);
			objForm.removeChild(objFupIframe);
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
					if (objXmlDoc) {
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