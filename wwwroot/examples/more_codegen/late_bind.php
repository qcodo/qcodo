<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Late Binding: The Default Load Method</div>
		By default, any object with related objects (e.g. in our <b>Examples Site Database</b>, an example
		of this is how the <b>Project</b> object has a related <b>ManagerPerson</b> object) will perform
		"late binding" on that related object.<br/><br/>

		So given our example, when you load a given <b>Project</b> object, the <b>$objManagerPerson</b>
		member variable is initially NULL.  But when you ask for the <b>ManagerPerson</b> property,
		the object first checks to see of <b>$objManagerPerson</b> is null, and if it is, it will
		call the appropriate <b>Load</b> method to then query the database to pull that <b>Person</b>
		object into memory, and then bind it to this <b>Project</b> object.  Note that any <i>subsequent</i>
		calls to the <b>ManagerPerson</b> property will simply return the already bound <b>Person</b>
		object (no additional query to the database is needed).  This <b>Person</b> is
		essentially bound, as late as possible, to the <b>Project</b>, thus the term "late binding".<br/><br/>

		The advantages of "late binding" is that the data going between the database and the application
		is as minimal as possible.  You only get the minimal amount data that you need, when you need it,
		and nothing else.  And fortunately, because the Qcodo generated code does the binding for you
		behind the scenes, there is nothing that you would need to manually code to check, enforce or
		execute this binding functionality.<br/><br/>

		The disadvantage, however, is that for some functionalities where you are performing <b>LoadAll</b>
		or <b>LoadArrayBy</b>, and you need to use related objects within those arrays, you end up with
		"N+1 round tripping".  This means that if you had 100 objects, you are essentially doing 101 round trips
		to the database: 1 queries to get the list of 100 objects, and 100 additional queries (one for
		each object to get its related object).<br/><br/>

		In this example, we <b>LoadAll</b> all the <b>Project</b> objects, and view each object's
		<b>ManagerPerson</b>.  Using the built in Qcodo Database Profiler, you can see that
		five database calls are made: One call to get all the projects (four rows in all), and then four calls
		to <b>Person::Load</b> (one for each of those projects).
	</div>


	<h3>List All the Projects and View Its Manager</h3>
<?php
	// Enable Profiling (we're assuming the Examples Site Database is at index 1)
	// NOTE: Profiling should only be enabled when you are actively wanting to profile a specific PHP script.
	// Because of SIGNIFICANT performance degradation, it should otherwise always be off.
	QApplication::$Database[1]->EnableProfiling();

	// Load the Project array
	// Note how even though we make two calls to ManagerPerson PER project, only ONE call to
	// Person::Load is made per project -- this is because ManagerPerson is bound to the
	// Project during the first call.  So the second call is using the ManagerPerson that's
	// already bound to that project object.
	$objProjectArray = Project::LoadAll();
	foreach ($objProjectArray as $objProject) {
		_p($objProject->Name . ' is managed by ' . $objProject->ManagerPerson->FirstName . ' ' . 
			$objProject->ManagerPerson->LastName);
		_p('<br/>', false);
	}

	_p('<br/>', false);

	// Output Profiling Data
	QApplication::$Database[1]->OutputProfiling();
?>



<?php require('../includes/footer.inc.php'); ?>