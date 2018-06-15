<?php
	/**
	 * Codegen Qcodo CLI file
	 * Part of the Qcodo Development Framework
	 * Copyright (c) 2005-2011, Quasidea Development, LLC
	 */

	// Execution timer
	$intStartTime = microtime();
	$intStartTime = explode(' ', $intStartTime);
	$intStartTime = $intStartTime[1] + $intStartTime[0];

	// Setup the Parameters for codegen
	$objParameters = new QCliParameterProcessor('codegen', 'Qcodo Code Generator v' . QCODO_VERSION);

	// Add new default parameter -- db index
	$objParameters->AddDefaultParameter('db_index', QCliParameterType::String, 'the DB Index name in configuration/database.php which is being codegenned');

	// Optional Parameters for Path to Codegen Settings
	$strDefaultPath = __APPLICATION__ . '/configuration/codegen/codegen.xml';
	$objParameters->AddNamedParameter('s', 'settings-path', QCliParameterType::Path, $strDefaultPath, 'path to the Codegen Settings XML file; defaults to ' . $strDefaultPath);
	$objParameters->Run();

	// Pull the Parameter Values
	$strSettingsXmlPath = $objParameters->GetValue('s');
	$strDbIndex = $objParameters->GetDefaultValue('db_index');

	define('__DATAGEN_CLASSES__', __APPLICATION__ . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'generated');
	define('__DATA_CLASSES__', __APPLICATION__ . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'Database');

	define('__DATAGEN_META_CONTROLS__', null);
	define('__DATA_META_CONTROLS__', null);

	define('__FORM_DRAFTS__', null);
	define('__PANEL_DRAFTS__', null);

	try {
		/////////////////////
		// Run Code Gen	
		QCodeGen::Run($strDbIndex, $strSettingsXmlPath);
		/////////////////////

		if ($strErrors = QCodeGen::$RootErrors) {
			printf("The following ROOT ERRORS were reported:\r\n%s\r\n\r\n", $strErrors);
		} else {
			printf("CodeGen settings (as evaluted from %s):\r\n%s\r\n\r\n", $_SERVER['argv'][1], QCodeGen::GetSettingsXml());
		}

		foreach (QCodeGen::$CodeGenArray as $objCodeGen) {
			printf("%s\r\n---------------------------------------------------------------------\r\n", $objCodeGen->GetTitle());
			printf("%s\r\n", $objCodeGen->GetReportLabel());
			printf("%s\r\n", $objCodeGen->GenerateAll());
			if ($strErrors = $objCodeGen->Errors)
				printf("The following errors were reported:\r\n%s\r\n", $strErrors);
			print("\r\n");
		}

		foreach (QCodeGen::GenerateAggregate() as $strMessage) {
			printf("%s\r\n\r\n", $strMessage);
		}

		$intEndTime = microtime();
		$intEndTime = explode(" ", $intEndTime);
		$intEndTime = $intEndTime[1] + $intEndTime[0];

		printf('Codegen took %ss', round($intEndTime - $intStartTime, 2));
		if (ini_get('max_execution_time')) printf(' (%ss maximum)', ini_get('max_execution_time'));
		print "\r\n";

		if (function_exists('memory_get_peak_usage')) {
			printf('Peak memory usage %s (%s maximum allocation)', QString::GetByteSize(memory_get_peak_usage(true)), ini_get('memory_limit'));
			print "\r\n";
		}
	} catch (Exception $objExc) {
		print 'error: ' . trim($objExc->getMessage()) . "\r\n";
		exit(1);
	}
