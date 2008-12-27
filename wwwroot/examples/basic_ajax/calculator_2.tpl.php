<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">AJAX Calculator</div>

		To show the ease of AJAX in a slightly more complex QForm, we take our <b>Calculator Example
		with Validation</b> from before... and we only change <i>one word</i>.<br/><br/>
		
		We change the <b>QServerAction</b> call to a <b>QAjaxAction</b> call, and now, we've
		created an AJAX-based calculator.  Note that even things like validation messages, etc.,
		will appear via AJAX and without a page refresh.
	</div>

	<div>
		Value 1: <?php $this->txtValue1->RenderWithError(); ?>
		<br/><br/>
		
		Value 2: <?php $this->txtValue2->RenderWithError(); ?>
		<br/><br/>
		
		Operation: <?php $this->lstOperation->Render(); ?>
		<br/><br/>
		
		<?php $this->btnCalculate->Render(); ?>
		<hr/>
		<?php $this->lblResult->Render(); ?>
	</div>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>