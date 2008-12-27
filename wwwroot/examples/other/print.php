<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Print Command Shortcuts</div>

		Developers will tend to use the following PHP <b>Print</b> methods fairly often
		in the template include files:
		<ul>
		<li>print($strSomeString)</li>
		<li>print(htmlentities($strSomeString))</li>
		<li>print(QApplication::Translate($strSomeString))</li>
		<li>print(QApplication::LocalizeInteger($intSomething)) (not yet implemented)</li>
		<li>print(QApplication::LocalizeFloat($fltSomething)) (not yet implemented)</li>
		<li>print(QApplication::LocalizeCurrency($fltSomething)) (not yet implemented)</li>
		</ul>

		Because of this, Qcodo has defined several global PHP functions which act as shortcuts
		to these specific commands:
		<ul>
		<li>_p($strSomeString, $blnHtmlEntities = true) - will print the passed in string.  By default, it will also perform <b>QApplication::HtmlEntities</b> first.  You can override this by setting $blnHtmlEntities = false.</li>
		<li>_t($strSomeString) -- will print a translated string via <b>QApplication::Translate</b></li>
		<li>_i($intSomething)</li>
		<li>_f($fltSomething)</li>
		<li>_c($fltSomething)</li>
		</ul>

		Please note: these are simply meant to be shortcuts to actual Qcodo functional
		calls to make your templates a little easier to read.  By no means do you have to
		use them.  Your templates can just as easily make the fully-named method/function calls.
	</div>

	Examples:
	<?php _p('Hello, world'); ?>

<?php require('../includes/footer.inc.php'); ?>