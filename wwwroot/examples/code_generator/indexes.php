<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Load Methods that Utilize Database Indexes</div>
		As you saw in the previous example, the Code Generator will always generate two load methods,
		<b>Load</b> and <b>LoadAll</b>, for every code generated class.  <b>Load</b> takes in the primary
		key (or primary keys if you have multiple PKs defined on the table) as the parameter, while
		<b>LoadAll</b> simply returns all the rows in the table.<br/><br/>
		
		Using database indexes, the code generator will also generate additional Load-type methods
		given the way you have defined those indexes.  In our <b>Examples Site Database</b>, there are quite a
		few indexes defined, but we will highlight two:
		<ul>
		<li>person.last_name</li>
		<li>login.username (UNIQUE)</li>
		</ul>
		
		Given these two indexes, the code generator has generated <b>LoadArrayByLastName</b> in the
		<b>Person</b> object, and it has defined <b>LoadByUsername</b> in the <b>Login</b> object.<br/><br/>
		
		Note that the <b>LastName</b> load method returns an array while the <b>Username</b> load method
		returns just a single object.  The code generator has recognized the UNIQUE property on the column,
		and it generated code accordingly.<br/><br/>

		You could also define indexes on multiple columns and the code generator will
		generate load methods based on those multi-column keys.
	</div>


	<h3>Using LoadByUsername to get a Single Login Object</h3>
<?php
	// Let's load a login object -- let's select the username 'jdoe'
	$objLogin = Login::LoadByUsername('jdoe');
?>
	Login ID: <?php _p($objLogin->Id); ?><br/>
	Login Username: <?php _p($objLogin->Username); ?><br/>
	Login Password: <?php _p($objLogin->Password); ?><br/>

	
	<h3>Using LoadArrayByLastName to get an Array of Person Objects</h3>
<?php
	// We'll load all the persons who has a last name of "Smith" into an array
	$objPersonArray = Person::LoadArrayByLastName('Smith');

	// Use foreach to iterate through that array and output the first and last
	// name of each person
	foreach ($objPersonArray as $objPerson)
		printf('&bull; ' . $objPerson->FirstName . ' ' . $objPerson->LastName . '<br/>');
?>


	<h3>Using CountByLastName to get a Count of All "Smiths" in the Database</h3>
	There are <?php _p(Person::CountByLastName('Smith')); ?> person(s) who have a last name of "Smith" in the system.
	
<?php require('../includes/footer.inc.php'); ?>