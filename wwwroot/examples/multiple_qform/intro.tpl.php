<?php
	// This is the HTML template include file for intro.php
	// Here is where you specify any HTML that you want rendered in the form, and here
	// is where you can specify which controls you want rendered and where.
?>
<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>
	
	<div class="instructions">
		<div class="instruction_title">Handling "Multiple QForms" on the Same Page</div>
		Qcodo only allows each front-end "web page" to only have a maximum of one <b>QForm</b> class per page.  Because of
		the many issues of managing and maintaining formstate across multiple <b>QForms</b>, Qcodo simply does not allow
		for the ability to have multiple <b>QForms</b> per page.<br/><br/>
		
		However, as the development of a Qcodo application matures, developers may find themselves wishing for this ability:
		<ul><li>As <b>QForms</b> are initially developed for simple, single-step tasks (e.g. "Post a Comment", "Edit a Project's Name", etc.),
		developers may want to be able to combine these simpler QForms together onto a single, larger, more cohesive QForm,
		utilizing AJAX to provide for a more "Single-Page Web Application" type of architecture.</li>
		<li>Moreover, developers may end up with a library of these <b>QForms</b> that they would want to reuse in multiple locations,
		thus allowing for a much better, more modularized codebase.</li></ul>

		Fortunately, the <b>QPanel</b> control was specifically designed to provide this kind of "Multiple <b>QForm</b>" functionality.
		In the example below, we create a couple of custom <b>QPanels</b> to help with the viewing and editing of a Project and its team members.  The
		comments in each of these custom controls explain how a custom <b>QPanel</b> provides similar functionality to an independent, stand-alone
		<b>QForm</b>, but also details the small differences in how the certain events need to be coded.<br/><br/>

		Next, to illustrate this point further we create a <b>PersonEditPanel</b>, which is based on the code generated
		<b>PersonEditFormBase</b> class.<br/><br/>

		Finally, we use a few <b>QAjaxActions</b> and <b>QAjaxControlActions</b> to tie them all together into a single-page web application.
	</div>

	<h2>View/Edit Example: Projects and Memberships</h2>

	<p>Please Select a Project: <?php $this->lstProjects->Render(); ?> &nbsp;&nbsp; <?php $this->objDefaultWaitIcon->Render(); ?></p>
	<?php $this->pnlLeft->Render(); ?>
	<?php $this->pnlRight->Render(); ?>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>