<?php
	require('../../includes/prepend.inc.php');
	
	class ExampleForm extends QForm {
		// Declare the Proxy Control
		// Notice how this control is NEVER RENDERED outright.  Instead, we use
		// RenderAsHref() and RenderAsEvents() on it.
		protected $pxyExample;
		protected $pnlHover;

		// For this example, show how to use custom HTML to trigger events that updates this Message label
		protected $lblMessage;

		protected function Form_Create() {
			// Define the Proxy
			$this->pxyExample = new QControlProxy($this);

			// Define a Message label
			$this->lblMessage = new QLabel($this);

			// Define a Panel to display/hide whenever we're hovering
			$this->pnlHover = new QPanel($this);
			$this->pnlHover->Text = 'Hovering over a button or link...';
			$this->pnlHover->Padding = 10;
			$this->pnlHover->BorderStyle = QBorderStyle::Solid;
			$this->pnlHover->BorderWidth = 1;
			$this->pnlHover->Width = 200;
			$this->pnlHover->BackColor = '#ffffcc';
			$this->pnlHover->Display = false;

			// Define any applicable actions on the Proxy
			// Note that all events will flow through to any DOM element (in the HTML) that is calling RenderAsEvents.
			$this->pxyExample->AddAction(new QClickEvent(), new QAjaxAction('pxyExample_Click'));
			$this->pxyExample->AddAction(new QClickEvent(), new QTerminateAction());
			$this->pxyExample->AddAction(new QMouseOverEvent(), new QToggleDisplayAction($this->pnlHover, true));
			$this->pxyExample->AddAction(new QMouseOutEvent(), new QToggleDisplayAction($this->pnlHover, false));
		}

		// Notice how the optional "action parameter" we used in the RenderAsHref() or RenderEvents() call gets passed in as $strParameter here.
		protected function pxyExample_Click($strFormId, $strControlId, $strParameter) {
			$this->lblMessage->Text = 'You clicked on: ' . $strParameter;
		}
	}

	ExampleForm::Run('ExampleForm');
?>