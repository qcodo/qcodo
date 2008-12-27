<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Integrating QForms and the Code Generator</div>

		When you code generate your objects, Qcodo will actually provide a starting
		point for this integration in the generated <b>Drafts</b>.  These generated
		scripts are definitely <i>drafts</i> or starting points from which you can create
		more elaborate, useful and functional <b>QForms</b> or <b>QPanels</b> for your application.<br/><br/>

		At a high level, this concept is very similar to the <b>scaffolding</b> which
		is provided by many other frameworks.  But note that because of the object-oriented
		approach of the <b>MetaControls</b> and <b>Meta DataGrids</b>, these <b>Drafts</b> can offer much more
		power and functionality over <b>scaffolding</b>.<br/><br/>

		It is difficult to show this in a one-page example, so if you would like to
		see this in action, we recommend that you check out <b>Demo Part II</b>
		in the <b><a href="http://www.qcodo.com/demos/" class="bodyLink">Qcodo Demos and
		Examples</a></b>.
	</div>

	To view one of the generated <b>Form Drafts</b>, please click here to
	view the <b><a href="<?php _p(__VIRTUAL_DIRECTORY__ . __FORM_DRAFTS__); ?>/person_list.php"
		class="bodyLink">Person List</a></b> page.

<?php require('../includes/footer.inc.php'); ?>