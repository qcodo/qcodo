<?php
	require('../../includes/prepend.inc.php');
	
	class CalculatorForm extends QForm {
		// Our Calculator needs 2 Textboxes (one for each operand)
		// A listbox of operations to choose from
		// A button to execute the calculation
		// And a label to output the result
		protected $txtValue1;
		protected $txtValue2;
		protected $lstOperation;
		protected $btnCalculate;
		protected $lblResult;
		
		// Define all the QContrtol objects for our Calculator
		// Make our textboxes IntegerTextboxes and make them required
		protected function Form_Create() {
			$this->txtValue1 = new QIntegerTextBox($this);
			$this->txtValue1->Required = true;

			$this->txtValue2 = new QIntegerTextBox($this);
			$this->txtValue2->Required = true;

			$this->lstOperation = new QListBox($this);
			$this->lstOperation->AddItem('+', 'add');
			$this->lstOperation->AddItem('-', 'subtract');
			$this->lstOperation->AddItem('*', 'multiply');
			$this->lstOperation->AddItem('/', 'divide');

			$this->btnCalculate = new QButton($this);
			$this->btnCalculate->Text = 'Calculate';
			
			// This is the **ONLY LINE** that has been changed: from QServerAction to QAjaxAction
			$this->btnCalculate->AddAction(new QClickEvent(), new QAjaxAction('btnCalculate_Click'));

			// With btnCalculate being responsible for the action, we set this QButton's CausesValidation to true
			// so that validation will occur on the form when click the button.
			// But if you set it to false, you'll see that integers and null entries would instead be always allowed.
			$this->btnCalculate->CausesValidation = true;

			$this->lblResult = new QLabel($this);
			$this->lblResult->HtmlEntities = false;
		}

		protected function Form_Load() {
			// Let's always clear the Result label
			$this->lblResult->Text = '';
		}

		protected function Form_Validate() {
			// If we are Dividing and if the divisor is 0, then this is not valid
			if (($this->lstOperation->SelectedValue == 'divide') &&
				($this->txtValue2->Text == 0)) {
					$this->txtValue2->Warning = 'Cannot Divide by Zero';
					return false;
				}

			// If we're here, then the custom Form validation rule validated properly
			return true;
		}

		// Perform the necessary operations on the operands, and output the value to the lblResult
		protected function btnCalculate_Click($strFormId, $strControlId, $strParameter) {
			switch ($this->lstOperation->SelectedValue) {
				case 'add':
					$mixResult = $this->txtValue1->Text + $this->txtValue2->Text;
					break;
				case 'subtract':
					$mixResult = $this->txtValue1->Text - $this->txtValue2->Text;
					break;
				case 'multiply':
					$mixResult = $this->txtValue1->Text * $this->txtValue2->Text;
					break;
				case 'divide':
					$mixResult = $this->txtValue1->Text / $this->txtValue2->Text;
					break;
				default:
					throw new Exception('Invalid Action');
			}

			if (isset($mixResult))
				$this->lblResult->Text = '<b>Your Result:</b> ' . $mixResult;
		}
	}

	// And now run our defined form
	CalculatorForm::Run('CalculatorForm');
?>