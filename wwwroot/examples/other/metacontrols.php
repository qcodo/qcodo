<?php
	require('../../includes/prepend.inc.php');

	// Define the Qform with all our Qcontrols
	class ExamplesForm extends QForm {
		// Local declarations of our Qcontrols
		protected $lblFirstName;
		protected $txtFirstName;
		protected $lblLastName;
		protected $txtLastName;
		protected $btnSave;
		protected $btnCancel;

		// Local instance of a Person MetaControl
		protected $mctPerson;

		// Initialize our Controls during the Form Creation process
		protected function Form_Create() {
			// For now, let's load Person of ID #1
			// Remember that $this is the Meta Control's parent, because every QControl
			// we get from PersonMetaControl should have $this form as its parent.
			$this->mctPerson = PersonMetaControl::Create($this, 1);

			// Instead of manually defining and setting up each QLabel and QTextBox,
			// we utilize MetaControl's _create() functionality to create them
			// for us.
			$this->lblFirstName = $this->mctPerson->lblFirstName_Create();
			$this->lblLastName = $this->mctPerson->lblLastName_Create();

			$this->txtFirstName = $this->mctPerson->txtFirstName_Create();
			$this->txtLastName = $this->mctPerson->txtLastName_Create();

			// Now, we customize these controls as we normally would
			// In this particular case, upon intial load, we want to see the Labels, but
			// we want the textboxes to be invisible
			$this->txtFirstName->Visible = false;
			$this->txtLastName->Visible = false;

			// Add a Pointer Cursor to the labels
			$this->lblFirstName->Cursor = QCursor::Pointer;
			$this->lblLastName->Cursor = QCursor::Pointer;

			// We can of course also define any additional controls we wish
			$this->btnSave = new QButton($this);
			$this->btnSave->Text = 'Save';
			$this->btnSave->Visible = false;
			$this->btnCancel = new QButton($this);
			$this->btnCancel->Text = 'Cancel';
			$this->btnCancel->Visible = false;

			// Finally, we can define all of our actions
			// ON some of these, we can override and set a CausesValidation handler
			$this->btnSave->AddAction(new QClickEvent(), new QAjaxAction('btnSave_Click', 'default', true));
			$this->btnCancel->AddAction(new QClickEvent(), new QAjaxAction('btnCancel_Click'));

			$this->lblFirstName->AddAction(new QClickEvent(), new QAjaxAction('lblFirstName_Click'));
			$this->lblLastName->AddAction(new QClickEvent(), new QAjaxAction('lblLastName_Click'));

			$this->txtFirstName->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnSave_Click', 'default', true));
			$this->txtFirstName->AddAction(new QEnterKeyEvent(), new QTerminateAction());
			$this->txtFirstName->AddAction(new QEscapeKeyEvent(), new QAjaxAction('btnCancel_Click', 'default', true));
			$this->txtFirstName->AddAction(new QEscapeKeyEvent(), new QTerminateAction());

			$this->txtLastName->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnSave_Click', 'default', true));
			$this->txtLastName->AddAction(new QEnterKeyEvent(), new QTerminateAction());
			$this->txtLastName->AddAction(new QEscapeKeyEvent(), new QAjaxAction('btnCancel_Click', 'default', true));
			$this->txtLastName->AddAction(new QEscapeKeyEvent(), new QTerminateAction());
		}

		// Define the Event Handlers
		protected function btnSave_Click($strFormId, $strControlId, $strParameter) {
			// Utilize Meta Control to update Person
			$this->mctPerson->SavePerson();

			// Unselect everything
			$this->Unselect();
		}

		protected function Unselect() {
			// Let's hide all the textboxes and show all the labels
			$this->txtFirstName->Visible = false;
			$this->lblFirstName->Visible = true;
			$this->txtLastName->Visible = false;
			$this->lblLastName->Visible = true;

			// Let's hide the Save and Cancel Buttons
			$this->btnSave->Visible = false;
			$this->btnCancel->Visible = false;

			// Finally, let's utilize the MetaControl to refresh all the data fields (in case a data was modified and saved
			// or a textbox was modified and NOT saved)
			$this->mctPerson->Refresh();
		}

		protected function btnCancel_Click($strFormId, $strControlId, $strParameter) {
			$this->Unselect();
		}

		protected function lblFirstName_Click($strFormId, $strControlId, $strParameter) {
			// In case we are currently Editing lblLastName, let's first implicitly unselect everything
			$this->Unselect();

			// Hide the Label and Show the Textboox
			$this->lblFirstName->Visible = false;
			$this->txtFirstName->Visible = true;
			$this->txtFirstName->Focus();

			// Finall, show the Save and Cancel Buttons
			$this->btnSave->Visible = true;
			$this->btnCancel->Visible = true;
		}

		protected function lblLastName_Click($strFormId, $strControlId, $strParameter) {
			// In case we are currently Editing lblFirstName, let's first implicitly unselect everything
			$this->Unselect();

			// Hide the Label and Show the Textboox
			$this->lblLastName->Visible = false;
			$this->txtLastName->Visible = true;
			$this->txtLastName->Focus();

			// Finall, show the Save and Cancel Buttons
			$this->btnSave->Visible = true;
			$this->btnCancel->Visible = true;
		}

		protected function Form_Validate() {
			// Blink and FOcus any errant control
			foreach ($this->GetErrorControls() as $objControl) {
				$objControl->Focus();
				$objControl->Blink();
			}
			
			return true;
		}
	}

	// Run the Form we have defined
	ExamplesForm::Run('ExamplesForm');
?>