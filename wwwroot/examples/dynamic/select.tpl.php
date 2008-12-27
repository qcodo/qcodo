<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Dynamically Creating QControls in a QDataGrid</div>
		In this example we will take our Paginated <b>QDataGrid</b>, and add a column which has a
		"Select" checkbox.  When clicking the checkbox, a server action will update the response label
		to say who has been selected or deselected.<br/><br/>

		To get this to work, we added a fourth column to put the checkbox.  In the HTML
		of that column, we make a call to a new <b>chkSelected_Render</b> method which
		we define.  This method checks to see if a checkbox for that <b>Person</b> has
		already been created (and if not, it will then create one).  The method
		then returns the rendered string for that checkbox.<br/><br/>
		
		Also, on the column object, itself, we need to make sure to set the <b>HtmlEntities</b>
		to <b>false</b>, so that the HTML of the checkbox doesn't get escaped.<br/><br/>

		And finally, we define a <b>QClickEvent</b> server action for the checkboxes which 
		will call the <b>chkSelected_Click</b> method to actually perform the action.  In order
		to let the <b>chkSelected_Click</b> method know <i>which</i> Person we just selected or
		deselected, we set the <b>ActionParameter</b> of each checkbox to the ID of the <b>Person</b>.<br/><br/>
	</div>

		<p><?php $this->lblResponse->Render(); ?></p>
		<?php $this->dtgPersons->Render(); ?>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>