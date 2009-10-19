<?php
	if (!defined('__PREPEND_INCLUDED__')) {
		// Ensure prepend.inc.php is only executed once
		define('__PREPEND_INCLUDED__', 1);


		/////////////////////////////////
		// Load in the Configuration File
		/////////////////////////////////
		/*
		 * configuration.inc.php should store any and all server-specific configuration
		 * settings and constants for your Qcodo-based application.
		 * 
		 * The following lines will
		 * 	1) Check to make sure configuration.inc.php exists in this directory
		 *  2) Load it in
		 *  3) Ensure that it is at least somewhat configured correctly
		 * 
		 * If you have not done so, be sure to copy either one of the following files:
		 * 	includes/qcodo/_core/configuration.inc.php-dist or
		 * 	includes/qcodo/_core/configuration.inc.php-full
		 * to includes/configuration.inc.php to set up your configuration constants file.
		 * 
		 * It is recommended that the configuration include file is in the same
		 * directory as this prepend include file.  But if you want to move the
		 * configuration file anywhere you want, be sure to provide a relative or
		 * absolute path to the file.
		 * 
		 * Finally, make sure you check and update configuration.inc.php and ensure
		 * all the constants are configured correctly.
		 */
		if (!file_exists(dirname(__FILE__) . '/configuration.inc.php'))
			exit('error: configuration.inc.php missing from includes/ directory; copy includes/qcodo/_core/configuration.inc.php-dist to the includes/ directory');
		require(dirname(__FILE__) . '/configuration.inc.php');
		if (realpath(__FILE__) != realpath(__INCLUDES__ . '/prepend.inc.php'))
			exit('error: __DOCROOT__ and/or __INCLUDES__ settings not valid in configuration.inc.php; update includes/configuration.inc.php with the correct settings');


		///////////////////////////
		// Load the Qcodo Framework
		///////////////////////////
		require(__QCODO_CORE__ . '/qcodo.inc.php');


		////////////////////////////////////
		// Initialize the QApplication Class
		////////////////////////////////////
		QApplication::Initialize();


		///////////////////////////////////////////////////////////////////////////////
		// Custom Global Functions, Other Include Files, and any Additional Setup Tasks
		///////////////////////////////////////////////////////////////////////////////

		/*
		 * In general, it iS *NOT* recommended that any other calls be put here in prepend.inc.php
		 * 
		 * If any custom defined functions, include files or setup is still required at this
		 * point, it is recommended that those calls be put in its own .inc.php include file, and that it
		 * be placed in the includes/auto_includes directory.  Files in there will be loaded/run in alphabetical
		 * order during QApplication::Initialize()
		 */
	}
?>