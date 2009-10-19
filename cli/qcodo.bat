@echo off

rem Qcodo CLI Runner Wrapper for Windows Batch Files
rem Copyright (c) 2005-2009, Quasidea Development, LLC

rem Update the following line to reflect the absolute path to the PHP CLI binary in your Windows system
set PHP_COMMAND=c:\php\php.exe

rem Calling qcodo_cli.inc.php to setup the PHP/Qcodo environment and to run the requested script
rem For more information, please refer to the comments in qcodo_cli.inc.php
%PHP_COMMAND% %~dp0\qcodo_cli.inc.php %*
