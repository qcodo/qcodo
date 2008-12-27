<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Using Association Tables</div>
		Qcodo also supports handling many-to-many relationships.  Typically, many-to-many relationships are
		mapped in the database using an <b>Association Table</b> (sometimes also called a <b>Mapping</b>
		or a <b>Join Table</b>).  It is basically a two-column table where both
		columns are Foreign Keys to two different tables.<br/><br/>

		Qcodo allows you to define a set suffix for all <b>Association Tables</b> (the default is "_assn").
		Whenever the code generator sees any table that ends in "_assn", it will mark it as a special
		table to be used/analyzed as an <b>Association Table</b>, associating two objects together in a many-to-many
		relationship.

		With the <b>Association Table</b> in place, Qcodo will generate five methods each for the two classes
		involved in this many-to-many relationship.  In our example, we created a <b>team_member_project_assn</b>
		table to represent a many-to-many relationship between <b>Person</b> and <b>Project</b>.<br/><br/>

		Qcodo will generate the following five methods in <b>Person</b> to deal with this many-to-many
		relationship:
		<ul>
		<li>GetProjectAsTeamMemberArray</li>
		<li>CountProjectsAsTeamMember</li>
		<li>AssociateProjectAsTeamMember</li>
		<li>UnassociateProjectAsTeamMember</li>
		<li>UnassociateAllProjectsAsTeamMember</li>
		</ul>

		Qcodo will also generate the following five methods in <b>Project</b> to deal with this many-to-many
		relationship:
		<ul>
		<li>GetPersonAsTeamMemberArray</li>
		<li>CountPeopleAsTeamMember</li>
		<li>AssociatePersonAsTeamMember</li>
		<li>UnassociatePersonAsTeamMember</li>
		<li>UnassociateAllPeopleAsTeamMember</li>
		</ul>
		
		Note that the structure of these five methods are very similar for both objects (get, count,
		associate, unassociate, and unassociate all).  In fact, you will also notice that this is 
		the same structure as the reverse one-to-many relationship in our previous example.  This especially
		makes sense considering that for all three examples, the object is dealing with the "-to-many" side
		of the relationship.  Regardless if it is a one-"to-many" or a many-"to-many", the five methods
		dealing with "-to-many" is consistent.<br/><br/>

		Also, similar to our previous example, note that the "AsTeamMember" token in all these methods are
		there because we named the <b>Association Table</b> in the database <b>team_member_project_assn</b>.
		If we simply named it <b>person_project_assn</b>, then
		the methods would be named without the "AsTeamMember" token (e.g. "GetProjectArray", "AssociatePerson",
		etc.)<br/><br/>
	</div>



	<h3>Person's Many-to-Many Relationship with Project (via team_member_project_assn)</h3>
<?php
	// Let's load a Person object -- let's select the Person with ID #2
	$objPerson = Person::Load(2);
?>
	Person ID: <?php _p($objPerson->Id); ?><br/>
	First Name: <?php _p($objPerson->FirstName); ?><br/>
	Last Name: <?php _p($objPerson->LastName); ?><br/><br/>



	<b><i>Listing of the Project(s) that This Person is a Team Member of</i></b><br/>
<?php
	foreach ($objPerson->GetProjectAsTeamMemberArray() as $objProject)
		_p('&bull; ' . $objProject->Name . '<br/>', false);
?>
	<br/>There are <?php _p($objPerson->CountProjectsAsTeamMember()); ?> project(s) that this person is a team member of.<br/><br/><br/>






	<h3>Project's Many-to-Many Relationship with Person (via team_member_project_assn)</h3>
<?php
	// Let's load a Project object -- let's select the Project with ID #1
	$objProject = Project::Load(1);
?>
	Project ID: <?php _p($objProject->Id); ?><br/>
	Project Name: <?php _p($objProject->Name); ?><br/><br/>



	<b><i>Listing of the Person(s) that This Project has as Team Members</i></b><br/>
<?php
	foreach ($objProject->GetPersonAsTeamMemberArray() as $objPerson)
		_p('&bull; ' . $objPerson->FirstName . ' ' . $objPerson->LastName . '<br/>', false);
?>
	<br/>There are <?php _p($objProject->CountPeopleAsTeamMember()); ?> person(s) that this project has as team members.<br/><br/><br/>



<?php require('../includes/footer.inc.php'); ?>