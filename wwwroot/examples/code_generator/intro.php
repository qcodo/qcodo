<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">The Examples Site Database</div>
		
		Before learning about the Code Generator, it might be good to first get acquainted with the
		data model which the Code Generator will be generating from.<br/><br/>
		
		Click on the "View Source" link in the upper righthand corner to view the
		<b>mysql_innodb.sql</b> to examine the data model in script form, or you can
		view an ER diagram of the data model below.<br/><br/>

		If you have not installed this <b>Examples Site Database</b> on your MySQL server, you might want to
		do that now.  After installing the database, you must also remember to
		<b><a href="<?php _p(__VIRTUAL_DIRECTORY__ . __DEVTOOLS__ . '/codegen.php'); ?>" class="bodyLink">code generate</a></b>
		the corresponding objects <i>before</i> trying to any of the further code generation examples.<br/><br/>
		
		Note that there is also a SQL Server version of this database script called <b>sql_server.sql</b>.<br/><br/>

		In the script, we have six tables defined.  The bulk of our examples will focus on the main three
		tables of the database:
		<ul>
		<li><b>login</b></li>
		<li><b>person</b></li>
		<li><b>project</b></li>
		</ul><br/>

		The <b>team_member_project_assn</b> table handles the many-to-many relationship between
		<b>person</b> and <b>project</b>.
		The <b>project_status_type</b> table is a <b>Type Table</b> which will be discussed in
		the example for <b>Type Tables</b>.  Finally the <b>person_with_lock</b> table is
		specifically used by the example for <b>Optimistic Locking</b>.
	</div>

	<img src="../images/data_model.png" alt="&quot;Examples Site Database&quot; data model" style="border-width: 1px; border-style: solid;" />
	
<?php require('../includes/footer.inc.php'); ?>