<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Creating a QDataGrid with Inline Editing</div>
		Using the techniques for dynamically creating controls and utilizing the AJAX
		features in Qcodo, we update our <b>Person</b> datagrid to include functionality for
		inline editing.<br/><br/>

		We must first add a <b>$intEditPersonId</b> in the QForm to keep track of which
		<b>Person</b> (if any) we are currently editing.  We then must define the First
		and Last Name <b>QTextBoxes</b>, as well as Save and Cancel <b>QButtons</b>.
		Note that we only need to define one of each, because only one Person can be edited
		at a time.  The textboxes have <b>QEscapeKeyEvents</b> defined on them to
		perform a "Cancel", and the "Save" button is set to be a <b>PrimaryButton</b>.  This
		allows the textboxes to be sensitive to the <b>Enter</b> and <b>Escape</b> keys for
		saving and cancelling, respectively.<br/><br/>

		We also define render methods for each of the columns
		to properly display either the name or the <b>QTextBox</b>, depending on the row we are
		rendering and which <b>Person</b> we are editing.<br/><br/>
		
		And finally, we add a <b>btnNew</b> at the bottom to allow the user to create new
		<b>Person</b> objects.  If they want to create a new person, the <b>$intEditPersonId</b>
		is set to -1, and we get the datagrid to basically act as if it's editing a blank person.
	</div>

	<?php $this->dtgPersons->Render(); ?>
	<div style="text-align: center; width: 670px; margin-top: 16px;">
		<?php $this->btnNew->Render(); ?>
	</div>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>