<?php
	require(dirname(__FILE__) . '/../_require_prepend.inc.php');
	require(__INCLUDES__ . '/examples/examples.inc.php');

	// Define the Qform with all our Qcontrols
	class ExamplesForm extends ExamplesBaseForm {
		// Local declarations of our Qcontrols
		protected $lblMessage;
		protected $btnButton;

		// Initialize our Controls during the Form Creation process
		protected function Form_Create() {
			// Define the Label
			$this->lblMessage = new QLabel($this);
			$this->lblMessage->Text = QDateTime::Now()->__toString('h:mm:ss z');

			// Define the Button
			$this->btnButton = new QButton($this);
			$this->btnButton->Text = 'Stop Polling';
			$this->btnButton->AddAction(new QClickEvent(), new QAjaxAction('btnButton_Click'));

			// Set the Polling Processor - override the default with a one-second recurrence interval
			$this->SetPollingProcessor('UpdateTimer', null, 1000);
		}

		// User-defined method to update the clock with the current time
		protected function UpdateTimer() {
			$this->lblMessage->Text = QDateTime::Now()->__toString('h:mm:ss z');
		}

		// The "btnButton_Click" Event handler
		protected function btnButton_Click($strFormId, $strControlId, $strParameter) {
			if ($this->IsPollingActive()) {
				$this->ClearPollingProcessor();
				$this->btnButton->Text = 'Start Polling';
			} else {
				$this->SetPollingProcessor('UpdateTimer', null, 1000);
				$this->btnButton->Text = 'Stop Polling';
			}
		}
	}

	// Run the Form we have defined
	ExamplesForm::Run('ExamplesForm');
?>