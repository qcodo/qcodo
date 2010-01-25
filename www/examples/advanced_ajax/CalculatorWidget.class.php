<?php
	// The Logic here is a bit cheesy... we cheat a little because we don't take into
	// account overflow or divide-by-zero errors.  Instead, we cop out by just truncating
	// values or setting them to zero.
	//
	// Obviously, not completely accurate -- but this is really just an example dialog box, and hopefully
	// this example will give you enough to understand how QDialogBox works overall. =)
	class CalculatorWidget extends QDialogBox {
		// PUBLIC Child Controls
		public $pnlValueDisplay;
		public $pxyNumberControl;
		public $pxyOperationControl;

		public $btnEqual;
		public $btnPoint;
		public $btnClear;

		public $btnUpdate;
		public $btnCancel;
		
		// Object Variables
		protected $strCloseCallback;
		protected $fltValue;
		
		// Default Overrides
		protected $blnMatteClickable = false;
		protected $strTemplate = 'CalculatorWidget.tpl.php';
		protected $strCssClass = 'calculator_widget';

		protected $fltInternalValue;
		protected $strCurrentOperation;
		protected $blnNextClears;

		public function __construct($strCloseCallback, $objParentObject, $strControlId = null) {
			parent::__construct($objParentObject, $strControlId);
			$this->strCloseCallback = $strCloseCallback;
			
			// Define local child controls
			$this->pnlValueDisplay = new QPanel($this);
			$this->pnlValueDisplay->CssClass = 'calculator_display';

			// Define the Proxy
			$this->pxyNumberControl = new QControlProxy($this);
			$this->pxyNumberControl->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'pxyNumber_Click'));

			$this->pxyOperationControl = new QControlProxy($this);
			$this->pxyOperationControl->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'pxyOperation_Click'));

			$this->btnEqual = new QButton($this);
			$this->btnEqual->Text = '=';
			$this->btnEqual->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnEqual_Click'));

			$this->btnPoint = new QButton($this);
			$this->btnPoint->Text = '.';
			$this->btnPoint->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnPoint_Click'));

			$this->btnClear = new QButton($this);
			$this->btnClear->Text = 'C';
			$this->btnClear->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnClear_Click'));
			
			$this->btnUpdate = new QButton($this);
			$this->btnUpdate->Text = 'Save/Update';
			$this->btnUpdate->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnUpdate_Click'));
			
			$this->btnCancel = new QButton($this);
			$this->btnCancel->Text = 'Cancel';
			$this->btnCancel->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnCancel_Click'));
		}

		public function pxyNumber_Click($strFormId, $strControlId, $strParameter) {
			if ($this->blnNextClears) {
				$this->blnNextClears = false;
				$this->pnlValueDisplay->Text = $strParameter;
			} else if ($this->pnlValueDisplay->Text === '0') {
				$this->pnlValueDisplay->Text = $strParameter;
			} else if (strlen($this->pnlValueDisplay->Text) < 13)
				$this->pnlValueDisplay->Text .= $strParameter;
		}
		
		public function btnPoint_Click() {
			if ($this->blnNextClears) {
				$this->pnlValueDisplay->Text = '0.';
				$this->blnNextClears = false;
			} else {
				if (strpos($this->pnlValueDisplay->Text, '.') === false)
					$this->pnlValueDisplay->Text .= '.';
			}
		}

		public function pxyOperation_Click($strFormId, $strControlId, $strParameter) {
			if ($this->strCurrentOperation && !$this->blnNextClears)
				$this->btnEqual_Click();
			$this->strCurrentOperation = $strParameter;
			$this->blnNextClears = true;
			if (strpos($this->pnlValueDisplay->Text, '.') !== false)
				$this->pnlValueDisplay->Text .= '0';

			$this->fltInternalValue = QType::Cast($this->pnlValueDisplay->Text, QType::Float);
			try {
				$this->fltInternalValue = QType::Cast($this->pnlValueDisplay->Text, QType::Integer);
			} catch (QInvalidCastException $objExc) {}
			
			$this->pnlValueDisplay->Text = $this->fltInternalValue;
		}
		
		public function btnEqual_Click() {
			$this->blnNextClears = true;

			if (strpos($this->pnlValueDisplay->Text, '.') !== false)
				$this->pnlValueDisplay->Text .= '0';
			$fltOtherValue = QType::Cast($this->pnlValueDisplay->Text, QType::Float);
			try {
				$fltOtherValue = QType::Cast($this->pnlValueDisplay->Text, QType::Integer);
			} catch (QInvalidCastException $objExc) {}

			switch ($this->strCurrentOperation) {
				case '+':
					$this->fltInternalValue = $this->fltInternalValue + $fltOtherValue;
					break;
				case '-':
					$this->fltInternalValue = $this->fltInternalValue - $fltOtherValue;
					break;
				case '*':
					$this->fltInternalValue = $this->fltInternalValue * $fltOtherValue;
					break;
				case '/':
					if ($fltOtherValue == 0)
						$this->fltInternalValue = 0;
					else
						$this->fltInternalValue = $this->fltInternalValue / $fltOtherValue;
					break;
			}

			$this->strCurrentOperation = null;
			$this->pnlValueDisplay->Text = substr('' . $this->fltInternalValue, 0, 13);
		}

		public function btnClear_Click() {
			$this->fltValue = 0;
			$this->pnlValueDisplay->Text = 0;

			$this->fltInternalValue = 0;
			$this->blnNextClears = true;
			$this->strCurrentOperation = null;
		}

		public function btnCancel_Click() {
			$this->HideDialogBox();
		}
		
		public function btnUpdate_Click() {
			$this->fltValue = $this->pnlValueDisplay->Text;
			call_user_func(array($this->objForm, $this->strCloseCallback));
			$this->HideDialogBox();
		}

		public function ShowDialogBox() {
			parent::ShowDialogBox();
			$this->pnlValueDisplay->Text = ($this->fltValue) ? $this->fltValue : 0;

			$this->fltInternalValue = 0;
			$this->blnNextClears = true;
			$this->strCurrentOperation = null;
		}

		public function __get($strName) {
			switch ($strName) {
				case "Value": return $this->fltValue;

				default:
					try {
						return parent::__get($strName);
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
			}
		}

		public function __set($strName, $mixValue) {
			$this->blnModified = true;

			switch ($strName) {
				case "Value":
					// Depending on the format of $mixValue, set $this->fltValue appropriately
					// It will try to cast to Integer if possible, otherwise Float, otherwise just 0
					$this->fltValue = 0;
					try {					
						$this->fltValue = QType::Cast($mixValue, QType::Float);
						break;
					} catch (QInvalidCastException $objExc) {}
					try {
						$this->fltValue = QType::Cast($mixValue, QType::Integer);
						break;
					} catch (QInvalidCastException $objExc) {}
					break;

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