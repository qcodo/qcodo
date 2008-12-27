<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Understanding State</div>
		Note that when you clicked on the button, the form actually posted back to itself.  However,
		the state of the form was remembered from one webpage view to the next.  This is known as
		<b>FormState</b>.<br/><br/>
		
		<b>QForm</b> objects, in fact, are stateful objects that maintain its state from one post to the next.<br/>
		<br/>

		In this example, we have an <b>$intCounter</b> defined in the form.  And basically, whenever
		you click on the button, we will increment <b>$intCounter</b> by one.  Note that the HTML template
		file is displaying <b>$intCounter</b> directly via a standard PHP <b>print</b> statement.<br/><br/>
		
		Also note that session variables, cookies, etc. are <i>not</i> being used here -- only <b>FormState</b>.  In fact,
		you can get an idea if you do <b>View Source...</b> in your browser of the HTML on this page.
		You will see a bunch of cryptic letters and numbers for the <b>Qform__FormState</b> hidden variable.
		Those letters and numbers actually represent the serialized version of this <b>QForm</b> object.
	</div>

	<p>The current count is: <?php _p($this->intCounter); ?></p>

	<p><?php $this->btnButton->Render(); ?></p>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>