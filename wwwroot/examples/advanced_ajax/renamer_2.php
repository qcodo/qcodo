<?php
	require('../../includes/prepend.inc.php');

	class ExampleForm extends QForm {
		protected $lblArray;
		protected $txtArray;

		protected function Form_Create() {
			for ($intIndex = 0; $intIndex < 10; $intIndex++) {
				// Create the Label -- we must remember to explicitly specify the
				// Control ID so that we can code javascript against it
				// Note, we are using the regular QLabel and not our custom SelectableLabel
				// because we will now store the "which label is selected" information on the
				// client/javascript side.
				$this->lblArray[$intIndex] = new QLabel($this, 'label' . $intIndex);
				$this->lblArray[$intIndex]->Text = 'This is a Test for Item #' . ($intIndex + 1);
				$this->lblArray[$intIndex]->CssClass = 'renamer_item';
				$this->lblArray[$intIndex]->ActionParameter = $intIndex;

				// Note that we now use a lblArray_Click function we write in JavaScript instead of
				// PHP to do the selection work.
				$this->lblArray[$intIndex]->AddAction(new QClickEvent(), new QJavaScriptAction('lblArray_Click(this)'));

				// Create the Textbox (hidden) -- we must remember to explicitly specify the
				// Control ID so that we can code javascript against it
				// Also, instead of making Visible false, we set Display to false.  This allows
				// the entire control to render as "display:none", so that we can code javascript
				// to make it appear and disappear (via a call to .toggleDisplay()).
				$this->txtArray[$intIndex] = new QTextBox($this, 'textbox' . $intIndex);
				$this->txtArray[$intIndex]->ActionParameter = $intIndex;
				$this->txtArray[$intIndex]->Display = false;

				// Create Actions to Save Textbox on Blur or on "Enter" Key
				$this->txtArray[$intIndex]->AddAction(new QBlurEvent(), new QAjaxAction('TextItem_Save'));
				$this->txtArray[$intIndex]->AddAction(new QEnterKeyEvent(), new QAjaxAction('TextItem_Save'));
				$this->txtArray[$intIndex]->AddAction(new QEnterKeyEvent(), new QTerminateAction());

				// Create Action to CANCEL/Revert Textbox on "Escape" Key
				$this->txtArray[$intIndex]->AddAction(new QEscapeKeyEvent(), new QAjaxAction('TextItem_Cancel'));
				$this->txtArray[$intIndex]->AddAction(new QEscapeKeyEvent(), new QTerminateAction());
			}
		}

		protected function TextItem_Save($strFormId, $strControlId, $strParameter) {
			$strValue = trim($this->txtArray[$strParameter]->Text);
			
			if (strlen($strValue)) {
				// Copy the Textbox value back to the Label
				$this->lblArray[$strParameter]->Text = $strValue;
			}

			// Hide the Textbox, get the label cleaned up and ready to go
			$this->lblArray[$strParameter]->Display = true;
			$this->txtArray[$strParameter]->Display = false;
			$this->lblArray[$strParameter]->CssClass = 'renamer_item';

			QApplication::ExecuteJavaScript('intSelectedIndex = -1;');
		}

		protected function TextItem_Cancel($strFormId, $strControlId, $strParameter) {
			// Hide the Textbox, get the label cleaned up and ready to go
			$this->lblArray[$strParameter]->Display = true;
			$this->txtArray[$strParameter]->Display = false;
			$this->lblArray[$strParameter]->CssClass = 'renamer_item';

			QApplication::ExecuteJavaScript('intSelectedIndex = -1;');
		}
	}
	
	ExampleForm::Run('ExampleForm');
?>