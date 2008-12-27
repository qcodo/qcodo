<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Sorting a QDataGrid by Columns</div>
		In this example we show how to make the datagrid sortable by individual columns.<br/><br/>

		For each column, we add the properties <b>OrderByClause</b> and <b>ReverseOrderByClause</b> (it is possible
		to also just define <b>OrderByClause</b>, and to leave <b>ReverseOrderByClause</b> undefined).  The <b>QQ Clause</b>
		you specify is given back to you when you call the <b>OrderByClause</b> property on the <b>QDataGrid</b>
		itself.<br/><br/>

		So what you do is you specify the <b>QQ OrderBy Clause</b> that you would want run
		for each column.  Then you pass the this clause to your class's <b>LoadAll</b> or <b>LoadArrayArrayByXXX</b> 
		method as one of the optional <b>QQ Clause</b> parameters.  Note that all Qcodo code generated <b>LoadAll</b> and <b>LoadArrayByXXX</b>
		methods take in an optional <b>$objOptionalClauses</b> parameter which conveniently uses the clause returned by the <b>QDataGrid</b>'s
		<b>OrderByClause</b> method.<br/><br/>

		Convenient how they end up working together, isn't it? =)
	</div>

		<?php $this->dtgPersons->Render(); ?>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>