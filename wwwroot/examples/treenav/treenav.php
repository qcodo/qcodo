<?php
	require('../../includes/prepend.inc.php');

	QForm::$FormStateHandler = 'QSessionFormStateHandler';

	class ExampleForm extends QForm {
		protected $tnvExample;
		protected $pnlCode;
		
		// Define all the QContrtol objects for our Calculator
		// Make our textboxes IntegerTextboxes and make them required
		protected function Form_Create() {
			$this->tnvExample = new QTreeNav($this);
			$this->tnvExample->CssClass = 'treenav';
			$this->tnvExample->AddAction(new QChangeEvent(), new QAjaxAction('tnvExample_Change'));

			$this->pnlCode = new QPanel($this);
			$this->pnlCode->CssClass = 'codeDisplay';

			$this->objDefaultWaitIcon = new QWaitIcon($this);

			// Create a treenav of the file/folder directory for qcodo includes
			$this->tnvExample_AddItems(dirname(__INCLUDES__ . '.'));
		}

		protected function tnvExample_AddItems($strDirectory, $objParentItem = null) {
			$objDirectory = opendir($strDirectory);
			if (!$objParentItem)
				$objParentItem = $this->tnvExample;

			while ($strFilename = readdir($objDirectory)) {
				if (($strFilename) && ($strFilename != '.') && ($strFilename != '..') && ($strFilename != 'configuration.inc.php') && ($strFilename != 'configuration_pro.inc.php') && ($strFilename != 'CVS')) {
					// Create the new TreeNavItem
					$tniFile = new QTreeNavItem($strFilename, $strDirectory . '/' . $strFilename, false, $objParentItem);

					// Recurse down the tree if we're at a directory
					if (is_dir($strDirectory . '/' . $strFilename)) {
						// We're currently looking at a directory -- make recursive call to go down the tree
						$this->tnvExample_AddItems($strDirectory . '/' . $strFilename, $tniFile);
					}
				}
			}

			closedir($objDirectory);
		}

		protected function tnvExample_Change($strFormId, $strControlId, $strParameter) {
			$objItem = $this->tnvExample->SelectedItem;
			if (is_dir($this->tnvExample->SelectedValue))
				$this->pnlCode->Text = 'Current directory is <b>' . $this->tnvExample->SelectedItem->Name . '</b>.  ' .
					'Please select a file on the left';
			else {
				$strCode = highlight_file($this->tnvExample->SelectedValue, true);
				$this->pnlCode->Text = $strCode;
			}
		}

	}

	// And now run our defined form
	ExampleForm::Run('ExampleForm');
?>