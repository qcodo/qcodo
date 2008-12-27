<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Qcodo and Foreign Key Relationships</div>
		In addition to your basic CRUD functionality, Qcodo will also analyze the Foreign Key relationships
		in your database to generate relationships between your objects.<br/><br/>
		
		Whenever you table has a column which is a Foreign Key to another table, the dependent class
		(the table with the FK) will have an instance of the independent class (the table where the FK
		links to).  So in our <b>Examples Site Database</b>, we have a <b>manager_person_id</b> column in our
		<b>project</b> table.  This results in a <b>ManagerPerson</b> property (of type <b>Person</b>) in our
		<b>Project</b> class.<br/><br/>
		
		Note that the <b>ManagerPerson</b> property is a read/write property.  It can be modified just like
		any other property, like <b>Name</b> and <b>Description</b>.
	</div>


	<h3>Load a Project Object and its ManagerPerson</h3>
<?php
	// Let's load a Project object -- let's select the Project with ID #3
	$objProject = Project::Load(3);
?>
	Project ID: <?php _p($objProject->Id); ?><br/>
	Project Name: <?php _p($objProject->Name); ?><br/><br/>

	Manager Person ID: <?php _p($objProject->ManagerPerson->Id); ?><br/>
	Manager's First Name: <?php _p($objProject->ManagerPerson->FirstName); ?><br/>
	Manager's Last Name: <?php _p($objProject->ManagerPerson->LastName); ?><br/><br/>


<?php require('../includes/footer.inc.php'); ?>