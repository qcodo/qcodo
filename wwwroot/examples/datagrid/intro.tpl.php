<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">An Introduction to the QDataGrid Class</div>
		The <b>QDataGrid</b> control is used to present a collection of objects or data in a grid-based
		(e.g. &lt;table&gt;) format.  All <b>QDataGrid</b> objects take in a <b>DataSource</b>, which can be an array
		of anything (or in our example, an array of Person objects).<br/><br/>
		
		In defining a <b>QDataGrid</b>, you must define a new <b>QDataGridColumn</b> for each column in your table.
		For each <b>QDataGridColumn</b> you can specify its name and how it should be rendered.
		The HTML definition in your <b>QDataGridColumn</b> will be rendered directly
		into your HTML output.  Inside your HTML definition, you can also specify PHP commands, methods,
		function calls and/or variables which can be used to output item-specific data.<br/><br/>
		
		Calls to PHP can be made by using &lt;?= and ?&gt; tags (see this example's code for more
		information).  Note that these PHP short tags are being used by Qcodo <i>internally</i> as delimiters
		on when the PHP engine should be used.  <b>QDataGrid</b> (and Qcodo in general, for that matter) offers
		full support of PHP installations with <b>php_short_tags</b> set to off.<br/><br/>
		
		Finally, the <b>QDataGrid</b>'s style is fully customizable, at both the column level and the row level.
		You can specify specific column style attributes (e.g. the last name should be in bold), and you can specify
		row attributes for all rows, just the header, and just alternating rows.
	</div>

		<?php $this->dtgPersons->Render(); ?>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>