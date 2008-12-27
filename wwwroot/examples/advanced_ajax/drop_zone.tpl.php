<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Defining Drop Zones</div>
		Move Handles also have definable "drop zones", which are basically <b>QPanel</b> or <b>QLabel</b>
		controls that are setup to receive a move handle's set of moveable controls.<br/><br/>
		
		Qcodo will calculate and reject the operation of any control trying to be moved into
		an area that is not defined as a valid drop zone.  By default, when any move handle is configured,
		it's parent is pre-defined as a valid drop zone.
		For all our previous examples, the move handle's parent was the form, which meant that the moveable
		controls were able to be dragged anywhere on the screen.<br/><br/>
		
		In this example, we setup <b>pnlPanel</b> to be a move handle and a moveable control, but we call
		<b>RemoveAllDropZones</b> to remove the entire from as a valid drop zone.  And then we call
		<b>AddDropZone</b> twice, one for each of the "drop zone" panels we created on the right.<br/><br/>
		
		Note how the interface now rejects any move of the moveable panel that doesn't end up in either
		of the drop zones.  Also note the feedback the interface gives to the user in terms of highlighting
		the drop zones and changing the mouse cursor.<br/><br/>
		
		Finally, also note that as the <b>pnlPanel</b> is moved from on drop zone to the next,
		the its <b>ParentControl</b> is actually being updated, as well, to being one of the two drop zone
		panels.
	</div>

	<?php $this->pnlPanel->Render('BackColor=#eeccff', 'Width=130', 'Height=50', 'Padding=10', 'BorderWidth=1'); ?>
	<?php $this->pnlDropZone1->Render('BackColor=#cccccc', 'Width=250', 'Height=150', 'Padding=10', 'BorderWidth=1'); ?>
	<?php $this->pnlDropZone2->Render('BackColor=#ccffee', 'Width=250', 'Height=150', 'Padding=10', 'BorderWidth=1'); ?>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>