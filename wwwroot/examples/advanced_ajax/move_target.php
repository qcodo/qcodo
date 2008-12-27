<?php
	require('../../includes/prepend.inc.php');

	// Define the Qform with all our Qcontrols
	class ExamplesForm extends QForm {
		// Local declarations of our Qcontrols
		protected $lblHandle;
		protected $txtTextbox;

		// Initialize our Controls during the Form Creation process
		protected function Form_Create() {
			// Define the Label
			$this->lblHandle = new QLabel($this);
			$this->lblHandle->Text = 'Please Enter your Name';

			// Make the Label's Positioning Absolute, and specify a starting location
			$this->lblHandle->Position = QPosition::Absolute;
			$this->lblHandle->Top = 450;
			$this->lblHandle->Left = 150;

			// Define the Textbox, and specify positioning and location
			$this->txtTextbox = new QTextBox($this);
			$this->txtTextbox->Position = QPosition::Absolute;
			$this->txtTextbox->Top = 480;
			$this->txtTextbox->Left = 150;
			
			// Let's assign the label and the textbox as moveable controls, handled
			// by the label.
			$this->lblHandle->AddControlToMove($this->lblHandle);
			$this->lblHandle->AddControlToMove($this->txtTextbox);
		}
	}

	// Run the Form we have defined
	ExamplesForm::Run('ExamplesForm');
?>