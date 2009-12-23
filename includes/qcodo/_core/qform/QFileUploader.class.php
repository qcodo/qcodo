<?php
	class QFileUploader extends QControl {
		protected $intExample;
		protected $strFoo;
		protected $strJavaScripts = '_core/control_file.js';
		protected $blnIsBlockElement = true;
		
		protected $strTempFilePath;
		protected $strFileName;
		protected $strFileSize;
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
			if (!$this->strTempFilePath) {
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
				$strHtml .= sprintf('<strong>%s</strong> (%s) &nbsp; <a href="#">Remove</a></div>', $this->strFileName, QString::GetByteSize($this->strFileSize));
			}

			return sprintf('<div id="%s" %s%s>%s</div>', $this->strControlId, $strAttributes, $strStyle, $strHtml);
		}

		public function GetEndScript() {
			$strToReturn = parent::GetEndScript();
			$strUniqueHash = md5(microtime() . rand(0, 1000000));

			if (($this->blnVisible) && (!$this->strTempFilePath)) {
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
		}
		
		public function HandleFileUploaded($strFormId, $strControlId, $strParameter) {
			$this->strTempFilePath = $_FILES[$this->strControlId . '_ctlflc']['tmp_name'];
			$this->strFileName = $_FILES[$this->strControlId . '_ctlflc']['name'];
			$this->strFileSize = $_FILES[$this->strControlId . '_ctlflc']['size'];
			$this->strMimeType = $_FILES[$this->strControlId . '_ctlflc']['type'];
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
				case 'Example': return $this->intExample;
				case 'Foo': return $this->strFoo;

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

				case 'Example': 
					try {
						return ($this->intExample = QType::Cast($mixValue, QType::Integer));
					} catch (QCallerException $objExc) { $objExc->IncrementOffset(); throw $objExc; }
				case 'Foo': 
					try {
						return ($this->strFoo = QType::Cast($mixValue, QType::String));
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