<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>
	
	<div class="instructions">
		<div class="instruction_title">Making Renameable Labels</div>
		
		With the use of <b>QLabel</b> and <b>QTextBox</b> controls that can show, hide and change
		<b>CssClass</b> names depending on what action we must execute, we use
		<b>QAjaxActions</b> and various events to create
		a list of "renameable labels", where the interaction is similar to how files
		and folders can be selected and then renamed in the Finder or in Windows Explorer.<br/><br/>
		
		To rename any of the labels below, click on it to highlight it.  And then click it again to
		rename it.  If you click elsewhere or hit return, the change will be saved.  If you hit
		escape, the change will be cancelled.
	</div>

		<?php for ($intIndex = 0; $intIndex < 10; $intIndex++) {
			_p('<p style="height: 16px;">', false);
			$this->lblArray[$intIndex]->Render();
			$this->txtArray[$intIndex]->Render('BorderWidth=1px','BorderColor=gray','BorderStyle=Solid');
			_p('</p>', false);
		} ?>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>