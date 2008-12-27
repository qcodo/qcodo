<?php
	// This is the HTML template include file for intro.php
	// Here is where you specify any HTML that you want rendered in the form, and here
	// is where you can specify which controls you want rendered and where.
?>
<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<div class="instruction_title"><?php _t('Internationalization and Translation') ?></div>
—
		<?php _t('
		Qcodo offers internationalization support via <b>QApplication::Translate()</b> (which internally
		will use the <b>QI18n</b> class).  Language and country
		settings can be setup in <b>prepend.inc.php</b>.  By default, Qcodo will check the session to determine
		which language and country is currently being used, but it is really up to the developer to
		determine how you want the language and country codes get discovered (e.g., via the URL, via
		GET arguments, etc.)'); ?>
		<br/><br/>

		<?php _t('Language files are in the GNU PO format (see'); ?>
		<a href="http://www.gnu.org/software/gettext/manual/html_node/gettext_9.html" class="bodyLink">http://www.gnu.org/software/gettext/manual/html_node/gettext_9.html</a>
		<?php _t('for more information), and are placed in the <b>/includes/qcodo/i18n</b> directory.'); ?>
		<br/><br/>

		<?php _t('
		To translate any piece of text, simply use <b>QApplication::Translate(xxx)</b>.  Or as a shortcut,
		if you want to do a PHP <b>print()</b> of any translated text in your template, you can use
		the qcodo printing shortcut <b>_t(xxx)</b> -- this does the equivalent of
		<b>print(QApplication::Translate(xxx))</b>.'); ?>
		<br/><br/>

		<?php _t('
		Note that generated Form Drafts and the QControls are all I18n aware -- they will translate themselves
		based on the selected language (as long as the appropriate language file exists).  Qcodo-specific
		langauge files are part of Qcodo core, and exist in <b>/includes/qcodo/_core/i18n</b>.  <b>Please Note:</b>
		we are desparately in need of more language files.  If you are able to contribute, please take
		the current en.po file and translate it to any currently unsupported language and feel free to
		submit it.  Also note that the Spanish translation (es.po) language files (both in the example
		and in Qcodo core) need to be corrected.'); ?>
		<br/><br/>

		<?php _t('
		Finally, due to the heavy processing of PO parsing, the results of the PO parsing are cached
		using QCache, and cached files are stored in <b>/includes/qcodo/cache/i18n</b>.'); ?>
	</div>

	<h2><?php _t('Internationalization Example'); ?></h2>

	<div>
		<?php _t('Current Language'); ?>: 
		<b><?php _p(QApplication::$LanguageCode ? QApplication::$LanguageCode : 'none'); ?></b>
		<br/><br/>
	
		<?php $this->btnEn->Render('Text="' . QApplication::Translate('Switch to') . ' en"'); ?>
		<?php $this->btnEs->Render('Text="' . QApplication::Translate('Switch to') . ' es"'); ?>
		<br/><br/>
		
		<?php _t('To view the People form draft translated into the selected language, go to'); ?>
		<a href="<?php _p(__VIRTUAL_DIRECTORY__ . __FORM_DRAFTS__); ?>/person_list.php" class="bodyLink"><?php _p(__VIRTUAL_DIRECTORY__ . __FORM_DRAFTS__); ?>/person_list.php</a>
	</div>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>