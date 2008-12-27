<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">The QDataGrid Variables -- $_ITEM, $_COLUMN, $_CONTROL and $_FORM</div>
		As you may have noticed in the first example, we make use of the $_ITEM variable when we render
		each row's column.  There are in fact three special variables used by the QDataGrid:
		<b>$_ITEM</b>, <b>$_COLUMN</b>, <b>$_CONTROL</b> and <b>$_FORM</b>.<br/><br/>
		
		<b>$_ITEM</b> represents a specific row's instance of the array of items you are iterating through.
		So in our example, the <b>DataSource</b> is an array of <b>Person</b> objects.  Therefore, <b>$_ITEM</b>
		is the  specific <b>Person</b> object for the row that we are rendering.

		<b>$_COLUMN</b> is the QDataGridColumn, <b>$_CONTROL</b> is the QDataGrid itself and <b>$_FORM</b> is the QForm itself.<br/><br/>

		So in our example, the first column shows the "Row Number", which is basically just the
		<b>CurrentRowIndex</b> property of the <b>QDataGrid</b> (e.g. <b>$_CONTROL</b>).  And the last column's
		"Full Name" is rendered by the <b>DisplayFullName</b> method we have defined in our <b>ExampleForm</b>
		(e.g. <b>$_FORM</b>).  Note that the <b>DisplayFullName</b> takes in a <b>Person</b> object.
		Subsequently, in our HTML defintion, we make the call to <b>$_FORM->DisplayFullName</b> passing in
		<b>$_ITEM</b>.<br/><br/>
		
		Finally, note that <b>DisplayFullName</b> is declared as a <b>Public</b> method.  This is because
		<b>DisplayFullName</b> is actually called by the <b>QDataGrid</b>, which only has the rights to call
		<b>Public</b> methods in your <b>ExampleForm</b> class.
	</div>

		<?php $this->dtgPersons->Render(); ?>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>