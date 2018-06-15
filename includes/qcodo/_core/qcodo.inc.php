<?php
	/* Unless otherwise specified, all files in the Qcodo Development Framework
	 * are under the following copyright and licensing policies:
	 * 
	 * Qcodo Development Framework for PHP
	 * http://www.qcodo.com/
	 * 
	 * The Qcodo Development Framework is distributed by Quasidea Development, LLC
	 * under the terms of The MIT License.  More information can be found at
	 * http://www.opensource.org/licenses/mit-license.php
	 * 
	 * Copyright (c) 2001 - 2018, Quasidea Development, LLC
	 * 
	 * Permission is hereby granted, free of charge, to any person obtaining a copy of
	 * this software and associated documentation files (the "Software"), to deal in
	 * the Software without restriction, including without limitation the rights to
	 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
	 * of the Software, and to permit persons to whom the Software is furnished to do
	 * so, subject to the following conditions:
	 * 
	 * The above copyright notice and this permission notice shall be included in all
	 * copies or substantial portions of the Software.
	 * 
	 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	 * SOFTWARE.
	 */

	// Versioning Information
	define('QCODO_VERSION', '0.5.0');

	// PHP Minimum Version Supported
	define('QCODO_PHP_MIN_VERSION', '5.3.0');

	// PHP Minimum Version Check
	if (version_compare(PHP_VERSION, QCODO_PHP_MIN_VERSION, '<'))
		exit(sprintf('Error: Qcodo requires PHP %s or later (installed version is %s)', QCODO_PHP_MIN_VERSION, PHP_VERSION));

	// Preload Required Framework Classes
	require(__QCODO_CORE__ . '/framework/QBaseClass.class.php');
	require(__QCODO_CORE__ . '/framework/QExceptions.class.php');
	require(__QCODO_CORE__ . '/framework/QType.class.php');
	require(__QCODO_CORE__ . '/framework/QApplicationBase.class.php');
	require(__QCODO_CORE__ . '/framework/QErrorHandler.class.php');

	// Load the Core Database Class
	require(__QCODO_CORE__ . '/framework/QDatabaseBase.class.php');        

	// Define Other Classes to be Preloaded on QApplication::Initialize()
	QApplicationBase::$PreloadedClassFile['qdatetime'] = __QCODO_CORE__ . '/framework/QDateTime.class.php';
	QApplicationBase::$PreloadedClassFile['qq'] = __QCODO_CORE__ . '/framework/QQuery.class.php';
	QApplicationBase::$PreloadedClassFile['qqn'] = __APPLICATION__ . '/Models/Database/Generated/QQN.class.php';

	// Handlers
	QApplicationBase::$ClassFile['qconsolehandlerbase'] = __QCODO_CORE__ . '/handlers/QConsoleHandlerBase.class.php';
	QApplicationBase::$ClassFile['qwebservicehandlerbase'] = __QCODO_CORE__ . '/handlers/QWebServiceHandlerBase.class.php';
	QApplicationBase::$ClassFile['qhandlerbase'] = __QCODO_CORE__ . '/handlers/QHandlerBase.class.php';

	// Define ClassFile Locations for Framework Classes
	QApplicationBase::$ClassFile['qrssfeed'] = __QCODO_CORE__ . '/framework/QRssFeed.class.php';
	QApplicationBase::$ClassFile['qrssimage'] = __QCODO_CORE__ . '/framework/QRssFeed.class.php';
	QApplicationBase::$ClassFile['qrsscategory'] = __QCODO_CORE__ . '/framework/QRssFeed.class.php';
	QApplicationBase::$ClassFile['qrssitem'] = __QCODO_CORE__ . '/framework/QRssFeed.class.php';
	QApplicationBase::$ClassFile['qerrorlogviewer'] = __QCODO_CORE__ . '/framework/QErrorLogViewer.class.php';
	QApplicationBase::$ClassFile['qemailserver'] = __QCODO_CORE__ . '/framework/QEmailServer.class.php';
	QApplicationBase::$ClassFile['qemailmessage'] = __QCODO_CORE__ . '/framework/QEmailServer.class.php';
	QApplicationBase::$ClassFile['qmimetype'] = __QCODO_CORE__ . '/framework/QMimeType.class.php';
	QApplicationBase::$ClassFile['qdatetime'] = __QCODO_CORE__ . '/framework/QDateTime.class.php';
	QApplicationBase::$ClassFile['qstring'] = __QCODO_CORE__ . '/framework/QString.class.php';
	QApplicationBase::$ClassFile['qstack'] = __QCODO_CORE__ . '/framework/QStack.class.php';
	QApplicationBase::$ClassFile['qcryptography'] = __QCODO_CORE__ . '/framework/QCryptography.class.php';
	QApplicationBase::$ClassFile['qsoapservice'] = __QCODO_CORE__ . '/framework/QSoapService.class.php';
	QApplicationBase::$ClassFile['qi18n'] = __QCODO_CORE__ . '/framework/QI18n.class.php';
	QApplicationBase::$ClassFile['qqueryexpansion'] = __QCODO_CORE__ . '/framework/QQueryExpansion.class.php';
	QApplicationBase::$ClassFile['qconvertnotation'] = __QCODO__ . '/codegen/QConvertNotation.class.php';
	QApplicationBase::$ClassFile['qlexer'] = __QCODO_CORE__ . '/framework/QLexer.class.php';
	QApplicationBase::$ClassFile['qregex'] = __QCODO_CORE__ . '/framework/QRegex.class.php';
	QApplicationBase::$ClassFile['qcliparameterprocessor'] = __QCODO_CORE__ . '/framework/QCliParameterProcessor.class.php';
	QApplicationBase::$ClassFile['qlog'] = __QCODO_CORE__ . '/framework/QLog.class.php';

	QApplicationBase::$ClassFile['qcodegen'] = __QCODO__ . '/codegen/QCodeGen.class.php';
	QApplicationBase::$ClassFile['qdatagen'] = __QCODO_CORE__ . '/framework/QDataGen.class.php';

	QApplicationBase::$ClassFile['qcache'] = __QCODO_CORE__ . '/framework/QCache.class.php';
	QApplicationBase::$ClassFile['qdatetimespan'] = __QCODO_CORE__ . '/framework/QDateTimeSpan.class.php';

	QApplicationBase::$ClassFile['qpdodatabase'] = __QCODO_CORE__ . '/database/QPdoDatabase.class.php';
