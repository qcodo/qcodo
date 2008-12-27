<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Manipulating LoadAll and LoadArrayBy Results</div>
		(Note: for more information about "QQ::"-related classes (a.k.a. <b>Qcodo Query</b>), please refer to section 3 of the
		Examples Site.)<br/><br/>

		All Code Genereated <b>LoadAll</b> and <b>LoadArrayByXXX</b> methods take in an optional
		<b>Qcodo Query Clauses</b> parameter, where you can specify an unlimited number of <b>QQClause</b>
		objects, including (but not limited) functionality that handles <b>ORDER BY</b>, <b>LIMIT</b>
		and <b>Object Expansion</b>.  We will
		discuss <b>Object Expansion</b> in the examples that deal with <b>Late Binding</b>
		and <b>Early Binding</b>.  But for this example, we'll focus on using
		using <b>QQ::OrderBy()</b> and <b>QQ::LimitInfo()</b> to manipulate how the results come out of the database.<br/><br/>
		
		<b>OrderBy</b> and <b>LimitInfo</b> are actually really straightforward to use.  Order By takes
		in any number of Qcodo Query Node columns, followed by an optional boolean (to specify ascending/decending),
		which will be used in a SQL ORDER BY clause in the SELECT statement.  So you can simply say
		<b>QQ::OrderBy(QQN::Person()->LastName)</b> to sort all the Person objects by last name.<br/><br/>

		<b>LimitInfo</b> takes in a Maximum Row Count, followed by an optional offset.
		So if you specified "10, 4", the result set would contain at most 10 rows, starting with row #5
		(the offset is based on a 0 index).
		Depending on which database platform you are on, the database adapter will appropriately handle
		how to deal with this Limit information.<br/><br/>

		As a final reminder, note that you can use either, both, more or none of these optional <b>QQClause</b>
		parameters whenever you make your <b>LoadAll</b> or <b>LoadArrayBy</b> calls.
	</div>


	<h3>List All the People, Ordered by Last Name then First Name</h3>
<?php
	// Load the Person array, sorted
	$objPersonArray = Person::LoadAll(QQ::Clause(
		QQ::OrderBy(QQN::Person()->LastName, QQN::Person()->FirstName)
	));
	foreach ($objPersonArray as $objPerson) {
		_p($objPerson->LastName . ', ' . $objPerson->FirstName . ' (ID #' . $objPerson->Id . ')');
		_p('<br/>', false);
	}
?>


	<h3>List Five People, Start with the Third from the Top, Ordered by Last Name then First Name</h3>
<?php
	// Load the Person array, sorted and limited
	// Note that because we want to start with row #3, we need to define "2" as the offset
	$objPersonArray = Person::LoadAll(QQ::Clause(
		QQ::OrderBy(QQN::Person()->LastName, QQN::Person()->FirstName),
		QQ::LimitInfo(5, 2)
	));
	foreach ($objPersonArray as $objPerson) {
		_p($objPerson->LastName . ', ' . $objPerson->FirstName . ' (ID #' . $objPerson->Id . ')');
		_p('<br/>', false);
	}
?>


<?php require('../includes/footer.inc.php'); ?>