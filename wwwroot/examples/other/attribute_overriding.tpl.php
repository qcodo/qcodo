<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Overriding QControl Attributes</div>
		All <b>QControl</b> classes have any number attributes which can be defined.  These are
		either general attributes attributable to all controls (e.g. control name, font, color, width, etc.),
		or they can also be specific attributes for specific controls (e.g. maxlength for textboxes,
		rows for listboxes, etc.)<br/><br/>
		
		There are two ways these attributes can be specified.  For most of these examples, you have
		seen these specified as properties on the object themselves, e.g. "$this->lblMessage->FontSize = 12".
		But note that they can also be assigned through <b>Attribute Overriding</b> in the HTML
		template, itself.<br/><br/>
		
		Whenever you make a render call, you can specify any settable property name to override the
		currently set value.  So in this example, we've programmatically set the <b>lblMessage</b> to have a
		blue font.  But note that we've overridden the font color in the HTML template to be green,
		and of course, it shows up as green.<br/><br/>

		Keep in mind that the settable property name must match the exact name of the property
		you are trying to set (it is case sensitive).  The value you are trying to set it to can be
		optionally encapsulated within quotes.<br/><br/>
		
		In theory, if you have a designer working on the design and a developer working on the display
		logic, the designer should be able to make changes to the design in the HTML template file
		without needing the intervention of the developer working on the <b>QForm</b> object definition.
	</div>

	<?php // We will override the ForeColor, FontBold and the FontSize.  Note how we can optionally
		  // add quotes around our value. ?>
	<p><?php $this->lblMessage->Render('ForeColor="#00ff00"', 'FontSize=18px', 'FontBold=true'); ?></p>
	<p><?php $this->btnButton->Render(); ?></p>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>