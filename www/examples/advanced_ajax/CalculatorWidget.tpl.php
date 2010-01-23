<?php $_CONTROL->pnlValueDisplay->Render(); ?>
<br/>
<table>
	<tr>
		<td colspan="3"><?php $_CONTROL->btnUpdate->Render('CssClass=calculator_top_button'); ?> <?php $_CONTROL->btnCancel->Render('CssClass=calculator_top_button'); ?></td>
		<td><input type="button" value="/" class="calculator_button" <?php $_CONTROL->pxyOperationControl->RenderAsEvents('/'); ?>/></td>
	</tr>
	<tr>
		<td><input type="button" value="7" class="calculator_button" <?php $_CONTROL->pxyNumberControl->RenderAsEvents(7); ?>/></td>
		<td><input type="button" value="8" class="calculator_button" <?php $_CONTROL->pxyNumberControl->RenderAsEvents(8); ?>/></td>
		<td><input type="button" value="9" class="calculator_button" <?php $_CONTROL->pxyNumberControl->RenderAsEvents(9); ?>/></td>
		<td><input type="button" value="*" class="calculator_button" <?php $_CONTROL->pxyOperationControl->RenderAsEvents('*'); ?>/></td>
	</tr>
	<tr>
		<td><input type="button" value="4" class="calculator_button" <?php $_CONTROL->pxyNumberControl->RenderAsEvents(4); ?>/></td>
		<td><input type="button" value="5" class="calculator_button" <?php $_CONTROL->pxyNumberControl->RenderAsEvents(5); ?>/></td>
		<td><input type="button" value="6" class="calculator_button" <?php $_CONTROL->pxyNumberControl->RenderAsEvents(6); ?>/></td>
		<td><input type="button" value="-" class="calculator_button" <?php $_CONTROL->pxyOperationControl->RenderAsEvents('-'); ?>/></td>
	</tr>
	<tr>
		<td><input type="button" value="1" class="calculator_button" <?php $_CONTROL->pxyNumberControl->RenderAsEvents(1); ?>/></td>
		<td><input type="button" value="2" class="calculator_button" <?php $_CONTROL->pxyNumberControl->RenderAsEvents(2); ?>/></td>
		<td><input type="button" value="3" class="calculator_button" <?php $_CONTROL->pxyNumberControl->RenderAsEvents(3); ?>/></td>
		<td><input type="button" value="+" class="calculator_button" <?php $_CONTROL->pxyOperationControl->RenderAsEvents('+'); ?>/></td>
	</tr>
	<tr>
		<td><input type="button" value="0" class="calculator_button" <?php $_CONTROL->pxyNumberControl->RenderAsEvents(0); ?>/></td>
		<td><?php $_CONTROL->btnPoint->Render('CssClass=calculator_button'); ?></td>
		<td><?php $_CONTROL->btnClear->Render('CssClass=calculator_button'); ?></td>
		<td><?php $_CONTROL->btnEqual->Render('CssClass=calculator_button'); ?></td>
	</tr>
</table>