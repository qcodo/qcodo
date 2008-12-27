<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Generated MetaControl Objects</div>
		As you build out more and more database-driven <b>QForms</b> and <b>QPanels</b>, you'll notice
		that you still may spend quite a bit of wasted time coding the same type of Control
		definition, setup and data binding procedures over and over again.  This becomes
		especially tedious when you are talking about modifying objects with a large
		number of fields.<br/><br/>
		
		Utilizing QControls and the code generator, Qcodo can generate <b>MetaControl</b> classes for each
		of your ORM classes.  <b>MetaControls</b> are essentially classes which contains functionality
		to simplify the <b>QControl</b> creation/definition, setup and data binding process for you.<br/><br/>
		
		Essentially, for each field in a class, you can have the <b>MetaControl</b> return for you a data bound
		and setup <b>QControl</b> for editing, or even a <b>QLabel</b> just for viewing.  But because these MetaControls
		are simply returning standard QControls, you can then modify them (stylizing, adding events, etc.) as you normally would
		any other control.

		You'll note in the PHP code that while it doesn't appear that we save that much in terms of Lines of Code,
		you will note that some of the more tedious, non application-specific code of literally making calls like
		<b>$this->txtFirstName = new QTextBox($this)</b> and setting up the <b>Text</b>, <b>Required</b> and <b>Name</b> properties
		of <b>$txtFirstName</b> is now done for you.<br/><br/>

		And because the <b>MetaControl</b> will be able to keep track <i>which</i> controls have been generated
		thus far, you can call
		(for example) <b>SavePerson()</b> on the <b>MetaControl</b>, and it will smartly go through any controls
		created thus far and bind the data back to the Person object.<br/><br/>

		We show this in our example below, where we have clickable labels and hidden textboxes to
		aid with the viewing and/or editing of Person #1.<br/><br/>

		Finally, note that because <b>MetaControls</b> encapsulate all the functionality for a given
		instance of a given object, and because it is able to keep track of and maintain its own
		set of controls, you can easily have multiple <b>MetaControls</b> on any <b>QForm</b> or <b>QPanel</b>,
		if you want to view or edit multiple objects of any class at the same time.
	</div>

	<p>Click on any label to edit:</p>
	<?php $this->lblFirstName->RenderWithName(); ?><?php $this->txtFirstName->RenderWithName(); ?>
	<?php $this->lblLastName->RenderWithName(); ?><?php $this->txtLastName->RenderWithName(); ?>

	<p>
		<?php $this->btnSave->Render(); ?>
		<?php $this->btnCancel->Render(); ?>
	</p>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>