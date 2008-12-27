<?php
	require('../../includes/prepend.inc.php');

	// Define the Qform with all our Qcontrols
	class ExamplesForm extends QForm {
		// Local declarations of our Qcontrols
		protected $pnlPanel;
		protected $pnlDropZone1;
		protected $pnlDropZone2;

		// Initialize our Controls during the Form Creation process
		protected function Form_Create() {
			// Define the Panel
			$this->pnlPanel = new QPanel($this);
			$this->pnlPanel->Text = 'You can click on me to drag me around.';

			// Make the Panel's Positioning Absolute, and specify a starting location
			$this->pnlPanel->Position = QPosition::Absolute;
			$this->pnlPanel->Top = 500;
			$this->pnlPanel->Left = 80;

			// Make the Panel Moveable, assigning itself as the target
			$this->pnlPanel->AddControlToMove($this->pnlPanel);

			// Create some larger panels to use as Drop Zones
			$this->pnlDropZone1 = new QPanel($this);
			$this->pnlDropZone1->Position = QPosition::Absolute;
			$this->pnlDropZone1->Top = 450;
			$this->pnlDropZone1->Left = 300;
			$this->pnlDropZone1->Text = 'Drop Zone 1';

			$this->pnlDropZone2 = new QPanel($this);
			$this->pnlDropZone2->Position = QPosition::Absolute;
			$this->pnlDropZone2->Top = 450;
			$this->pnlDropZone2->Left = 600;
			$this->pnlDropZone2->Text = 'Drop Zone 2';

			// Finally, let's setup the drop zones for pnlPanel
			// Note that when a movehandle is first initialized, it's current parent
			// is pre-defined as a dropzone.  Because pnlPanel's parent is the main form,
			// pnlPanel currenlty has the main form as a drop zone.  We don't want that here,
			// so we will first remove all the dropzones, and then we will add the two dropzone
			// panels as the only two valid drop zones for pnlPanel.
			$this->pnlPanel->RemoveAllDropZones();
			$this->pnlPanel->AddDropZone($this->pnlDropZone1);
			$this->pnlPanel->AddDropZone($this->pnlDropZone2);
		}
	}

	// Run the Form we have defined
	ExamplesForm::Run('ExamplesForm');
?>