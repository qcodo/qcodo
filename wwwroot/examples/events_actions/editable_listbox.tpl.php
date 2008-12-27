<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Combining Multiple Actions on Events</div>
		We can combine mulitple actions together for events, and we can also use the same set of
		actions for on multiple events or controls.<br/><br/>
		
		In this example, we have a listbox, and we allow the user to dynamically add items to that
		listbox.  On submitting, we want to perform the following actions:
		<ul>
		<li>Disable the Listbox (via Javascript)</li>
		<li>Disable the Textbox (via Javascript)</li>
		<li>Disable the Button (via Javascript)</li>
		<li>Make an AJAX call to the PHP method <b>AddListItem</b></li>
		</ul>
		
		The PHP method <b>AddListItem</b> will then proceed to add the item into the listbox, and re-enable all
		the controls that were disabled.<br/><br/>
		
		Note that what we are doing is combining multiple actions together into an action array (e.g. <b>QAction[]</b>).
		Also note that this action array is defined on two different controls: the button (as a <b>QClickEvent</b>)
		and the textbox (as a <b>QEnterKeyEvent</b>).<br/><br/>
		
		Also note that we also add a <b>QTerminateAction</b> action to the textbox in response to
		the <b>QEnterKeyEvent</b>.  The reason for this is that on some browsers, hitting the enter
		key in a textbox would cause the form to do a traditional form.submit() call.  Given the way
		Qforms operates with named actions, and especially given the fact that this Qform is using AJAX-based
		actions, we do <i>not</i> want the browser to be haphazardly performing submits.<br/><br/>
		
		Finally, while this example uses <b>QAjaxAction</b> to make that an AJAX-based call to the PHP
		<b>AddListItem</b> method, note that this example can just as easily have made the call to
		<b>AddListItem</b> via a standard <b>QServerAction</b>.  The concept of combining multiple actions
		together and the concept of reusing an array of actions on different controls/events remain the same.
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