<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Introduction to QPanel and QLabel</div>
		It may seem funny that we are "introducing" the <b>QPanel</b> and <b>QLabel</b> controls
		now, especially since we have already been using them a lot throughout the examples.<br/><br/>

		On the surface, it may seem that <b>QLabel</b> is very simple -- you specify the <b>Text</b>
		that you want it to display and maybe some styles around it, and then you can just <b>Render</b>
		it out.  And while <b>QLabel</b> and <b>QPanel</b> controls should certainly be used for 
		that purpose, they also offer a lot more in functionality.<br/><br/>

		Both the <b>QLabel</b> and <b>QPanel</b> controls extend from the <b>QBlockControl</b> class.
		The only difference between the two is that labels will render as a &lt;span&gt; and panels will render
		as a &lt;div&gt;.  And in fact, because in HTML there is very little difference between &lt;span&gt;
		and &lt;div&gt; anyway, it is safe to say that a <b>QLabel</b> with its <b>DisplayStyle</b> set to
		"block" will be equivalent to a <b>QPanel</b> with its <b>DisplayStyle</b> set to "inline".<br/><br/>
		
		In addition to defining the <b>Text</b> to display, these controls can also use a <b>Template</b> file.
		Moreover, these controls can also have any of its unrendered child controls auto-rendered out.  This offers
		a <i>lot</i> of power and flexibility, basically allowing you to render out an arbitrary number of dynamically
		created controls, without needing to hard code or specify these controls anywhere or on any template.<br/><br/>

		The order of rendering for block controls are:
		<ul>
		<li>Display the <b>Text</b> (if applicable)</li>
		<li>Pull in the <b>Template</b> and render it (if applicable)</li>
		<li>If <b>AutoRenderChildren</b> is set to true, then get all child controls and call <b>Render</b> on all of them
		that have not been rendered yet</li>
		</ul>
		
		In our example below, we define a <b>QPanel</b> and assign textboxes as child controls.  We specify
		a <b>Text</b> value and also setup a <b>Template</b>.  Finally, we render that entire panel out (complete
		with the text, template and child controls) with a single <b>Render</b> call.<br/><br/>
		
		Note that even though 10 textboxes are being rendered, we never explicitly code a <b>QTextBox->Render</b>
		call <i>anywhere</i> in our code.
	</div>

		<?php $this->pnlPanel->Render(); ?>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>