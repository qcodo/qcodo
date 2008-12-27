<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Non-Table Bound Attributes</div>
		Occasionally you may need to create custom database queries which retrieve other columns
		or values which are not in the table itself.  This may be a one-off column in another
		table, a calculated value, etc.<br/><br/>
		
		You can utlize the generated data object to easily retrieve these <b>virtual attributes</b>
		from the object, itself, as long as you code your custom database query correctly.  In short,
		if you prefix any additional or non-table bound columns with a double-underscore ("__"), the
		generated object will read in the column as a virtual attribute.  You can then use the generated
		<b>GetVirtualAttribute</b> method to retrieve the value of the data.<br/><br/>
		
		In our example below, we create a custom SQL query which uses SQL's <b>COUNT</b> function and
		subselects to calculate the number of team member for each project.<br/><br/>

		By utilizing <b>Virtual Attributes</b>, complex queries with calculated values,
		subselects, etc. can be retrieved in a single database query, and all the values can be
		stored in the data object, itself.
	</div>

<?php
	// Let's Define the Query
	$strQuery =
		'SELECT
			project.*,
			(
				SELECT
					COUNT(*)
				FROM
					team_member_project_assn
				WHERE
					project_id = project.id
			) AS __team_member_count
		FROM
			project';

	// Get the Database object from the Project table
	$objDatabase = Project::GetDatabase();
?>

	<h3>List All the Projects and Its Team Member Count</h3>
<?php

	// Query() the Database and Instantiate on the ResultSet into a Project[] array
	$objProjectArray = Project::InstantiateDbResult($objDatabase->Query($strQuery));

	// Iterate through the Project array
	foreach ($objProjectArray as $objProject) {
		_p(QApplication::HtmlEntities($objProject->Name) . ' has ' . $objProject->GetVirtualAttribute('team_member_count') . ' team members.');
		_p('<br/>', false);
	}
?>



<?php require('../includes/footer.inc.php'); ?>