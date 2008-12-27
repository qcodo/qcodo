<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Making Events Conditional</div>
		Somtimes we want events to trigger conditionally.  Given our editable listbox, a good example
		of this is that we want the submitting of the new Item to only happen if the user has
		typed in something in the textbox.<br/><br/>

		Basically, if the textbox is blank, no event should trigger.  (You can verify this now by
		clicking "Add Item" without while keeping the textbox completely blank.)<br/><br/>

		Qcodo supports this by allowing all events to have optional conditions.  These conditions
		are written as custom javascript code into the Event constructor itself.<br/><br/>

		In this example, we explicitly name the textbox's ControlId as "txtItem" so that we can
		write custom javascript as conditionals to the button's <b>QClickEvent</b> and the textbox's
		<b>QEnterKeyEvent</b>.		
	</div>

	<div>
		<?php $this->lstListbox->RenderWithName(); ?>
		<br/><br/>

		<?php $this->txtItem->RenderWithName(); ?>
		<br/><br/>

		<?php $this->btnAdd->Render(); ?>
		<br/><br/>

		<?php $this->lblSelected->RenderWithName(); ?>
	</div>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>