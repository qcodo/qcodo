<?php
	require('../../includes/prepend.inc.php');
	
	class ExampleForm extends QForm {
		// Declare the DataGrid
		protected $dtgPersons;

		protected function Form_Create() {
			// Define the DataGrid
			$this->dtgPersons = new QDataGrid($this);
			$this->dtgPersons->CellPadding = 5;
			$this->dtgPersons->CellSpacing = 0;
			
			// Define Columns
			// We will use $_ITEM, $_CONTROL and $_FORM to show how you can make calls to the Person object
			// being itereated ($_ITEM), the QDataGrid itself ($_CONTROL), and the QForm itself ($_FORM).
			$this->dtgPersons->AddColumn(new QDataGridColumn('Row Number', '<?= ($_CONTROL->CurrentRowIndex + 1) ?>'));
			$this->dtgPersons->AddColumn(new QDataGridColumn('First Name', '<?= $_ITEM->FirstName ?>', 'Width=200'));
			$this->dtgPersons->AddColumn(new QDataGridColumn('Last Name', '<?= $_ITEM->LastName ?>', 'Width=200'));
			$this->dtgPersons->AddColumn(new QDataGridColumn('Full Name', '<?= $_FORM->DisplayFullName($_ITEM) ?>', 'Width=300'));

			// Specify the Datagrid's Data Binder method
			$this->dtgPersons->SetDataBinder('dtgPersons_Bind');

			// Make the DataGrid look nice
			$objStyle = $this->dtgPersons->RowStyle;
			$objStyle->FontSize = 12;

			$objStyle = $this->dtgPersons->AlternateRowStyle;
			$objStyle->BackColor = '#eaeaea';

			$objStyle = $this->dtgPersons->HeaderRowStyle;
			$objStyle->ForeColor = 'white';
			$objStyle->BackColor = '#000066';

		}
		
		// DisplayFullName will be called by the DataGrid on each row, whenever it tries to render
		// the Full Name column.  Note that we take in the $objPerson as a Person parameter.  Also
		// note that DisplayFullName is a PUBLIC function -- because it will be called by the QDataGrid class.
		public function DisplayFullName(Person $objPerson) {
			$strToReturn = sprintf('%s, %s', $objPerson->LastName, $objPerson->FirstName);
			return $strToReturn;
		}

		protected function dtgPersons_Bind() {
			// We must be sure to load the data source
			$this->dtgPersons->DataSource = Person::LoadAll();
		}
	}

	ExampleForm::Run('ExampleForm');
?>