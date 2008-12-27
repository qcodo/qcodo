<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Other Client-Side Action Types</div>
		Below is a sampling of just <i>some</i> of the other <b>QAction</b> types that are available to you
		as part of the core Qcodo distribution.
		<br/><br/>
		
		Notice that all of these <b>QActions</b> simply render out javascript to perform the action,
		so the interaction the user experience is completely done on the client-side (e.g. no server/ajax calls here).
		<br/><br/>
		
		View the code for the details, and for more information or for a listing of <i>all</i> the <b>QActions</b> and <b>QEvents</b>, please
		see the <b>Documentation</b> section of the Qcodo website.
	</div>

	<style type="text/css">
		.panelHover {background-color: #eeeeff; border-color: #333399; border-style: solid; border-width: 1px; width: 400px; padding: 10px;}
		.panelHighlight {background-color: #ffeeee; border-color: #993333; cursor: pointer;}
	</style>
	
	<table>
		<tr>
			<td colspan="2"><b>Set the Focus / Select to the Textbox</b> (Note that Select only works on QTextBox)</td>
		</tr>
		<tr>
			<td style="width:250px;"><?php $this->btnFocus->Render(); ?> <?php $this->btnSelect->Render(); ?></td>
			<td><?php $this->txtFocus->Render(); ?></td>
		</tr>

		<tr>
			<td colspan="2"><br/><b>Set the Display on the Textbox</b></td>
		</tr>
		<tr>
			<td style="width:250px;"><?php $this->btnToggleDisplay->Render(); ?></td>
			<td><?php $this->txtDisplay->Render(); ?></td>
		</tr>

		<tr>
			<td colspan="2"><br/><b>Set the Enabled on the Textbox</b></td>
		</tr>
		<tr>
			<td style="width:250px;"><?php $this->btnToggleEnable->Render(); ?></td>
			<td><?php $this->txtEnable->Render(); ?></td>
		</tr>

	</table>


	<br/><br/>
	<?php $this->pnlHover->Render(); ?>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>