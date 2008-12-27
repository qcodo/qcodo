<?php
	require('../../includes/prepend.inc.php');

	// Define the Qform with all our Qcontrols
	class ExamplesForm extends QForm {
		// Local declarations of our Qcontrols
		protected $btnButton;

		// The class member variable of the intCounter to show off a Qform's state
		protected $intCounter = 0;

		// Initialize our Controls during the Form Creation process
		protected function Form_Create() {
			// Definte the Button
			$this->btnButton = new QButton($this);
			$this->btnButton->Text = 'Click Me!';

			// Add a Click event handler to the button -- the action to run is a ServerAction (e.g. PHP method)
			// called "btnButton_Click"
			$this->btnButton->AddAction(new QClickEvent(), new QServerAction('btnButton_Click'));
		}

		// The "btnButton_Click" Event handler
		protected function btnButton_Click($strFormId, $strControlId, $strParameter) {
			// Increment our counter
			$this->intCounter++;
		}
	}

	// Run the Form we have defined
	ExamplesForm::Run('ExamplesForm');
?>