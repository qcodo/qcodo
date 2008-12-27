<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Analyzing Reverse Relationships</div>
		Although it's a bit hard to undrestand at first, one of the unique and more powerful features of Qcodo
		is its ability to generate code to handle reverse relationships as well.
		Given our previous example with the <b>Project</b> and <b>ManagerPerson</b>, we showed how
		Qcodo generated code in the <b>Project</b> class to handle the relationship.  But Qcodo will also geneate
		code in the <b>Person</b> class to handle the reverse aspects of this relationship.<br/><br/>

		In this case, <b>Person</b> is on the "to Many" side of a "One to Many" relationship with <b>Project</b>.
		So Qcodo will generate the following methods in <b>Person</b> to deal with this reverse
		relationship:
		<ul>
		<li>GetProjectsAsManagerArray</li>
		<li>CountProjectsAsManager</li>
		<li>AssociateProjectAsManager</li>
		<li>UnassociateProjectAsManager</li>
		<li>UnassociateAllProjectsAsManager</li>
		<li>DeleteAssociatedProjectAsManager</li>
		<li>DeleteAllProjectsAsManager</li>
		</ul>

		And in fact, Qcodo will generate the same seven methods for any "One to Many" reverse relationship
		(get, count all, associate, unassociate, and unassociate all, delete associated, and delete all associated).
		Note that the "AsManager" token in all these methods are there because we named the column in the
		<b>project</b> table <b>manager_person_id</b>.  If we simply named it as <b>person_id</b>,
		the methods would be named without the "AsManager" token (e.g. "GetProjectsArray", "CountProjects",
		etc.)<br/><br/>
		
		Also note that <b>GetProjectsAsManagerArray</b> utilizes the <b>LoadArrayByManagerPersonId</b>
		method in the <b>Project</b> object.  Of course, this was generated because <b>manager_person_id</b> is already
		an index (as well as a Foreign Key) in the <b>project</b> table.<br/><br/>
		
		Qcodo's Reverse Relationships functionality
		is dependent on the data model having indexes defined on all columns that are foreign keys.  For many
		database platforms (e.g. MySQL) this should not be a problem b/c the index is created implicitly by the engine.
		But for some (e.g. SQL Server) platforms, make sure that you have indexes defined on your Foreign Key columns,
		or else you forgo being able to use the Reverse Relationship functionality.

		<h3>Unique Reverse Relationships (e.g. "One to One" Relationships)</h3>

		Qcodo will generate a different set of code if it knows the reverse relationship to be a "Zero
		to One" or "One to One" type of relationship.  This occurs in the relationship between
		our <b>login</b> and <b>person</b> tables.  Note that <b>login</b>.<b>person_id</b> is a unique
		column.  Therefore, Qcodo recognizes this as a "Zero- or One-to-One" relationship.  So for the
		reverse relationship, Qcodo will not generate the five methods (listed above) in the <b>Person</b>
		table for the <b>Login</b> relationship.  Instead, Qcodo generates a <b>Login</b> property in
		<b>Person</b> object which can be set, modified, etc. just like the <b>Person</b> property in
		the <b>Login</b> object.

		<h3>Self-Referential Tables</h3>

		Qcodo also has full support for self-referential tables (e.g. a <b>category</b> table that
		contains a <b>parent_category_id</b> column which would foreign key back to itself).
		In this case, the qcodo will generated the following seven methods to assist with the reverse
		relationship for this self-reference:
		<ul>
		<li>GetChildCategoryArray</li>
		<li>CountChildCategories</li>
		<li>AssociateChildCategory</li>
		<li>UnassocaiteChildCategory</li>
		<li>UnassociateAllChildCategories</li>
		<li>DeleteChildCategory</li>
		<li>DeleteAllChildCategories</li>
		</ul>
		(Note that even though this is being documented here, self-referential tables aren't actually
		defined in the <b>Examples Site Database</b>.)
	</div>



	<h3>Person's Reverse Relationships with Project (via project.manager_person_id)<br/>and Login (via login.person_id)</h3>
<?php
	// Let's load a Person object -- let's select the Person with ID #1
	$objPerson = Person::Load(7);
?>
	Person ID: <?php _p($objPerson->Id); ?><br/>
	First Name: <?php _p($objPerson->FirstName); ?><br/>
	Last Name: <?php _p($objPerson->LastName); ?><br/><br/><br/>



	<b><i>Listing of the Project(s) that This Person Manages</i></b><br/>
<?php
	foreach ($objPerson->GetProjectAsManagerArray() as $objProject)
		_p('&bull; ' . $objProject->Name . '<br/>', false);
?>
	<br/>There are <?php _p($objPerson->CountProjectsAsManager()); ?> project(s) that this person manages.<br/><br/><br/>



	<b><i>This Person's Login Object</i></b><br/>
	Username: <?php _p($objPerson->Login->Username); ?><br/>
	Password: <?php _p($objPerson->Login->Password); ?><br/>


<?php require('../includes/footer.inc.php'); ?>