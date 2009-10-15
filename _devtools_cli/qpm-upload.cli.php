<?php
	// Setup the Parameters for qpm-upload
	$objParameters = new QCliParameterProcessor('qpm-upload', 'Qcodo Package Manager (QPM) Uploader Tool v' . QCODO_VERSION);

	// Package Name is always required
	$objParameters->AddDefaultParameter('package_name', QCliParameterType::String, 'the name of the QPM package you are wanting to upload');

	// Optional Parameters include Username, Password, "Live" mode, and "Force" upload
	$objParameters->AddNamedParameter('u', 'user', QCliParameterType::String, null, 'the qcodo.com username to use, or if not specified, it will use the information stored in the QPM Settings file');
	$objParameters->AddNamedParameter('p', 'password', QCliParameterType::String, null, 'the qcodo.com password to use, or if not specified, it will use the information stored in the QPM Settings file');
	$objParameters->AddFlagParameter('l', 'live', 'actually perform the live upload - by default, calling qpm-upload will only *report* to you files that will be uploaded; specify the "live" flag to actually perform the upload');
	$objParameters->AddFlagParameter('f', 'force', 'force the upload, even if the most recent Qcodo version is more recent than what is currently installed here');
	$objParameters->AddNamedParameter('s', 'settings-path', QCliParameterType::Path, null, 'path to the QPM Settings XML file; defaults to ' . __DEVTOOLS_CLI__ . '/settings_qpm.xml');
	$objParameters->Run();

	// Pull the Parameter Values
	$strPackageName = $objParameters->GetDefaultValue('package_name');
	$blnLive = $objParameters->GetValue('l');
	$blnForce = $objParameters->GetValue('f');
	$strUsername = $objParameters->GetValue('u');
	$strPassword = $objParameters->GetValue('p');
	$strSettingsFilePath = $objParameters->GetValue('s');

	try {
		$objQpm = new QPackageManager($strPackageName, $strUsername, $strPassword, $blnLive, $blnForce, $strSettingsFilePath);
		$objQpm->PerformUpload();
	} catch (Exception $objExc) {
		print 'error: ' . trim($objExc->getMessage()) . "\r\n";
		exit(1);
	}
?>