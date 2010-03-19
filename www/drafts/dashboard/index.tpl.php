<?php require(__INCLUDES__ . '/header.inc.php'); ?>
<?php $this->RenderBegin(); ?>

	<div id="titleBar">
		<h2 id="right"><a href="<?php _p(__VIRTUAL_DIRECTORY__ . __FORM_DRAFTS__) ?>/index.php">&laquo; <?php _t('Go to "Form Drafts"'); ?></a></h2>
		<h2><?php _t('Panel Drafts') ?></h2>
		<h1><?php $this->pnlTitle->Render(); ?></h1>
	</div>

	<div id="dashboard">
		<div id="left">
			<p><strong><?php _t('Select a Class to View/Edit') ?></strong></p>
			<p><?php $this->lstClassNames->Render('FontSize=10px','Width=100px'); ?></p>
			<p><?php $this->objDefaultWaitIcon->Render(); ?></p>
		</div>
		<div id="right">
			<?php $this->pnlList->Render(); ?>
			<?php $this->pnlEdit->Render(); ?>
		</div>
	</div>
	
	<div id="draftList">
		<p>&nbsp;</p>
		<h1><?php _t('Form Drafts') ?></h1>
		<p class="create"><a href="<?php _p(__VIRTUAL_DIRECTORY__ . __FORM_DRAFTS__) ?>"><?php _t('Go to "Form Drafts"') ?></a></p>
	</div>
	
<?php $this->RenderEnd(); ?>
<?php require(__INCLUDES__ . '/footer.inc.php'); ?>
