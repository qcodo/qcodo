<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Making a Control Moveable</div>
		Here we demonstrate the moveable controls capability of Qcodo, also known as 
		"Drag and Drop".<br/><br/>

		All <b>QControls</b> are capable of being moved.  However, only <b>QLabel</b> and <b>QPanel</b>
		controls are capable of being "move handles".  A "move handle" is anything that you can click
		which can begin execution of a move.  For example, in a standard GUI (e.g. Windows
		or the Mac OS), you cannot just click anywhere on a window to make the window move.  You
		can only click on a window's <b>Title Bar</b> to get that window to move.  So while
		the window, itself, is a moveable object, the window's <b>Title Bar</b> is the "move
		handle".  And in this case, the "move handle" is targetted to move itself as well as the
		window it is connected to.<br/><br/>

		The <b>QLabel</b> and <b>QPanel</b> controls have an <b>AddControlToMove</b> method defined
		on it, which takes in a <b>QControl</b> as a parameter.  Making this call will cause your
		<b>QLabel</b> or <b>QPanel</b> to become a "move handle".  And the <b>QControl</b> you pass
		in will be added as a control that will be moved by it.<br/><br/>
		
		In this example, we define a simple <b>QPanel</b>, and we add itself as a moveable control so
		that you can click on the panel and move it around.
	</div>

	<?php $this->pnlPanel->Render('BackColor=#eeccff', 'Width=130', 'Height=50', 'Padding=10', 'BorderWidth=1'); ?>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>