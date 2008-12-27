<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<style type="text/css">
		.image_canvas {border-width: 4px; border-style: solid; border-color: #a9f;}
	</style>

	<div class="instructions">
		<div class="instruction_title">Creating Your Own Control</div>

		Many developers may want to create their own, custom QControl to perform a very specific interaction.
		Alternatively, developers may wish to utilize exernal JavaScript libraries like Dojo, Yahoo! YUI, etc.
		to create their own set of QControls with a polished "Web 2.0" shine.
		<br/><br/>

		Whatever the case may be, Qcodo makes it easy to implement custom controls, complete with javascript
		input and output hooks, within the QControl architecture.
		<br/><br/>

		The core distribution comes with QSampleControl, which can act as a vanilla, sample starting point from
		which a developer can begin implementing his or her custom control.<br/><br/>
	</div>

	<?php $this->ctlCustom->Render(); ?>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>