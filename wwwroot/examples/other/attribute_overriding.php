<?php
	require('../../includes/prepend.inc.php');

	// Define the Qform with all our Qcontrols
	class ExampleForm extends QForm {
		// Local declarations of our Qcontrols
		protected $lblMessage;
		protected $btnButton;

		// Initialize our Controls during the Form Creation process
		protected function Form_Create() {
			// Define the Label
			// Even though we are programatically setting the ForeColor property
			// to Blue here, it will be overridden to Green in the HTML template.
			$this->lblMessage = new QLabel($this);
			$this->lblMessage->Text = 'Click the button to change my message.';
			$this->lblMessage->ForeColor = '#0000ff';

			// Define the Button
			$this->btnButton = new QButton($this);
			$this->btnButton->Text = 'Click Me!';

			// Add a Click event handler to the button -- the action to run is an AjaxAction.
			// The AjaxAction names a PHP method (which will be run asynchronously) called "btnButton_Click"
			$this->btnButton->AddAction(new QClickEvent(), new QAjaxAction('btnButton_Click'));
		}

		// The "btnButton_Click" Event handler
		protected function btnButton_Click($strFormId, $strControlId, $strParameter) {
			$this->lblMessage->Text = 'Hello, world!';
		}
	}

	// Run the Form we have defined
	ExampleForm::Run('ExampleForm');
?>