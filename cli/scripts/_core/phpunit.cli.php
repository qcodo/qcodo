<?php
	if (extension_loaded('xdebug')) {
	    ini_set('xdebug.show_exception_trace', 0);
	}

	set_include_path('.' . PATH_SEPARATOR . __QCODO_CORE__ . PATH_SEPARATOR . get_include_path());

	require_once 'PHPUnit/Util/Filter.php';
	PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

	require 'PHPUnit/TextUI/Command.php';
	define('PHPUnit_MAIN_METHOD', 'PHPUnit_TextUI_Command::main');
	array_shift($_SERVER['argv']);
	PHPUnit_TextUI_Command::main();
?>