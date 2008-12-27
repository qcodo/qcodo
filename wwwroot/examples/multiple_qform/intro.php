<?php
	require('../../includes/prepend.inc.php');

	// We need to bring in the custom QPanels we've created
	require('PersonEditPanel.class.php');
	require('ProjectViewPanel.class.php');
	require('ProjectEditPanel.class.php');
	
	// Define the Qform with all our Qcontrols
	class ExamplesForm extends QForm {
		// Local declarations of our Qcontrols
		protected $lstProjects;
		protected $pnlLeft;
		protected $pnlRight;
		protected $txtBlah;
		protected $btnBlah;

		// Initialize our Controls during the Form Creation process
		protected function Form_Create() {
			// Setup the Dropdown of Project Names
			$this->lstProjects = new QListBox($this);
			$this->lstProjects->AddItem('- Select One -', null, true);
			foreach (Project::LoadAll(QQ::Clause(QQ::OrderBy(QQN::Project()->Name))) as $objProject)
				$this->lstProjects->AddItem($objProject->Name, $objProject->Id);
			$this->lstProjects->AddAction(new QChangeEvent(), new QAjaxAction('lstProjects_Change'));

			// Setup our Left and Right Panel Placeholders
			// Notice that both panels have "AutoRenderChildren" set to true so that
			// instantiated child panels will automatically get displayed
			$this->pnlLeft = new QPanel($this);
			$this->pnlLeft->Position = QPosition::Relative;
			$this->pnlLeft->CssClass = 'panelDefault';
			$this->pnlLeft->AutoRenderChildren = true;

			$this->pnlRight = new QPanel($this);
			$this->pnlRight->Position = QPosition::Relative;
			$this->pnlRight->CssClass = 'panelDefault panelRight';
			$this->pnlRight->AutoRenderChildren = true;
			
			$this->objDefaultWaitIcon = new QWaitIcon($this);
		}

		// The "btnButton_Click" Event handler
		protected function lstProjects_Change($strFormId, $strControlId, $strParameter) {
			// First, remove all children panels from both pnlLeft and pnlRight
			$this->pnlLeft->RemoveChildControls(true);
			$this->pnlRight->RemoveChildControls(true);

			// Now, we create a new ProjectViewPanel, and set its parent to pnlLeft
			if ($intProjectId = $this->lstProjects->SelectedValue)
				$pnlProjectView = new ProjectViewPanel($this->pnlLeft, Project::Load($intProjectId), $this->pnlRight->ControlId);
		}

		// Method Call back for any of the RightPanel panels (see note in ProjectViewPanel for more information)
		public function CloseRightPanel($blnUpdatesMade) {
			// First, remove all children panels from both pnlRight
			$this->pnlRight->RemoveChildControls(true);

			// If Updates were Made, then Re-Draw Left Panel to reflect the changes
			// Note that this is a "brute force" method to update the entire left panel
			// Of course, if you want, you can more finely tune this update process by only updating specific
			// controls, etc., depending on what was updated/changed.
			if ($blnUpdatesMade) {
				$this->pnlLeft->RemoveChildControls(true);
				if ($intProjectId = $this->lstProjects->SelectedValue)
					$pnlProjectView = new ProjectViewPanel($this->pnlLeft, Project::Load($intProjectId), $this->pnlRight->ControlId);
			}
		}
	}

	// Run the Form we have defined
	ExamplesForm::Run('ExamplesForm');
?>