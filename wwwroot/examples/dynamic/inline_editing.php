<?php
	require('../../includes/prepend.inc.php');
	
	class ExampleForm extends QForm {
		// Declare the DataGrid, and the buttons and textboxes for inline editing
		protected $dtgPersons;
		protected $txtFirstName;
		protected $txtLastName;
		protected $btnSave;
		protected $btnCancel;
		protected $btnNew;

		// This value is either a Person->Id, "null" (if nothing is being edited), or "-1" (if creating a new Person)
		protected $intEditPersonId = null;

		protected function Form_Create() {
			// Define the DataGrid
			$this->dtgPersons = new QDataGrid($this);
			$this->dtgPersons->CellPadding = 5;
			$this->dtgPersons->CellSpacing = 0;

			// Define Columns -- we will define render helper methods to help with the rendering
			// of the HTML for most of these columns
			$this->dtgPersons->AddColumn(new QDataGridColumn('Person ID', '<?= $_ITEM->Id ?>', 'Width=100',
				array('OrderByClause' => QQ::OrderBy(QQN::Person()->Id), 'ReverseOrderByClause' => QQ::OrderBy(QQN::Person()->Id, false))));
				
			// Setup the First and Last name columns with HtmlEntities set to false (because they may be rendering textbox controls)
			$this->dtgPersons->AddColumn(new QDataGridColumn('First Name', '<?= $_FORM->FirstNameColumn_Render($_ITEM) ?>', 'Width=200', 'HtmlEntities=false',
				array('OrderByClause' => QQ::OrderBy(QQN::Person()->FirstName), 'ReverseOrderByClause' => QQ::OrderBy(QQN::Person()->FirstName, false))));
			$this->dtgPersons->AddColumn(new QDataGridColumn('Last Name', '<?= $_FORM->LastNameColumn_Render($_ITEM) ?>', 'Width=200', 'HtmlEntities=false',
				array('OrderByClause' => QQ::OrderBy(QQN::Person()->LastName), 'ReverseOrderByClause' => QQ::OrderBy(QQN::Person()->LastName, false))));

			// Again, we setup the "Edit" column and ensure that the column's HtmlEntities is set to false
			$this->dtgPersons->AddColumn(new QDataGridColumn('Edit', '<?= $_FORM->EditColumn_Render($_ITEM) ?>', 'Width=120', 'HtmlEntities=false'));

			// Let's pre-default the sorting by id (column index #0) and use AJAX
			$this->dtgPersons->SortColumnIndex = 0;
			$this->dtgPersons->UseAjax = true;

			// Specify the DataBinder method for the DataGrid
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

			// Create the other textboxes and buttons -- make sure we specify
			// the datagrid as the parent.  If they hit the escape key, let's perform a Cancel.
			// Note that we need to terminate the action on the escape key event, too, b/c
			// many browsers will perform additional processing that we won't not want.
			$this->txtFirstName = new QTextBox($this->dtgPersons);
			$this->txtFirstName->Required = true;
			$this->txtFirstName->MaxLength = 50;
			$this->txtFirstName->Width = 200;
			$this->txtFirstName->AddAction(new QEscapeKeyEvent(), new QAjaxAction('btnCancel_Click'));
			$this->txtFirstName->AddAction(new QEscapeKeyEvent(), new QTerminateAction());

			$this->txtLastName = new QTextBox($this->dtgPersons);
			$this->txtLastName->Required = true;
			$this->txtLastName->MaxLength = 50;
			$this->txtLastName->Width = 200;
			$this->txtLastName->AddAction(new QEscapeKeyEvent(), new QAjaxAction('btnCancel_Click'));
			$this->txtLastName->AddAction(new QEscapeKeyEvent(), new QTerminateAction());

			// We want the Save button to be Primary, so that the save will perform if the 
			// user hits the enter key in either of the textboxes.
			$this->btnSave = new QButton($this->dtgPersons);
			$this->btnSave->Text = 'Save';
			$this->btnSave->AddAction(new QClickEvent(), new QAjaxAction('btnSave_Click'));
			$this->btnSave->PrimaryButton = true;
			$this->btnSave->CausesValidation = true;

			// Make sure we turn off validation on the Cancel button
			$this->btnCancel = new QButton($this->dtgPersons);
			$this->btnCancel->Text = 'Cancel';
			$this->btnCancel->AddAction(new QClickEvent(), new QAjaxAction('btnCancel_Click'));		
			$this->btnCancel->CausesValidation = false;

			// Finally, let's add a "New" button
			$this->btnNew = new QButton($this);
			$this->btnNew->Text = 'New';
			$this->btnNew->AddAction(new QClickEvent(), new QAjaxAction('btnNew_Click'));		
			$this->btnNew->CausesValidation = false;
		}

		protected function dtgPersons_Bind() {
			$objPersonArray = $this->dtgPersons->DataSource = Person::LoadAll(QQ::Clause(
				$this->dtgPersons->OrderByClause,
				$this->dtgPersons->LimitClause
			));

			// If we are editing someone new, we need to add a new (blank) person to the data source
			if ($this->intEditPersonId == -1)
				array_push($objPersonArray, new Person());

			// Bind the datasource to the datagrid
			$this->dtgPersons->DataSource = $objPersonArray;
		}

		// When we Render, we need to see if we are currently editing someone
		protected function Form_PreRender() {
			// We want to force the datagrid to refresh on EVERY button click
			// Normally, the datagrid won't re-render on the ajaxactions because nothing
			// in the datagrid, itself, is being modified.  But considering that every ajax action
			// on the page (e.g. every button click) makes changes to things that AFFECT the datagrid,
			// we need to explicitly force the datagrid to "refresh" on every event/action.  Therefore,
			// we make the call to Refresh() in Form_PreRender
			$this->dtgPersons->Refresh();

			// If we are adding or editing a person, then we should disable the edit button
			if ($this->intEditPersonId)
				$this->btnNew->Enabled = false;
			else
				$this->btnNew->Enabled = true;
		}

		// If the person for the row we are rendering is currently being edited,
		// show the textbox.  Otherwise, display the contents as is.
		public function FirstNameColumn_Render(Person $objPerson) {
			if (($objPerson->Id == $this->intEditPersonId) ||
				(($this->intEditPersonId == -1) && (!$objPerson->Id)))
				return $this->txtFirstName->RenderWithError(false);
			else
				// Because we are rendering with HtmlEntities set to false on this column
				// we need to make sure to escape the value
				return QApplication::HtmlEntities($objPerson->FirstName);
		}

		// If the person for the row we are rendering is currently being edited,
		// show the textbox.  Otherwise, display the contents as is.
		public function LastNameColumn_Render(Person $objPerson) {
			if (($objPerson->Id == $this->intEditPersonId) ||
				(($this->intEditPersonId == -1) && (!$objPerson->Id)))
				return $this->txtLastName->RenderWithError(false);
			else
				// Because we are rendering with HtmlEntities set to false on this column
				// we need to make sure to escape the value
				return QApplication::HtmlEntities($objPerson->LastName);
		}

		// If the person for the row we are rendering is currently being edited,
		// show the Save & Cancel buttons.  And the rest of the rows edit buttons
		// should be disabled.  Otherwise, show the edit button normally.
		public function EditColumn_Render(Person $objPerson) {
			if (($objPerson->Id == $this->intEditPersonId) ||
				(($this->intEditPersonId == -1) && (!$objPerson->Id)))
				// We are rendering the row of the person we are editing OR we are rending the row
				// of the NEW (blank) person.  Go ahead and render the Save and Cancel buttons.
				return $this->btnSave->Render(false) . '&nbsp;' . $this->btnCancel->Render(false);
			else {
				// Get the Edit button for this row (we will create it if it doesn't yet exist)
				$strControlId = 'btnEdit' . $objPerson->Id;
				$btnEdit = $this->GetControl($strControlId);
				if (!$btnEdit) {
					// Create the Edit button for this row in the DataGrid
					// Use ActionParameter to specify the ID of the person
					$btnEdit = new QButton($this->dtgPersons, $strControlId);
					$btnEdit->Text = 'Edit This Person';
					$btnEdit->ActionParameter = $objPerson->Id;
					$btnEdit->AddAction(new QClickEvent(), new QAjaxAction('btnEdit_Click'));
					$btnEdit->CausesValidation = false;
				}

				// If we are currently editing a person, then set this Edit button to be disabled
				if ($this->intEditPersonId)
					$btnEdit->Enabled = false;
				else
					$btnEdit->Enabled = true;

				// Return the rendered Edit button
				return $btnEdit->Render(false);
			}
		}

		// Handle the action for the Edit button being clicked.  We must
		// setup the FirstName and LastName textboxes to contain the name of the person
		// we are editing.
		protected function btnEdit_Click($strFormId, $strControlId, $strParameter) {
			$this->intEditPersonId = $strParameter;
			$objPerson = Person::Load($strParameter);
			$this->txtFirstName->Text = $objPerson->FirstName;
			$this->txtLastName->Text = $objPerson->LastName;

			// Let's put the focus on the FirstName Textbox
			QApplication::ExecuteJavaScript(sprintf('qcodo.getControl("%s").focus()', $this->txtFirstName->ControlId));
		}

		// Handle the action for the Save button being clicked.
		protected function btnSave_Click($strFormId, $strControlId, $strParameter) {
			if ($this->intEditPersonId == -1)
				$objPerson = new Person();
			else
				$objPerson = Person::Load($this->intEditPersonId);

			$objPerson->FirstName = trim($this->txtFirstName->Text);
			$objPerson->LastName = trim($this->txtLastName->Text);
			$objPerson->Save();

			$this->intEditPersonId = null;
		}

		// Handle the action for the Cancel button being clicked.
		protected function btnCancel_Click($strFormId, $strControlId, $strParameter) {
			$this->intEditPersonId = null;
		}

		// Handle the action for the New button being clicked.  Clear the
		// contents of the Firstname and LastName textboxes.
		protected function btnNew_Click($strFormId, $strControlId, $strParameter) {
			$this->intEditPersonId = -1;
			$this->txtFirstName->Text = '';
			$this->txtLastName->Text = '';

			// Let's put the focus on the FirstName Textbox
			QApplication::ExecuteJavaScript(sprintf('qcodo.getControl("%s").focus()', $this->txtFirstName->ControlId));
		}
	}

	ExampleForm::Run('ExampleForm');
?>