<?php
	require('../../includes/prepend.inc.php');
	
	class DataRepeaterExample extends QForm {
		protected $dtrPersons;

		protected function Form_Create() {
			$this->dtrPersons = new QDataRepeater($this);
			
			// Let's set up pagination -- note that the form is the parent
			// of the paginator here, because it's on the form where we
			// make the call toe $this->dtrPersons->Paginator->Render()
			$this->dtrPersons->Paginator = new QPaginator($this);
			$this->dtrPersons->ItemsPerPage = 6;

			// Let's create a second paginator
			$this->dtrPersons->PaginatorAlternate = new QPaginator($this);

			// Enable AJAX-based rerendering for the QDataRepeater
			$this->dtrPersons->UseAjax = true;

			// DataRepeaters use Templates to define how the repeated
			// item is rendered
			$this->dtrPersons->Template = 'dtr_persons.tpl.php';
			
			// Finally, we define the method that we run to bind the data source to the datarepeater
			$this->dtrPersons->SetDataBinder('dtrPersons_Bind');
		}

		protected function dtrPersons_Bind() {
			// This function defines how we load the data source into the Data Repeater
			$this->dtrPersons->TotalItemCount = Person::CountAll();
			$this->dtrPersons->DataSource = Person::LoadAll(QQ::Clause($this->dtrPersons->LimitClause));
		}
	}

	DataRepeaterExample::Run('DataRepeaterExample');
?>