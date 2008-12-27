<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Adding Pagination to Your QDataGrid</div>
		Now, we will add pagination to our datagrid.<br/><br/>

		In order to enable pagination, we need to define a <b>QPaginator</b> object and assign it to
		the <b>QDataGrid</b>.  Because the <b>QPaginator</b> will be rendered by the <b>QDataGrid</b>
		(instead of being rendered on the form via a <b>$this->objPaginator->Render()</b>
		call), we will set the <b>QDataGrid</b> as the <b>QPaginator</b>'s parent in the
		<b>QPaginator</b> constructor call.<br/><br/>
		
		In the locally defined <b>dtgPersons_Bind</b> method, in addition to setting the datagrid's <b>DataSource</b>,
		we also give the datagrid the <b>TotalItemCount</b> (via a <b>Person::CountAll</b> call).
		And finally, when we make the <b>Person::LoadAll</b> call, we make sure to
		pass in the datagrid's <b>LimitClause</b>, which will pass the paging information
		into our <b>LoadAll</b> call to only retrieve the items on the page we are
		currently viewing.
	</div>

		<?php $this->dtgPersons->Render(); ?>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>