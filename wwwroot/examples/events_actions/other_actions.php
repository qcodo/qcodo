<?php
	require('../../includes/prepend.inc.php');

	class ExampleForm extends QForm {
		protected $btnFocus;
		protected $btnSelect;
		protected $txtFocus;

		protected $btnToggleDisplay;
		protected $txtDisplay;

		protected $btnToggleEnable;
		protected $txtEnable;
		
		protected $pnlHover;

		protected function Form_Create() {
			// Define the Textboxes
			$this->txtFocus = new QTextBox($this);
			$this->txtFocus->Text = 'Example Text Here';
			$this->txtDisplay = new QTextBox($this);
			$this->txtDisplay->Text = 'Example Text Here';
			$this->txtEnable = new QTextBox($this);
			$this->txtEnable->Text = 'Example Text Here';

			// QFocusControlAction example
			$this->btnFocus = new QButton($this);
			$this->btnFocus->Text = 'Set Focus';
			$this->btnFocus->AddAction(new QClickEvent(), new QFocusControlAction($this->txtFocus));

			// QSelectControlAction example
			$this->btnSelect = new QButton($this);
			$this->btnSelect->Text = 'Select All in Textbox';
			$this->btnSelect->AddAction(new QClickEvent(), new QSelectControlAction($this->txtFocus));

			// QToggleDisplayAction example
			$this->btnToggleDisplay = new QButton($this);
			$this->btnToggleDisplay->Text = 'Toggle the Display (show/hide)';
			$this->btnToggleDisplay->AddAction(new QClickEvent(), new QToggleDisplayAction($this->txtDisplay));

			// QToggleEnableAction example
			$this->btnToggleEnable = new QButton($this);
			$this->btnToggleEnable->Text = 'Toggle the Enable (enabled/disabled)';
			$this->btnToggleEnable->AddAction(new QClickEvent(), new QToggleEnableAction($this->txtEnable));

			// QCssClassAction example
			$this->pnlHover = new QPanel($this);
			$this->pnlHover->HtmlEntities = false;
			$this->pnlHover->Text = 'Example of <b>QCssClassAction</b><br/><br/>(Uses QMouseOver and QMouseOut to Temporarily Override the Panel\'s CSS Style)';

			// Set a Default Style
			$this->pnlHover->CssClass = 'panelHover';

			// Add QMouseOver and QMouseOut actions to set and then reset temporary style overrides
			// Setting the TemporaryCssClass to "null" will "reset" the style back to the default
			$this->pnlHover->AddAction(new QMouseOverEvent(), new QCssClassAction('panelHighlight', true));
			$this->pnlHover->AddAction(new QMouseOutEvent(), new QCssClassAction());
		}
	}

	ExampleForm::Run('ExampleForm');
?>