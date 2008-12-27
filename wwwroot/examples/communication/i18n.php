<?php
	require('../../includes/prepend.inc.php');

	// NOTE: IF YOU ARE RUNNING THIS EXAMPLE FROM YOUR OWN DEVELOPMENT ENVIRONMENT
	// you **MUST** remember to copy the custom es.po file from this directory and
	// place it into /includes/qcodo/i18n

	class ExamplesForm extends QForm {
		protected $btnEs;
		protected $btnEn;

		// Initialize our Controls during the Form Creation process
		protected function Form_Create() {
			// Note how we do not define any TEXT properties here -- we define them
			// in the template, so that translation and langauge switches can occur
			// even after this form is created
			$this->btnEs = new QButton($this);
			$this->btnEs->ActionParameter = 'es';
			$this->btnEs->AddAction(new QClickEvent(), new QServerAction('button_Click'));

			$this->btnEn = new QButton($this);
			$this->btnEn->ActionParameter = 'en';
			$this->btnEn->AddAction(new QClickEvent(), new QServerAction('button_Click'));
		}

		// The "btnButton_Click" Event handler
		protected function button_Click($strFormId, $strControlId, $strParameter) {
			// NORMALLY -- these settings are setup in prepend.inc
			// But it is pulled out here to illustrate

			$_SESSION['language_code'] = $strParameter;

			// In order for I18n Translation to be enabled, you must have a language code
			// defined and the QI18n object must be initialized
			QApplication::$LanguageCode = $strParameter;
			QI18n::Initialize();
		}
	}

	// Run the Form we have defined
	// The QForm engine will look to intro.tpl.php to use as its HTML template include file
	ExamplesForm::Run('ExamplesForm');
?>