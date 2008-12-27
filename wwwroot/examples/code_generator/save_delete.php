<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Saving and Deleting Objects</div>
		The C, U and D in CRUD is handled by the code generated <b>Save</b> and <b>Delete</b> methods in
		every object.<br/><br/>
		
		<b>Delete</b> should hopefully be self-explanatory.  <b>Save</b> will either call a SQL INSERT
		or a SQL UPDATE, depending on whether the object was created brand new or if it was restored via
		a Load method of some kind.<br/><br/>
		
		Note that you can also call <b>Save</b> passing in true for the optional <b>$blnForceInsert</b>
		parameter.  If you pass in true, then it will force the <b>Save</b> method to call SQL INSERT.
		Note that dependning on how your table is set up (e.g. if you have certain columns marked as
		UNIQUE), forcing the INSERT <i>may</i> throw an exception.
	</div>


	<h3>Load a Person Object, Modify It, and Save</h3>
<?php
	// Let's load a Person object -- let's select the Person with ID #3
	$objPerson = Person::Load(3);
?>
	<b><i>Before the Save</i></b><br/>
	Person ID: <?php _p($objPerson->Id); ?><br/>
	First Name: <?php _p($objPerson->FirstName); ?><br/>
	Last Name: <?php _p($objPerson->LastName); ?><br/><br/>

<?php
	// Update the field and save
	$objPerson->FirstName = 'FooBar';
	$objPerson->Save();
	
	// Restore the same person object just to make sure we
	// have a clean object from the database
	$objPerson = Person::Load(3);
?>

	<b><i>After the Save</i></b><br/>
	Person ID: <?php _p($objPerson->Id); ?><br/>
	First Name: <?php _p($objPerson->FirstName); ?><br/>
	Last Name: <?php _p($objPerson->LastName); ?><br/><br/>

<?php
	// Let's clean up -- once again update the field and save
	$objPerson->FirstName = 'Ben';
	$objPerson->Save();
?>

	<b><i>Cleaning Up</i></b><br/>
	Person ID: <?php _p($objPerson->Id); ?><br/>
	First Name: <?php _p($objPerson->FirstName); ?><br/>
	Last Name: <?php _p($objPerson->LastName); ?><br/><br/>

<?php require('../includes/footer.inc.php'); ?>