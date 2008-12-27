<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Customizing the QDataGrid subclass</div>
		
		All QDataGrids, by default, can be customized by altering the <b>QDataGrid</b>
		custom subclass in <b>/includes/qform/QDataGrid.inc</b>.  This class extends from
		the <b>QDataGridBase</b> class which is in the Qcodo core.<br/><br/>
		
		In the subclass, you can feel free to override rendering methods, including
		<b>GetPaginatorRowHtml</b>, <b>GetHeaderRowHtml</b>, <b>GetDataGridRowHtml</b> and
		<b>GetFooterRowHtml</b>.<br/><br/>
		
		In our example below, we have defined a <b>PaginatorAlternate</b> (so that we can
		render 2 paginators for this single datagrid), then set <b>ShowFooter</b> to true,
		and then finally implemented our own custom <b>GetFooterRowHtml</b> method (which
		basically just calls <b>GetPaginatorRowHtml</b> with the <b>PaginatorAlternate</b>
		object.
	</div>

		<?php $this->dtgPersons->Render(); ?>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>