<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<style type="text/css">
		.image_canvas {border-width: 4px; border-style: solid; border-color: #a9f;}
	</style>

	<div class="instructions">
		<div class="instruction_title">QImageControl</div>

		This example shows off the <b>QImageControl</b> control.  It <b>REQUIRES</b> that GD be installed.  Moreover, for <strong>QImageControl</strong> support
		with JPEG, PNG and/or GIF images, it requires that GD be installed <em>with</em> those respective graphic file format libraries.
		<br/><br/>

		The <strong>QImageControl</strong> control is capable of scaling image files from anywhere in the filesystem (not just in docroot), and displaying it as a control.
		The <strong>Width</strong> and <strong>Height</strong> properties define the maximum size of the image canvas.  While the <strong>ScaleImageUp</strong>
		and <strong>ScaleCanvasDown</strong> properties are flags that act accordingly if the original image size is smaller or ends up being smaller than the
		canvas defined by <strong>Width</strong> and <strong>Height</strong>.
		<br/><br/>

		Similar to <strong>QImageLabel</strong>, a Cache Folder can be specified within the docroot to store rendered images, which will be used in the future
		if the same image file with the same specifications is rendered again.
		<br/><br/>

		Also, a <strong>BackColor</strong> can be defined for the canvas, itself. An <strong>ImageType</strong> can also be specified to "convert" the image type to a different
		type (e.g. JPEG, PNG or GIF).
		<br/><br/>

		Finally, note that any of <strong>Width</strong>, <strong>Height</strong> and <strong>ImageType</strong> can all be left blank, which would cause Qcodo to
		make the best educated guesses as to what to set them to at render time.
		<br/><br/>
		
		<b>Note:</b> Notice that <strong>QImageControl</strong> can be constructed outside of the QForm context, allowing you to call
		<strong>RenderImage($strDestinationFilePath)</strong> independently (outside of QForms/QControls), giving a
		nice, modular class to help with standard image rescaling for image files without the need of QForms (e.g. if you want
		perform batch or back-end operations to rescale whole directories of images, etc.).
	</div>

	<div style="background-color: #cde; padding: 4px 20px 4px 20px; height: 40px; width: 400px;">
	<div style="float: left;">
		<?php $this->txtWidth->RenderWithName('Width=50','MaxLength=3'); ?>
	</div>
	<div style="float: left; margin-left: 20px;">
		<?php $this->txtHeight->RenderWithName('Width=50','MaxLength=3'); ?>
	</div>
	<div style="float: left; margin-left: 20px;">
		<br/>
		<?php $this->chkScaleCanvasDown->Render(); ?>
	</div>
	<div style="float: left; margin-left: 20px;">
		<br/>
		<?php $this->btnUpdate->Render(); ?>
	</div>
	</div>

	<br clear="all"/>

	<?php $this->imgSample->Render(); ?>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>