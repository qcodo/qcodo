<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Using Type Tables</div>
		<b>Type Tables</b> are two column tables (id and name) which are essentially built into
		an enumerated type by Qcodo.  So while only some database vendors (e.g. MySQL) offers support for
		a formal ENUM column type, Qcodo provides support for the enumerated column types for <i>all</i>
		database vendors through the use of <b>Type Tables</b>.<br/><br/>
		
		Similar to <b>Association Tables</b>, the code generator will look for a user-defined suffix
		(the default is "_type") to mark certain tables as <b>Type Tables</b>.  <b>Type Tables</b> can
		only have 2 columns, a primary key ID and a unique name.<br/><br/>
		
		A <b>Type</b> object will be generated from the table, but note that this <b>Type</b> object will
		<i>not</i> have the CRUD functionality generated for it.  Instead, constants will be defined,
		one for each row in the <b>Type Table</b>.<br/><br/>
		
		Because this is supposed to be an enumerated data type of some kind, the idea is that rows
		should <i>not</i> be added by the application, but instead, added by developers.  So whenever
		a new enumerated value needs to be added to this <b>Type</b> object, you should manually do the SQL INSERT
		into this <b>Type Table</b>, and then re-code generate.<br/><br/>
		
		In our example below, we show the contents of <b>ProjectStatusType</b>.  Note how the <b>Project</b>
		class has a relationship with <b>ProjectStatusType</b>, and how we can display a <b>Project</b>
		object's status using the static methods of <b>ProjectStatusType</b>.
	</div>


	<h3>List All the Project Status Types (Name and Token Value)</h3>
<?php
	// All Enumerated Types should go from 1 to "MaxId"
	for ($intIndex = 1; $intIndex <= ProjectStatusType::MaxId; $intIndex++) {
		// We use the Code Generated ToString and ToToken to output a constant's value
		_p(ProjectStatusType::ToString($intIndex) . ' - ' . ProjectStatusType::ToToken($intIndex));

		// We can even use the Enums as PHP constants
		if ($intIndex == ProjectStatusType::Cancelled)
			_p(' (This means that the project would be cancelled)');

		_p('<br/>', false);
	}
?>


	<h3>Load a Project Object and View Its Project Status</h3>
<?php
	// Let's load a Project object -- let's select the Project with ID #3
	$objProject = Project::Load(3);
?>
	Project ID: <?php _p($objProject->Id); ?><br/>
	Project Name: <?php _p($objProject->Name); ?><br/>
	Project Status: <?php _p(ProjectStatusType::ToString($objProject->ProjectStatusTypeId)); ?><br/>


<?php require('../includes/footer.inc.php'); ?>