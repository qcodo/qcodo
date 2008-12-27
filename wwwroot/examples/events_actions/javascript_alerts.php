<?php
	require('../../includes/prepend.inc.php');

	class ExampleForm extends QForm {
		protected $lblMessage;
		protected $btnJavaScript;
		protected $btnAlert;
		protected $btnConfirm;

		protected function Form_Create() {
			// Define the Controls
			$this->lblMessage = new QLabel($this);
			$this->lblMessage->Text = 'Click on the "QConfirmAction Example" button to change.';

			// Define different buttons to show off the various JavaScript-based Actions
			$this->btnJavaScript = new QButton($this);
			$this->btnJavaScript->Text = 'QJavaScriptAction Example';
			$this->btnJavaScript->AddAction(new QClickEvent(), new QJavaScriptAction('SomeArbitraryJavaScript();'));

			// Define different buttons to show off the various Alert-based Actions
			$this->btnAlert = new QButton($this);
			$this->btnAlert->Text = 'QAlertAction Example';
			$this->btnAlert->AddAction(new QClickEvent(), new QAlertAction('This is a test of the "QAlertAction" example.\r\nIsn\'t this fun? =)'));

			// Define different buttons to show off the various Confirm-based Actions
			$this->btnConfirm = new QButton($this);
			$this->btnConfirm->Text = 'QConfirmAction Example';
			$this->btnConfirm->AddAction(new QClickEvent(), new QConfirmAction('Are you SURE you want to update the lblMessage?'));
			// Notice: this next action ONLY RUNS if the user hit "Ok"
			$this->btnConfirm->AddAction(new QClickEvent(), new QAjaxAction('btnConfirm_Click'));
		}

		protected function btnConfirm_Click() {
			// Update the Label
			if ($this->lblMessage->Text == 'Hello, world!')
				$this->lblMessage->Text = 'Buh Bye!';
			else
				$this->lblMessage->Text = 'Hello, world!';
		}
	}

	ExampleForm::Run('ExampleForm');
?>