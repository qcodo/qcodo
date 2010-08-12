<?php
	class QDateTimePicker extends QDateTimePickerBase {
		///////////////////////////
		// QDateTimePicker Preferences
		///////////////////////////

		// Feel free to specify global display preferences/defaults for all QDateTimePicker controls
		protected $strCssClass = 'datetimepicker';

		protected $strDateTimePickerType = QDateTimePickerType::Date;
		protected $strDateTimePickerFormat = QDateTimePickerFormat::MonthDayYear;

		protected $intMinimumYear = 1970;
		protected $intMaximumYear = 2015;

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