<?php
	// A subclass of TextBox with its validate method overridden -- Validate will also ensure
	// that the Text is a valid email address

	class QEmailTextBox extends QTextBox {
		///////////////////////////
		// Private Member Variables
		///////////////////////////

		protected $strLabelForInvalid;
		protected $intMaxLength = 256;
		protected $intMinLength = 3;

		//////////
		// Methods
		//////////
		public function __construct($objParentObject, $strControlId = null) {
			parent::__construct($objParentObject, $strControlId);

			$this->strLabelForInvalid = QApplication::Translate('Invalid e-mail address');
		}

		public function Validate() {
			if (parent::Validate()) {
				if( QEmailUtils::IsEmailValid($this->strText) == false ) {
					$this->strValidationError = $this->strLabelForInvalid;
					return false;
				}
			} else
				return false;

			$this->strValidationError = '';
			return true;
		}

		/////////////////////////
		// Public Properties: GET
		/////////////////////////
		public function __get($strName) {
			switch ($strName) {
				// MISC
				case 'LabelForInvalid': return $this->strLabelForInvalid;

				default:
					try {
						return parent::__get($strName);
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
			}
		}

		/////////////////////////
		// Public Properties: SET
		/////////////////////////
		public function __set($strName, $mixValue) {
			$this->blnModified = true;

			switch ($strName) {
				// MISC
				case 'LabelForInvalid':
					try {
						$this->strLabelForInvalid = QType::Cast($mixValue, QType::String);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				default:
					try {
						parent::__set($strName, $mixValue);
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
					break;
			}
		}
	}
?>