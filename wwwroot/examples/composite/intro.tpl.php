<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>
	
	<div class="instructions">
		<div class="instruction_title">Creating a Control of Controls</div>

		Often times you will want to be able to combine a bunch of small controls into a larger control,
		also known as creating a <b>Composite Control</b>.  In addition to this composite control
		containing many smaller controls, the composite control would be able to define
		its own layout, as well as handling its own server- or ajax-based actions.<br/><br/>

		With a modularized set of smaller controls, layout, and events/actions, an architecture
		utilizing <b>Composite Controls</b> can see a lot of modularity and reuse for commonly
		used, more-complex interactions throughout your entire web application.<br/><br/>

		In this example, we will create a sample <b>SampleComposite</b> control, which (for lack
		of a better example) will contain a <b>QLabel</b> and two <b>QButtons</b>.  The control itself
		will contain an integer value, and the label will report what the integer value is.  The two buttons
		will be triggered by <b>QClickEvents</b> to increment or decrement that value.<br/><br/>
		
		Now, notice how even though we seem to have a lot of small controls on the page (e.g. 7 buttons, each
		with their own event handlers!), the actual form is quite simple, because we are using the
		<b>SampleComposite</b> control over and over again.

		Be sure and view the source of <b>SampleComposite.inc</b>, which of course will contain the code
		for the composite control which is doing the bulk of the work in this example.
	</div>

	<table border="0">
		<tr>
			<td><?php $this->objCounter1->Render(); ?></td>
			<td align="center" style="width:40px;font-weight: bold; font-size: 28px;">+</td>
			<td><?php $this->objCounter2->Render(); ?></td>
			<td align="center" style="width:40px;font-weight: bold; font-size: 28px;">+</td>
			<td><?php $this->objCounter3->Render(); ?></td>
		</tr>
	</table>

	<p>
		<?php $this->btnButton->Render(); ?><br/><br/>
		<?php $this->lblMessage->Render(); ?>
	</p>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>