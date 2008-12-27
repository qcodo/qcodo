<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Specifying Which Controls to Move</div>
		Hopefully this example shows why not all <b>QControl</b> objects can be move handles.<br/><br/>
		
		Below, we have rendered a <b>QLabel</b> and a <b>QTextBox</b>.  We want the textbox to be moveable,
		but if we make the textbox a "move handle" to move itself, the user will no longer be able to click
		"into" the textbox to enter in data.  Therefore, we specify the label to be the "move handle",
		and we add the label (itself) and the textbox as targets to be moved by the label.<br/><br/>
		
		This is done by making two calls to the label's <b>AddControlToMove</b> method.  The first call
		is made to add the label (itself), and the second call is made to add the textbox.<br/><br/>
		
		Note how you will move both controls when you drag the label around, and also note how you can
		still click "into" the textbox to enter in data.
	</div>

	<p><?php $this->lblHandle->Render('BackColor=#ddffdd', 'Width=250', 'Padding=4', 'DisplayStyle=block'); ?></p>
	<p><?php $this->txtTextbox->Render('Width=250'); ?></p>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>