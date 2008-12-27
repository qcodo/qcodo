<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Qcodo Query Clauses</div>
		All <b>Qcodo Query</b> method calls take in an optional set of <b>QQ Clauses</b>.  <b>QQ Clauses</b> allow you
		alter the result set by performing the equivalents of most of your major SQL clauses, including JOIN, ORDER BY,
		GROUP BY and DISTINCT.<br/><br/>

		The following is the list of QQ Clause classes and what parameters they take:
		<ul>
		<li>QQ::OrderBy(array/list of QQNodes)</li>
		<li>QQ::GroupBy(array/list of QQNodes)</li>
		<li>QQ::Count(QQNode, string)</li>
		<li>QQ::Minimum(QQNode, string)</li>
		<li>QQ::Maximum(QQNode, string)</li>
		<li>QQ::Average(QQNode, string)</li>
		<li>QQ::Expand(QQNode)</li>
		<li>QQ::ExpandAsArray(QQNode for an Association Table)</li>
		<li>QQ::LimitInfo(integer[, integer = 0])</li>
		<li>QQ::Distinct()</li>
		</ul>

		<b>OrderBy</b> and <b>GroupBy</b> follow the conventions of SQL ORDER BY and GROUP BY.  It takes in a
		list of one or more <b>QQ Column Nodes</b>. This list could be a parameterized list and/or an array.<br/><br/>
		
		Specifically for <b>OrderBy</b>, to specify a <b>QQ Node</b> that you wish to order by in descending
		order, add a "false" after the QQ Node.  So for example, <b>QQ::OrderBy(QQN::Person()->LastName, false,
		QQN::Person()->FirstName)</b> will do the SQL equivalent of "ORDER BY last_name DESC, first_name ASC".<br/><br/>

		<b>Count</b>, <b>Minimum</b>, <b>Maximum </b>and <b>Average</b> are aggregation-related clauses, and
		only work when <b>GroupBy</b> is specified.  These methods take in an attribute name, which
		can then be restored using <b>GetVirtualAttribute()</b> on the object.<br/><br/>

		<b>Expand</b> and <b>ExapndAsArray</b> deals with Object Expansion / Early Binding.  More on this
		can be seen in the "Early Binding of Related Objects" example.<br/><br/>
		
		<b>LimitInfo</b> will limit the result set.  The first integer is the maximum number of rows
		you wish to limit the query to.  The <i>optional</i> second integer is the offset (if any).<br/><br/>
		
		And finally, <b>Distinct</b> will cause the query to be called with SELECT DISTINCT.<br/><br/>
		
		All clauses must be wrapped around a single <b>QQ::Clause()</b> call, which takes in any
		number of clause classes for your query.
	</div>



	<h3>Select all People, Ordered by Last Name then First Name</h3>
<?php
	$objPersonArray = Person::QueryArray(
		QQ::All(),
		QQ::Clause(
			QQ::OrderBy(QQN::Person()->LastName, QQN::Person()->FirstName)
		)
	);

	foreach ($objPersonArray as $objPerson) {
		_p($objPerson->FirstName . ' ' . $objPerson->LastName);
		_p('<br/>', false);
	}
?>



	<h3>Select all People, Ordered by Last Name then First Name, Limited to the first 4 results</h3>
<?php
	$objPersonArray = Person::QueryArray(
		QQ::All(),
		QQ::Clause(
			QQ::OrderBy(QQN::Person()->LastName, QQN::Person()->FirstName),
			QQ::LimitInfo(4)
		)
	);

	foreach ($objPersonArray as $objPerson) {
		_p($objPerson->FirstName . ' ' . $objPerson->LastName);
		_p('<br/>', false);
	}
?>



	<h3>Select all Projects and the Count of Team Members (if applicable)</h3>
<?php
	$objProjectArray = Project::QueryArray(
		QQ::All(),
		QQ::Clause(
			QQ::GroupBy(QQN::Project()->Id),
			QQ::Count(QQN::Project()->PersonAsTeamMember->PersonId, 'team_member_count')
		)
	);

	foreach ($objProjectArray as $objProject) {
		_p($objProject->Name . ' (' . $objProject->GetVirtualAttribute('team_member_count') . ' team members)');
		_p('<br/>', false);
	}
?>

<?php require('../includes/footer.inc.php'); ?>