<?php
	// This is the HTML template include file for intro.php
	// Here is where you specify any HTML that you want rendered in the form, and here
	// is where you can specify which controls you want rendered and where.
?>
<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>
	
	<div class="instructions">
		<div class="instruction_title">Learning the Basics</div>
		Welcome to your first <b>QForm</b>!  This example shows how you can create a few
		<b>QControl</b> objects (in this case, a <b>QLabel</b> and a <b>QButton</b>) and set their text
		inside.  It also assigns a <b>QClickEvent</b> on the button to a <b>QServerAction</b>.  
		This server action (which is a PHP method) will simply modify the label to say
		"Hello, World!".<br/><br/>

		All <b>QForm</b> objects use an HTML include file -- in thise case, we define the HTML in
		the <b>intro.tpl.php</b> file.  Note that there are <b>RenderBegin()</b> and <b>RenderEnd()</b>
		methods which are required to be called within the template in order to output the
		appropriate &lt;form&gt; tags, and also outputs any additional HTML and JavaScript
		that makes the <b>QForm</b> work.  (Qcodo will in fact throw an exception
		if either <b>RenderBegin</b> and <b>RenderEnd</b> are not called.)<br/><br/>
		
		Click on the "View Source" link in the upper righthand corner to view the
		<b>intro.php</b> and <b>intro.tpl.php</b> code, which together define this <b>QForm</b>
		you are seeing.
	</div>

	<h2>Hello World Example</h2>

	<p><?php $this->lblMessage->Render(); ?></p>
	<p><?php $this->btnButton->Render(); ?></p>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>