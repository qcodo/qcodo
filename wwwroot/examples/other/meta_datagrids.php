<?php
	require('../../includes/prepend.inc.php');

	// Define the Qform with all our Qcontrols
	class ExamplesForm extends QForm {
		// Local declarations of the DataGrid
		protected $dtgProjects;
		protected $pxyExample;

		protected function Form_Create() {
			// Define the DataGrid -- note that the Meta DataGrid is a DataGrid, itself --
			// so let's just use it as a datagrid
			$this->dtgProjects = new ProjectDataGrid($this);

			// DataBinding is already configured -- so we do not need to worry about it

			// But remember that dtgProjects is just a regular datagrid, as well
			// So we can configure as we see fit, e.g. adding pagination or styling
			$this->dtgProjects->Paginator = new QPaginator($this->dtgProjects);
			$this->dtgProjects->ItemsPerPage = 6;
			$this->dtgProjects->AlternateRowStyle->CssClass = 'alternate';

			// All we need to do is to utilize the ProjectDataGrid built-in functionality
			// to create, define and setup the various columns that WE choose, in the order
			// that WE want.  NOTE that we use simple string-based property names, OR QQuery
			// node descriptors to specify what we want for each column.
			$this->dtgProjects->MetaAddColumn('Name');
			$this->dtgProjects->MetaAddColumn('StartDate');
			$this->dtgProjects->MetaAddColumn(QQN::Project()->EndDate);

			// We can easily add columns from linked/related tables.  However, to do this
			// you MUST use a QQuery node descriptor.  No string-based properties allowed.
			// Bonus: the Meta DataGrid will even automatically add sorting for columns in related tables.
			$colUsername = $this->dtgProjects->MetaAddColumn(QQN::Project()->ManagerPerson->Login->Username);

			// And remember, since it's a regular datagrid with regular columns,
			// we can stylize as we see fit
			$colUsername->BackColor = '#cef';
			$colUsername->Name = 'Manager\'s Username';

			// Also, note that MetaAddColumn and MetaAddTypeColumn can use attribute overriding as well
			$this->dtgProjects->MetaAddTypeColumn('ProjectStatusTypeId', 'ProjectStatusType', 'FontBold=true');

			$this->pxyExample = new QControlProxy($this);
			$this->pxyExample->AddAction(new QClickEvent(), new QAjaxAction('pxyExample_Click'));

			// FInally, there are even Meta methods to add an Edit Button column
			$this->dtgProjects->MetaAddEditProxyColumn($this->pxyExample, 'Click Me', 'Faux Edit Column');
		}

		// Instead of actually redirecting you to an example edit project page, we'll
		// use a DisplayAlert() call as a stub function.  Hopefully, you get the idea. =)
		protected function pxyExample_Click($strFormId, $strControlId, $strParameter) {
			QApplication::DisplayAlert('Pretending to edit Project #' . $strParameter);
		}
	}

	// Run the Form we have defined
	ExamplesForm::Run('ExamplesForm');
?>