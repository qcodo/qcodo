<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Qcodo Query Conditions</div>
		All <b>Qcodo Query</b> method calls require a <b>QQ Condition</b>. <b>QQ Conditions</b> allow you
		to create a nested/hierarchical set of conditions to describe what essentially becomes your
		WHERE clause in a SQL query statement.<br/><br/>

		The following is the list of QQ Condition classes and what parameters they take:
		<ul>
		<li>QQ::All()</li>
		<li>QQ::None()</li>
		<li>QQ::Equal(QQNode, Value)</li>
		<li>QQ::NotEqual(QQNode, Value)</li>
		<li>QQ::GreaterThan(QQNode, Value)</li>
		<li>QQ::LessThan(QQNode, Value)</li>
		<li>QQ::GreaterOrEqual(QQNode, Value)</li>
		<li>QQ::LessOrEqual(QQNode, Value)</li>
		<li>QQ::IsNull(QQNode)</li>
		<li>QQ::IsNotNull(QQNode)</li>
		<li>QQ::In(QQNode, array of string/int/datetime)</li>
		<li>QQ::Like(QQNode, string)</li>
		</ul>
		
		For almost all of the above <b>QQ Conditions</b>, you are comparing a column with some value.  The <b>QQ Node</b> parameter
		represents that column.  However, value can be either a static value (like an integer, a string, a datetime, etc.)
		<i>or</i> it can be another <b>QQ Node</b>.<br/><br/>
		
		And finally, there are three special <b>QQ Condition</b> classes which take in any number of additional <b>QQ Condition</b> classes:
		<ul>
		<li>QQ::AndCondition()</li>
		<li>QQ::OrCondition()</li>
		<li>QQ::Not() - "Not" can only take in one <b>QQ Condition</b> class</li>
		</ul>
		(conditions can be passed in as parameters and/or as arrays)<br/><br/>
		
		Because And/Or/Not conditions can take in <i>any</i> other condition, including other And/Or/Not conditions, you can
		embed these conditions into other conditions to create what ends up being a logic tree for your entire SQL Where clause.  See
		below for more information on this.
	</div>

	<h3>Select all People where: the first name is alphabetically "greater than" the last name</h3>
<?php
	$objPersonArray = Person::QueryArray(
		// Notice how we are comparing to QQ Column Nodes together
		QQ::GreaterThan(QQN::Person()->FirstName, QQN::Person()->LastName)
	);

	foreach ($objPersonArray as $objPerson){
		_p($objPerson->FirstName . ' ' . $objPerson->LastName);
		_p('<br/>', false);
	}
?>

	<h3>Select all Projects where: the manager's first name is alphabetically "greater than" the last name, or who's name contains "Website"</h3>
<?php
	$objProjectArray = Project::QueryArray(
		QQ::OrCondition(
			QQ::GreaterThan(QQN::Project()->ManagerPerson->FirstName, QQN::Project()->ManagerPerson->LastName),
			QQ::Like(QQN::Project()->Name, '%Website%')
		)
	);

	foreach ($objProjectArray as $objProject) {
		_p(sprintf('%s (managed by %s %s)', $objProject->Name, $objProject->ManagerPerson->FirstName, $objProject->ManagerPerson->LastName));
		_p('<br/>', false);
	}
?>


	<h3>Select all Projects where: the Project ID <= 2 AND (the manager's first name is alphabetically "greater than" the last name, or who's name contains "Website")</h3>
<?php
	$objProjectArray = Project::QueryArray(
		QQ::AndCondition(
			QQ::OrCondition(
				QQ::GreaterThan(QQN::Project()->ManagerPerson->FirstName, QQN::Project()->ManagerPerson->LastName),
				QQ::Like(QQN::Project()->Name, '%Website%')
			),
			QQ::LessOrEqual(QQN::Project()->Id, 2)
		)
	);

	foreach ($objProjectArray as $objProject) {
		_p(sprintf('%s (managed by %s %s)', $objProject->Name, $objProject->ManagerPerson->FirstName, $objProject->ManagerPerson->LastName));
		_p('<br/>', false);
	}
?>

<?php require('../includes/footer.inc.php'); ?>