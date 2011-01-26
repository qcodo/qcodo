<?php require(__INCLUDES__ . '/header.inc.php'); ?>
<?php $this->RenderBegin(); ?>

<div id="container">
	<div id="headerContainer">
		<div id="headerBorder">
			<div id="header">
				<div id="hleft">
					<span class="hsmall"><?php _t('Panel Drafts') ?></span><br/>
					<span class="hbig"><?php $this->pnlTitle->Render(); ?></span>
				</div>
				<div id="hright">
					<a href="<?php _p(__VIRTUAL_DIRECTORY__ . __FORM_DRAFTS__) ?>/index.php">&laquo; <?php _t('Go to "Form Drafts"'); ?></a>
				</div>
				<br class="clear"/>
			</div>
		</div>
	</div>

	<div id="content">
		<div id="dashboard">
			<div id="dleft">
				<strong><?php _t('Select a Class to View/Edit') ?></strong><br/>
				<br/>
				<?php $this->lstClassNames->Render(); ?>
				<?php $this->objDefaultWaitIcon->Render(); ?>
			</div>
			<div id="dright">
				<?php $this->pnlList->Render(); ?>
				<?php $this->pnlEdit->Render(); ?>
			</div>
		</div>
	</div>
</div>
<?php $this->RenderEnd(); ?>
<?php require(__INCLUDES__ . '/footer.inc.php'); ?>