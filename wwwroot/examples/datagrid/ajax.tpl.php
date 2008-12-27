<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Enabling AJAX-based Sorting and Pagination</div>
		In this example, we modify our sortable and paginated <b>QDataGrid</b> to now
		perform AJAX-based sorting and pagination.<br/><br/>
		
		We literally just add <i>one line</i> of code to enable AJAX.<br/><br/>
		
		By setting <b>UseAjax</b> to <b>true</b>, the sorting and pagiantion features will now execute
		via AJAX.  Try it out, and notice how paging and resorting doesn't involve the browser
		to do a full page refresh.
	</div>

		<?php $this->dtgPersons->Render(); ?>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>