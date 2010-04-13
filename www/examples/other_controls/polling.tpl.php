<?php require(__INCLUDES__ . '/examples/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Making Periodic AJAX Calls via a Polling Processor</div>
		This example demonstrates how to add Polling (a.k.a. "Periodical Updates") to your QForms. This polling
		mechanism can be used to simulate a real-time stream from the server to client. Some common use cases
		include	progress bars and web-based chat applications.
		<br/><br/>

		We add polling to our QForm by calling <b>SetPollingProcessor</b> in <b>Form_Create</b>, passing in the
		name (and optionally the parent object) of the user-defined method to repeatedly call, and (also optionally)
		the	polling interval in milliseconds (default is 2.5 seconds). In this example, we are updating the clock
		below with the current time every second. Clicking the button stops the polling by calling
		<b>ClearPollingProcessor</b>.
		<br/><br/>

		Finally, note that because every polling recurrence involves an HTTP request to the server, care should
		be taken when setting the polling interval to avoid excessive server load.

	</div>
	<strong>The Current Time is:</strong><br/>
	<?php $this->lblMessage->Render(); ?>
	<p><?php $this->btnButton->Render(); ?><p/>

	<?php $this->RenderEnd(); ?>
<?php require(__INCLUDES__ . '/examples/footer.inc.php'); ?>