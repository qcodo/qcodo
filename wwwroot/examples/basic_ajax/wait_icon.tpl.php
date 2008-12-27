<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Spinners!</div>
		
		In this Hello World example, we add a <b>QWaitIcon</b>, sometimes also known as "Spinners",
		which will be displayed during the entire AJAX call.<br/><br/>
		
		To add the <b>QWaitIcon</b>, you can define a <b>DefaultWaitIcon</b> in your form,
		passing in a <b>QWaitIcon</b> object.  At this point forward, every <b>QAjaxAction</b>
		will, by default, use the defined wait icon to be displayed during your AJAX call.
		<br/><br/>
		
		This display can be overridden by either passing in <b>null</b> for the wait icon to your
		ajax action call, or alternatively you can pass in <i>another</i> <b>QWaitIcon</b> object
		defined in your form.<br/><br/>
		
		Be sure to remember to render your wait icon on your page!  (Note: artificial sleep/wait time
		has been added to the <b>btnButton_Click</b> method in order to illustrate the spinner in
		action)
	</div>

	<p><?php $this->lblMessage->Render(); ?></p>
	<p><?php $this->btnButton->Render(); ?>
		<?php $this->btnButton2->Render(); ?></p>

	<p><?php $this->objDefaultWaitIcon->Render('Position=absolute','Top=450px','Left=200px'); ?></p>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>