<?php
	/* This includes library file is used by the qcodo_updater.cli and qcodo_updater.phpexe scripts
	 * to perform the Qcodo Update Utility functionality.
	 */

	// Call the CLI prepend.inc.php
	require('cli_prepend.inc.php');

	// Finally, load the QUpdateUtility class itself
	require(__QCODO_CORE__ . '/framework/QUpdateUtility.class.php');

	// Ensure that there are parameters
	if ($_SERVER['argc'] < 2)
		QUpdateUtility::PrintUpdaterInstructions();

	// Setup Parameter Defaults
	$strInteractionType = QUpdateUtility::Interactive;
	$blnQuietMode = false;

	for ($intIndex = 1; $intIndex < $_SERVER['argc']; $intIndex++) {
		$strArgument = strtolower($_SERVER['argv'][$intIndex]);

		if ($strArgument == '--quiet') {
			if ($intIndex == $_SERVER['argc'] - 1)
				QUpdateUtility::Error('No Qcodo Version was specified');
			$blnQuietMode = true;
		} else if (substr($strArgument, 0, strlen('--interaction')) == '--interaction') {
			$strArgument = substr($strArgument, strlen('--interaction='));
			switch ($strArgument) {
				case QUpdateUtility::Interactive:
					$strInteractionType = QUpdateUtility::Interactive;
					break;
				case QUpdateUtility::Rename:
					$strInteractionType = QUpdateUtility::Rename;
					break;
				case QUpdateUtility::Force:
					$strInteractionType = QUpdateUtility::Force;
					break;
				case QUpdateUtility::ReportOnly:
					$strInteractionType = QUpdateUtility::ReportOnly;
					break;
				default:
					QUpdateUtility::Error('Invalid Interaction Mode: ' . $strArgument);
					break;
			}

			if ($intIndex == $_SERVER['argc'] - 1)
				QUpdateUtility::Error('No Qcodo Version was specified');
		} else if ($strArgument == '--help') {
			QUpdateUtility::PrintUpdaterInstructions(true);
		} else {
			if (($intIndex != ($_SERVER['argc'] - 1)) ||
				(substr($strArgument, 0, 1) == '-'))
				QUpdateUtility::Error('Invalid Option/Argument: ' . $strArgument);
		}
	}

	if ($strInteractionType != QUpdateUtility::ReportOnly)
		printf("Qcodo Update Utility - Performing '%s' Update...\r\n", $strInteractionType);

	$strVersion = $_SERVER['argv'][$_SERVER['argc'] - 1];
	$objUpdateUtility = new QUpdateUtility($strVersion);
 	$objUpdateUtility->RunUpdater($strInteractionType, $blnQuietMode);
?>