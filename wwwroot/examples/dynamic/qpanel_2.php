<?php
	require('../../includes/prepend.inc.php');
	
	class ExampleForm extends QForm {
		// Declare the panels and the buttons
		// Notice how we don't declare the textboxes that we will be moving back and forth.
		// We do this to demonstrate that the panel can manage its own set of dynamically controls
		// through using GetChildControls() and AutoRenderChildren
		protected $pnlLeft;
		protected $pnlRight;
		protected $btnMoveLeft;
		protected $btnMoveRight;
		protected $btnDeleteLeft;

		protected function Form_Create() {
			// Define the Panels
			$this->pnlLeft = new QPanel($this);
			$this->pnlLeft->CssClass = 'textbox_panel';

			$this->pnlRight = new QPanel($this);
			$this->pnlRight->CssClass = 'textbox_panel';

			// Let's have the panels auto render any and all child controls
			$this->pnlLeft->AutoRenderChildren = true;
			$this->pnlRight->AutoRenderChildren = true;

			// Define the Buttons
			$this->btnMoveLeft = new QButton($this);
			$this->btnMoveLeft->Text = '<<';
			$this->btnMoveLeft->AddAction(new QClickEvent(), new QAjaxAction('MoveTextbox'));
			$this->btnMoveLeft->ActionParameter = 'left';

			$this->btnMoveRight = new QButton($this);
			$this->btnMoveRight->Text = '>>';
			$this->btnMoveRight->AddAction(new QClickEvent(), new QAjaxAction('MoveTextbox'));
			$this->btnMoveRight->ActionParameter = 'right';

			$this->btnDeleteLeft = new QButton($this);
			$this->btnDeleteLeft->Text = 'Delete One From Left';
			$this->btnDeleteLeft->AddAction(new QClickEvent(), new QAjaxAction('btnDeleteLeft_Click'));

			// Define a bunch of textboxes, and put it into the left Panel
			for ($intIndex = 1; $intIndex <= 10; $intIndex++) {
				// The parent must be the panel, because the panel is going to be responsible
				// for rendering it.
				$txtTextbox = new QTextBox($this->pnlLeft);
				$txtTextbox->Text = sprintf('Textbox #%s', $intIndex);
				$txtTextbox->Width = 250;
			}
		}

		// Handle the action for the Button being clicked.  We want to basically
		// move one of the textboxes from one panel to the other
		protected function MoveTextbox($strFormId, $strControlId, $strParameter) {
			if ($strParameter == 'left') {
				$pnlSource = $this->pnlRight;
				$pnlDestination = $this->pnlLeft;
			} else {
				$pnlSource = $this->pnlLeft;
				$pnlDestination = $this->pnlRight;
			}

			// Get the Source's Child Controls
			$objChildControls = $pnlSource->GetChildControls();
			
			// Only make the move if source has at least one control to move
			if (count($objChildControls) > 0) {
				// Set the parent of the last control in this array to be the destination panel,
				// essentially moving it from one panel to the other
				$objChildControls[count($objChildControls) - 1]->SetParentControl($pnlDestination);
			}
		}
		
		// Handle the action to delete a control from pnlLeft
		protected function btnDeleteLeft_Click($strFormId, $strControlId, $strParameter) {
			// Get the left panel's Child Controls
			$objChildControls = $this->pnlLeft->GetChildControls();

			// Only remove if pnlLeft has at least one control to remove
			if (count($objChildControls) > 0) {
				// Set the parent of the last control in this array to be NULL,
				// essentially removing it from the panel (and the form altogether)
				$objChildControls[count($objChildControls) - 1]->SetParentControl(null);
			}
		}
	}

	ExampleForm::Run('ExampleForm');
?>