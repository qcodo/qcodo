<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Triggering Events after a Delay</div>
		Sometimes, you may want events to trigger their assigned actions after a delay.  A good example
		of this here is the <b>QKeyPressEvent</b> we added below.  As the user enters in data into the textbox,
		we make an AJAX call to update the label.  However, in order to make the system a bit more usable
		and streamlined, we have added a half-second (500 ms) delay on the <b>QKeyPressEvent</b>, so that 
		we are not making too many AJAX calls as the user is still entering in data.<br/><br/>
		
		Basically, this allows the action to be triggered only after the user is done typing in the data.
		<br/><br/>

		Note that we maybe could have used a QChangeEvent on the textbox to achieve a similar effect.  But
		realize that QChangeEvent (which utilizes a javascript <b>onchange</b> event handler) will only be
		triggered after the control <i>loses focus</i> and has been changed -- it won't be triggered purely
		by the fact that the text in the textbox has been changed.
	</div>

	<p><?php $this->txtItem->RenderWithName(); ?></p>

	<p><?php $this->lblSelected->RenderWithName(); ?></p>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>