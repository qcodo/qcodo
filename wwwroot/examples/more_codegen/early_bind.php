<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Early Binding: Using Object Expansion</div>
		(Note: for more information about "QQ::"-related classes (a.k.a. <b>Qcodo Query</b>), please refer to section 3 of the
		Examples Site.)<br/><br/>

		When you need to perform LoadAll or LoadArray calls, and would like to include related objects
		in order to limit round tripping, you can use Qcodo's <b>Object Expansion</b> functionality to 
		specify which Foreign Key columns that you want to expand immediately.<br/><br/>

		The <b>Object Expansion</b> function, which is generated into each object in the ORM,
		will bind these related objects when the objects are initially created, thus the term
		"early binding".<br/><br/>

		In our example here, we will perform the <i>exact same task</i> as the previous example, pulling
		all the <b>Project</b> objects and displaying each object's <b>ManagerPerson</b>.  Note
		that the <i>only difference</i> in our code is that we've added a <b>QQ::Expand()</b> clause.
		There is <i>no other difference</i> with the way we access the restored objects and their related
		objects.<br/><br/>

		The end result is that instead of displaying the data using 5 queries, we have now cut this down
		to just 1 query.  This is accomplished because of the LEFT JOIN which is executed
		by the code generated ORM and the passed in <b>QQ::Expand()</b> clause.<br/><br/>

		But more importantly, because the way we access the objects is the exact same, this
		kind of round trip optimization can be done <i>after</i> the page is functional and complete.  This
		follows the general philosophy of Qcodo, which is to first focus on making your application
		functional, then focus on making your application more optimized.  The value of doing this is
		because often engineers can get bogged down on making an application as optimized as possible,
		and in doing so they can unnecessarily overengineer some pieces of functionality.
		If the focus is on getting the application functional, first, then after the application is in
		a usable state, you can profile the functionality that tends to get used more often and simply
		focus on optimizing this smaller subset of heavily-used functionality.
		<br/><br/>
		
		For information about Expanding through Association Tables, please refer to the "Handling Association Tables"
		example in Section 3.
	</div>


	<h3>List All the Projects and View Its Manager</h3>
<?php
	// Enable Profiling (we're assuming the Examples Site Database is at index 1)
	// NOTE: Profiling should only be enabled when you are actively wanting to profile a specific PHP script.
	// Because of SIGNIFICANT performance degradation, it should otherwise always be off.
	QApplication::$Database[1]->EnableProfiling();

	// Load the Project array
	// The following line of code is the ONLY line of code we will modify
	$objProjectArray = Project::LoadAll(  QQ::Clause(QQ::Expand(QQN::Project()->ManagerPerson))  );
	foreach ($objProjectArray as $objProject) {
		_p(QApplication::HtmlEntities($objProject->Name) . ' is managed by ' . $objProject->ManagerPerson->FirstName . ' ' . 
			$objProject->ManagerPerson->LastName);
		_p('<br/>', false);
	}
	_p('<br/>', false);

	// Output Profiling Data
	QApplication::$Database[1]->OutputProfiling();
?>



<?php require('../includes/footer.inc.php'); ?>