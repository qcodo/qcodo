<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Optimistic Locking and TIMESTAMP Columns</div>
		If you are generating any table that has a TIMESTAMP column, then Qcodo will automatically
		generate the functionality to perform <b>Optimistic Locking</b> for that object.  In this example,
		the <b>person_with_lock</b> table is defined with a TIMESTAMP column so that we can demonstrate
		<b>Optimistic Locking</b>.<br/><br/>
		
		<b>Optimistic Locking</b> is the loosest form of row- or object-level locking that, in general,
		works best for database-driven web-based applications.  In short, everyone is always
		allowed to read any row/object.  However, on save, you are only allowed to save if your
		object is considered "fresh".  Once your object is "stale", then you locked out from being
		able to save that stale object.  (Note that this is sometimes also called a
		"mid-air collision".)<br/><br/>
		
		Programatically, this is done via TIMESTAMP columns.  Remember that TIMESTAMP columns are updated
		by the database on every UPDATE.<br/><br/>

		So whenever you <b>Load</b> an object, you also get the latest TIMESTAMP information.  On
		<b>Save</b>, the TIMESTAMP in your object will be checked against the TIMESTAMP in the database.
		If they match, then the framework knows your data is still fresh, and it will allow the <b>Save</b>.
		If they do not match, then it is safe to say that the data in the object is now stale, and Qcodo
		will throw an <b>Optimistic Locking Exception</b>.<br/><br/>
		
		Note that the <b>Optimistic Locking</b> constraint can be overridden at any time by simply
		passing in the optional <b>$blnForceUpdate</b> as true when calling <b>Save</b>.
	</div>


	<h3>Object Save and Double Saves on the PersonWithLock Object</h3>
	<form method="post" action="<?php _p(QApplication::$ScriptName); ?>"><div>
		Saving a Single Object will perform the save normally<br/>
		<input type="submit" id="single" name="single" value="Save 1 Object"/><br/><br/><br/>

		Attempting to save a Two Instances of the Same Object will throw an <b>Optimistic Locking Exception</b><br/>
		<input type="submit" id="double" name="double" value="Save 2 Objects (same Instance)"/><br/><br/><br/>

		Using <b>$blnForceUpdate</b> to avoid the <b>Optimistic Locking Exception</b><br/>
		<input type="submit" id="double_force" name="double_force" value="Force Update Second Object"/><br/><br/><br/>
	</div></form>

<?php
	// Load the Two Person objects (same instance -- let them both be Person ID #4)
	$objPerson1 = PersonWithLock::Load(5);
	$objPerson2 = PersonWithLock::Load(5);

	// Some RDBMS Vendors' TIMESTAMP is only precise to the second
	// Let's force a delay to the next second to ensure timestamp functionality
	// Note: on most web applications, because Optimistic Locking are more application user-
	// level constraints instead of systematic ones, this delay is inherit with the web
	// application paradigm.  The following delay is just to simulate that paradigm.
	$dttNow = new QDateTime(QDateTime::Now);
	while ($objPerson1->SysTimestamp == $dttNow->__toString(QDateTime::FormatIso))
		$dttNow = new QDateTime(QDateTime::Now);

	// Make Changes to the Object so that the Save Will Update Something
	if ($objPerson1->FirstName == 'Al') {
		$objPerson1->FirstName = 'Alfred';
		$objPerson2->FirstName = 'Al';		
	} else {
		$objPerson1->FirstName = 'Al';
		$objPerson2->FirstName = 'Alfred';
	}

	switch (true) {
		case array_key_exists('single', $_POST):
			$objPerson1->Save();
			_p('Person Id #' . $objPerson1->Id . ' saved.  Name is now ' . $objPerson1->FirstName);
			_p('.<br/>', false);
			break;


		case array_key_exists('double', $_POST):
			$objPerson1->Save();
			_p('Person Id #' . $objPerson1->Id . ' saved.  Name is now ' . $objPerson1->FirstName);
			_p('.<br/>', false);

			// Try Saving Person #2 -- this should fail and throw an exception
			$objPerson2->Save();
			_p('Person Id #' . $objPerson2->Id . ' saved.  Name is now ' . $objPerson2->FirstName);
			_p('.<br/>', false);
			break;


		case array_key_exists('double_force', $_POST):
			$objPerson1->Save();
			_p('Person Id #' . $objPerson1->Id . ' saved.  Name is now ' . $objPerson1->FirstName);
			_p('.<br/>', false);

			// Try Saving Person #2 -- use $blnForceUpdate to avoid an exception
			$objPerson2->Save(false, true);
			_p('Person Id #' . $objPerson2->Id . ' saved.  Name is now ' . $objPerson2->FirstName);
			_p('.<br/>', false);
			break;
	}
?>

<?php require('../includes/footer.inc.php'); ?>