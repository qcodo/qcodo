<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Learning about Validation</div>
		
		In this example, we extend our calculator application to include Validation.<br/><br/>
		
		As we mentioned earlier, Qforms will go through a validation process just before it executes
		any Server-based actions, if needed.  If the Control that triggers the ServerAction has its
		<b>CausesValidation</b> property set to "true", then before executing the ServerAction, the Form will
		go through every visible control in the entire Form and call <b>Validate()</b>.  Only after ensuring
		that every control is valid, will the Form go ahead and execute the assigned ServerAction.
		Otherwise, every Control that had its <b>Validate()</b> fail will have its ValidationError property
		set with the appropriate error message.<br/><br/>

		<i>What</i> the validation checks for is dependent on the control you are using.  In general,
		QControls that have their <b>Required</b> property set to "true" will check to ensure that data
		was at least entered or selected.  Some controls have additional rules.  For example, we'll use
		<b>QIntegerTextBox</b> here to have Qforms ensure that the data entered in our two textboxes are
		valid integers.<br/><br/>

		So we will utilize the Qforms validation in our application by doing the following:
		<ul>
			<li>Set our <b>btnCalculate</b> button's <b>CausesValidation</b> property to true</li>
			<li>Use <b>QIntegerTextBox</b> classes</li>
			<li>For those textboxes, we will use <b>RenderWithError()</b> instead of <b>Render()</b> in the HTML
			template code.  This is because <b>Render()</b> only renders the control, itself, with no
			other markers or placeholders.  <b>RenderWithError()</b> will be sure to render any error/warning
			messages for that control if needed.</li>
			<li>Lastly, we will add our first "business rule": ensure that the user does not divide by 0.
			This rule will be implemented as an <b>if</b> statement in the <b>Form_Validate</b> method.</li>
		</ul>

		For more advanced users, note that <b>CausesValidation</b> can also be set to <b>QCausesValidation::SiblingsAndChildren</b>
		or <b>QCausesValidation::SiblingsOnly</b>.  This functionality is geared for developers who are creating more
		complex <b>QForms</b> with child controls (either dynamically created, via custom composite controls, custom <b>QPanels</b>, etc.),
		and allows for more finely-tuned direction as to specify a specific subset of controls that should be validated, instead
		of validating against all controls on the form.<br/><br/>

		<b>SiblingsAndChildren</b> specifies to validate all sibling controls and their children of the control that is triggering
		the action, while <b>SiblingsOnly</b> specifies to validate the triggering control's siblings, only.
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