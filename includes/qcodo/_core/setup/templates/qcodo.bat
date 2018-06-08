@echo off

rem Qcodo CLI Runner Wrapper for Windows Batch Files
rem Copyright (c) 2005-2018, Quasidea Development, LLC

rem Update the following line to reflect the absolute path to the PHP CLI binary in your Windows system
set PHP_COMMAND=c:\php\php.exe

rem Calling bootstrap.php to setup the PHP/Qcodo environment and to run the requested script
%PHP_COMMAND% %~dp0\bootstrap\console.php %*
