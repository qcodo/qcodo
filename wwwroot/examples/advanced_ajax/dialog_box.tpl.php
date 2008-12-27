<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Extending QPanels to Create Modal "Dialog Boxes"</div>
		In general UI programming, there are two kinds of dialog boxes: modal and non-modal.  Modal dialog boxes are
		the most common. When a program displays a modal dialog box, the user cannot switch between the dialog box
		and the program's main UI.  The user must explicitly close the dialog box, usually by clicking either "Ok" or "Cancel".
		<br/><br/>

		Obviously, with the current state of HTML and browser technologies, the <b>alert()</b> Javascript method
		is still the only <i>true</i> way to have any level of a modal dialog interaction.  And unfortuantely,
		<b>alert()</b> has very few features in terms of functionality.  Some web applications do attempt to use
		pop-up windows with <b>onblur()</b> events as a way to <i>mimick</i> dialog modality, but this is not considered
		a very clean or usable approach.
		<br/><br/>
		
		In <b>Qcodo</b>, a standard extension to the <b>QPanel</b> control is the <b>QDialogBox</b>, which gives you
		modal-like qualities in a control that can look and act like a stand-alone modal dialog box.
		<br/><br/>

		Because it extends the <b>QPanel</b> control, you have full use of all the <b>QPanel's</b> resources
		to build and design the content of the dialog box, itself, including using separate template files and
		adding child controls, events, actions and validation.
		<br/><br/>

		The two examples below show a simple "display only" dialog box, and a more complex dialog box that is meant to be a
		"calculator widget" with intra-control communication, where the contents of the calculator in the dialog box
		can be copied into a textbox on the main form.
	</div>
	
	<style type="text/css">
		.calculator_widget { width: 220px; height: 300px; background-color: #999999; padding: 10px; border-width: 1px; border-color: black; border-style: solid; }
		.calculator_display { text-align: right; padding: 4px; width: 208px; border-width: 1px; border-style: solid; border-color: black; background-color: white; font: 24px verdana, arial, helvetica; }
		.calculator_button { width: 50px; height: 45px; font: 20px verdana, arial, helvetica; font-weight: bold; border-width: 1px; background-color: #eeffdd; }
		.calculator_top_button { width: 78px; height: 45px; font: 10px verdana, arial, helvetica; color: white; border-width: 1px; background-color: #336644; }
	</style>

	<fieldset style="width: 400px;">
		<legend>Simple Message Example</legend>
		<?php $this->dlgSimpleMessage->Render(); ?>
		<p><?php $this->btnDisplaySimpleMessage->Render(); ?></p>
		<p><?php $this->btnDisplaySimpleMessageJsOnly->Render(); ?></p>
	</fieldset>
	<br/><br/>
	<fieldset style="width: 400px;">
		<legend>Calculator Widget Example</legend>
		<?php $this->dlgCalculatorWidget->Render(); ?>
		<p>Current Value: <?php $this->txtValue->Render(); ?></p>
		<p><?php $this->btnCalculator->Render(); ?></p>
	</fieldset>
	
	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>