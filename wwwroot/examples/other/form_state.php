<?php
	require('../../includes/prepend.inc.php');

	// First of all, let's override the way QForm stores state information.
	// We will use the session-based FormState Handler, instead of the standard/default
	// formstate handler.  Also, let's encrypt the formstate index by defining
	// an encryption key.
	//
	// NOTE: This preference can be set, globally, by updating the QForm class
	// which is located at /includes/qform/QForm.inc
	QForm::$FormStateHandler = 'QSessionFormStateHandler';
	QForm::$EncryptionKey = '\rSome.Random!Key\0';

	// Everything else below should be the exact same as our original Hello, World! example
	class ExampleForm extends QForm {
		// Local declarations of our Qcontrols
		protected $lblMessage;
		protected $btnButton;

		// Initialize our Controls during the Form Creation process
		protected function Form_Create() {
			// Define the Label
			$this->lblMessage = new QLabel($this);
			$this->lblMessage->Text = 'Click the button to change my message.';

			// Define the Button
			$this->btnButton = new QButton($this);
			$this->btnButton->Text = 'Click Me!';

			// Add a Click event handler to the button
			$this->btnButton->AddAction(new QClickEvent(), new QServerAction('btnButton_Click'));
		}

		// The "btnButton_Click" Event handler
		protected function btnButton_Click($strFormId, $strControlId, $strParameter) {
			$this->lblMessage->Text = 'Hello, world!';
		}
	}

	// Run the Form we have defined
	ExampleForm::Run('ExampleForm');
?>