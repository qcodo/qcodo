<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<style type="text/css">
		.image_canvas {border-width: 4px; border-style: solid; border-color: #a9f;}
	</style>

	<div class="instructions">
		<div class="instruction_title">What time is it?</div>

		Qcodo includes several different QControls that assist with user input of dates and datetimes.
		<br/><br/>

		<strong>Note that beginning with Qcodo 0.3.39</strong>, the original <strong>QCalendar</strong> class
		(which popped up a calendar in a new window) has been renamed <strong>QCalendarPopup</strong>.
		<br/><br/>

		<b>QDateTimePicker</b> is the "default" control, in that the templates for MetaControls for tables with date
		or datetime columns will, by default, generate <b>QDateTimePicker</b> instances.  While not "sexy" or glamourous by
		any stretch of the imagination, it offers an immense amount of utility, in that it allows for very distinct
		control over date, time and datetime components.  By contrast, the DHTML-based <b>QCalendar</b> control offers, by definition,
		no support for any time-based component.
		<br/><br/>
		
		<b>QDateTimeTextBox</b> allows for textbox-based input of date and datetime values, utilizing QDateTime's constructor
		to parse a wide number of date and datetime formats.
		<br/><br/>
		
		And finally, <b>QCalendar</b> and <b>QCalendarPopup</b> are visual calendar controls to assist with date picking.  As the naming
		suggestions, <b>QCalendarPopup</b> will popup a calendar in a new window, while <b>QCalendar</b> is a DHTML-based calendar widget
		which <strong>requires</strong> a "linked" QDateTimeTextbox control.
	</div>

	<div style="background-color: #cde; padding: 8px; height: 40px; width: 400px; border: 1px solid #68a; margin-bottom: 12px;">
		<?php $this->lblResult->Render('HtmlEntities=false'); ?>
	</div>

	<div style="float: left;">
		<strong>QDateTimeTextBox</strong><br/>
		<?php $this->dtxDateTimeTextBox->Render(); ?><br/>
		<?php $this->btnDateTimeTextBox->Render(); ?>
	</div>
	<div style="float: left; margin-left: 45px;">
		<strong>QCalendar</strong> (DHTML)<br/>
		<?php $this->dtxCalendar->Render(); ?>
		<?php $this->calCalendar->Render(); ?><br/>
		<?php $this->btnCalendar->Render(); ?>
	</div>
	<div style="float: left; margin-left: 45px;">
		<strong>QCalendarPopup</strong> (in a new window)<br/>
		<?php $this->calCalendarPopup->Render(); ?><br/>
		<?php $this->btnCalendarPopup->Render(); ?>
	</div>
	<br clear="all"/>
	<br clear="all"/>
	<div style="float: left;">
		<strong>QDateTimePicker</strong> (Date only)<br/>
		<?php $this->dtpDatePicker->Render(); ?>
		<?php $this->btnDatePicker->Render(); ?>
	</div>
	<div style="float: left; margin-left: 45px;">
		<strong>QDateTimePicker</strong> (Date and Time)<br/>
		<?php $this->dtpDateTimePicker->Render(); ?>
		<?php $this->btnDateTimePicker->Render(); ?>
	</div>
	<br clear="all"/>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>