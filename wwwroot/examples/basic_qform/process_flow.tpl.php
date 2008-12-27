<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Understanding the QForm Process Flow</div>

		First of all, don't adjust your screen. =)<br/><br/>

		The "Form_blah called" messages you see are
		showing up to illustrate how the <b>QForm</b> process flow works.<br/><br/>

		As we mentioned earlier, <b>QForm</b> objects are stateful, with the state persisting through
		all the user interactions (e.g. ServerActions, etc.).  But note that <b>QForm</b> objects are also
		event-driven.  This is why the we state that QForms is a "stateful, event-driven architecture
		for web-based forms."  On every execution of a <b>QForm</b>, the following actions happen:
		
		<ol>
		<li>The first thing the Form object does is internally determine if we are viewing this
		page fresh (e.g. not via a post back) or if we have actually posted back (e.g. via the
		triggering of a control's action which would post back to the server).</li>
		<li>If it is posted back, then it will retrieve the form's state from the <b>FormState</b>,
		which is a hidden form variable containing the serialized data for the actual Form instance.
		It will then go through all the controls and update their values according to the user-entered
		data submitted via the post, itself.</li>
		<li>Next, regardless if we're post back or not, the <b>Form_Run</b> method (if defined) will be
		triggered. Again, this will be run regardless if we're viewing the page fresh or if we've
		re-posted back to the page.</li>
		<li>Next, if we are viewing the page fresh (e.g. not via a post back), the <b>Form_Create</b>
		method (if defined) will be run (<b>Form_Create</b> is typically where you would define and
		instantiate your various <b>QForm</b> controls).  Otherwise, the <b>Form_Load</b> (if defined) will
		be run.</li>
		<li>Next, if we're posted back because of a <b>QServerAction</b> or <b>QAjaxAction</b> that points to a
		specific PHP method, then the following will happen:
		<ul>
		<li>First, if the control that triggered the event has its <b>CausesValidation</b> property set, then
		the form will go through validation.  The form will call <b>Validate()</b> on the relavent controls,
		and then it will call <b>Form_Validate</b> on itself.  (More information on validation can be seen in the upcoming Calculator examples.)</li>
		<li>Next, if validation runs successfully <b>or</b> if no validation is requested
		(because <b>CausesValidation</b> was set to false), then the PHP method that the action points to will be run.</li>
		</ul>
		So in this repeat of the "Hello World" example, when you click on <b>btnButton</b>, the <b>btnButton_Click</b> method
		will be excuted during this step.</li>
		<li>If defined, the <b>Form_PreRender</b> method will then be run.</li>
		<li>The HTML include template file is included (to render out the HTML).</li>
		<li>And finally, the <b>Form_Exit</b> (if defined) is run after the HTML has been completely outputted.</li>
		</ol>

		So, basically, a <b>QForm</b> can have any combination of the five following methods defined to help
		customize <b>QForm</b> and <b>QControl</b> processing:
		<ul>
			<li>Form_Run</li>
			<li>Form_Load</li>
			<li>Form_Create</li>
			<li>Form_Validate</li>
			<li>Form_PreRender</li>
			<li>Form_Exit</li>
		</ul>
	</div>

	<p><?php $this->lblMessage->Render(); ?></p>
	<p><?php $this->btnButton->Render(); ?></p>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>
