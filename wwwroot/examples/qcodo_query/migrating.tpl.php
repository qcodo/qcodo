<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Migrating from Beta 2's Manual Queries</div>
		
		This page is primarily for applications that are attempting to migrate from Beta 2 to Beta 3 and the new <b>Qcodo Query</b> architecture.
		The <b>default</b> installation of Beta 3 does not offer support for the old Beta 2-style of Manual Queries.
		Deprecated functionality includes <b>QueryHelper</b>, <b>ArrayQueryHelper</b>, and <b>QQueryExpansion</b>,
		as well as the string-based (hard coded) <b>SortBy</b> and <b>LimitInfo</b> parameters.

		While the functionality in <b>Qcodo Query</b> is going to be the future of Qcodo, this deprecation causes a particular painpoint
		for developers wishing to migrate a Beta 2 codebase to Beta 3 and beyond, specifically with regards to custom <b>LoadArray</b> methods.
		While the ultimate solution would be to rewrite any modules still written in the old Beta 2-style, given time and other
		constraints, this is often not a feasible solution.<br/><br/>
		
		There are a few mechanisms which helps provide <i>some</i> assistance with backward compatability for applications and
		developers in this situation.  First off, the <b>codegen_settings.xml</b> file now has a <b>&lt;manualQuery support="true/false"/&gt;</b> tag
		which takes in a boolean value.  Setting this to <b>true</b> will cause the codegen templates to include the original
		<b>QueryHelper</b> and <b>ArrayQueryHelper</b> functionality, including support for Beta 2-style <b>Object Expansion</b>.<br/><br/>
		
		This will allow any Beta 2-style custom <b>Load</b> and <b>LoadArray</b> method to continue working in Beta 3.<br/><br/>
		
		In addition to this, the <b>QDataGridBase</b>, <b>QPaginatedControl</b> and <b>QDataGridColumn</b> classes have all been retrofitted
		with the original <b>SortBy</b> and <b>LimitInfo</b> properties which can now pass the original string-based parameters back and forth
		to custom load methods.  And in fact, <b>SortBy</b> specifically can convert <i>simple</i> QQ::OrderBy clauses into a string-based SortBy
		command, too.<br/><br/>
		
		What this allows is for <i>custom</i> "Beta 2" <b>DataGrids</b> to be able to operate with your <i>custom</i> "Beta 2" <b>LoadArray</b> methods in a Beta 3 codebase (and beyond).<br/><br/>
		
		Unfortunately, one thing that will <i>not</i> be able to be retrofitted to Beta 2 is any <i>code-generated</i> <b>LoadArray</b> methods.  These methods
		will need to take in the new <b>QQ::OrderBy</b> and <b>QQ:LimitInfo</b> parameters, and will not be retrofitted to take in its string-based counterparts.<br/><br/>

		<i>Only in this case</i>, what this means is that if you have completely <i>custom</i> "Beta 2" <b>DataGrids</b> that load against <i>code-generated</i> <b>LoadArray</b> methods,
		then the datagrids will need to be slightly altered so that it is using <b>OrderByClause</b> and <b>LimitClause</b> instead of
		the Beta 2-style <b>SortInfo</b> and <b>LimitInfo</b> properties.  And specifically for <b>QDataGridColumns</b>, <b>OrderByClause</b> and
		<b>ReverseOrderByClause</b> will need to be used instead of <b>SortByCommand</b> and <b>ReverseSortByCommand</b>.
		
		And finally, obviously any <i>code-generated</i> <b>DataGrid</b> will continue working with any <i>code-generated</i> <b>LoadArray</b> method.
		<br/><br/>

		So, just to recap, a summary of what <b>DataGrid</b>/<b>LoadArray</b> pairs from Beta 2 will work in Beta 3:
		<br/><br/>
		<table cellpadding="4px" cellspacing="0px">
			<tr>
				<td></td>
				<td style="border-width: 1px 0px 0px 1px; border-style: solid; border-color: black; background-color: #cccccc;">Custom DataGrid</td>
				<td style="border-width: 1px 1px 0px 1px; border-style: solid; border-color: black; background-color: #cccccc;">Code Generated DataGrid (e.g. ListFormBase)</td>
			</tr>
			<tr>
				<td style="border-width: 1px 0px 0px 1px; border-style: solid; border-color: black; background-color: #cccccc;">Custom LoadArray Method</td>
				<td style="border-width: 1px 0px 0px 1px; border-style: solid; border-color: black;">Should Work<br/><span style="font-size: 10px;">(requires <b>manualQuery support="true"</b>)</span></td>
				<td style="border-width: 1px 1px 0px 1px; border-style: solid; border-color: black;">Not Applicable<br/><span style="font-size: 10px;">(by definition, code generated datagrids do not use custom Load methods)</span></td>
			</tr>
			<tr>
				<td style="border-width: 1px 0px 1px 1px; border-style: solid; border-color: black; background-color: #cccccc;">Code Generated LoadArray Method</td>
				<td style="border-width: 1px 0px 1px 1px; border-style: solid; border-color: black;">Will Not Work<br/><span style="font-size: 10px;">(but only parts of the datagrid will need to be modified)</span></td>
				<td style="border-width: 1px 1px 1px 1px; border-style: solid; border-color: black;">Fully Functional<br/><span style="font-size: 10px;">(will utilize <b>Qcodo Query</b>)</span></td>
			</tr>
		</table>
		<br/>

		The example below shows a custom Beta 2-style <b>DataGrid</b> and a custom Beta 2-style <b>LoadArray</b> method working together...
		even though the applicaiton is running "Qcodo <?php _p(QCODO_VERSION); ?>".  The other example shows Beta 2-style <b>Object Expansion</b> in 
		a custom <b>LoadArray</b> method.
	</div>

	<h3>Custom "Beta 2" DataGrid Example</h3>
	<?php $this->dtgPerson->Render() ?>





	<h3>Custom "Beta 2" Object Expansion Example</h3>
<?php
	// As an example, we will include the Project object here to define a custom LoadArray method
	// This is here for demonstration purposes only.  Normally, this code would reside in the class file, itself.
	require(__DATAGEN_CLASSES__ . '/ProjectGen.class.php');
	class Project extends ProjectGen {
		public static function LoadArrayByMinimumId($intId, $strOrderBy = null, $strLimit = null, $objExpansionMap = null) {
			// Call to ArrayQueryHelper to Get Database Object and Get SQL Clauses
			Project::ArrayQueryHelper($strOrderBy, $strLimit, $strLimitPrefix, $strLimitSuffix, $strExpandSelect, $strExpandFrom, $objExpansionMap, $objDatabase);

			// Escape the Parameter(s)
			$intId = $objDatabase->SqlVariable($intId);

			// Setup the SQL Query
			$strQuery = sprintf('
				SELECT
				%s
					project.*
					%s
				FROM
					`project` AS `project`
					%s
				WHERE
					project.id > %s
				%s
				%s', $strLimitPrefix, $strExpandSelect, $strExpandFrom,$intId,
				$strOrderBy, $strLimitSuffix);

			// Perform the Query and Instantiate the Result
			$objDbResult = $objDatabase->Query($strQuery);
			return Project::InstantiateDbResult($objDbResult);
		}
	}

	// Lets use Beta 2-style Object Expansion (using QQueryExpansion) to Load an Array of Projects
	$objExpansionMap[Project::ExpandManagerPerson] = true;
	$objProjectArray = Project::LoadArrayByMinimumId(2, null, null, $objExpansionMap);
	foreach ($objProjectArray as $objProject)
		printf('ID #%s: %s (managed by %s %s)<br/>', $objProject->Id, $objProject->Name, $objProject->ManagerPerson->FirstName, $objProject->ManagerPerson->LastName);
?>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>