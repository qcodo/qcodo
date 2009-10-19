<?php
	/**
	 * This is a standard, sample QForm which you can use as a starting
	 * point to build any QForm page that you wish.
	 */

	// Include prepend.inc to load Qcodo
	require('includes/prepend.inc.php');

	class SampleForm extends QForm {
		protected $lblMessage;
		protected $btnButton;

		protected function Form_Create() {
			$this->lblMessage = new QLabel($this);
			$this->lblMessage->Text = 'Click the button to change my message.';

			$this->btnButton = new QButton($this);
			$this->btnButton->Text = 'Click Me';
			$this->btnButton->AddAction(new QClickEvent(), new QServerAction('btnButton_Click'));
		}

		protected function btnButton_Click($strFormId, $strControlId, $strParameter) {
			$this->lblMessage->Text = 'Hello, World!';
		}
	}

	SampleForm::Run('SampleForm');
?>