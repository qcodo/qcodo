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
	 * Copyright (c) 2001 - 2007, Quasidea Development, LLC
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
	define('QCODO_VERSION', '0.3.43 (Qcodo Beta 3)');

	// Preload Required Framework Classes
	require(__QCODO_CORE__ . '/framework/QBaseClass.class.php');
	require(__QCODO_CORE__ . '/framework/QExceptions.class.php');
	require(__QCODO_CORE__ . '/framework/QType.class.php');
	require(__QCODO_CORE__ . '/framework/QApplicationBase.class.php');

	// Setup the Error Handler
	require(__QCODO_CORE__ . '/error.inc.php');

	// Setup the Autoloader
	function __autoload($strClassName) {
		QApplication::Autoload($strClassName);
	}

	// Start Output Buffering	
	function __ob_callback($strBuffer) {
		return QApplication::OutputPage($strBuffer);
	}
	ob_start('__ob_callback');

	// Qcodo Signature
	header(sprintf('X-Powered-By: PHP/%s; Qcodo/%s', PHP_VERSION, QCODO_VERSION));

	// Preload Other Framework Classes
	require(__QCODO_CORE__ . '/framework/QDatabaseBase.class.php');
	if (version_compare(PHP_VERSION, '5.2.0', '<'))
		// Use the Legacy (Pre-5.2.0) QDateTime class
		require(__QCODO_CORE__ . '/framework/QDateTime.legacy.class.php');
	else
		// Use the New QDateTime class (which extends PHP DateTime)
		require(__QCODO_CORE__ . '/framework/QDateTime.class.php');

	// Define Classes to be Preloaded on QApplication::Initialize()
	QApplicationBase::$PreloadedClassFile['_enumerations'] = __QCODO_CORE__ . '/qform/_enumerations.inc.php';
	QApplicationBase::$PreloadedClassFile['qcontrolbase'] = __QCODO_CORE__ . '/qform/QControlBase.class.php';
	QApplicationBase::$PreloadedClassFile['qcontrol'] = __QCODO__ . '/qform/QControl.class.php';
	QApplicationBase::$PreloadedClassFile['qformbase'] = __QCODO_CORE__ . '/qform/QFormBase.class.php';
	QApplicationBase::$PreloadedClassFile['qform'] = __QCODO__ . '/qform/QForm.class.php';
	QApplicationBase::$PreloadedClassFile['_actions'] = __QCODO_CORE__ . '/qform/_actions.inc.php';
	QApplicationBase::$PreloadedClassFile['_events'] = __QCODO_CORE__ . '/qform/_events.inc.php';
	QApplicationBase::$PreloadedClassFile['qq'] = __QCODO_CORE__ . '/framework/QQuery.class.php';

	// Define ClassFile Locations for FormState Handlers
	QApplicationBase::$ClassFile['qformstatehandler'] = __QCODO_CORE__ . '/qform_state_handlers/QFormStateHandler.class.php';
	QApplicationBase::$ClassFile['qsessionformstatehandler'] = __QCODO_CORE__ . '/qform_state_handlers/QSessionFormStateHandler.class.php';
	QApplicationBase::$ClassFile['qfileformstatehandler'] = __QCODO_CORE__ . '/qform_state_handlers/QFileFormStateHandler.class.php';

	// Define ClassFile Locations for Framework Classes
	QApplicationBase::$ClassFile['qrssfeed'] = __QCODO_CORE__ . '/framework/QRssFeed.class.php';
	QApplicationBase::$ClassFile['qrssimage'] = __QCODO_CORE__ . '/framework/QRssFeed.class.php';
	QApplicationBase::$ClassFile['qrsscategory'] = __QCODO_CORE__ . '/framework/QRssFeed.class.php';
	QApplicationBase::$ClassFile['qrssitem'] = __QCODO_CORE__ . '/framework/QRssFeed.class.php';
	QApplicationBase::$ClassFile['qemailserver'] = __QCODO_CORE__ . '/framework/QEmailServer.class.php';
	QApplicationBase::$ClassFile['qemailmessage'] = __QCODO_CORE__ . '/framework/QEmailServer.class.php';
	QApplicationBase::$ClassFile['qmimetype'] = __QCODO_CORE__ . '/framework/QMimeType.class.php';
	QApplicationBase::$ClassFile['qdatetime'] = __QCODO_CORE__ . '/framework/QDateTime.class.php';
	QApplicationBase::$ClassFile['qstring'] = __QCODO_CORE__ . '/framework/QString.class.php';
	QApplicationBase::$ClassFile['qstack'] = __QCODO_CORE__ . '/framework/QStack.class.php';
	QApplicationBase::$ClassFile['qcryptography'] = __QCODO_CORE__ . '/framework/QCryptography.class.php';
	QApplicationBase::$ClassFile['qsoapservice'] = __QCODO_CORE__ . '/framework/QSoapService.class.php';
	QApplicationBase::$ClassFile['qi18n'] = __QCODO_CORE__ . '/framework/QI18n.class.php';
	QApplicationBase::$ClassFile['qqn'] = __DATAGEN_CLASSES__ . '/QQN.class.php';
	QApplicationBase::$ClassFile['qqueryexpansion'] = __QCODO_CORE__ . '/framework/QQueryExpansion.class.php';
	QApplicationBase::$ClassFile['qconvertnotation'] = __QCODO__ . '/codegen/QConvertNotation.class.php';
	QApplicationBase::$ClassFile['qlexer'] = __QCODO_CORE__ . '/framework/QLexer.class.php';
	QApplicationBase::$ClassFile['qregex'] = __QCODO_CORE__ . '/framework/QRegex.class.php';

	QApplicationBase::$ClassFile['qcache'] = __QCODO_CORE__ . '/framework/QCache.class.php';
	QApplicationBase::$ClassFile['qdatetimespan'] = __QCODO_CORE__ . '/framework/QDateTimeSpan.class.php';

	// Define ClassFile Locations for Qform Classes
	QApplicationBase::$ClassFile['qfontfamily'] = __QCODO_CORE__ . '/qform/QFontFamily.class.php';

	QApplicationBase::$ClassFile['qcalendar'] = __QCODO_CORE__ . '/qform/QCalendar.class.php';
	QApplicationBase::$ClassFile['qcalendarpopup'] = __QCODO_CORE__ . '/qform/QCalendarPopup.class.php';
	QApplicationBase::$ClassFile['qdatetimepicker'] = __QCODO_CORE__ . '/qform/QDateTimePicker.class.php';
	QApplicationBase::$ClassFile['qdatetimetextbox'] = __QCODO_CORE__ . '/qform/QDateTimeTextBox.class.php';

	QApplicationBase::$ClassFile['qcheckbox'] = __QCODO_CORE__ . '/qform/QCheckBox.class.php';
	QApplicationBase::$ClassFile['qfilecontrol'] = __QCODO_CORE__ . '/qform/QFileControl.class.php';
	QApplicationBase::$ClassFile['qradiobutton'] = __QCODO_CORE__ . '/qform/QRadioButton.class.php';

	QApplicationBase::$ClassFile['qblockcontrol'] = __QCODO_CORE__ . '/qform/QBlockControl.class.php';
	QApplicationBase::$ClassFile['qlabel'] = __QCODO_CORE__ . '/qform/QLabel.class.php';
	QApplicationBase::$ClassFile['qpanel'] = __QCODO_CORE__ . '/qform/QPanel.class.php';
	QApplicationBase::$ClassFile['qcontrolproxy'] = __QCODO_CORE__ . '/qform/QControlProxy.class.php';
	QApplicationBase::$ClassFile['qdialogbox'] = __QCODO_CORE__ . '/qform/QDialogBox.class.php';

	QApplicationBase::$ClassFile['qimagebase'] = __QCODO_CORE__ . '/qform/QImageBase.class.php';
	QApplicationBase::$ClassFile['qimagelabelbase'] = __QCODO_CORE__ . '/qform/QImageLabelBase.class.php';
	QApplicationBase::$ClassFile['qimagelabel'] = __QCODO__ . '/qform/QImageLabel.class.php';
	QApplicationBase::$ClassFile['qimagecontrolbase'] = __QCODO_CORE__ . '/qform/QImageControlBase.class.php';
	QApplicationBase::$ClassFile['qimagecontrol'] = __QCODO__ . '/qform/QImageControl.class.php';
	QApplicationBase::$ClassFile['qimagerollover'] = __QCODO_CORE__ . '/qform/QImageRollover.class.php';

	QApplicationBase::$ClassFile['qfileasset'] = __QCODO__ . '/qform/QFileAsset.class.php';
	QApplicationBase::$ClassFile['qfileassetbase'] = __QCODO_CORE__ . '/qform/QFileAssetBase.class.php';
	QApplicationBase::$ClassFile['qfileassetdialog'] = __QCODO_CORE__ . '/qform/QFileAssetDialog.class.php';

	QApplicationBase::$ClassFile['qcontrollabel'] = __QCODO_CORE__ . '/qform/QControlLabel.class.php';

	QApplicationBase::$ClassFile['qactioncontrol'] = __QCODO_CORE__ . '/qform/QActionControl.class.php';
	QApplicationBase::$ClassFile['qbuttonbase'] = __QCODO_CORE__ . '/qform/QButtonBase.class.php';
	QApplicationBase::$ClassFile['qbutton'] = __QCODO__ . '/qform/QButton.class.php';
	QApplicationBase::$ClassFile['qimagebutton'] = __QCODO_CORE__ . '/qform/QImageButton.class.php';
	QApplicationBase::$ClassFile['qlinkbutton'] = __QCODO_CORE__ . '/qform/QLinkButton.class.php';

	QApplicationBase::$ClassFile['qlistcontrol'] = __QCODO_CORE__ . '/qform/QListControl.class.php';
	QApplicationBase::$ClassFile['qlistitem'] = __QCODO_CORE__ . '/qform/QListItem.class.php';
	QApplicationBase::$ClassFile['qlistboxbase'] = __QCODO_CORE__ . '/qform/QListBoxBase.class.php';
	QApplicationBase::$ClassFile['qlistbox'] = __QCODO__ . '/qform/QListBox.class.php';
	QApplicationBase::$ClassFile['qlistitemstyle'] = __QCODO_CORE__ . '/qform/QListItemStyle.class.php';
	QApplicationBase::$ClassFile['qcheckboxlist'] = __QCODO_CORE__ . '/qform/QCheckBoxList.class.php';
	QApplicationBase::$ClassFile['qradiobuttonlist'] = __QCODO_CORE__ . '/qform/QRadioButtonList.class.php';
	QApplicationBase::$ClassFile['qtreenav'] = __QCODO_CORE__ . '/qform/QTreeNav.class.php';
	QApplicationBase::$ClassFile['qtreenavitem'] = __QCODO_CORE__ . '/qform/QTreeNavItem.class.php';

	QApplicationBase::$ClassFile['qtextboxbase'] = __QCODO_CORE__ . '/qform/QTextBoxBase.class.php';
	QApplicationBase::$ClassFile['qtextbox'] = __QCODO__ . '/qform/QTextBox.class.php';
	QApplicationBase::$ClassFile['qfloattextbox'] = __QCODO_CORE__ . '/qform/QFloatTextBox.class.php';
	QApplicationBase::$ClassFile['qintegertextbox'] = __QCODO_CORE__ . '/qform/QIntegerTextBox.class.php';
	QApplicationBase::$ClassFile['qwritebox'] = __QCODO_CORE__ . '/qform/QWriteBox.class.php';

	QApplicationBase::$ClassFile['qpaginatedcontrol'] = __QCODO_CORE__ . '/qform/QPaginatedControl.class.php';
	QApplicationBase::$ClassFile['qpaginatorbase'] = __QCODO_CORE__ . '/qform/QPaginatorBase.class.php';
	QApplicationBase::$ClassFile['qpaginator'] = __QCODO__ . '/qform/QPaginator.class.php';

	QApplicationBase::$ClassFile['qdatagridbase'] = __QCODO_CORE__ . '/qform/QDataGridBase.class.php';
	QApplicationBase::$ClassFile['qdatagridcolumn'] = __QCODO_CORE__ . '/qform/QDataGridColumn.class.php';
	QApplicationBase::$ClassFile['qdatagridrowstyle'] = __QCODO_CORE__ . '/qform/QDataGridRowStyle.class.php';
	QApplicationBase::$ClassFile['qdatagrid'] = __QCODO__ . '/qform/QDataGrid.class.php';

	QApplicationBase::$ClassFile['qdatarepeater'] = __QCODO_CORE__ . '/qform/QDataRepeater.class.php';

	QApplicationBase::$ClassFile['qwaiticon'] = __QCODO_CORE__ . '/qform/QWaitIcon.class.php';
	QApplicationBase::$ClassFile['qcontrolgrouping'] = __QCODO_CORE__ . '/qform/QControlGrouping.class.php';
	QApplicationBase::$ClassFile['qdropzonegrouping'] = __QCODO_CORE__ . '/qform/QDropZoneGrouping.class.php';

	if (__DATAGEN_CLASSES__) {
		@include(__DATAGEN_CLASSES__ . '/_class_paths.inc.php');
		@include(__DATAGEN_CLASSES__ . '/_type_class_paths.inc.php');
	}

	// Special Print Functions / Shortcuts
	// NOTE: These are simply meant to be shortcuts to actual Qcodo functional
	// calls to make your templates a little easier to read.  By no means do you have to
	// use them.  Your templates can just as easily make the fully-named method/function calls.
		/**
		 * Standard Print function.  To aid with possible cross-scripting vulnerabilities,
		 * this will automatically perform QApplication::HtmlEntities() unless otherwise specified.
		 *
		 * @param string $strString string value to print
		 * @param boolean $blnHtmlEntities perform HTML escaping on the string first
		 */
		function _p($strString, $blnHtmlEntities = true) {
			// Standard Print
			if ($blnHtmlEntities && (gettype($strString) != 'object'))
				print(QApplication::HtmlEntities($strString));
			else
				print($strString);
		}

		/**
		 * Standard Print as Block function.  To aid with possible cross-scripting vulnerabilities,
		 * this will automatically perform QApplication::HtmlEntities() unless otherwise specified.
		 * 
		 * Difference between _b() and _p() is that _b() will convert any linebreaks to <br/> tags.
		 * This allows _b() to print any "block" of text that will have linebreaks in standard HTML.
		 *
		 * @param string $strString
		 * @param boolean $blnHtmlEntities
		 */
		function _b($strString, $blnHtmlEntities = true) {
			// Text Block Print
			if ($blnHtmlEntities && (gettype($strString) != 'object'))
				print(nl2br(QApplication::HtmlEntities($strString)));
			else
				print(nl2br($strString));
		}

		/**
		 * Standard Print-Translated function.  Note: Because translation typically
		 * occurs on coded text strings, NO HTML ESCAPING will be performed on the string.
		 * 
		 * Uses QApplication::Translate() to perform the translation (if applicable)
		 *
		 * @param string $strString string value to print via translation
		 */
		function _t($strString) {
			// Print, via Translation (if applicable)
			print(QApplication::Translate($strString));
		}

		function _i($intNumber) {
			// Not Yet Implemented
			// Print Integer with Localized Formatting
		}

		function _f($intNumber) {
			// Not Yet Implemented
			// Print Float with Localized Formatting
		}

		function _c($strString) {
			// Not Yet Implemented
			// Print Currency with Localized Formatting
		}
	//////////////////////////////////////
?>