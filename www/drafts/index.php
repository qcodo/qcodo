<?php
	// Include prepend.inc to load Qcodo
	require(dirname(__FILE__) . '/../../includes/prepend.inc.php');

	// Security check for ALLOW_REMOTE_ADMIN
	// To allow access REGARDLESS of ALLOW_REMOTE_ADMIN, simply remove the line below
	QApplication::CheckRemoteAdmin();

	// Iterate through the files in this "form_drafts" folder, looking for files
	// that end in _edit.php or _list.php
	$strSuffixes = array('_edit.php', '_list.php');
	$strObjectArray = array();
	$objDirectory = new DirectoryIterator(dirname(__FILE__));
	
	foreach ($objDirectory as $objFileinfo) {
		$strFilename = $objFileinfo->getFilename();
		if ( !$objFileinfo->IsDir() ) {
			$blnFound = false;
			// strip the suffix (if applicable)
			foreach ($strSuffixes as $strSuffix) {
				if ((!$blnFound) && 
					(substr($strFilename, strlen($strFilename) - strlen($strSuffix)) == $strSuffix)) {
					$strFilename = substr($strFilename, 0, strlen($strFilename) - strlen($strSuffix));
					$blnFound = true;
				}
			}

			if ($blnFound)
				$strObjectArray[$strFilename] = true;
		}
	}

	// Sort the list of objects
	ksort($strObjectArray);

	$strPageTitle = QApplication::Translate('List of Form Drafts');
	require(__INCLUDES__ . '/header.inc.php');
?>

<div id="container">
	<div id="headerContainer">
		<div id="headerBorder">
			<div id="header">
				<div id="hleft">
					<span class="hsmall"><?php _t('Form Drafts') ?></span><br/>
					<span class="hbig"><?php _t('List of Form Drafts') ?></span>
				</div>
				<div id="hright">
					<a href="<?php _p(__VIRTUAL_DIRECTORY__ . __PANEL_DRAFTS__) ?>/index.php">&laquo; <?php _t('Go to "Panel Drafts"'); ?></a>
				</div>
				<br class="clear"/>
			</div>
		</div>
	</div>

	<div id="content">
<?php
		foreach ($strObjectArray as $strObject=>$blnValue) {
			printf('<div class="title">%s</div><p class="buttons"><a href="%s/%s_list.php">%s</a>&nbsp;&nbsp;<a href="%s/%s_edit.php">%s</a></p>',
				$strObject, __VIRTUAL_DIRECTORY__ . __FORM_DRAFTS__, $strObject, QApplication::Translate('View List'),
				__VIRTUAL_DIRECTORY__ . __FORM_DRAFTS__, $strObject, QApplication::Translate('Create New'));
		}
?>
	</div>
</div>
	
<?php require (__INCLUDES__ . '/footer.inc.php'); ?>