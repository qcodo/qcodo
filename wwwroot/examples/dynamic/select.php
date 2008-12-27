<?php
	require('../../includes/prepend.inc.php');
	
	class ExampleForm extends QForm {
		// Declare the DataGrid and Response Label
		protected $dtgPersons;
		protected $lblResponse;

		protected function Form_Create() {
			// Define the DataGrid
			$this->dtgPersons = new QDataGrid($this);
			$this->dtgPersons->CellPadding = 5;
			$this->dtgPersons->CellSpacing = 0;
			
			// Specify Pagination with 10 items per page
			$objPaginator = new QPaginator($this->dtgPersons);
			$this->dtgPersons->Paginator = $objPaginator;
			$this->dtgPersons->ItemsPerPage = 10;

			// Define Columns
			$this->dtgPersons->AddColumn(new QDataGridColumn('Person ID', '<?= $_ITEM->Id ?>', 'Width=100',
				array('OrderByClause' => QQ::OrderBy(QQN::Person()->Id), 'ReverseOrderByClause' => QQ::OrderBy(QQN::Person()->Id, false))));
			$this->dtgPersons->AddColumn(new QDataGridColumn('First Name', '<?= $_ITEM->FirstName ?>', 'Width=200',
				array('OrderByClause' => QQ::OrderBy(QQN::Person()->FirstName), 'ReverseOrderByClause' => QQ::OrderBy(QQN::Person()->FirstName, false))));
			$this->dtgPersons->AddColumn(new QDataGridColumn('Last Name', '<?= $_ITEM->LastName ?>', 'Width=200',
				array('OrderByClause' => QQ::OrderBy(QQN::Person()->LastName), 'ReverseOrderByClause' => QQ::OrderBy(QQN::Person()->LastName, false))));

			// For the last column we will be calling a PHP method on the form
			// to help with the dynamic creation of a Checkbox
			$this->dtgPersons->AddColumn(new QDataGridColumn('Select Person', '<?= $_FORM->chkSelected_Render($_ITEM) ?>',
				// And we make sure to set HtmlEntities to "false" so that our checkbox doesn't get HTML Escaped
				'HtmlEntities=false'
			));

			// Let's pre-default the sorting by last name (column index #2)
			$this->dtgPersons->SortColumnIndex = 2;

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
			
			
			// Define the Label -- keep it blank for now
			$this->lblResponse = new QLabel($this);
			$this->lblResponse->HtmlEntities = false;
		}

		protected function dtgPersons_Bind() {
			// Let the datagrid know how many total items and then get the data source
			$this->dtgPersons->TotalItemCount = Person::CountAll();
			$this->dtgPersons->DataSource = Person::LoadAll(QQ::Clause(
				$this->dtgPersons->OrderByClause,
				$this->dtgPersons->LimitClause
			));
		}

		// This method (declared as public) will help with the checkbox column rendering
		public function chkSelected_Render(Person $objPerson) {
			// In order to keep track whether or not a Person's Checkbox has been rendered,
			// we will use explicitly defined control ids.
			$strControlId = 'chkSelected' . $objPerson->Id;

			// Let's see if the Checkbox exists already
			$chkSelected = $this->GetControl($strControlId);
			
			if (!$chkSelected) {
				// Define the Checkbox -- it's parent is the Datagrid (b/c the datagrid is the one calling
				// this method which is responsible for rendering the checkbox.  Also, we must
				// explicitly specify the control ID
				$chkSelected = new QCheckBox($this->dtgPersons, $strControlId);
				$chkSelected->Text = 'Select';
				
				// We'll use Control Parameters to help us identify the Person ID being copied
				$chkSelected->ActionParameter = $objPerson->Id;
				
				// Let's assign a server action on click
				$chkSelected->AddAction(new QClickEvent(), new QServerAction('chkSelected_Click'));
			}

			// Render the checkbox.  We want to *return* the contents of the rendered Checkbox,
			// not display it.  (The datagrid is responsible for the rendering of this column).
			// Therefore, we must specify "false" for the optional blnDisplayOutput parameter.
			return $chkSelected->Render(false);
		}
		
		// This btnCopy_Click action will actually perform the copy of the person row being copied
		protected function chkSelected_Click($strFormId, $strControlId, $strParameter) {
			// We look to the Parameter for the ID of the person being checked
			$intPersonId = $strParameter;
			
			// Let's get the selected person
			$objPerson = Person::Load($intPersonId);
			
			// Let's respond to the user what just happened
			if ($this->GetControl($strControlId)->Checked)
				$strResponse = QApplication::HtmlEntities('You just selected ' . $objPerson->FirstName . ' ' . $objPerson->LastName . '.');
			else
				$strResponse = QApplication::HtmlEntities('You just deselected ' . $objPerson->FirstName . ' ' . $objPerson->LastName . '.');
			$strResponse .= '<br/>';

			// Now, let's go through all the checkboxes and list everyone who has been selected
			$strNameArray = array();
			foreach ($this->GetAllControls() as $objControl) {
				if (substr($objControl->ControlId, 0, 11) == 'chkSelected') {
					if ($objControl->Checked) {
						$objPerson = Person::Load($objControl->ActionParameter);
						$strName = QApplication::HtmlEntities($objPerson->FirstName . ' ' . $objPerson->LastName);
						array_push($strNameArray, $strName);
					}
				}
			}
			$strResponse .= 'The list of people who are currently selected: ' . implode(', ', $strNameArray);

			// Provide feedback to the user by updating the Response label
			$this->lblResponse->Text = $strResponse;
		}
	}

	ExampleForm::Run('ExampleForm');
?>