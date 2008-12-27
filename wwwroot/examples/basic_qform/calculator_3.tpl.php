<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Custom Renderers and Control Properties</div>
		
		In our final Calculator example, we show how you can use custom renderers to affect layout,
		as well as use control properties to change the appearance of your QControls.<br/><br/>
		
		The Qcodo distribution includes a sample custom renderer, <b>RenderWithName</b>, which is
		defined in your QControl custom class (which is at /includes/qform/QControl.inc).
		We'll use this <b>RenderWithName</b> for our calculator's textboxes and listbox.  We've also
		made sure to assign <b>Name</b> properties to these QControls.<br/><br/>
		
		Note how "Value 1" and "Value 2" are in all caps and boldfaced, while "Operation" is not.  This is
		because the textboxes are set to <b>Required</b> while the listbox is not.  And the sample
		<b>RenderWithName</b> method has code which will boldface/allcaps the names of any required controls.<br/><br/>
		
		We've also made some changes to the styling and such to the various controls.  Note that you can
		programmatically make these changes in our form definition (in <b>Form_Create</b>), and you can
		also make these changes as "Attribute Overrides" in the HTML template itself (see the "Other Tidbits"
		section for more information on <b>Attribute Overriding</b>).
		
		And finally, in our HTML template, we are now using the <b>RenderWithName</b> calls.  Because of that,
		we no longer need to hard code the "Value 1" and "Value 2" HTML in the template.
	</div>

	<div>
		<?php $this->txtValue1->RenderWithName(); ?>
		<br/><br/>

		<?php $this->txtValue2->RenderWithName(); ?>
		<br/><br/>

		<?php $this->lstOperation->RenderWithName(); ?>
		<br/><br/>

		<?php $this->btnCalculate->Render('Width=200px','Height=100px','FontNames=Courier'); ?>
		<hr/>
		<?php $this->lblResult->Render('FontSize=20px','FontItalic=true'); ?>
	</div>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>