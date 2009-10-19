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
		 * If you have not done so, be sure to copy either one of the following files:
		 * 	includes/qcodo_core/configuration.inc.php-dist or
		 * 	includes/qcodo_core/configuration.inc.php-full
		 * to includes/configuration.inc.php to set up your configuration constants file.
		 * 
		 * It is recommended that the configuration include file is in the same
		 * directory as this prepend include file.  But if you want to move the
		 * configuration file anywhere you want, be sure to provide a relative or
		 * absolute path to the file.
		 */
		require(dirname(__FILE__) . '/configuration.inc.php');


		///////////////////////////
		// Load the Qcodo Framework
		///////////////////////////
		require(__QCODO_CORE__ . '/qcodo.inc.php');


		///////////////////////////////////////////////
		// Define and Initialize the QApplication Class
		///////////////////////////////////////////////
		require(__INCLUDES__ . '/QApplication.class.php');
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