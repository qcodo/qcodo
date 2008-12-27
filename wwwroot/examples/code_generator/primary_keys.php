<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Primary Keys in Your Tables</div>

		In order for any ORM architecture to work, there must be at least some kind of Primary Key defined
		on any table for which you want an object generated.  But what is unique about Qcodo's ORM is that it does
		<i>not</i> impose any requirements on <i>how</i> to define your Primary Keys.  (Note that you can also
		still use the framework against any database that contains tables that do <i>not</i> have primary keys,
		it is just that those specific tables will not be generated as objects.)<br/><br/>

		Your Primary Key column or columns can be named however you wish.  Moreover, Qcodo supports Primary Key columns
		that are both "automatically incremented" and <i>not</i> "automatically incremented".  ("Automatically
		incremented" columns are known as auto_incremement, identity, or using a sequence,
		depending on which database platform you are using).<br/><br/>

		Qcodo also offers <i>some</i> support for tables that have multiple-column Primary Keys defined on it.
		For tables that have multi-column Primary Keys, Qcodo will fully generate the object
		itself.  But note that you will <i>not</i> be able to use this generated object as a related object for
		another table (in other words, Qcodo does not support multi-column <i>Foreign</i> Keys).  However,
		with all the generated <b>Load</b> methods in these objects, it is still possible to fully develop
		an application with tables that use multi-column Foreign Keys.  Basically, whenever you want to access
		a related object via a multi-column Foreign Key, you can simply call that object's <b>Load</b> method
		directly to retrieve that object.<br/><br/>
		
		If you are code generating against a legacy application or database that has tables with multiple-column
		Primary Keys, then this level of support should hopefully suffice.  But if you are creating a new application
		or database, then it is recommended that all tables have a single-column Primary Key (with one that
		preferably is sequenced, auto_increment, or identity, depending on which DB platform you are using).
	</div>

<?php require('../includes/footer.inc.php'); ?>