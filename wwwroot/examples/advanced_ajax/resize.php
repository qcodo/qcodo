<?php
	require('../../includes/prepend.inc.php');

	// Define the Qform with all our Qcontrols
	class ExamplesForm extends QForm {
		// Local declarations of our QPanels to Resize
		protected $pnlLeftTop;
		protected $pnlLeftBottom;
		protected $pnlRight;
		
		// Local declarations of our two resize sliders (which
		// are also QPanels)
		protected $pnlVerticalResizer;
		protected $pnlHorizontalResizer;

		// Initialize our Controls during the Form Creation process
		protected function Form_Create() {
			// Define the main Panels which are to be resized
			// Note that positioning, location and sizes MUST BE defined here for the resize mechanism
			// to work properly.  Also note that resizable panels must be set to Absolute positioning.
			$this->pnlLeftTop = new QPanel($this);
			$this->pnlLeftTop->Position = QPosition::Absolute;
			$this->pnlLeftTop->Top = 0;
			$this->pnlLeftTop->Left = 0;
			$this->pnlLeftTop->Width = 195;
			$this->pnlLeftTop->Height = 95;
			$this->pnlLeftTop->Text = 'The Qcodo Development Framework is an open-source PHP 5 framework that focuses ' .
				'on freeing developers from unnecessary tedious, mundane coding.<br/><br/>The result is that developers ' .
				'can do what they do best: focus on implementing functionality and usability, improving performance and ' .
				'ensuring security.';
			$this->pnlLeftTop->Overflow = QOverflow::Auto;

			$this->pnlLeftBottom = new QPanel($this);
			$this->pnlLeftBottom->Position = QPosition::Absolute;
			$this->pnlLeftBottom->Top = 108;
			$this->pnlLeftBottom->Left = 0;
			$this->pnlLeftBottom->Width = 195;
			$this->pnlLeftBottom->Height = 187;
			$this->pnlLeftBottom->Text = 'It is a completely object-oriented framework that takes the best of PHP and ' .
				'provides a truly rapid application development platform. Initial prototypes roll out in minutes ' .
				'instead of hours. Iterations come around in hours instead of days (or even weeks). As projects ' .
				'iterate into more cohesive solutions, the framework allows developers to take prototypes to the next ' .
				'level by providing the capability of bringing the application maturity.';
			$this->pnlLeftBottom->Overflow = QOverflow::Auto;

			$this->pnlRight = new QPanel($this);
			$this->pnlRight->Position = QPosition::Absolute;
			$this->pnlRight->Top = 0;
			$this->pnlRight->Left = 208;
			$this->pnlRight->Width = 392;
			$this->pnlRight->Height = 292;
			$this->pnlRight->Text = 'Qcodo was designed for truly rapid application development which focuses on quick ' .
				'prototypes and rapid iterations. The intersection where design meets code always starts with the data ' .
				'model, and the expectation is that the data model will change, grow and adapt throughout the life of ' .
				'the application. These changes would inherently ripple out through to the rest of the code base. But ' .
				'instead of these changes being a burden to the development team, Code Generation can be implemented ' .
				'to make those changes occur with little to no interruption.<br/><br/>By analyzing the internal ' .
				'structure of your data model, Qcodo can generate not only the object code (the Object Relational ' .
				'Model), but also basic HTML pages to create, restore, update and delete those objects. This gives ' .
				'developers a great starting point to begin prototyping, even before a single line of code has been ' .
				'manually written. It allows the developer to focus more on writing business logic, implementing ' .
				'usability, etc., instead of spending time with more tedious and mundane (though required) database ' .
				'to object to HTML code.<br/><br/>These implementations and customizations are written in a code base ' .
				'separate than the code that is generated. So the key is that whenever changes are made to the data ' .
				'model, customizations in your PHP objects and HTML will be preserved, even when the code is regenerated.';
			$this->pnlRight->Overflow = QOverflow::Auto;

			// Define the Resizer Panels
			// Note that the Resizer Panels are just Regular Panels... but set up to resize other controls.
			// Also note that a resizer can only be set to be a "NorthSouth" or a "EastWest" resizer.
			// When set up to be a EastWest resizer, the "Upper" controls refer to the east controls,
			// and the "Lower" controls refer to the west controls.
			// Minimum and Maximum Resize values refer to the minimum values of the Resizer's
			// CSS Top: and Left: values
			$this->pnlVerticalResizer = new QPanel($this);
			$this->pnlVerticalResizer->Position = QPosition::Absolute;
			$this->pnlVerticalResizer->Top = 100;
			$this->pnlVerticalResizer->Left = 0;
			$this->pnlVerticalResizer->Width = 196;
			$this->pnlVerticalResizer->Height = 8;
			$this->pnlVerticalResizer->AddUpperControlToResize($this->pnlLeftTop);
			$this->pnlVerticalResizer->AddLowerControlToResize($this->pnlLeftBottom);
			$this->pnlVerticalResizer->ResizeHandleDirection = QResizeHandleDirection::Vertical;
			$this->pnlVerticalResizer->ResizeHandleMinimum = 30;
			$this->pnlVerticalResizer->ResizeHandleMaximum = 220;
			
			// This is needed for IE
			$this->pnlVerticalResizer->Text = '<img src="../images/spacer.png" width="1" height="1" alt=""/>';

			$this->pnlHorizontalResizer = new QPanel($this);
			$this->pnlHorizontalResizer->Position = QPosition::Absolute;
			$this->pnlHorizontalResizer->Top = 0;
			$this->pnlHorizontalResizer->Left = 200;
			$this->pnlHorizontalResizer->Width = 8;
			$this->pnlHorizontalResizer->Height = 292;
			$this->pnlHorizontalResizer->AddUpperControlToResize($this->pnlLeftTop);
			$this->pnlHorizontalResizer->AddUpperControlToResize($this->pnlLeftBottom);
			// Note that we also need to include the pnlVerticalResizer Panel as something that is resized
			$this->pnlHorizontalResizer->AddUpperControlToResize($this->pnlVerticalResizer);
			$this->pnlHorizontalResizer->AddLowerControlToResize($this->pnlRight);
			$this->pnlHorizontalResizer->ResizeHandleDirection = QResizeHandleDirection::Horizontal;
			$this->pnlHorizontalResizer->ResizeHandleMinimum = 80;
			$this->pnlHorizontalResizer->ResizeHandleMaximum = 480;
		}
	}

	// Run the Form we have defined
	ExamplesForm::Run('ExamplesForm');
?>