<?php
	/* This includes library file is used by the qcodo_downloader.cli and qcodo_downloader.phpexe scripts
	 * to perform the Qcodo Update Utility's File Downloading functionality.
	 */

	// Call the CLI prepend.inc.php
	require('cli_prepend.inc.php');

	// Finally, load the QUpdateUtility class itself
	require(__QCODO_CORE__ . '/framework/QUpdateUtility.class.php');

	// Ensure that there are parameters
	if ($_SERVER['argc'] != 5)
		QUpdateUtility::PrintDownloaderInstructions();

	$strVersion = trim(strtolower($_SERVER['argv'][1]));
	if (($strVersion == 'stable') || ($strVersion == 'development'))
		QUpdateUtility::Error('Invalid Version format: ' . $strVersion);

	$objUpdateUtility = new QUpdateUtility($strVersion);
	$objUpdateUtility->RunDownloader($_SERVER['argv'][2], $_SERVER['argv'][3], $_SERVER['argv'][4]);
?>