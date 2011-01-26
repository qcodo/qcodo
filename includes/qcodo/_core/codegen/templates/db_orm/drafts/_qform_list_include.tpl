<template OverwriteFlag="true" DocrootFlag="true" DirectorySuffix="" TargetDirectory="<%= __FORM_DRAFTS__ %>" TargetFileName="<%= QConvertNotation::UnderscoreFromCamelCase($objTable->ClassName) %>_list.tpl.php"/>
<?php
	// This is the HTML template include file (.tpl.php) for the <%= QConvertNotation::UnderscoreFromCamelCase($objTable->ClassName) %>_list.php
	// form DRAFT page.  Remember that this is a DRAFT.  It is MEANT to be altered/modified.

	// Be sure to move this out of this directory before modifying to ensure that subsequent 
	// code re-generations do not overwrite your changes.

	$strPageTitle = QApplication::Translate('<%= $objTable->ClassNamePlural %>') . ' - ' . QApplication::Translate('List All');
	require(__INCLUDES__ . '/header.inc.php');
?>

	<?php $this->RenderBegin() ?>
	
	<div id="container">
		<div id="headerContainer">
			<div id="headerBorder">
				<div id="header">
					<div id="hleft">
						<span class="hsmall"><?php _t('List All'); ?></span><br/>
						<span class="hbig"><?php _t('<%= $objTable->ClassNamePlural %>'); ?></span>
					</div>
					<div id="hright">
						<a href="<?php _p(__VIRTUAL_DIRECTORY__ . __FORM_DRAFTS__) ?>/index.php">&laquo; <?php _t('Go to "Form Drafts"'); ?></a>
					</div>
					<br class="clear"/>
				</div>
			</div>
		</div>
		
		<div id="content">

	<?php $this->dtg<%= $objTable->ClassNamePlural %>->Render(); ?>

			<div id="formActions">
				<p class="buttons">
					<a href="<?php _p(__VIRTUAL_DIRECTORY__ . __FORM_DRAFTS__) ?>/<%= QConvertNotation::UnderscoreFromCamelCase($objTable->ClassName) %>_edit.php"><?php _t('Create a New'); ?> <?php _t('<%= $objTable->ClassName %>');?></a>			
				</p>
			</div>
		</div>
	</div>
	
	<?php $this->RenderEnd() ?>
	
<?php require(__INCLUDES__ . '/footer.inc.php'); ?>