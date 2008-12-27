<?php
	/* This includes library file is used by the codegen.cli and codegen.phpexe scripts
	 * to simply fire up and run the QCodeGen object, itself.
	 */

	// Call the CLI prepend.inc.php
	require('cli_prepend.inc.php');

	// Include the QCodeGen class library
	require(__QCODO__. '/codegen/QCodeGen.class.php');

	function PrintInstructions() {
		global $strCommandName;
		print('Qcodo Code Generator (Command Line Interface) - ' . QCODO_VERSION . '
Copyright (c) 2001 - 2007, QuasIdea Development, LLC
This program is free software with ABSOLUTELY NO WARRANTY; you may
redistribute it under the terms of The MIT License.

Usage: ' . $strCommandName . ' CODEGEN_SETTINGS

Where CODEGEN_SETTINGS is the absolute filepath of the codegen_settings.xml
file, containing the code generator settings.

For more information, please go to www.qcodo.com
');
		exit();
	}

	if ($_SERVER['argc'] != 2)
		PrintInstructions();

	/////////////////////
	// Run Code Gen	
	QCodeGen::Run($_SERVER['argv'][1]);
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
?>