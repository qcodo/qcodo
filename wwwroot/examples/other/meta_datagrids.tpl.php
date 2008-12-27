<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Generated DataGrid Objects</div>
		Although the <i>concept</i> is known as a <b>Meta</b> DataGrid... the generated <b>DataGrid</b> objects
		in and of itself is just a subclass of the actual <b>QDataGrid</b> object.  (Note that this is different
		than a <b>MetaControl</b>, which is <i>not</i> a control, but is in fact a single data object
		and a collection of controls that can be generated from and linked to it.)<br/><br/>
		
		A generated/meta datagrid is simply a <b>QDataGrid</b> with a bunch of <b>Meta___()</b> methods to
		allow you to easily define and add columns for a given data class.<br/><br/>
		
		Using simple string properties or more complex (and more powerful) <b>Qcodo Query Nodes</b>, you can
		add any column (even columns from linked tables) to the datagrid, and the meta-functionality
		will automatically take care of things like the column's <b>Title</b>, <b>Html</b>, and <b>Sorting</b> properties.<br/><br/>

		It even comes with its own <b>MetaDataBinder()</b>, and the datagrid is already set up to use that
		as its databinder (but of course, even this is override-able). <br/><br/>

		But again, similar to <b>MetaControls</b>, note that the datagrid is just a regular <b>QDataGrid</b> object,
		and the columns are just regular <b>QDataGridColumn</b> objects, which means that you can modify 
		the colums or the datagrid itself however you see fit.
	</div>

	<?php $this->dtgProjects->Render(); ?>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>