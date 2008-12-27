<?php
	require('../../includes/prepend.inc.php');

	class ExampleForm extends QForm {
		protected $lstFont;
		protected $lblMessage;
		protected $txtMessage1;
		protected $txtMessage2;

		protected function Form_Create() {
			// First, we'll scan the Qcodo Fonts directory for all the available
			// PFB (Adobe Type 1) font files, and create a listbox of them.
			$this->lstFont = new QListBox($this);
			$objFontDirectory = opendir(__QCODO__ . '/fonts');
			while ($strFile = readdir($objFontDirectory))
				if ($intPosition = strpos($strFile, '.pfb'))
					$this->lstFont->AddItem(new QListItem(substr($strFile, 0, $intPosition), $strFile));
			$this->lstFont->SelectedIndex = 0;
			$this->lstFont->AddAction(new QChangeEvent(), new QAjaxAction('lstFont_Change'));




			// The QImageLabel Control is similar to the Label control, where you can specify
			// its text, font, and actions on it
			$this->lblMessage = new QImageLabel($this);
			$this->lblMessage->Text = 'Click me to toggle my message.';
			$this->lblMessage->FontSize = '28';

			// The FontNames we specify must be the file of a font binary.  This can either be
			// a TrueType font file (.ttf) or it can be a PostScript Type 1 typeface (.pfb).  PostScript
			// Type 1 typefaces must also have the accompanying .afm file (font metrics file).  Font Files
			// can either be placed in the current directory, or it can be placed in
			// /includes/qform/fonts
			$this->lblMessage->FontNames = $this->lstFont->SelectedValue;

			// FontSmoothing -- only for PostScript Type 1 files (this is ignored for TrueType)
			// Should be set to TRUE at smaller sizes, and FALSE at larger sizes
			$this->lblMessage->SmoothFont = true;

			// Specify the Colors -- note that QImageLabels are also capable of having
			// a BackgroundTransparent value set to true, too.  If BackgroundTransparent is set to true,
			// be sure to have the BackColor closely match the actual background that the image will be placed
			// on, so that the anti aliasing functionality will more closely match the image label's actual
			// background.
			$this->lblMessage->ForeColor = 'ffccaa';
			$this->lblMessage->BackColor = '331188';
			$this->lblMessage->BackgroundTransparent = false;

			// Specify Padding
			// Note that because we are not explicitly specifying the Width and Height, the image label's
			// actual width and height will be dynamically determined based on the length/size of the
			// text. 
			$this->lblMessage->PaddingWidth = 15;
			$this->lblMessage->PaddingHeight = 10;
			
			// If you wanted to explicitly set a width and height, you could do so here
//			$this->lblMessage->Width = 400;
//			$this->lblMessage->Height = 100;
			
			// Along with the Width and Height, you can then either specify Horizontal/Veritcal
			// alignment, *OR* you can specify an absolute X- and Y- coordinate.  Note: you *must*
			// specify a Horizontal or Vertical alignment of "NotSet" in order for QImageLabel
			// to recognize the X- or Y- coordinate preference.  Otherwise, the X-/Y- coordinate
			// will be dynamically calculated.
//			$this->lblMessage->HorizontalAlign = QHorizontalAlign::Center;
//			$this->lblMessage->VerticalAlign = QVerticalAlign::NotSet;
//			$this->lblMessage->YCoordinate = 30;

			// Just like any QControl, we can even specify events/actions.  Let's add a ClickEvent action.
			$this->lblMessage->AddAction(new QClickEvent(), new QAjaxAction('lblMessage_Click'));
			$this->lblMessage->Cursor = QCursor::Pointer;

			// And finally, let's specify a CacheFolder so that the images are cached
			// Notice that this CacheFolder path is a complete web-accessible relative-to-docroot path
			$this->lblMessage->CacheFolder = __VIRTUAL_DIRECTORY__ . __EXAMPLES__ . '/image_label/cache';




			// Add a couple of other textboxes for fun
			$this->txtMessage1 = new QTextBox($this);
			$this->txtMessage1->Text = 'Click me to toggle my message.';
			$this->txtMessage1->MaxLength = 50;

			$this->txtMessage2 = new QTextBox($this);
			$this->txtMessage2->Text = 'Hello, World!';
			$this->txtMessage2->MaxLength = 50;
		}

		protected function lblMessage_Click($strFormId, $strControlId, $strParameter) {
			// We will toggle between the two textbox messages so that we can show
			// off the dynamic rendering capability of the QImageLabel
			if ($this->lblMessage->Text == $this->txtMessage1->Text)
				$this->lblMessage->Text = $this->txtMessage2->Text;
			else
				$this->lblMessage->Text = $this->txtMessage1->Text;
		}
		
		protected function lstFont_Change($strFormId, $strControlId, $strParameter) {
			// Set the lblMessage's font to the new font file
			$this->lblMessage->FontNames = $this->lstFont->SelectedValue;
		}
	}

	ExampleForm::Run('ExampleForm');
?>