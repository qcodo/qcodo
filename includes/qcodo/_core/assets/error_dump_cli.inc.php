<?php
	/**
	 * Qcodo Error Dump for CLI
	 */

	$strMessage = sprintf('%s in PHP CLI Script - %s', QErrorHandler::$Type, QApplication::$ScriptName);
	$intPadding = floor((QErrorHandler::$CliReportWidth - strlen($strMessage) - 1) / 2);
	printf("\r\n%s %s %s\r\n", str_repeat('=', $intPadding), $strMessage, str_repeat('=', $intPadding));
	print(QErrorHandler::$Message . "\r\n");
	printf("%s\r\n", str_repeat('-', QErrorHandler::$CliReportWidth));

	printf("   %9s Type: %s\r\n", QErrorHandler::$Type, QErrorHandler::$ObjectType);
	printf("      Source File: %s;  Line: %s\r\n", QErrorHandler::$Filename, QErrorHandler::$LineNumber);
	printf("     Version Info: PHP v%s;  Zend Engine v%s;  Qcodo v%s\r\n", PHP_VERSION, zend_version(), QCODO_VERSION);
	if (array_key_exists('OS', $_SERVER))	printf(" Operating System: %s\r\n", $_SERVER['OS']);
	if (array_key_exists('PWD', $_SERVER))	printf("Working Directory: %s\r\n", $_SERVER['PWD']);
	if (array_key_exists('USER', $_SERVER))	printf("      Run As User: %s\r\n", $_SERVER['USER']);
	foreach (QErrorHandler::$ErrorAttributeArray as $objErrorAttribute) if (!$objErrorAttribute->MultiLine) {
		printf("%17s: %s\r\n", $objErrorAttribute->Label, $objErrorAttribute->Content);
	}

	printf("\r\nCall Stack:\r\n    ");
	print(trim(str_replace("\n", "\n    ", QErrorHandler::$StackTrace)));
	print("\r\n\r\n");

	foreach (QErrorHandler::$ErrorAttributeArray as $objErrorAttribute) if ($objErrorAttribute->MultiLine) {
		printf("\r\n%s:\r\n    ", $objErrorAttribute->Label);
		print(trim(str_replace("\n", "\n    ", $objErrorAttribute->Content)));
		print("\r\n\r\n");
	}

	printf("%s Report Generated: %s\r\n", QErrorHandler::$Type, QErrorHandler::$DateTimeOfError);

	if (QErrorHandler::$FileNameOfError) {
		printf("   %s Report Logged: %s\r\n", QErrorHandler::$Type, QErrorHandler::$FileNameOfError);
	} else {
		printf("Report NOT Logged\r\n");
	}

	if (QErrorHandler::$FileNameOfError) { ?>
<!--qcodo--<error valid="true">
<type><?php print(QErrorHandler::$Type); ?></type>
<title><?php print(QErrorHandler::$Message); ?></title>
<datetime><?php print(QErrorHandler::$DateTimeOfError); ?></datetime>
<isoDateTime><?php print(QErrorHandler::$IsoDateTimeOfError); ?></isoDateTime>
<filename><?php print(QErrorHandler::$FileNameOfError); ?></filename>
<script>PHP CLI <?php print(QApplication::$ScriptName); ?></script>
<server><?php if (array_key_exists('PWD', $_SERVER)) print($_SERVER['PWD']); ?></server>
<agent><?php if (array_key_exists('USER', $_SERVER)) print($_SERVER['USER']); ?></agent>
</error>-->
<?php } ?>