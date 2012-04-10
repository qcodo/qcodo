<?php
	/**
	 * @property string $CalendarImageSource
	 */
	class QCalendar extends QControl {
		protected $dtxLinkedControl;
		protected $strCalendarImageSource;

		protected $strJavaScripts = '_core/calendar.js';
		protected $strCssClass = 'calendar';
		protected $strJavaScriptSetDateOverride = null;

		public function ParsePostData() {}
		public function Validate() {return true;}
		public function GetControlHtml() {
			// Pull any Attributes
			$strAttributes = $this->GetAttributes();

			// Pull any styles
			if ($strStyle = $this->GetStyleAttributes())
				$strStyle = 'style="' . $strStyle . '"';

			$strImageStyle = '';
			if (file_exists(__DOCROOT__ . $this->strCalendarImageSource)) {
				$strSizeInfo = getimagesize(__DOCROOT__ . $this->strCalendarImageSource);
				$strImageStyle = 'style="width: ' . $strSizeInfo[0] . 'px; height: ' . $strSizeInfo[1] . 'px;"';
			}

			$strToReturn = sprintf('<img id="%s" src="%s" %s/><div id="%s_cal" %s%s></div>',
				$this->strControlId,
				$this->strCalendarImageSource,
				$strImageStyle,
				$this->strControlId,
				$strAttributes,
				$strStyle);

			return $strToReturn;
		}
		public function AddAction($objEvent, $objAction) {
			throw new QCallerException('QCalendar does not support custom events');
		}

		/**
		 * The constructor for the QCalendar QControl object
		 * @param mixed $objParentObject
		 * @param QDateTimeTextBox $dtxLinkedControl the linked QDateTimeTextBox to this calendar icon/control
		 * @param string $strControlId optional
		 * @param boolean $blnAutoShowOnClick whether or not the calendar should automatically display upon clicking the qdatetimetexbox (default is yes/true)
		 * @throws QCallerException
		 */
		public function __construct($objParentObject, QDateTimeTextBox $dtxLinkedControl, $strControlId = null, $blnAutoShowOnClick = true) {
			try {
				parent::__construct($objParentObject, $strControlId);
			} catch (QCallerException $objExc) { $objExc->IncrementOffset(); throw $objExc; }

			// Setup Linked DateTimeTextBox control
			$this->dtxLinkedControl = $dtxLinkedControl;

			// Other Setup
			$this->strCalendarImageSource = __IMAGE_ASSETS__ . '/calendar.png';
			
			if ($blnAutoShowOnClick) {
				$this->dtxLinkedControl->RemoveAllActions(QClickEvent::EventName);
				$this->dtxLinkedControl->AddAction(new QClickEvent(), new QJavaScriptAction("qc.getC('" . $this->strControlId . "').showCalendar(); "));
				$this->dtxLinkedControl->AddAction(new QClickEvent(), new QBlurControlAction($this->dtxLinkedControl));
				$this->dtxLinkedControl->AddAction(new QClickEvent(), new QTerminateAction());
			}
		}

		public function GetEndScript() {
			$strToReturn = parent::GetEndScript();

			if ($this->strJavaScriptSetDateOverride) {
				$strToReturn .= sprintf('qc.regCAL("%s", "%s", "%s", "%s", null); ',
						$this->strControlId, $this->dtxLinkedControl->ControlId, QApplication::Translate('Today'), QApplication::Translate('Cancel'));
				$strToReturn .= sprintf('qc.getC("%s").setDate = %s;', $this->strControlId, $this->strJavaScriptSetDateOverride);

			} else if (QDateTime::$Translate) {
				$strShortNameArray = array();
				$strLongNameArray = array();
				$strDayArray = array();
				$dttMonth = new QDateTime('2000-01-01');
				for ($intMonth = 1; $intMonth <= 12; $intMonth++) {
					$dttMonth->Month = $intMonth;
					$strShortNameArray[] = '"' . $dttMonth->ToString('MMM') . '"';
					$strLongNameArray[] = '"' . $dttMonth->ToString('MMMM') . '"';
				}
				$dttDay = new QDateTime('Sunday');
				for ($intDay = 1; $intDay <= 7; $intDay++) {
					$strDay = $dttDay->ToString('DDD');

					$strDay = html_entity_decode($strDay, ENT_COMPAT, QApplication::$EncodingType);
					if (function_exists('mb_substr'))
						$strDay = mb_substr($strDay, 0, 2);
					else
						// Attempt to account for multibyte day -- may not work if the third character is multibyte
						$strDay = substr($strDay, 0, strlen($strDay) - 1);
					$strDay = QApplication::HtmlEntities($strDay);
					$strDayArray[] = '"' . $strDay . '"';
					$dttDay->Day++;
				}
				$strArrays = sprintf('new Array(new Array(%s), new Array(%s), new Array(%s))',
					implode(', ', $strLongNameArray), implode(', ', $strShortNameArray), implode(', ', $strDayArray));
				$strToReturn .= sprintf('qc.regCAL("%s", "%s", "%s", "%s", %s); ',
					$this->strControlId, $this->dtxLinkedControl->ControlId, QApplication::Translate('Today'), QApplication::Translate('Cancel'), $strArrays);
			} else {
				$strToReturn .= sprintf('qc.regCAL("%s", "%s", "%s", "%s", null); ',
					$this->strControlId, $this->dtxLinkedControl->ControlId, QApplication::Translate('Today'), QApplication::Translate('Cancel'));
			}
			return $strToReturn;
		}

		/////////////////////////
		// Public Properties: GET
		/////////////////////////
		public function __get($strName) {
			switch ($strName) {
				case 'CalendarImageSource': return $this->strCalendarImageSource;
				case 'DateTime': return $this->dtxLinkedControl->DateTime;
				case 'JavaScriptSetDateOverride': return $this->strJavaScriptSetDateOverride;

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
				
				case 'CalendarImageSource':
					try {
						return ($this->strCalendarImageSource = QType::Cast($mixValue, QType::String));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset(); throw $objExc;
					}

				case 'JavaScriptSetDateOverride':
					try {
						return ($this->strJavaScriptSetDateOverride = QType::Cast($mixValue, QType::String));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset(); throw $objExc;
					}

				default:
					try {
						return (parent::__set($strName, $mixValue));
					} catch (QCallerException $objExc) { $objExc->IncrementOffset(); throw $objExc; }
			}
		}
	}
?>