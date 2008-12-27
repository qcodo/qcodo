<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title">Moving Controls Between Panels</div>
		With the concept of a <b>QLabel</b> or <b>QPanel</b> being able to have an arbitrary
		number of child controls, we use this example to show how you can dynamically
		change a control's parent, to essentially "move" a control from one panel to the next.<br/><br/>
		
		The example below has two <b>QPanel</b> controls, as well as ten <b>QTextBox</b> controls
		who's parents are one of the panels.  The buttons have <b>QAjaxActions</b> which will
		move the textboxes back and forth between the panels, or remove the textbox altogether.<br/><br/>
		
		Again, note that we are not hard coding a <b>QTextBox->Render</b> <i>anywhere</i> in our code.  We
		are simply using the concept of <b>ParentControls</b> and using the two <b>QBlockControl</b> controls'
		<b>AutoRenderChildren</b> functionality to dynamically render the textboxes in the
		appropriate places.<br/><br/>
		
		Finally, notice that while we are doing this using AJAX-based actions, you can just as easily use
		Server-based actions as well.
	</div>

		<table cellspacing="0" cellpadding="5" border="0">
			<tr>
				<td valign="top"><?php $this->pnlLeft->Render(); ?></td>
				<td valign="top"><?php $this->pnlRight->Render(); ?></td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<br/><br/>
					<?php $this->btnMoveLeft->Render(); ?>
					<?php $this->btnMoveRight->Render(); ?><br/>
					<?php $this->btnDeleteLeft->Render(); ?>
				</td>
			</tr>
		</table>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>