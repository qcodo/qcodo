<?php
	// This is the HTML template include file for intro.php
	// Here is where you specify any HTML that you want rendered in the form, and here
	// is where you can specify which controls you want rendered and where.
?>
<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>
	
	<div class="instructions">
		<div class="instruction_title">The QListControl Family of Controls</div>
		<b>QListControl</b> controls handle simple lists of objects which can be selected.  In its most
		basic form, we are basically talking about HTML listboxes (e.g. &lt;select&gt;) with name/value
		pairs (e.g. &lt;option&gt;).<br/><br/>

		Of course, listboxes can be single- and multiple-select.  But note that sometimes,
		you may want to display this list as a list of labeled checkboxes (which basically acts
		like a multiple-select listbox) or a list of labeled radio buttons (which acts like a
		single-select listbox).  Qcodo includes the <b>QListBox</b>, <b>QCheckboxList</b> and
		<b>QRadioButtonList</b> controls which all inherit from QListControl to allow you to
		present the data and functionality that you need to in the most user-friendly way possible.<br/><br/>

		In this example we create a <b>QListBox</b> control.  This single-select listbox will pull its data
		from the <b>Person</b> table in the database.  Also, if you select a person, we will update the
		<b>lblMessage</b> label to show what you have selected.<br/><br/>

		If you do a <b>View Source...</b> in your browser to view the HTML,
		you'll note that the &lt;option&gt; values are arbitrary indexes (starting with 0).  This is
		done intentionally.  <b>QListControl</b> uses arbitrary listcontrol indexes to lookup the specific
		value that was assigned to that <b>QListItem</b>.  It allows you to do things like put in non-string
		based data into the value, or even to have multiple listitems point have the same exact value.<br/><br/>
		
		And in fact, this is what we have done.  The actual value of each <b>QListItem</b> is <i>not</i> a
		<b>Person</b> Id, but it is in fact the <b>Person</b> object, itself.  Note that in our
		<b>lstPersons_Change</b>, we never need to re-lookup the <b>Person</b> via a <b>Person::Load</b>.  We
		simply display the <b>Person's</b> name directly from the object that is returned by the <b>SelectedValue</b>
		call on our <b>QListBox</b>.
	</div>

	<div>
		<?php $this->lstPersons->Render(); ?><br/><br/>
		Currently Selected: <?php $this->lblMessage->Render(); ?>
	</div>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>