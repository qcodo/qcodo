<?php
	require('../../includes/prepend.inc.php');

	class ExampleForm extends QForm {
		protected $flaSample;

		protected $lblMessage;
		protected $btnButton;

		protected function Form_Create() {
			// Define the sample QFileAssset control -- make it required to show off validation
			$this->flaSample = new QFileAsset($this);
			$this->flaSample->Required = true;

			// Let's make the File Icon "clickable" -- allowing users to download / view the currently uploaded file
			// We need to do two things -- first, set a temporaryuploadpath that is within the docroot
			// and then we need to set ClickToView to true
			$this->flaSample->TemporaryUploadPath = __DOCROOT__ . __EXAMPLES__ . '/other_controls/temp_uploads';
			$this->flaSample->ClickToView = true;

			// NOTICE: If we are wanting users to immediately "click to view" files that are uploaded directly to the docroot,
			// we MUST take security precautions to prevent users from executing arbitrary code on the system.
			// Precautions could be: defining / overriding our own GetWebUrl() method in QFileAsset or
			// limiting the "types" of files that a user could upload.  We will go ahead and do this limiting here.
			$this->flaSample->FileAssetType = QFileAssetType::Image;

			// Feel free to uncomment this yourself, but note that you can pre-define the File property.
			// Notice how the path is an absolute path to a file.
			// Also notice that the file doesn't even need to be in the docroot.
//			$this->flaSample->File = __DOCROOT__ . __IMAGE_ASSETS__ . '/calendar.png';

			// Add Styling
			$this->flaSample->CssClass = 'file_asset';
			$this->flaSample->imgFileIcon->CssClass = 'file_asset_icon';

			$this->lblMessage = new QLabel($this);
			$this->lblMessage->Text = 'Click on the button to change this message.';

			// The "Form Submit" Button -- notice how the form is being submitted via AJAX, even though we are handling
			// File Uploads on the form.
			$this->btnButton = new QButton($this);
			$this->btnButton->Text = 'Click Me';
			$this->btnButton->AddAction(new QClickEvent(), new QAjaxAction('btnButton_Click'));
			$this->btnButton->CausesValidation = true;
		}

		protected function btnButton_Click($strFormId, $strControlId, $strParameter) {
			$this->lblMessage->Text = 'Thanks for uploading the file: ' . $this->flaSample->FileName;
		}
	}

	// And now run our defined form
	ExampleForm::Run('ExampleForm');
?>