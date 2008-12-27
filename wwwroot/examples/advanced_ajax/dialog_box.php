<?php
	require('../../includes/prepend.inc.php');
	require('CalculatorWidget.class.php');

	// Define the Qform with all our Qcontrols
	class ExamplesForm extends QForm {
		// Local declarations of our Qcontrols
		protected $dlgSimpleMessage;
		protected $btnDisplaySimpleMessage;
		protected $btnDisplaySimpleMessageJsOnly;

		protected $dlgCalculatorWidget;
		protected $txtValue;
		protected $btnCalculator;

		// Initialize our Controls during the Form Creation process
		protected function Form_Create() {
			// Define the Simple Message Dialog Box
			$this->dlgSimpleMessage = new QDialogBox($this);
			$this->dlgSimpleMessage->Text = '<i>Hello, world!</i><br/><br/>This is a standard, no-frills dialog box.<br/><br/>Notice how the contents of the dialog '.
				'box can scroll, and notice how everything else in the application is grayed out.<br/><br/>Because we set <b>MatteClickable</b> to <b>true</b> ' .
				'(by default), you can click anywhere outside of this dialog box to "close" it.<br/><br/>Additional text here is just to help show the scrolling ' .
				'capability built-in to the panel/dialog box via the "Overflow" property of the control.';

			// Let's setup some basic appearance options
			// This could and should normally be done in a separate CSS class using the CssClass property
			$this->dlgSimpleMessage->Width = '500px';
			$this->dlgSimpleMessage->Height = '300px';
			$this->dlgSimpleMessage->Overflow = QOverflow::Auto;
			$this->dlgSimpleMessage->Padding = '10px';
			$this->dlgSimpleMessage->FontSize = '24px';
			$this->dlgSimpleMessage->FontNames = QFontFamily::Georgia;
			$this->dlgSimpleMessage->BackColor = '#eeffdd';

			// Make sure this Dislog Box is "hidden"
			// Like any other QPanel or QControl, this can be toggled using the "Display" or the "Visible" property
			$this->dlgSimpleMessage->Display = false;

			// The First "Display Simple Message" button will utilize an AJAX call to Show the Dialog Box
			$this->btnDisplaySimpleMessage = new QButton($this);
			$this->btnDisplaySimpleMessage->Text = 'Display Simple Message QDialogBox';
			$this->btnDisplaySimpleMessage->AddAction(new QClickEvent(), new QAjaxAction('btnDisplaySimpleMessage_Click'));

			// The Second "Display Simple Message" button will utilize Client Side-only JavaScripts to Show the Dialog Box
			// (No postback/postajax is used)
			$this->btnDisplaySimpleMessageJsOnly = new QButton($this);
			$this->btnDisplaySimpleMessageJsOnly->Text = 'Display Simple Message QDialogBox (ClientSide Only)';
			$this->btnDisplaySimpleMessageJsOnly->AddAction(new QClickEvent(), new QShowDialogBox($this->dlgSimpleMessage));



			// Define the CalculatorWidget example. passing in the Method Callback for whenever the Calculator is Closed
			$this->dlgCalculatorWidget = new CalculatorWidget('btnCalculator_Close', $this);
			$this->dlgCalculatorWidget->Visible = false;

			// Setup the Value Textbox and Button for this example
			$this->txtValue = new QTextBox($this);

			$this->btnCalculator = new QButton($this);
			$this->btnCalculator->Text = 'Show Calculator Widget';
			$this->btnCalculator->AddAction(new QClickEvent(), new QAjaxAction('btnCalculator_Click'));
		}

		protected function btnDisplaySimpleMessage_Click($strFormId, $strControlId, $strParameter) {
			// "Show" the Dialog Box using the ShowDialogBox() method
			$this->dlgSimpleMessage->ShowDialogBox();
		}
		
		protected function btnCalculator_Click($strFormId, $strControlId, $strParameter) {
			// Setup the Calculator Widget's Value
			$this->dlgCalculatorWidget->Value = trim($this->txtValue->Text);

			// And Show it
			$this->dlgCalculatorWidget->ShowDialogBox();
		}
		
		// Setup the "Callback" function for when the calculator closes
		// This needs to be a public method
		public function btnCalculator_Close() {
			$this->txtValue->Text = $this->dlgCalculatorWidget->Value;
		}
	}

	// Run the Form we have defined
	ExamplesForm::Run('ExamplesForm');
?>