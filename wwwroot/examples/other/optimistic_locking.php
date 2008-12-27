<?php
	require('../../includes/prepend.inc.php');

	// Define the Qform with all our Qcontrols
	class ExampleForm extends QForm {
		// Local declarations of our Qcontrols
		protected $lblMessage;

		protected $txtFirstName1;
		protected $txtLastName1;
		protected $lblTimestamp1;
		protected $btnSave1;
		protected $btnForceUpdate1;

		protected $txtFirstName2;
		protected $txtLastName2;
		protected $lblTimestamp2;
		protected $btnSave2;
		protected $btnForceUpdate2;

		// Local Person Objects
		protected $objPerson1;
		protected $objPerson2;
		
		// Get the Current Person Object (just for reference)
		protected $objPersonReference;

		// Initialize our Controls during the Form Creation process
		protected function Form_Create() {
			// Pull two instances of the same object -- PersonWithLock with ID #5
			$this->objPerson1 = PersonWithLock::Load(5);
			$this->objPerson2 = PersonWithLock::Load(5);

			// Define the controls
			$this->lblMessage = new QLabel($this);
			$this->lblMessage->HtmlEntities = false;

			$this->txtFirstName1 = new QTextBox($this);
			$this->txtFirstName1->Text = $this->objPerson1->FirstName;

			$this->txtLastName1 = new QTextBox($this);
			$this->txtLastName1->Text = $this->objPerson1->LastName;
			
			$this->lblTimestamp1 = new QLabel($this);
			$this->lblTimestamp1->Text = $this->objPerson1->SysTimestamp;
			$this->lblTimestamp1->HtmlEntities = false;

			$this->txtFirstName2 = new QTextBox($this);
			$this->txtFirstName2->Text = $this->objPerson2->FirstName;

			$this->txtLastName2 = new QTextBox($this);
			$this->txtLastName2->Text = $this->objPerson2->LastName;
			
			$this->lblTimestamp2 = new QLabel($this);
			$this->lblTimestamp2->Text = $this->objPerson2->SysTimestamp;
			$this->lblTimestamp2->HtmlEntities = false;

			// Define the Buttons
			$this->btnSave1 = new QButton($this);
			$this->btnSave1->ActionParameter = '1';

			$this->btnForceUpdate1 = new QButton($this);
			$this->btnForceUpdate1->ActionParameter = '1';

			$this->btnSave2 = new QButton($this);
			$this->btnSave2->ActionParameter = '2';

			$this->btnForceUpdate2 = new QButton($this);
			$this->btnForceUpdate2->ActionParameter = '2';

			// Add a Click event handlers
			$this->btnSave1->AddAction(new QClickEvent(), new QServerAction('SavePerson'));
			$this->btnForceUpdate1->AddAction(new QClickEvent(), new QServerAction('SavePersonForceUpdate'));
			$this->btnSave2->AddAction(new QClickEvent(), new QServerAction('SavePerson'));
			$this->btnForceUpdate2->AddAction(new QClickEvent(), new QServerAction('SavePersonForceUpdate'));
		}
		
		protected function Form_PreRender() {
			// Update the Reference Person on every pre render
			$this->objPersonReference = PersonWithLock::Load(5);
			
			// Add the "Stale" Message (if applicable)
			if ($this->objPersonReference->SysTimestamp != $this->objPerson1->SysTimestamp)
				$this->lblTimestamp1->Text = sprintf('%s <b>STALE DATA</b>', $this->objPerson1->SysTimestamp);
			else
				$this->lblTimestamp1->Text = $this->objPerson1->SysTimestamp;

			if ($this->objPersonReference->SysTimestamp != $this->objPerson2->SysTimestamp)
				$this->lblTimestamp2->Text = sprintf('%s <b>STALE DATA</b>', $this->objPerson2->SysTimestamp);
			else
				$this->lblTimestamp2->Text = $this->objPerson2->SysTimestamp;
		}
		
		protected function Form_Load() {
			// Clear the Message (if applicable)
			$this->lblMessage->Text = '';
		}

		// The SavePerson Event Handler
		protected function SavePerson($strFormId, $strControlId, $strParameter) {
			if ($strParameter == '1') {
				$objPerson = $this->objPerson1;
				$objPerson->FirstName = $this->txtFirstName1->Text;
				$objPerson->LastName = $this->txtLastName1->Text;
			} else {
				$objPerson = $this->objPerson2;
				$objPerson->FirstName = $this->txtFirstName2->Text;
				$objPerson->LastName = $this->txtLastName2->Text;
			}

			// Try and do the Save, Catch the Optimistic Lock Exception
			try {
				$objPerson->Save();

				// Reload the Person so that it's fresh
				if ($strParameter == '1')
					$this->objPerson1 = PersonWithLock::Load(5);
				else
					$this->objPerson2 = PersonWithLock::Load(5);
			} catch (QOptimisticLockingException $objExc) {
				// Lock Exception Thrown, Report the Error
				$this->lblMessage->Text = '<br/>Optimistic Locking Constraint -- the <b>PersonWithLock</b> you ' .
					'are saving has "stale" data.<br/>' .
					'If you want to save anyway, use "Force Update".';
			}
		}

		// The SavePersonForceUpdate Event handler
		protected function SavePersonForceUpdate($strFormId, $strControlId, $strParameter) {
			if ($strParameter == '1') {
				$objPerson = $this->objPerson1;
				$objPerson->FirstName = $this->txtFirstName1->Text;
				$objPerson->LastName = $this->txtLastName1->Text;
			} else {
				$objPerson = $this->objPerson2;
				$objPerson->FirstName = $this->txtFirstName2->Text;
				$objPerson->LastName = $this->txtLastName2->Text;
			}

			// Do the Save, Forcing the Update
			$objPerson->Save(false, true);

			// Reload the Person so that it's fresh
			if ($strParameter == '1')
				$this->objPerson1 = PersonWithLock::Load(5);
			else
				$this->objPerson2 = PersonWithLock::Load(5);
		}
	}

	// Run the Form we have defined
	ExampleForm::Run('ExampleForm');
?>