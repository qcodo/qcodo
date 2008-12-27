<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Using a Relationships Script</div>
		Our <b>Examples Site Database</b> uses the InnoDB storage engine in MySQL, which has
		full support for Foreign Keys to help define relationships between tables.<br/><br/>

		However, sometimes you maybe using a platform which does not offer Foreign Key support
		(e.g. MySQL with MyISAM tables), or alternatively, you may want to have relationships
		defined in your objects but you do not want to incur the performance and/or restriction
		of using a programmatic foreign key constraint.<br/><br/>

		The code generator supports this by allowing you to define a <b>Relationships Script</b> to
		a relationships script file.  This is just a plain textfile that you write to
		define any "foreign keys" you have in your database (without explicitly defining
		a real foreign key).  This file can be formatted in one of two ways.  The standard "qcodo"
		format is basically:
		<blockquote><p>table1.column1 => table2.column2</p></blockquote>
		where <b>table1.column1</b> is meant to be a Foreign Key to <b>table2.column2</b>.  The other
		option is to use standard ANSI "sql" format:
		<blockquote><p>ALTER TABLE table1 ADD CONSTRAINT foo_bar FOREIGN KEY column1 ON table2(column2);</p></blockquote>
		This format is more compatible with ER Diagramming applications which can generate SQL scripts for use
		with the database.  You can simply point the code generator to use the generated SQL script to help
		with your "virtual" foreign keys.
		<br/><br/>

		Once you have your relationships script defined, you can specify the location of this script
		file in the <b>RelationshipsScript</b> directive of your codegen settings XML file.
		<br/><br/>
		
		Please <b>View Source</b> to view the <b>Examples Site Database</b> SQL script using MyISAM tables, as
		well as its corresponding <b>relationships.txt</b> file.  The combination of this MyISAM script
		and the <b>relationships.txt</b> file should functionally give you the same, equivalent
		database as the InnoDB version of our <b>Examples Site Database</b>.
	</div>

<?php require('../includes/footer.inc.php'); ?>