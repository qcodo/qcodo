<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">QQ and Reverse Relationships</div>
		The power of Qcodo's ORM is the ability not just to code generate the code to handle foreign key
		relationships, but also the ability to have that code handle the "reverse" foreign key relationships. 
		So in the Examples Site data model, we're talking about not just a <b>Project</b> and its <b>ManagerPerson</b>
		property... but we're also talking about a Person and methods like <b>GetProjectAsManagerArray()</b>.<br/><br/>
		
		<b>Qcodo Query</b> also has this built-in capability, which works very similar to the way <b>QQ</b> handles Associations.
		And this should make sense -- from <b>Person's</b> point of view, it has a "-to-Many" relationship with <b>Project</b> as a Manager
		(via the reverse relationship), and it has a "-to-Many" relationship with <b>Project</b> as a Team Member (via the
		association table).  Therefore <b>QQ</b> has the ability to perform the full set of <b>QQ</b> functionality
		(including conditions, expansions, ordering, grouping, etc.) on tables related via these reverse relationships
		just as it would on tables related via a direct foreign key or association table.<br/><br/>

		The naming standards for the relationship as well as the differences between <b>Expand</b> vs. <b>ExpandAsArray</b>
		are all the exact same as the case with association tables.
	</div>

	<h3>Get All People, Specifying the Project They Manage (if any), for Projects that have 'ACME' or 'WEB' in it</h3>
	<i>Notice how some people may be listed twice, if they manage more than one project.</i><br/><br/>
<?php
	$objPersonArray = Person::QueryArray(
		QQ::OrCondition(
			QQ::Like(QQN::Person()->ProjectAsManager->Name, '%ACME%'),
			QQ::Like(QQN::Person()->ProjectAsManager->Name, '%HR%')
		),
		// Let's expand on the Project, itself
		QQ::Clause(
			QQ::Expand(QQN::Person()->ProjectAsManager),
			QQ::OrderBy(QQN::Person()->LastName, QQN::Person()->FirstName)
		)
	);

	foreach ($objPersonArray as $objPerson) {
		printf('%s %s (managing the "%s" project)<br/>',
			QApplication::HtmlEntities($objPerson->FirstName),
			QApplication::HtmlEntities($objPerson->LastName),
			QApplication::HtmlEntities($objPerson->_ProjectAsManager->Name));
	}
?>



	<br/>
	<h3>Same as above, but this time, use ExpandAsArray()</h3>
	<i>Notice how each person is only listed once... but each person has an internal/virtual <b>_ProjectAsManagerArray</b> which may list more than one project.</i><br/><br/>
<?php
	$objPersonArray = Person::QueryArray(
		QQ::OrCondition(
			QQ::Like(QQN::Person()->ProjectAsManager->Name, '%ACME%'),
			QQ::Like(QQN::Person()->ProjectAsManager->Name, '%HR%')
		),
		// Let's expandasarray on the Project, itself
		QQ::Clause(
			QQ::ExpandAsArray(QQN::Person()->ProjectAsManager),
			QQ::OrderBy(QQN::Person()->LastName, QQN::Person()->FirstName)
		)
	);
	
	foreach ($objPersonArray as $objPerson) {
		_p($objPerson->FirstName . ' ' . $objPerson->LastName);
		_p('<br/>', false);

		// Now, instead of using the _ProjectAsManager virtual attribute, we will use
		// the _ProjectAsManagerArray virtual attribute, which gives us an array of Project objects
		$strProjectNameArray = array();
		foreach ($objPerson->_ProjectAsManagerArray as $objProject)
			array_push($strProjectNameArray, QApplication::HtmlEntities($objProject->Name));

		printf('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;via: %s<br/>', implode(', ', $strProjectNameArray));
	}
?>

<?php require('../includes/footer.inc.php'); ?>