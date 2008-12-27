<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Introduction to Qcodo Query</div>
		The querying logic behind all the Load methods in your ORM classes is powered by <b>Qcodo Query</b>,
		or <b>QQ</b> for short.  Put simply, <b>QQ</b> is a completely object oriented API to perform any SELECT-based
		query on your database to return any result or hierarchy of your ORM objects.<br/><br/>

		While the ORM classes utilize basic, straightforward SELECT statements in its Load methods,
		<b>QQ</b> is capable of infinitely more complex queries.  In fact, any SELECT a developer
		would need to do against a database should be possible with <b>QQ</b>*.<br/><br/>

		<div style="font-size: 9px; line-height: 11px;">* Beta 3 Prerelease note: this is the
		eventual goal with Qcodo Query.  Currently, subselects and partial selects are still not yet available
		in QQ.  But please know that they are slated to be offically supported in Qcodo.)</div><br/>

		At its core, any <b>QQ</b> query will return a collection of objects of the same type (e.g. a collection of
		Person objects).  But the power of <b>QQ</b> is that we can branch beyond this core collection by bringing in
		any related objects, performing any SQL-based clause (including WHERE, ORDER BY, JOIN, aggregations, etc.) on both
		the core set of Person rows <i>and</i> any of these related objects rows.<br/><br/>

		Every code generated class in your ORM will have the three following static <b>Qcodo Query</b> methods:
		<ul>
		<li><b>QuerySingle</b>: to perform a Qcodo Query to return just a single object (typically for queries where you expect only one row)</li>
		<li><b>QueryArray</b>: to perform a Qcodo Query to return just an array of objects</li>
		<li><b>QueryCount</b>: to perform a Qcodo Query to return an integer of the count of rows (e.g. "COUNT (*)")</li>
		</ul>

		All three Qcodo Query methods expect two parameters, a <b>QQ Condition</b> and an optional set of <b>QQ Clauses</b>.
		<b>QQ Conditions</b> are typically conditions that you would expect to find in a SQL WHERE clause, including <b>Equal</b>,
		<b>GreaterThan</b>, <b>IsNotNull</b>, etc.  <b>QQ Clauses</b> are additional clauses that you could add to alter
		your SQL statement, including methods to perform SQL equivalents of JOIN, DISTINCT, GROUP BY, ORDER BY and LIMIT.
		<br/><br/>

		And finally, both <b>QQ Condition</b> and <b>QQ Clause</b> objects will expect <b>QQ Node</b> parameters.  <b>QQ Nodes</b> can
		either be tables, individual columns within the tables, or even association tables.  <b>QQ Node</b> classes for your
		entire ORM is code generated for you.
		<br/><br/>
		
		The next few examples will examine all three major constructs (<b>QQ Node</b>, <b>QQ Condition</b> and <b>QQ Clause</b>) in greater
		detail.<br/><br/>
		
		And as a final note, notice that <b>Qcodo Query</b> doesn't have any construct to describe what would normally be your SELECT clause.
		This is because we take advantage of the code generation process to allow <b>Qcodo Query</b> to automagically "know" which
		fields that should be SELECT-ed based on the query, conditions and clauses you are performing.  This will allow a lot
		greater flexbility in your data model.  Because the framework is now taking care of column names, etc., instead of the
		developer needing to manually hard code it, you can make changes to columns in your tables without needing to rewrite
		your <b>Qcodo Query</b> calls.
	</div>



	<h3>QuerySingle Example</h3>
<?php
	$objPerson = Person::QuerySingle(
		QQ::Equal(QQN::Person()->Id, 1)
	);

	// Notice that QuerySingle returned just a single Person object
	_p($objPerson->FirstName . ' ' . $objPerson->LastName);
	_p('<br/>', false);
?>



	<h3>QueryArray Example</h3>
<?php
	$objPersonArray = Person::QueryArray(
		QQ::In(QQN::Person()->Id, array(5, 6, 8))
	);

	// Notice that QueryArray returns an array of Person objects... this will
	// be true even if the result set only yields 1 row.=
	foreach ($objPersonArray as $objPerson) {
		_p($objPerson->FirstName . ' ' . $objPerson->LastName);
		_p('<br/>', false);
	}
?>



	<h3>QueryCount Example</h3>
<?php
	$intCount = Person::QueryCount(
		QQ::In(QQN::Person()->Id, array(5, 6, 8))
	);

	// Notice that QueryCount returns an integer
	_p($intCount . ' rows.');
?>



<?php require('../includes/footer.inc.php'); ?>