<?php
	// Include prepend.inc to load Qcodo
	require(dirname(__FILE__) . '/../../../includes/prepend.inc.php');

	// Security check for ALLOW_REMOTE_ADMIN
	// To allow access REGARDLESS of ALLOW_REMOTE_ADMIN, simply remove the line below
	QApplication::CheckRemoteAdmin();


	// Let's "magically" determine the list of genereated Class Panel Drafts by
	// just traversing through this directory, looking for "*ListPanel.class.php" and "*EditPanel.class.php"

	// Obviously, if you are wanting to make your own dashbaord, you should change this and use more
	// hard-coded means to determine which classes' paneldrafts you want to include/use in your dashboard.
	$objDirectory = opendir(dirname(__FILE__));
	$strClassNameArray = array();
	while ($strFile = readdir($objDirectory)) {
		if ($intPosition = strpos($strFile, 'ListPanel.class.php')) {
			$strClassName = substr($strFile, 0, $intPosition);
			$strClassNameArray[$strClassName] = $strClassName . 'ListPanel';
			require($strClassName . 'ListPanel.class.php');
			require($strClassName . 'EditPanel.class.php');
		}
	}


	class Dashboard extends QForm {
		protected $lstClassNames;

		protected $pnlTitle;
		protected $pnlList;
		protected $pnlEdit;

		protected function Form_Create() {
			$this->pnlTitle = new QPanel($this);
			$this->pnlTitle->Text = QApplication::Translate('AJAX Dashboard');

			$this->pnlList = new QPanel($this, 'pnlList');
			$this->pnlList->AutoRenderChildren = true;

			$this->pnlEdit = new QPanel($this, 'pnlEdit');
			$this->pnlEdit->AutoRenderChildren = true;
			$this->pnlEdit->Visible = false;

			$this->lstClassNames = new QListBox($this);
			$this->lstClassNames->AddItem(QApplication::Translate('- Select One -'), null);

			// Use the strClassNameArray as magically determined above to aggregate the listbox of classes
			// Obviously, this should be modified if you want to make a custom dashboard
			global $strClassNameArray;
			foreach ($strClassNameArray as $strKey => $strValue)
				$this->lstClassNames->AddItem($strKey, $strValue);
			$this->lstClassNames->AddAction(new QChangeEvent(), new QAjaxAction('lstClassNames_Change'));
			
			$this->objDefaultWaitIcon = new QWaitIcon($this);
		}

		/**
		 * This Form_Validate event handler allows you to specify any custom Form Validation rules.
		 * It will also Blink() on all invalid controls, as well as Focus() on the top-most invalid control.
		 */
		protected function Form_Validate() {
			// By default, we report that Custom Validations passed
			$blnToReturn = true;

			$blnFocused = false;
			foreach ($this->GetErrorControls() as $objControl) {
				// Set Focus to the top-most invalid control
				if (!$blnFocused) {
					$objControl->Focus();
					$blnFocused = true;
				}

				// Blink on ALL invalid controls
				$objControl->Blink();
			}

			return $blnToReturn;
		}

		protected function lstClassNames_Change($strFormId, $strControlId, $strParameter) {
			// Get rid of all child controls for list and edit panels
			$this->pnlList->RemoveChildControls(true);
			$this->pnlEdit->RemoveChildControls(true);
			$this->pnlEdit->Visible = false;

			if ($strClassName = $this->lstClassNames->SelectedValue) {
				// We've selected a Class Name
				$objNewPanel = new $strClassName($this->pnlList, 'SetEditPane', 'CloseEditPane');
				$this->pnlTitle->Text = $this->lstClassNames->SelectedName;
			} else {
				$this->pnlTitle->Text = 'AJAX Dashboard';
			}
		}

/*		public function SetListPane(QPanel $objPanel) {
			$this->pnlList->RemoveChildControls(true);
			$objPanel->SetParentControl($this->pnlList);
		}*/

		public function CloseEditPane($blnUpdatesMade) {
			// Close the Edit Pane
			$this->pnlEdit->RemoveChildControls(true);
			$this->pnlEdit->Visible = false;

			// If updates were made, let's "brute force" the updates to the screen by just refreshing the list pane altogether
			if ($blnUpdatesMade)
				$this->pnlList->Refresh();
		}

		public function SetEditPane(QPanel $objPanel = null) {
			$this->pnlEdit->RemoveChildControls(true);
			if ($objPanel) {
				$objPanel->SetParentControl($this->pnlEdit);
				$this->pnlEdit->Visible = true;
			} else {
				$this->pnlEdit->Visible = false;
			}
		}
	}

	Dashboard::Run('Dashboard');
?>