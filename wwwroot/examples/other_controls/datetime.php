<?php
	require('../../includes/prepend.inc.php');

	class ExampleForm extends QForm {
		protected $dtxDateTimeTextBox;
		protected $btnDateTimeTextBox;

		protected $calCalendar;
		protected $dtxCalendar;
		protected $btnCalendar;

		protected $calCalendarPopup;
		protected $btnCalendarPopup;

		protected $dtpDatePicker;
		protected $btnDatePicker;

		protected $dtpDateTimePicker;
		protected $btnDateTimePicker;

		protected $lblResult;

		protected function Form_Create() {
			// Create Sample Date and DateTime Controls
			$this->calCalendarPopup = new QCalendarPopup($this);

			$this->dtxDateTimeTextBox = new QDateTimeTextBox($this);

			// Note that QCalendar REQUIRES a "linked" QDateTimeTextBox
			$this->dtxCalendar = new QDateTimeTextBox($this, 'foo');
			$this->calCalendar = new QCalendar($this, $this->dtxCalendar);

			// To make things easier, let's make sure the $dtxCalendar is disabled, and clicking
			// on it makes the calendar appear.
			$this->dtxCalendar->AddAction(new QFocusEvent(), new QBlurControlAction($this->dtxCalendar));
			$this->dtxCalendar->AddAction(new QClickEvent(), new QShowCalendarAction($this->calCalendar));

			// QDateTimePicker can have different "Types"
			$this->dtpDatePicker = new QDateTimePicker($this);
			$this->dtpDatePicker->DateTimePickerType = QDateTimePickerType::Date;

			$this->dtpDateTimePicker = new QDateTimePicker($this);
			$this->dtpDateTimePicker->DateTimePickerType = QDateTimePickerType::DateTime;

			// To View the "Results"
			$this->lblResult = new QLabel($this);
			$this->lblResult->Text = 'Results...';

			// Various Buttons
			$this->btnCalendar = new QButton($this);
			$this->btnCalendar->Text = 'Update';
			$this->btnCalendar->AddAction(new QClickEvent(), new QAjaxAction('btnUpdate_Click'));

			// NOTE -- to get the Value of the Calendar, we MUST look it up from the linked dtxCalendar
			$this->btnCalendar->ActionParameter = $this->dtxCalendar->ControlId;

			$this->btnCalendarPopup = new QButton($this);
			$this->btnCalendarPopup->Text = 'Update';
			$this->btnCalendarPopup->AddAction(new QClickEvent(), new QAjaxAction('btnUpdate_Click'));
			$this->btnCalendarPopup->ActionParameter = $this->calCalendarPopup->ControlId;

			$this->btnDateTimeTextBox = new QButton($this);
			$this->btnDateTimeTextBox->Text = 'Update';
			$this->btnDateTimeTextBox->AddAction(new QClickEvent(), new QAjaxAction('btnUpdate_Click'));
			$this->btnDateTimeTextBox->ActionParameter = $this->dtxDateTimeTextBox->ControlId;

			$this->btnDatePicker = new QButton($this);
			$this->btnDatePicker->Text = 'Update';
			$this->btnDatePicker->AddAction(new QClickEvent(), new QAjaxAction('btnUpdate_Click'));
			$this->btnDatePicker->ActionParameter = $this->dtpDatePicker->ControlId;

			$this->btnDateTimePicker = new QButton($this);
			$this->btnDateTimePicker->Text = 'Update';
			$this->btnDateTimePicker->AddAction(new QClickEvent(), new QAjaxAction('btnUpdate_Click'));
			$this->btnDateTimePicker->ActionParameter = $this->dtpDateTimePicker->ControlId;
		}

		protected function btnUpdate_Click($strFormId, $strControlId, $strParameter) {
			$objControlToLookup = $this->GetControl($strParameter);
			$dttDateTime = $objControlToLookup->DateTime;

			// If a DateTime value is NOT selected or is INVALID, then this will be NULL
			if ($dttDateTime) {
				$this->lblResult->Text = 'QDateTime object:<br/>';
				if (!$dttDateTime->IsDateNull())
					$this->lblResult->Text .= 'Date: <strong>' . $dttDateTime->__toString('DDD MMM D YYYY') . '</strong><br/>';
				else
					$this->lblResult->Text .= 'Date: <strong>Null</strong><br/>';
				if (!$dttDateTime->IsTimeNull())
					$this->lblResult->Text .= 'Time: <strong>' . $dttDateTime->__toString('h:mm:ss z') . '</strong>';
				else
					$this->lblResult->Text .= 'Time: <strong>Null</strong>';
			} else {
				$this->lblResult->Text = 'QDateTime object: <strong>Null</strong>';
			}
		}
	}

	// And now run our defined form
	ExampleForm::Run('ExampleForm');
?>