<?php
	require('../../includes/prepend.inc.php');
	
	// As an example, we will include the Person object here to define a custom LoadArray method
	// This is here for demonstration purposes only.  Normally, this code would reside in the class file, itself.
	require(__DATAGEN_CLASSES__ . '/PersonGen.class.php');
	class Person extends PersonGen {
		// Note that this LoadByMinimumId method is written in the "Beta 2"-style of Manual Queries
		public static function LoadArrayByMinimumId($intId, $strOrderBy = null, $strLimit = null, $objExpansionMap = null) {
			// Call to ArrayQueryHelper to Get Database Object and Get SQL Clauses
			Person::ArrayQueryHelper($strOrderBy, $strLimit, $strLimitPrefix, $strLimitSuffix, $strExpandSelect, $strExpandFrom, $objExpansionMap, $objDatabase);

			// Escape the Parameter(s)
			$intId = $objDatabase->SqlVariable($intId);

			// Setup the SQL Query
			$strQuery = sprintf('
				SELECT
				%s
					`person`.`id` AS `id`,
					`person`.`first_name` AS `first_name`,
					`person`.`last_name` AS `last_name`
					%s
				FROM
					`person` AS `person`
					%s
				WHERE
					id > %s
				%s
				%s', $strLimitPrefix, $strExpandSelect, $strExpandFrom,$intId,
				$strOrderBy, $strLimitSuffix);

			// Perform the Query and Instantiate the Result
			$objDbResult = $objDatabase->Query($strQuery);
			return Person::InstantiateDbResult($objDbResult);
		}

		// Note that this CountByMinimumId method is written in the "Beta 2"-style of Manual Queries
		public static function CountByMinimumId($intId) {
			// Call to ArrayQueryHelper to Get Database Object and Get SQL Clauses
			Person::QueryHelper($objDatabase);

			// Escape the Parameter(s)
			$intId = $objDatabase->SqlVariable($intId);

			// Setup the SQL Query
			$strQuery = sprintf('
				SELECT
					COUNT(*)
				FROM
					person
				WHERE
					id > %s', $intId);

			// Perform the Query and Instantiate the Result
			$objDbResult = $objDatabase->Query($strQuery);
			$objArray = $objDbResult->FetchArray();
			return $objArray[0];
		}
	}






	// Note that this ExampleForm has a DataGrid which is defined in the "Beta 2"-style of DataGrids
	// (Especially in terms of "SortByCommand" and "LimitInfo")
	class ExampleForm extends QForm {
		protected $dtgPerson;

		// DataGrid Columns
		protected $colId;
		protected $colFirstName;
		protected $colLastName;

		protected function Form_Create() {
			// Setup DataGrid Columns

			// Note that although we are using "Beta 2" style of SortBy and LimitInfo, QDataGrid does have support to "convert" QQ::OrderBy to SortBy strings.
			$this->colId = new QDataGridColumn(QApplication::Translate('Id'), '<?= $_ITEM->Id; ?>', array('OrderByClause' => QQ::OrderBy(QQN::Person()->Id), 'ReverseOrderByClause' => QQ::OrderBy(QQN::Person()->Id, false)));
/*			$this->colId = new QDataGridColumn(QApplication::Translate('Id'), '<?= $_ITEM->Id; ?>', 'SortByCommand="id ASC"', 'ReverseSortByCommand="id DESC"');*/

			$this->colFirstName = new QDataGridColumn(QApplication::Translate('First Name'), '<?= $_ITEM->FirstName; ?>', 'SortByCommand="first_name ASC"', 'ReverseSortByCommand="first_name DESC"');
			$this->colLastName = new QDataGridColumn(QApplication::Translate('Last Name'), '<?= $_ITEM->LastName; ?>', 'SortByCommand="last_name ASC"', 'ReverseSortByCommand="last_name DESC"');

			// Setup DataGrid
			$this->dtgPerson = new QDataGrid($this);
			$this->dtgPerson->CellSpacing = 0;
			$this->dtgPerson->CellPadding = 4;
			$this->dtgPerson->BorderStyle = QBorderStyle::Solid;
			$this->dtgPerson->BorderWidth = 1;
			$this->dtgPerson->GridLines = QGridLines::Both;
			$this->dtgPerson->SortColumnIndex = 0;

			// Datagrid Paginator
			$this->dtgPerson->Paginator = new QPaginator($this->dtgPerson);
			$this->dtgPerson->ItemsPerPage = 5;

			// Specify Whether or Not to Refresh using Ajax
			$this->dtgPerson->UseAjax = true;

			// Add the Columns to the DataGrid
			$this->dtgPerson->AddColumn($this->colId);
			$this->dtgPerson->AddColumn($this->colFirstName);
			$this->dtgPerson->AddColumn($this->colLastName);
		}

		protected function Form_PreRender() {
			// Note that we are calling the Beta 2-style custom Count and LoadArray methods.
			$this->dtgPerson->TotalItemCount = Person::CountByMinimumId(4);
			$this->dtgPerson->DataSource = Person::LoadArrayByMinimumId(4, $this->dtgPerson->SortInfo, $this->dtgPerson->LimitInfo);
		}
	}

	// Go ahead and run this form object
	ExampleForm::Run('ExampleForm');
?>