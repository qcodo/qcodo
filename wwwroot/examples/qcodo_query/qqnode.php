<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Qcodo Query Nodes</div>
		<b>QQ Nodes</b> are any object table or association table (type tables are excluded), as well as any
		column within those tables.  <b>QQ Node</b> classes for your entire data model is generated for you
		during the code generation process.<br/><br/>

		But in addition to this, <b>QQ Nodes</b> are completely interlinked together, matching the relationships
		that you have defined as foreign keys (or virtual foreign keys using a relationships script) in your
		database.<br/><br/>
		
		To get at a specific <b>QQ Node</b>, you will need to call <b>QQN::ClassName()</b>, where "ClassName" is the name of the class
		for your table (e.g. "Person").  From there, you can use property getters to get at any column or relationship.
		<br/><br/>
		
		Naming standards for the columns are the same as the naming standards for the public getter/setter properties on the object, itself.
		So just as <b>$objPerson->FirstName</b> will get you the "First Name" property of a Person object,
		<b>QQN::Person()->FirstName</b> will refer to the "person.first_name" column in the database.<br/><br/>
		
		Naming standards for relationships are the same way.  The tokenization of the relationship reflected in a class's
		property and method names will also be reflected in the QQ Nodes.  So just as <b>$objProject->ManagerPerson</b> will
		get you a Person object which is the manager of a given project, <b>QQN::Project()->ManagerPerson</b> refers to the
		person table's row where person.id = project.manager_person_id.<br/><br/>
		
		And of course, because <i>everything</i> that is linked together in the database is also linked together in your <b>QQ Nodes</b>,
		<b>QQN::Project()->ManagerPerson->FirstName</b> would of course refer to the person.first_name of the person who is the
		project manager of that particular row in the project table.

	</div>

<?php require('../includes/footer.inc.php'); ?>