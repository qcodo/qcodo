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

			// Note that for the HTML, you can specify the PHP interpreter to kick in to interpret objects, methods,
			// call functions, etc.  Simply use "<?= =>" tags to specify what needs to be interpreted.  Note that
			// the use of the <?= short tag is *NOT* a function of PHP short tag support, but more a standard
			// delimeter that Qcodo happens to use to specify when PHP interpretation should begin and end.
			
			// Note that you can use Attribute Overriding here define styles for specific columns (e.g. the last name
			// is always in bold)
			$this->dtgPersons->AddColumn(new QDataGridColumn('First Name', 'First Name is "<?= $_ITEM->FirstName ?>"', 'Width=200'));
			$this->dtgPersons->AddColumn(new QDataGridColumn('Last Name', '<?= $_ITEM->LastName ?>', 'FontBold=true'));

			// Specify the local Method which will actually bind the data source to the datagrid.
			// In order to not over-bloat the form state, the datagrid will use the data source only when rendering itself,
			// and then it will proceed to remove the data source from memory.  Because of this, you will need to define
			// a "data binding" method which will set the datagrid's data source.  You specify the name of the method
			// here.  The framework will be responsible for calling your data binding method whenever the datagrid wants
			// to render itself.
			$this->dtgPersons->SetDataBinder('dtgPersons_Bind');

			// Update the styles of all the rows, or for just specific rows
			// (e.g. you can specify a specific style for the header row or for alternating rows)
			// Note that styles are hierarchical and inherit from each other.  For example, the default RowStyle
			// sets the FontSize as 12px, and because that attribute is not overridden in AlternateRowStyle
			// or HeaderRowStyle, both those styles will use the 12px Font Size.
			$objStyle = $this->dtgPersons->RowStyle;
			$objStyle->BackColor = '#ffddff';
			$objStyle->FontSize = 12;

			$objStyle = $this->dtgPersons->AlternateRowStyle;
			$objStyle->BackColor = '#ccccff';

			$objStyle = $this->dtgPersons->HeaderRowStyle;
			$objStyle->ForeColor = '#ddeeff';
			$objStyle->BackColor = '#420182';

		}

		protected function dtgPersons_Bind() {
			// We load the data source, and set it to the datagrid's DataSource parameter
			$this->dtgPersons->DataSource = Person::LoadAll();
		}
	}

	ExampleForm::Run('ExampleForm');
?>