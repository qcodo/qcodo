<?php
	class QDateTimePicker extends QDateTimePickerBase {
		///////////////////////////
		// QDateTimePicker Preferences
		///////////////////////////

		// Feel free to specify global display preferences/defaults for all QDateTimePicker controls
		protected $strCssClass = 'datetimepicker';

		protected $strDateTimePickerType = QDateTimePickerType::Date;
		protected $strDateTimePickerFormat = QDateTimePickerFormat::MonthDayYear;

		// If these stay null, then it will use the $DefaultMinimumYear and $DefaultMaximumYear static variables on QDateTimePicker
		protected $intMinimumYear = null;
		protected $intMaximumYear = null;

		// Default format of the Hour field (see http://www.php.net/date for more info)
		// Two digit representation of the hour in 12-hour format followed by UPPER-CASE 'AM' or 'PM'
		protected $strHourFormat = 'g A';  

		// Default format of Month field (see http://www.php.net/strftime for more info)
		// Abbreviated month name, based on the locale
		protected $strMonthFormat = '%b';

		// Optional HTML to Insert Between ListBoxes
		protected $strDividerHtml;
		protected $strSpacerHtml;
	}
?>