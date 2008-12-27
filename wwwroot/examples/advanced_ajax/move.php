<?php
	require('../../includes/prepend.inc.php');

	// Define the Qform with all our Qcontrols
	class ExamplesForm extends QForm {
		// Local declarations of our Qcontrols
		protected $pnlPanel;

		// Initialize our Controls during the Form Creation process
		protected function Form_Create() {
			// Define the Panel
			$this->pnlPanel = new QPanel($this);
			$this->pnlPanel->Text = 'You can click on me to drag me around.';

			// Make the Panel's Positioning Absolute, and specify a starting location
			$this->pnlPanel->Position = QPosition::Absolute;
			$this->pnlPanel->Top = 450;
			$this->pnlPanel->Left = 150;

			// Finally, let's make this moveable.  We do this by using the methods
			// which specify it as a move handle, and we assign itself as the target
			// control which it will move.
			$this->pnlPanel->AddControlToMove($this->pnlPanel);
		}
	}

	// Run the Form we have defined
	ExamplesForm::Run('ExamplesForm');
?>