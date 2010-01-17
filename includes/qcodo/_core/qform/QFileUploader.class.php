<?php
	class QFileUploader extends QControl {
		protected $strJavaScripts = '_core/control_file.js';
		protected $blnIsBlockElement = true;

		protected $pxyRemoveFile;

		protected $strFilePath;
		protected $strFileName;
		protected $intFileSize;
		protected $strMimeType;
		
		protected $strCssClass = 'fileUploader';
		
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
		public function Validate() {return true;}

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
				$strHtml .= sprintf('<input type="button" class="button" id="%s_button" value="Browse..."/>', $this->strControlId);

				$strHtml .= sprintf('<div class="progress" id="%s_progress" style="display: none;">', $this->strControlId);
				$strHtml .= sprintf('<div class="size" id="%s_size">n/a</div>', $this->strControlId);

				$strHtml .= '<div class="bar">';
				$strHtml .= sprintf('<div class="status" id="%s_status">Uploading...</div>', $this->strControlId);
				$strHtml .= sprintf('<div class="fill" id="%s_fill"></div>', $this->strControlId);
				$strHtml .= '</div>';

				$strHtml .= '<div class="cancel"><a href="#">Cancel</a></div>';
				$strHtml .= '</div>';
			} else {
				$strHtml .= sprintf('<strong>%s</strong> (%s) &nbsp; <a href="#" %s>Remove</a></div>',
					$this->strFileName, QString::GetByteSize($this->intFileSize), $this->pxyRemoveFile->RenderAsEvents(null, false));
			}

			return sprintf('<div id="%s" %s%s>%s</div>', $this->strControlId, $strAttributes, $strStyle, $strHtml);
		}

		public function GetEndScript() {
			$strToReturn = parent::GetEndScript();
			$strUniqueHash = md5(microtime() . rand(0, 1000000));

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
		}

		public function HandleFileUploaded($strFormId, $strControlId, $strParameter) {
			$this->strFilePath = $_FILES[$this->strControlId . '_ctlflc']['tmp_name'];
			$this->strFileName = $_FILES[$this->strControlId . '_ctlflc']['name'];
			$this->intFileSize = $_FILES[$this->strControlId . '_ctlflc']['size'];
			$this->strMimeType = $_FILES[$this->strControlId . '_ctlflc']['type'];
			$this->Refresh();
		}

		public function HandleFileRemoved($strFormId, $strControlId, $strParameter) {
			$this->strFilePath = null;
			$this->strFileName = null;
			$this->intFileSize = null;
			$this->strMimeType = null;
			$this->Refresh();
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

					$this->intFileSize = filesize($strFile);
					$this->strFilePath = $strFile;
					return $strFile;

				case 'FileName': 
					try {
						return ($this->strFileName = QType::Cast($mixValue, QType::String));
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