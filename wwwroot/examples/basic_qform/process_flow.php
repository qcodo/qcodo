<?php
	require('../../includes/prepend.inc.php');

	// Define the Qform with all our Qcontrols
	class ExamplesForm extends QForm {
		// Local declarations of our Qcontrols
		protected $lblMessage;
		protected $btnButton;

		protected function Form_Run() {
			_p('<b>Form_Run</b> called<br/>', false);
		}

		// Initialize our Controls during the Form Creation process
		protected function Form_Create() {
			_p('<b>Form_Create</b> called<br/>', false);
			// Define the Label -- Set HtmlEntities to false because we intend on hard coding HTML into the Control
			$this->lblMessage = new QLabel($this);
			$this->lblMessage->HtmlEntities = false;
			$this->lblMessage->Text = 'Click the button to change my message.';

			// Definte the Button
			$this->btnButton = new QButton($this);
			$this->btnButton->Text = 'Click Me!';
			
			// We add CausesValidation to the Button so that Form_Validate() will get called
			$this->btnButton->CausesValidation = true;
			
			// Add a Click event handler to the button -- the action to run is a ServerAction (e.g. PHP method)
			// called "btnButton_Click"
			$this->btnButton->AddAction(new QClickEvent(), new QServerAction('btnButton_Click'));
		}

		protected function Form_Load() {
			_p('<b>Form_Load</b> called<br/>', false);
		}

		protected function Form_PreRender() {
			_p('<b>Form_PreRender</b> called<br/>', false);
		}

		protected function Form_Validate() {
			_p('<b>Form_Validate</b> called<br/>', false);

			// Form_Validate needs to return true or false
			return true;
		}

		protected function Form_Exit() {
			_p('<b>Form_Exit</b> called<br/>', false);
		}

		// The "btnButton_Click" Event handler
		protected function btnButton_Click($strFormId, $strControlId, $strParameter) {
			_p('<b>btnButton_Click</b> called<br/>', false);
			$this->lblMessage->Text = 'Hello, world!<br/>';
			$this->lblMessage->Text .= 'Note that instead of <b>Form_Create</b> being called, we are now calling <b>Form_Load</b> and <b>btnButton_Click</b>';
		}
	}

	// Run the Form we have defined
	ExamplesForm::Run('ExamplesForm');
?>