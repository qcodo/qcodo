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
			
			// To create pagination, we will create a new paginator, and specify the datagrid
			// as the paginator's parent.  (We do this because the datagrid is the control
			// who is responsible for rendering the paginator, as opposed to the form.)
			$objPaginator = new QPaginator($this->dtgPersons);
			$this->dtgPersons->Paginator = $objPaginator;
			
			// Now, with a paginator defined, we can set up some additional properties on
			// the datagrid.  For purposes of this example, let's make the datagrid show
			// only 5 items per page.
			$this->dtgPersons->ItemsPerPage = 20;

			// Define Columns
			$this->dtgPersons->AddColumn(new QDataGridColumn('Person ID', '<?= $_ITEM->Id ?>', 'Width=100',
				array('OrderByClause' => QQ::OrderBy(QQN::Person()->Id), 'ReverseOrderByClause' => QQ::OrderBy(QQN::Person()->Id, false))));
			$this->dtgPersons->AddColumn(new QDataGridColumn('First Name', '<?= $_ITEM->FirstName ?>', 'Width=200',
				array('OrderByClause' => QQ::OrderBy(QQN::Person()->FirstName), 'ReverseOrderByClause' => QQ::OrderBy(QQN::Person()->FirstName, false))));
			$this->dtgPersons->AddColumn(new QDataGridColumn('Last Name', '<?= $_ITEM->LastName ?>', 'Width=200',
				array('OrderByClause' => QQ::OrderBy(QQN::Person()->LastName), 'ReverseOrderByClause' => QQ::OrderBy(QQN::Person()->LastName, false))));

			// Let's pre-default the sorting by last name (column index #2)
			$this->dtgPersons->SortColumnIndex = 2;

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

			// Because browsers will apply different styles/colors for LINKs
			// We must explicitly define the ForeColor for the HeaderLink.
			// The header row turns into links when the column can be sorted.
			$objStyle = $this->dtgPersons->HeaderLinkStyle;
			$objStyle->ForeColor = 'white';
		}

		protected function dtgPersons_Bind() {
			// We must first let the datagrid know how many total items there are
			$this->dtgPersons->TotalItemCount = Person::CountAll();

			// Next, we must be sure to load the data source, passing in the datagrid's
			// limit info into our loadall method.
			$this->dtgPersons->DataSource = Person::LoadAll(QQ::Clause(
				$this->dtgPersons->OrderByClause,
				$this->dtgPersons->LimitClause
			));
		}
	}

	ExampleForm::Run('ExampleForm');
?>