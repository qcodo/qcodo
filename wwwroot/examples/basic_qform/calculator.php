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
		protected function Form_Create() {
			$this->txtValue1 = new QTextBox($this);
			
			$this->txtValue2 = new QTextBox($this);
			
			$this->lstOperation = new QListBox($this);
			$this->lstOperation->AddItem('+', 'add');
			$this->lstOperation->AddItem('-', 'subtract');
			$this->lstOperation->AddItem('*', 'multiply');
			$this->lstOperation->AddItem('/', 'divide');
			
			$this->btnCalculate = new QButton($this);
			$this->btnCalculate->Text = 'Calculate';
			$this->btnCalculate->AddAction(new QClickEvent(), new QServerAction('btnCalculate_Click'));
			
			$this->lblResult = new QLabel($this);
			$this->lblResult->HtmlEntities = false;
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
			
			$this->lblResult->Text = '<b>Your Result:</b> ' . $mixResult;
		}
	}
	
	// And now run our defined form
	CalculatorForm::Run('CalculatorForm');
?>