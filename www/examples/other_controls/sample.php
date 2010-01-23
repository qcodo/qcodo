<?php
	require(dirname(__FILE__) . '/../_require_prepend.inc.php');
	require(__INCLUDES__ . '/examples/examples.inc.php');

	class ExampleForm extends ExamplesBaseForm {
		protected $ctlCustom;

		protected function Form_Create() {
			// Get the Custom Control
			$this->ctlCustom = new QSampleControl($this);

			// Note that custom controls can act just like regular controls,
			// complete with events and attributes
			$this->ctlCustom->Foo = 'Click on me!';
			$this->ctlCustom->AddAction(new QClickEvent(), new QAlertAction('Hello, world!'));
		}
	}

	// And now run our defined form
	ExampleForm::Run('ExampleForm');
?>