<?php
	// First, let's make sure that path_to_prepend.txt exists
	$strPathToPrependTextFile = dirname(__FILE__) . '/path_to_prepend.txt';
	if (!is_file($strPathToPrependTextFile))
		exit("No path_to_prepend.txt file was found.\r\nPlease be sure to specify the absolute path to prepend.inc.php in the ./path_to_prepend.txt file!\r\n");

	// Next, use the absolute path found in path_to_prepend.txt
	$strPathToPrepend = trim(file_get_contents($strPathToPrependTextFile));

	if (!is_dir($strPathToPrepend))
		exit("The text value found in the ./path_to_prepend.txt file does not appear to be a valid directory.\r\nPlease be sre to specify the correct absolute path to prepend.inc.php in the ./path_to_prepend.txt file!\r\n");

	// If it exists, require() it -- otherwise, report the error
	if (file_exists($strPathToPrepend . '/prepend.inc.php'))
		require($strPathToPrepend . '/prepend.inc.php');
	else
		exit("The prepend.inc.php file was not found at $strPathToPrepend.\r\nPlease be sre to specify the correct absolute path to prepend.inc.php in the ./path_to_prepend.txt file!\r\n");

	// Finally, verify that __DEVTOOLS_CLI__ is configured correctly
	if (!is_file(__DEVTOOLS_CLI__ . '/' . basename(__FILE__)))
		exit("The __DEVTOOLS_CLI__ configuration constant in configuration.inc.php does not appear to be set correctly.\r\n");

	$objStat1 = stat(__DEVTOOLS_CLI__ . '/' . basename(__FILE__));
	$objStat2 = stat(__FILE__);
	if ($objStat1['ino'] != $objStat2['ino'])
		exit("The __DEVTOOLS_CLI__ configuration constant in configuration.inc.php does not appear to be set correctly [INode Mismatch].\r\n");

	// Finally, turn off output buffering
	ob_end_flush();
?>