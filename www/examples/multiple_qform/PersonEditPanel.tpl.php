		<div class="title_action"><?php _p($_CONTROL->strTitleVerb); ?></div>
		<div class="title"><?php _t('Person')?></div>
		<br class="item_divider" />

		<?php $_CONTROL->lblId->RenderWithName(); ?>
		<br class="item_divider" />

		<?php $_CONTROL->txtFirstName->RenderWithName(); ?>
		<br class="item_divider" />

		<?php $_CONTROL->txtLastName->RenderWithName(); ?>
		<br class="item_divider" />

		<?php $_CONTROL->lstLogin->RenderWithName(); ?>
		<br class="item_divider" />

		<?php $_CONTROL->lstProjectsAsTeamMember->RenderWithName(true, "Rows=10"); ?>
		<br class="item_divider" />


		<br />
		<?php $_CONTROL->btnSave->Render() ?>
		&nbsp;&nbsp;&nbsp;
		<?php $_CONTROL->btnCancel->Render() ?>
