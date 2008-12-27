<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">QQ and Association Tables (Many-to-Many Relationships)</div>
		One key feature of <b>Qcodo Query</b> is its ability to treat relationships in Association tables just like
		any other foreign key relationship.  <b>QQ</b> has the ability to perform the full set of <b>QQ</b> functionality
		(including conditions, expansions, ordering, grouping, etc.) on tables related via association tables
		just as it would on tables related via a direct foreign key.<br/><br/>

		Naming standards for the many to many relationship are the same as the naming standards for the public methods
		for associating/unassociating in the class, itself.  So just as <b>$objPerson->Get<span style="text-decoration: underline;">ProjectAsTeamMember</span>Array</b> will
		retrieve an array of Project objects that are associated to this Person object as a "Team Member", 
		<b>QQN::Person()->ProjectAsTeamMember</b> will refer to the "team_member_project_assn" association table joined against
		the "person" table.<br/><br/>

		And again, because all the <b>QQ Nodes</b> are linked together, you can go from there to pull the project table, itself, as
		well as any columns from that project table.  In fact, the linkages can go indefinitely.
		<b>QQN::Person()->ProjectAsTeamMember->Project->ManagerPerson->FirstName</b> refers to the "first name of the manager
		of any project that this person is a team member of."<br/><br/>

		More importantly, when performing <b>Qcodo Queries</b> across association tables, we can <b>Expand</b> on the many-to-many
		relationship, which would use a special virtual attribute to help describe the individual object, itself, which was involved for the join.
		In this case, if we were to do a query of the person table, expanding on any ProjectAsTeamMember objects, the actual project that is joined is available
		to the Person object as $objPerson->_ProjectAsTeamMember.<br/><br/>

		And finally, on a similar note, you could instead use <b>ExpandAsArray</b> which would do a similar expansion
		on the associated object, but store it as an array.  See below for the differences of each.
	</div>

	<h3>Get All People Who Are on a Project Managed by Karen Wolfe (Person ID #7)</h3>
<?php
	$objPersonArray = Person::QueryArray(
		QQ::Equal(QQN::Person()->ProjectAsTeamMember->Project->ManagerPersonId, 7),
		// Because we are doing a join on a many-to-many relationship, we may end up with repeats (e.g. someone
		// who is a team member of more than one project that is managed by karen wolfe).  Therefore, we declare this as DISTINCT
		// to get rid of the redundant entries
		QQ::Clause(
			QQ::Distinct(),
			QQ::OrderBy(QQN::Person()->LastName, QQN::Person()->FirstName)
		)
	);
	
	foreach ($objPersonArray as $objPerson) {
		_p($objPerson->FirstName . ' ' . $objPerson->LastName);
		_p('<br/>', false);
	}
?>



	<br/>
	<h3>Get All People Who Are on a Project Managed by Karen Wolfe (Person ID #7)<br/>showing the Project which is involved in the JOIN via Expand()</h3>
	<i>Notice how some people may be listed twice, once for each project which he or she is part of that is managed by Karen Wolfe.</i><br/><br/>
<?php
	$objPersonArray = Person::QueryArray(
		QQ::Equal(QQN::Person()->ProjectAsTeamMember->Project->ManagerPersonId, 7),
		// Let's expand on the Project, itself
		QQ::Clause(
			QQ::Expand(QQN::Person()->ProjectAsTeamMember->Project),
			QQ::OrderBy(QQN::Person()->LastName, QQN::Person()->FirstName)
		)
	);
	
	foreach ($objPersonArray as $objPerson) {
		printf('%s %s (via the "%s" project)<br/>',
			QApplication::HtmlEntities($objPerson->FirstName),
			QApplication::HtmlEntities($objPerson->LastName),
			// Use the _ProjectAsTeamMember virtual attribute, which gives us the Project object
			QApplication::HtmlEntities($objPerson->_ProjectAsTeamMember->Name));
	}
?>



	<br/>
	<h3>Same as above, but this time, use ExpandAsArray()</h3>
	<i>Notice how each person is only listed once... but each person has an internal/virtual <b>_ProjectAsTeamMemberArray</b> which may list more than one project.</i><br/><br/>
<?php
	$objPersonArray = Person::QueryArray(
		QQ::Equal(QQN::Person()->ProjectAsTeamMember->Project->ManagerPersonId, 7),
		QQ::Clause(
			// Let's ExpandArray on the Association Table, itself
			QQ::ExpandAsArray(QQN::Person()->ProjectAsTeamMember),
			// ExpandArray dictates that the PRIMARY sort MUST be on the root object (in this case, QQN::Person())
			// Any secondary sort can follow
			QQ::OrderBy(QQN::Person()->LastName, QQN::Person()->FirstName, QQN::Person()->ProjectAsTeamMember->Project->Name)
		)
	);
	
	foreach ($objPersonArray as $objPerson) {
		_p($objPerson->FirstName . ' ' . $objPerson->LastName);
		_p('<br/>', false);

		// Now, instead of using the _ProjectAsTeamMember virtual attribute, we will use
		// the _ProjectAsTeamMemberArray virtual attribute, which gives us an array of Project objects
		$strProjectNameArray = array();
		foreach ($objPerson->_ProjectAsTeamMemberArray as $objProject)
			array_push($strProjectNameArray, QApplication::HtmlEntities($objProject->Name));

		printf('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;via: %s<br/>', implode(', ', $strProjectNameArray));
	}
?>

<?php require('../includes/footer.inc.php'); ?>