<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Hello World, Revisited</div>
		This example revisits our original Hello World example to show how you can easily
		change a postback-based form and interactions into AJAX-postback based ones.<br/><br/>
		
		Whereas before, we executed a <b>QServerAction</b> on the button's click, we have now changed
		that to a <b>QAjaxAction</b>.  Everything else remains the same.<br/><br/>
		
		The result is the exact same interaction, but now performed Asynchronously via AJAX.  Note
		that after clicking the button, the page doesn't "refresh" -- but the label's contents
		changes as defined in the PHP method <b>btnButton_Click</b>.
	</div>

	<p><?php $this->lblMessage->Render(); ?></p>
	<p><?php $this->btnButton->Render(); ?></p>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>