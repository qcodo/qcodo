<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">The Four-Function Calculator: Our First Simple Application</div>
		We can combine this understanding of statefulness and events to make our first simple
		Qforms application.<br/><br/>

		This calculator is just a collection of two <b>QTextBox</b> objects (one for each operand), a
		<b>QListBox</b> object containing the four arithmetic functions, a <b>QButton</b> object to execute
		the operation, and a <b>QLabel</b> to view the result.

		Note that there is no validation, checking, etc. currently in the Qform.  Any string data
		will be parsed by PHP to see if there is any numeric data, and if not, it will be parsed as 0.  Dividing
		by zero will throw a PHP error.
	</div>

	<div>
		Value 1: <?php $this->txtValue1->Render(); ?>
		<br/><br/>

		Value 2: <?php $this->txtValue2->Render(); ?>
		<br/><br/>

		Operation: <?php $this->lstOperation->Render(); ?>
		<br/><br/>

		<?php $this->btnCalculate->Render(); ?>
		<hr/>
		<?php $this->lblResult->Render(); ?>
	</div>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>