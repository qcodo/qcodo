<?php
	class ProjectEditPanel extends QPanel {
		// Child Controls must be Publically Accessible so that they can be rendered in the template
		// Typically, you would want to do this by having public __getters for each control
		// But for simplicity of this demo, we'll simply make the child controls public, themselves.
		public $txtName;
		public $btnSave;
		public $btnCancel;

		// The Local Project object which this panel represents
		protected $objProject;

		// The Reference to the Main Form's "Method Callback" so that the form can perform additional
		// tasks after save or cancel has been clicked
		protected $strMethodCallBack;

		// Specify the Template File for this custom QPanel
		protected $strTemplate = 'ProjectEditPanel.tpl.php';

		// Customize the Look/Feel
		protected $strPadding = '10px';
		protected $strBackColor = '#fefece';

		// We Create a new __constructor that takes in the Project we are "viewing"
		// The functionality of __construct in a custom QPanel is similar to the QForm's Form_Create() functionality
		public function __construct($objParentObject, $objProject, $strMethodCallBack, $strControlId = null) {
			// First, let's call the Parent's __constructor
			try {
				parent::__construct($objParentObject, $strControlId);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Next, we set the local project object
			$this->objProject = $objProject;
			
			// Let's record the reference to the form's MethodCallBack
			// See note in ProjectViewPanel for more on this.
			$this->strMethodCallBack = $strMethodCallBack;

			// Let's set up the other local child control
			// Notice that we define the child controls' parents to be "this", which is this ProjectEditPanel object.
			$this->txtName = new QTextBox($this, 'txtProjectName');
			$this->txtName->Text = $objProject->Name;
			$this->txtName->Name = 'Project Name';
			$this->txtName->Required = true;
			$this->txtName->CausesValidation = true;

			// We need to add some Enter and Esc key Events on the Textbox
			$this->txtName->AddAction(new QEnterKeyEvent(), new QAjaxControlAction($this, 'btnSave_Click'));
			$this->txtName->AddAction(new QEnterKeyEvent(), new QTerminateAction());
			$this->txtName->AddAction(new QEscapeKeyEvent(), new QAjaxControlAction($this, 'btnCancel_Click'));
			$this->txtName->AddAction(new QEscapeKeyEvent(), new QTerminateAction());

			$this->btnSave = new QButton($this);
			$this->btnSave->Text = 'Save';
			$this->btnSave->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnSave_Click'));
			$this->btnSave->CausesValidation = true;

			$this->btnCancel = new QButton($this);
			$this->btnCancel->Text = 'Cancel';
			$this->btnCancel->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnCancel_Click'));
		}

		// Because we don't need any Form_PreRender type of functionality, we do not override GetControlHtml()
//		public function GetControlHtml() {}

		// Event Handlers Here
		public function btnSave_Click($strFormId, $strControlId, $strParameter) {
			// Go ahead and update the project's name
			$this->objProject->Name = $this->txtName->Text;
			$this->objProject->Save();
			
			// And call the Form's Method CallBack, passing in "true" to state that we've made an update
			$strMethodCallBack = $this->strMethodCallBack;
			$this->objForm->$strMethodCallBack(true);
		}

		public function btnCancel_Click($strFormId, $strControlId, $strParameter) {
			// Call the Form's Method CallBack, passing in "false" to state that we've made no changes
			$strMethodCallBack = $this->strMethodCallBack;
			$this->objForm->$strMethodCallBack(false);
		}
	}