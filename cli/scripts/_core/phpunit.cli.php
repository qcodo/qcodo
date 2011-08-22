<?php
	set_include_path('.' . PATH_SEPARATOR . __QCODO_CORE__ . PATH_SEPARATOR . get_include_path());

//	require_once (__QCODO_CORE__ . '/PHPUnit/Util/Filter.php');
//	PHPUnit_Util_Filter::getInstance()->addFileToFilter(__FILE__, 'PHPUNIT');

	require_once (__QCODO_CORE__ . '/PHPUnit/Autoload.php');
	define('PHPUnit_MAIN_METHOD', 'PHPUnit_TextUI_Command::main');

	array_shift($_SERVER['argv']);
	PHPUnit_TextUI_Command::main();
?>