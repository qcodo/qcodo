<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Your Tables as PHP Objects</div>
		The Code Generator will more or less create a PHP object for each table in your database.
		For our three main tables (<b>login</b>, <b>person</b> and <b>project</b>), the Code Generator
		created the following PHP classes:
		<ul>
		<li>Login</li>
		<li>LoginGen</li>
		<li>Person</li>
		<li>PersonGen</li>
		<li>Project</li>
		<li>ProjectGen</li>
		</ul>

		<b>LoginGen</b>, <b>PersonGen</b> and <b>ProjectGen</b> are the generated classes
		which contain all the code to handle your database CRUD (create, restore, update
		and delete) functionality.  The <b>Login</b>, <b>Person</b> and <b>Project</b>
		classes inherit from the generated classes, and they are known as custom
		subclasses.<br/><br/>
		
		Note that on any subsequent code generation, while the generated classes will be overwritten,
		the custom subclasses will not be touched.  So you should feel free to make changes
		to these custom subclasses, override methods, introduce additional functionality, etc.
		as well as re-execute the code generator at any time.  Your changes and class customizations
		will remain intact.<br/><br/>

		For every object, the Code Generator will generate the getter and setter properties for each
		of the attributes in the table.  It will also generate the following basic CRUD methods:
		<ul>
		<li>Load</li>
		<li>LoadAll</li>
		<li>CountAll</li>
		<li>Save</li>
		<li>Delete</li>
		</ul>

		The example below shows how we can use the <b>Load</b> and <b>LoadAll</b> methods and the
		properties to view some the data.  Feel free to <b>View Source</b> to view the PHP code
		for <b>objects.php</b> which makes these calls.
	</div>


	<h3>Displaying the Properties of a Project</h3>
<?php
	// Let's load a project object -- let's select the
	// project with ID #2
	$objProject = Project::Load(2);
?>
	Project ID: <?php _p($objProject->Id); ?><br/>
	Project Name: <?php _p($objProject->Name); ?><br/>
	Project Decsription: <?php _p($objProject->Description); ?><br/>
	Project Start Date: <?php _p($objProject->StartDate); ?><br/>
	Project End Date: <?php _p($objProject->EndDate); ?><br/>
	Project Budget: <?php _p($objProject->Budget); ?><br/>

	
	<h3>Using LoadAll to get an Array of Person Objects</h3>
<?php
	// We'll load all the persons into an array
	$objPersonArray = Person::LoadAll();
	
	// Use foreach to iterate through that array and output the first and last
	// name of each person
	foreach ($objPersonArray as $objPerson)
		printf('&bull; ' . $objPerson->FirstName . ' ' . $objPerson->LastName . '<br/>');
?>


	<h3>Using CountAll to get a Count of All Persons in the Database</h3>
	There are <?php _p(Person::CountAll()); ?> person(s) in the system.
	
<?php require('../includes/footer.inc.php'); ?>