<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Hello World, Revisited... Again...</div>
		By default, the <b>QForm</b> engine will insert <b>.tpl</b> to the PHP script's file path to use as the 
		template file path.  For example, for the very first example, the script with the form defintion
		was named <b>intro.php</b>.  Therefore, by default, Qcodo used <b>intro.tpl.php</b> as the HTML template
		include file (the "tpl" signifying that it's an HTML template).<br/><br/>
		
		For many reasons you may want to use a different filename or even
		specify a different filepath altogether.  In fact, the Qcodo Code Generator does this when it generates the
		form_draft template files into a separate directory than the form_drafts, themselves.<br/><br/>
		
		The <b>QForm::Run</b> method takes in an optional second parameter where you can specify the exact
		filepath of the template file you wish to use, overriding the default "script_name.tpl.php".
	</div>

	<?php // We will override the ForeColor, FontBold and the FontSize.  Note how we can optionally
		  // add quotes around our value. ?>
	<p><?php $this->lblMessage->Render(); ?></p>
	<p><?php $this->btnButton->Render(); ?></p>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>