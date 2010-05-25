<?php
	class QFileUploader extends QControl {
		protected $strJavaScripts = '_core/control_file.js';
		protected $blnIsBlockElement = true;

		protected $pxyRemoveFile;
		protected $pxyCancelUpload;

		protected $strFilePath;
		protected $strFileName;
		protected $intFileSize;
		protected $strMimeType;
		protected $strDownloadUrl;

		protected $strTemporaryUploadFolder = '/tmp';

		protected $strCssClass = 'fileUploader';

		protected $strFileUploadedCallbackMethod;
		protected $objFileUploadedCallbackObject;

		protected $strFileRemovedCallbackMethod;
		protected $objFileRemovedCallbackObject;

		/**
		 * If this control needs to update itself from the $_POST data, the logic to do so
		 * will be performed in this method.
		 */
		public function ParsePostData() {}

		/**
		 * If this control has validation rules, the logic to do so
		 * will be performed in this method.
		 * @return boolean
		 */
		public function Validate() {
			$this->strValidationError = null;
			if ($this->blnRequired) {
				if (!$this->strFilePath) {
					if ($this->strName)
						$this->strValidationError = sprintf('%s is required', $this->strName);
					else
						$this->strValidationError = 'Required';
					return false;
				}
			}
			return true;
		}

		/**
		 * Get the HTML for this Control.
		 * @return string
		 */
		public function GetControlHtml() {
			// Pull any Attributes
			$strAttributes = $this->GetAttributes();

			// Pull any styles
			if ($strStyle = $this->GetStyleAttributes())
				$strStyle = 'style="' . $strStyle . '"';

			// Return the HTML
			$strHtml = null;
			if (!$this->strFilePath) {
				$strHtml .= sprintf('<input type="button" class="button" id="%s_button" value="Browse"/>', $this->strControlId);
				$strHtml .= sprintf('<span id="%s_ospan"><iframe id="%s_iframe" scrolling="no" style="display: none;"></iframe></span>', $this->strControlId, $this->strControlId);

				$strHtml .= sprintf('<div class="progress" id="%s_progress" style="display: none;">', $this->strControlId);
				$strHtml .= sprintf('<div class="size" id="%s_size"><img src="%s/spinner_14.gif"/></div>', $this->strControlId, __IMAGE_ASSETS__);

				$strHtml .= '<div class="bar">';
				$strHtml .= sprintf('<div class="status" id="%s_status">Uploading...</div>', $this->strControlId);
				$strHtml .= sprintf('<div class="fill" id="%s_fill"></div>', $this->strControlId);
				$strHtml .= '</div>';

				$strHtml .= sprintf('<div class="cancel"><a href="#" %s>Cancel</a></div>', $this->pxyCancelUpload->RenderAsEvents(null, false));
				$strHtml .= '</div>';
			} else if ($this->strDownloadUrl) {
				$strHtml .= sprintf('<strong><a href="%s">%s</a></strong> (%s) &nbsp; <a href="#" %s>Remove</a>',
					$this->strDownloadUrl, $this->strFileName, QString::GetByteSize($this->intFileSize), $this->pxyRemoveFile->RenderAsEvents(null, false));
			} else {
				$strHtml .= sprintf('<strong>%s</strong> (%s) &nbsp; <a href="#" %s>Remove</a>',
					$this->strFileName, QString::GetByteSize($this->intFileSize), $this->pxyRemoveFile->RenderAsEvents(null, false));
			}
			
			return sprintf('<div id="%s" %s%s>%s</div>', $this->strControlId, $strAttributes, $strStyle, $strHtml);
		}

		public function GetEndScript() {
			$strToReturn = parent::GetEndScript();
			$strUniqueHash = substr(md5(microtime() . rand(0, 1000000)), 4, 16);

			if (($this->blnVisible) && (!$this->strFilePath)) {
				$strToReturn .= sprintf('qc.regFUP("%s", "%s", "%s"); ',
					$this->strControlId, QApplication::$RequestUri, $strUniqueHash
				);
			}

			return $strToReturn;
		}

		/**
		 * Constructor for this control
		 * @param mixed $objParentObject Parent QForm or QControl that is responsible for rendering this control
		 * @param string $strControlId optional control ID
		 */
		public function __construct($objParentObject, $strControlId = null) {
			try {
				parent::__construct($objParentObject, $strControlId);
			} catch (QCallerException $objExc) { $objExc->IncrementOffset(); throw $objExc; }

			$this->AddAction(new QFileUploadedEvent(), new QAjaxControlAction($this, 'HandleFileUploaded'));

			$this->pxyRemoveFile = new QControlProxy($this);
			$this->pxyRemoveFile->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'HandleFileRemoved'));
			$this->pxyRemoveFile->AddAction(new QClickEvent(), new QTerminateAction());

			$this->pxyCancelUpload = new QControlProxy($this);
			$this->pxyCancelUpload->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'HandleFileCancelled'));
			$this->pxyCancelUpload->AddAction(new QClickEvent(), new QTerminateAction());
		}

		/**
		 * Used internally by Qcodo to handle the javascript-based post call to update form and control
		 * state when a file has been uploaded.  This will also make a call to any FileUploadedCallback if one was set.
		 * @param string $strFormId
		 * @param string $strControlId
		 * @param string $strParameter
		 * @return void
		 */
		public function HandleFileUploaded($strFormId, $strControlId, $strParameter) {
			$this->strValidationError = null;
			$this->strFilePath = $_FILES[$this->strControlId . '_ctlflc']['tmp_name'];
			$this->strFileName = $_FILES[$this->strControlId . '_ctlflc']['name'];
			$this->intFileSize = $_FILES[$this->strControlId . '_ctlflc']['size'];
			$this->strMimeType = $_FILES[$this->strControlId . '_ctlflc']['type'];

			// Save the File in a slightly more permanent temporary location
			$strTempFilePath = $this->strTemporaryUploadFolder . '/' . basename($this->strFilePath) . rand(1000, 9999);
			copy($this->strFilePath, $strTempFilePath);
			$this->strFilePath = $strTempFilePath;

			$this->Refresh();
			if ($this->strFileUploadedCallbackMethod) call_user_func_array(
				array($this->objFileUploadedCallbackObject, $this->strFileUploadedCallbackMethod),
				array($this->objForm->FormId, $this->strControlId, $this->strActionParameter));
		}

		/**
		 * Used internally by Qcodo to handle the javascript-based post call to update form and control
		 * state when a file has been removed.  This will also make a call to any FileRemovedCallback if one was set.
		 * @param string $strFormId
		 * @param string $strControlId
		 * @param string $strParameter
		 * @return void
		 */
		public function HandleFileRemoved($strFormId, $strControlId, $strParameter) {
			$this->strFilePath = null;
			$this->strFileName = null;
			$this->intFileSize = null;
			$this->strMimeType = null;
			$this->strDownloadUrl = null;
			$this->Refresh();
			if ($this->strFileRemovedCallbackMethod) call_user_func_array(
				array($this->objFileRemovedCallbackObject, $this->strFileRemovedCallbackMethod),
				array($this->objForm->FormId, $this->strControlId, $this->strActionParameter));
		}

		public function HandleFileCancelled($strFormId, $strControlId, $strParameter) {
			$this->strFilePath = null;
			$this->strFileName = null;
			$this->intFileSize = null;
			$this->strMimeType = null;
			$this->strDownloadUrl = null;
			$this->Refresh();
		}

		public function SetFileUploadedCallback($objCallbackObject, $strCallbackMethod) {
			$this->objFileUploadedCallbackObject = $objCallbackObject;
			$this->strFileUploadedCallbackMethod = $strCallbackMethod;
		}

		public function SetFileRemovedCallback($objCallbackObject, $strCallbackMethod) {
			$this->objFileRemovedCallbackObject = $objCallbackObject;
			$this->strFileRemovedCallbackMethod = $strCallbackMethod;
		}

		/**
		 * Used to remove a previously-set file to this FileUploader control
		 * @return void
		 */
		public function RemoveFile() {
			$this->HandleFileRemoved(null, null, null);
		}

		// For any HTML code that needs to be rendered at the END of the QForm when this control is INITIALLY rendered.
//		public function GetEndHtml() {
//			$strToReturn = parent::GetEndHtml();
//			return $strToReturn;
//		}

		/////////////////////////
		// Public Properties: GET
		/////////////////////////
		public function __get($strName) {
			switch ($strName) {
				case 'FilePath': return $this->strFilePath;
				case 'FileName': return $this->strFileName;
				case 'FileSize': return $this->intFileSize;
				case 'MimeType': return $this->strMimeType;
				case 'TemporaryUploadFolder': return $this->strTemporaryUploadFolder;
				case 'DownloadUrl': return $this->strDownloadUrl;

				default:
					try {
						return parent::__get($strName);
					} catch (QCallerException $objExc) { $objExc->IncrementOffset(); throw $objExc; }
			}
		}

		/////////////////////////
		// Public Properties: SET
		/////////////////////////
		public function __set($strName, $mixValue) {
			$this->blnModified = true;

			switch ($strName) {

				case 'FilePath': 
					try {
						$strFile = QType::Cast($mixValue, QType::String);
					} catch (QCallerException $objExc) { $objExc->IncrementOffset(); throw $objExc; }

					if (!is_file($strFile))
						throw new QCallerException('File does not exist: ' . $strFile);

					$strNewFilePath = $this->strTemporaryUploadFolder . '/' . md5(microtime());
					copy($strFile, $strNewFilePath);
					$this->intFileSize = filesize($strNewFilePath);
					$this->strFilePath = $strNewFilePath;
					return $strFile;

				case 'FileName': 
					try {
						return ($this->strFileName = QType::Cast($mixValue, QType::String));
					} catch (QCallerException $objExc) { $objExc->IncrementOffset(); throw $objExc; }

				case 'TemporaryUploadFolder':
					try {
						return ($this->strTemporaryUploadFolder = QType::Cast($mixValue, QType::String));
					} catch (QCallerException $objExc) { $objExc->IncrementOffset(); throw $objExc; }

				case 'DownloadUrl':
					try {
						return ($this->strDownloadUrl = QType::Cast($mixValue, QType::String));
					} catch (QCallerException $objExc) { $objExc->IncrementOffset(); throw $objExc; }

				default:
					try {
						return (parent::__set($strName, $mixValue));
					} catch (QCallerException $objExc) { $objExc->IncrementOffset(); throw $objExc; }
			}
		}
	}

	class QFileUploadedEvent extends QEvent {
		const EventName = 'onfileuploaded';
		protected $strJavaScriptEvent = 'onfileuploaded';
	}
?>