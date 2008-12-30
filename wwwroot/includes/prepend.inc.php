<?php
	if (!defined('__PREPEND_INCLUDED__')) {
		// Ensure prepend.inc is only executed once
		define('__PREPEND_INCLUDED__', 1);


		///////////////////////////////////
		// Define Server-specific constants
		///////////////////////////////////	
		/*
		 * This assumes that the configuration include file is in the same directory
		 * as this prepend include file.  For security reasons, you can feel free
		 * to move the configuration file anywhere you want.  But be sure to provide
		 * a relative or absolute path to the file.
		 */
		require(dirname(__FILE__) . '/configuration.inc.php');


		//////////////////////////////
		// Include the Qcodo Framework
		//////////////////////////////
		require(__QCODO_CORE__ . '/qcodo.inc.php');


		//////////////////////////////////////////////
		// Define and Initialize the Application Class
		//////////////////////////////////////////////
		require(__QCODO__ . '/QApplication.class.php');
		QApplication::Initialize();


		////////////////////////////////////////////////////////
		// Start Session Handler for Non-CLI Calls (if required)
		////////////////////////////////////////////////////////
		if (!QApplication::$CliMode) session_start();


		//////////////////////////
		// Custom Global Functions
		//////////////////////////	
		// NOTE: Define any custom global functions (if any) here...


		////////////////
		// Include Files
		////////////////
		// NOTE: Include any other include files (if any) here...


		/////////////////////////
		// Additioanl Setup Tasks
		/////////////////////////
		// NOTE: Include any other setup tasks (if any) here...

		// Setup Internationalization and Localization (if applicable)
		// QApplication::InitializeI18n();
	}
?>