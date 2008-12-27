<?php
	// This is the HTML template include file for intro.php
	// Here is where you specify any HTML that you want rendered in the form, and here
	// is where you can specify which controls you want rendered and where.
?>
<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>
	
	<div class="instructions">
		<div class="instruction_title">Dynamically Created Image Labels</div>
		The <b>QImageLabel</b> allows you to create dynamically generated images based on text strings.
		The image label can also take font, size, color and positioning attributes to allow you to
		add a great level of sophistication/visual polish to your web applications.<br/><br/>

		The control <i>requires</i> the <b>GD</b> library.  In order for fonts to render properly, you must provide
		either a <b>TrueType</b> (.ttf) file or a pair of <b>PostScript Type 1</b> (.pfb and .afm) typeface files.  Note that rendering <b>TrueType</b> 
		will require the <b>FreeType</b> library, and rendering <b>PostScript Type 1</b> will require the
		<b>T1Lib</b> library.  See the <b><a href="http://www.php.net/" class="bodyLink">PHP Documentation</a></b>
		for more information.  The typeface files can either be put in the current directory, or it can be placed in
		<b>/includes/qform/fonts</b>.<br/><br/>
		
		Note that events/actions can be defined on the image label, as we have defined a <b>QClickEvent</b> in our
		example <b>QImageLabel</b> below.<br/><br/>
		
		Finally, the <b>QImageLabel</b> provides a great deal of functionality to help layout the text string <i>within</i>
		the image itself.  The width/height and internal positioning of the image is determined by the following steps:
		<ol>
		<li>If no width/height is set, then calculate the bounding box.  Set the <b>Width</b> or <b>Height</b> to be
		the dimensions of the bounding box, plus the <b>PaddingWidth</b> or <b>PaddingHeight</b> (if specified).</li>
		<li>Otherwise, if an alignment is specified, set the internal X- or Y- coordinate of the text to match
		the requested alignment (e.g. left/center/right or top/middle/bottom)</li>
		<li>Otherwise, set the internal X- or Y- coordinate to be the explictly specified <b>XCoordinate</b> or
		<b>YCoordinate</b> value</li>
		</ol>
		
		In our example below, we left <b>Width</b> and <b>Height</b> unspecified, and we set the padding width
		and height at 10.
	</div>

	<p><?php $this->lblMessage->Render(); ?></p>

	<h3>Messages that this image will toggle between:</h3>
	<div>
		Message 1: <?php $this->txtMessage1->Render(); ?><br/>
		Message 2: <?php $this->txtMessage2->Render(); ?><br/><br/:>
	</div>

	<div>
		Selected Font: <?php $this->lstFont->Render(); ?>
	</div>
	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>