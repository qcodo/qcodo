<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Simple QDataRepeater using AJAX-triggered Pagination</div>		
		The main difference between a <b>QDataGrid</b> and a <b>QDataRepeater</b> is that while a
		<b>QDataGrid</b> is in a table
		and has a lot structure to help define how that table should be rendered, a <b>QDataRepeater</b>
		is basically without structure.  You simply specify a template file which will be used to 
		define how you wish each <b>Person</b> object to be rendered.<br/><br/>

		This very simple <b>QDataRepeater</b> has a <b>QPaginator</b> defined with it, and
		its <b>UseAjax</b> property set to true.
		With this combination, the user will be able to page through the collection of <b>Person</b> items
		without a page refresh.<br/><br/>

		Note that because the <b>QPaginator</b> is rendered by the <i>form</i> (as opposed to the example
		with <b>QDataGrid</b> where the <i>datagrid</i> rendered the paginator), we will set the <i>form</i>
		as the paginator's parent.<br/><br/>
		
		Also, note that QDataRepeater allows you to set <i>two</i> paginators: a <b>Paginator</b> and a 
		<b>PaginatorAlternate</b>.  This is to offer listing pages which have the paginator at the
		top and at the bottom of the page.
		
		The same variables of <b>$_FORM</b>, <b>$_CONTROL</b> and <b>$_ITEM</b> that
		you would have used with a <b>QDataGrid</b>
		are also available to you in your <b>QDataRepeater</b> template file.
	</div>

	<div>
		<?php $this->dtrPersons->Paginator->Render(); ?>
		<br/>

		<?php $this->dtrPersons->Render(); ?>
		<br/>

		<?php $this->dtrPersons->PaginatorAlternate->Render(); ?>
	</div>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>