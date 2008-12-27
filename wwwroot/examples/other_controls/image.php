<?php
	require('../../includes/prepend.inc.php');

	class ExampleForm extends QForm {
		protected $imgSample;
		protected $txtWidth;
		protected $txtHeight;
		protected $chkScaleCanvasDown;
		protected $btnUpdate;

		protected function Form_Create() {
			// Get a Sample Image
			$this->imgSample = new QImageControl($this);
			$this->imgSample->ImagePath = 'earthlights.jpg';
			$this->imgSample->Width = 400;
			$this->imgSample->Height = 250;
			$this->imgSample->CssClass = 'image_canvas';
			
			$this->txtWidth = new QIntegerTextBox($this);
			$this->txtWidth->Minimum = 0;
			$this->txtWidth->Maximum = 1000;
			$this->txtWidth->Name = 'Width';
			$this->txtWidth->Text = 400;
			
			$this->txtHeight = new QIntegerTextBox($this);
			$this->txtHeight->Minimum = 0;
			$this->txtHeight->Maximum = 700;
			$this->txtHeight->Name = 'Height';
			$this->txtHeight->Text = 250;
			
			$this->chkScaleCanvasDown = new QCheckBox($this);
			$this->chkScaleCanvasDown->Checked = false;
			$this->chkScaleCanvasDown->Text = 'Scale Canvas Down';

			$this->btnUpdate = new QButton($this);
			$this->btnUpdate->Text = 'Update Image';
			$this->btnUpdate->AddAction(new QClickEvent(), new QAjaxAction('btnUpdate_Click'));
			$this->btnUpdate->CausesValidation = true;
		}

		// Let's ensure that a width or a height value is specified -- just so that we don't get people rendering really large versions of the image
		protected function Form_Validate() {
			if (!trim($this->txtWidth->Text) && !trim($this->txtHeight->Text)) {
				$this->txtWidth->Warning = 'For this example, you must specifiy at least a width OR a height value';
				return false;
			}
			return true;
		}

		protected function btnUpdate_Click($strFormId, $strControlId, $strParameter) {
			$this->imgSample->Width = $this->txtWidth->Text;
			$this->imgSample->Height = $this->txtHeight->Text;
			$this->imgSample->ScaleCanvasDown = $this->chkScaleCanvasDown->Checked;
		}
	}

	// And now run our defined form
	ExampleForm::Run('ExampleForm');
?>