<?php
	require('../../includes/prepend.inc.php');

	// Define the Qform with all our Qcontrols
	class ExamplesForm extends QForm {
		// Local declarations of our Qcontrols
		protected $lblMessage;
		protected $btnButton;
		protected $btnButton2;

		// Initialize our Controls during the Form Creation process
		protected function Form_Create() {
			// Define the Label
			$this->lblMessage = new QLabel($this);
			$this->lblMessage->Text = 'Click the button to change my message.';

			// Define two Buttons
			$this->btnButton = new QButton($this);
			$this->btnButton->Text = 'Click Me!';
			$this->btnButton2 = new QButton($this);
			$this->btnButton2->Text = '(No Spinner)';

			// Define the Wait Icon -- we need to remember to "RENDER" this wait icon, too!
			$this->objDefaultWaitIcon = new QWaitIcon($this);

			// Add a Click event handler to the button -- the action to run is an AjaxAction.
			$this->btnButton->AddAction(new QClickEvent(), new QAjaxAction('btnButton_Click'));

			// Add a second click event handler which will use NO spinner
			$this->btnButton2->AddAction(new QClickEvent(), new QAjaxAction('btnButton_Click', null));
		}

		// The "btnButton_Click" Event handler
		protected function btnButton_Click($strFormId, $strControlId, $strParameter) {
			// Let's add artificial latency/wait to show the spinner
			sleep(1);
			if ($this->lblMessage->Text == 'Hello, world!')
				$this->lblMessage->Text = 'Click the button to change my message.';
			else
				$this->lblMessage->Text = 'Hello, world!';
		}
	}

	// Run the Form we have defined
	ExamplesForm::Run('ExamplesForm');
?>