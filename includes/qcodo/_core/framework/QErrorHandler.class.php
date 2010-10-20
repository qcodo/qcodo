<?php
	/**
	 * Qcodo Error Handler
	 * 
	 * If we are in this class, we must assume that the application is in an unstable state.
	 * 
	 * Thus, we cannot depend on any other qcodo or application-based classes or objects
	 * to help with the error processing.
	 *
	 * Therefore, all classes and functionality for error handling must be defined in this class
	 * in order to minimize any dependency on the rest of the framework.
	 */
	class QErrorHandler {
		// Static Properties that should always be set on any error
		public static $Type;
		public static $Message;
		public static $ObjectType;
		public static $Filename;
		public static $LineNumber;
		public static $StackTrace;

		// Properties that are calculated based on the error information above
		public static $FileLinesArray;
		public static $MessageBody;

		// Static Properties that can be optionally set
		public static $RenderedPage;
		public static $ErrorAttributeArray = array();
		public static $AdditionalMessage;

		// Other Properties
		public static $DateTimeOfError;
		public static $FileNameOfError;
		public static $IsoDateTimeOfError;
		
		public static $CliReportWidth = 138;

		protected static function Run() {
			// Get the RenderedPage (if applicable)
			if (ob_get_length()) {
				QErrorHandler::$RenderedPage = ob_get_contents();
				ob_clean();
			}

			// Setup the FileLinesArray
			if (is_file(QErrorHandler::$Filename))
				QErrorHandler::$FileLinesArray = file(QErrorHandler::$Filename);
			else if (strpos(QErrorHandler::$Filename, 'eval()') !== false)
				QErrorHandler::$FileLinesArray = array('File listing unavailable; eval()\'d code');
			else
				QErrorHandler::$FileLinesArray = array('File Not Found: ' . QErrorHandler::$Filename);

			// Set up the MessageBody
			if (QErrorHandler::$AdditionalMessage)
				QErrorHandler::$MessageBody = htmlentities(QErrorHandler::$AdditionalMessage) . '<br/>' . htmlentities(QErrorHandler::$Message);
			else
				QErrorHandler::$MessageBody = htmlentities(QErrorHandler::$Message);
			QErrorHandler::$MessageBody = str_replace(" ", "&nbsp;", str_replace("\n", "<br/>\n", QErrorHandler::$MessageBody));
			QErrorHandler::$MessageBody = str_replace(":&nbsp;", ": ", QErrorHandler::$MessageBody);

			// Figure Out DateTime (and if we are logging, the filename of the error log)
			$strMicrotime = microtime();
			$strParts = explode(' ', $strMicrotime);
			$strMicrotime = substr($strParts[0], 2);
			$intTimestamp = $strParts[1];
			QErrorHandler::$DateTimeOfError = date('l, F j Y, g:i:s.' . $strMicrotime . ' A T', $intTimestamp);
			QErrorHandler::$IsoDateTimeOfError = date('Y-m-d H:i:s T', $intTimestamp);
			if (defined('__ERROR_LOG__') && __ERROR_LOG__ && defined('ERROR_LOG_FLAG') && ERROR_LOG_FLAG)
				QErrorHandler::$FileNameOfError = sprintf('qcodo_error_%s_%s.html', date('Y-m-d_His', $intTimestamp), $strMicrotime);

			// Cleanup
			unset($strMicrotime);
			unset($strParts);
			unset($strMicrotime);
			unset($intTimestamp);

			// Generate the Error Dump
			if (!ob_get_level()) ob_start();
			if (QApplication::$CliMode)
				require(__QCODO_CORE__ . '/assets/error_dump_cli.inc.php');
			else
				require(__QCODO_CORE__ . '/assets/error_dump.inc.php');
				
			// Do We Log???
			if (defined('__ERROR_LOG__') && __ERROR_LOG__ && defined('ERROR_LOG_FLAG') && ERROR_LOG_FLAG) {
				// Log to File in __ERROR_LOG__
				$strContents = ob_get_contents();

				QApplication::MakeDirectory(__ERROR_LOG__, 0777);
				$strFileName = sprintf('%s/%s', __ERROR_LOG__, QErrorHandler::$FileNameOfError);
				file_put_contents($strFileName, $strContents);
				@chmod($strFileName, 0666);
			}

			if (QApplication::$RequestMode == QRequestMode::Ajax) {
				if (defined('ERROR_FRIENDLY_AJAX_MESSAGE') && ERROR_FRIENDLY_AJAX_MESSAGE) {
					// Reset the Buffer
					while(ob_get_level()) ob_end_clean();
		
					// Setup the Friendly Response
					header('Content-Type: text/xml');
					$strToReturn = '<controls/><commands><command>alert("' . str_replace('"', '\\"', ERROR_FRIENDLY_AJAX_MESSAGE) . '");</command></commands>';
					if (QApplication::$EncodingType)
						printf("<?xml version=\"1.0\" encoding=\"%s\"?><response>%s</response>\r\n", QApplication::$EncodingType, $strToReturn);
					else
						printf("<?xml version=\"1.0\"?><response>%s</response>\r\n", $strToReturn);
					return false;
				}
			} else {
				if (!QApplication::$CliMode) header("HTTP/1.1 500 Internal Server Error");
				if (defined('ERROR_FRIENDLY_PAGE_PATH') && ERROR_FRIENDLY_PAGE_PATH && !QApplication::$CliMode) {
					// Reset the Buffer
					while(ob_get_level()) ob_end_clean();
					require(ERROR_FRIENDLY_PAGE_PATH);		
				}
			}

			exit();
		}



		public static function PrepDataForScript($strData) {
			$strData = str_replace("\\", "\\\\", $strData);
			$strData = str_replace("\n", "\\n", $strData);
			$strData = str_replace("\r", "\\r", $strData);
			$strData = str_replace("\"", "&quot;", $strData);
			$strData = str_ireplace("</script>", "&lt/script&gt", $strData);
			return $strData;
		}



		public static function HandleException(Exception $objException) {
			// If we still have access to QApplicationBase, set the error flag on the Application
			if (class_exists('QApplicationBase'))
				QApplicationBase::$ErrorFlag = true;
	
			// If we are currently dealing with reporting an error, don't go on
			if (QErrorHandler::$Type)
				return;
	
			// Setup the QErrorHandler Object
			QErrorHandler::$Type = 'Exception';
			$objReflection = new ReflectionObject($objException);
			QErrorHandler::$Message = $objException->getMessage();
			QErrorHandler::$ObjectType = $objReflection->getName();
			QErrorHandler::$Filename = $objException->getFile();
			QErrorHandler::$LineNumber = $objException->getLine();
			QErrorHandler::$StackTrace = trim($objException->getTraceAsString());
	
			// Special Setup for Database Exceptions
			if ($objException instanceof QDatabaseExceptionBase) {
				QErrorHandler::$ErrorAttributeArray[] = new QErrorAttribute('Database Error Number', $objException->ErrorNumber, false);
	
				if ($objException->Query) {
					QErrorHandler::$ErrorAttributeArray[] = new QErrorAttribute('Query', $objException->Query, true);
				}
			}
	
			// Sepcial Setup for DataBind Exceptions
			if ($objException instanceof QDataBindException) {
				if ($objException->Query) {
					QErrorHandler::$ErrorAttributeArray[] = new QErrorAttribute('Query', $objException->Query, true);
				}
			}

			QErrorHandler::Run();
		}



		public static function HandleError($intErrorNumber, $strErrorString, $strErrorFile, $intErrorLine) {
			// If a command is called with "@", then we should return
			if (error_reporting() == 0)
				return;
	
			// If we still have access to QApplicationBase, set the error flag on the Application
			if (class_exists('QApplicationBase'))
				QApplicationBase::$ErrorFlag = true;
	
			// If we are currently dealing with reporting an error, don't go on
			if (QErrorHandler::$Type)
				return;
	
			// Setup the QErrorHandler Object
			QErrorHandler::$Type = 'Error';
			QErrorHandler::$Message = $strErrorString;
			QErrorHandler::$Filename = $strErrorFile;
			QErrorHandler::$LineNumber = $intErrorLine;
			
			switch ($intErrorNumber) {
				case E_ERROR:
					QErrorHandler::$ObjectType = 'E_ERROR';
					break;
				case E_WARNING:
					QErrorHandler::$ObjectType = 'E_WARNING';
					break;
				case E_PARSE:
					QErrorHandler::$ObjectType = 'E_PARSE';
					break;
				case E_NOTICE:
					QErrorHandler::$ObjectType = 'E_NOTICE';
					break;
				case E_STRICT:
					QErrorHandler::$ObjectType = 'E_STRICT';
					break;
				case E_CORE_ERROR:
					QErrorHandler::$ObjectType = 'E_CORE_ERROR';
					break;
				case E_CORE_WARNING:
					QErrorHandler::$ObjectType = 'E_CORE_WARNING';
					break;
				case E_COMPILE_ERROR:
					QErrorHandler::$ObjectType = 'E_COMPILE_ERROR';
					break;
				case E_COMPILE_WARNING:
					QErrorHandler::$ObjectType = 'E_COMPILE_WARNING';
					break;
				case E_USER_ERROR:
					QErrorHandler::$ObjectType = 'E_USER_ERROR';
					break;
				case E_USER_WARNING:
					QErrorHandler::$ObjectType = 'E_USER_WARNING';
					break;
				case E_USER_NOTICE:
					QErrorHandler::$ObjectType = 'E_USER_NOTICE';
					break;
				case E_DEPRECATED:
					QErrorHandler::$ObjectType = 'E_DEPRECATED';
					break;
				case E_USER_DEPRECATED:
					QErrorHandler::$ObjectType = 'E_USER_DEPRECATED';
					break;
				case E_RECOVERABLE_ERROR:
					QErrorHandler::$ObjectType = 'E_RECOVERABLE_ERROR';
					break;
				default:
					QErrorHandler::$ObjectType = 'Unknown';
					break;
			}
	
			// Setup the Stack Trace
			QErrorHandler::$StackTrace = "";
			$objBackTrace = debug_backtrace();
			for ($intIndex = 0; $intIndex < count($objBackTrace); $intIndex++) {
				$objItem = $objBackTrace[$intIndex];
				
				$strKeyFile = (array_key_exists('file', $objItem)) ? $objItem['file'] : '';
				$strKeyLine = (array_key_exists('line', $objItem)) ? $objItem['line'] : '';
				$strKeyClass = (array_key_exists('class', $objItem)) ? $objItem['class'] : '';
				$strKeyType = (array_key_exists('type', $objItem)) ? $objItem['type'] : '';
				$strKeyFunction = (array_key_exists('function', $objItem)) ? $objItem['function'] : '';
				
				QErrorHandler::$StackTrace .= sprintf("#%s %s(%s): %s%s%s()\n",
					$intIndex,
					$strKeyFile,
					$strKeyLine,
					$strKeyClass,
					$strKeyType,
					$strKeyFunction);
			}

			QErrorHandler::Run();
		}
		
		/**
		 * A modified version of var_export to use var_dump via the output buffer, which
		 * can better handle recursive structures.
		 * @param mixed $mixData
		 * @param boolean $blnHtmlEntities
		 * @return string
		 */
		public static function VarExport($mixData, $blnHtmlEntities = true) {
			if (($mixData instanceof QForm) || ($mixData instanceof QControl))
				$mixData->PrepForVarExport();
			ob_start();
			var_dump($mixData);
			
			$strToReturn = ob_get_clean();

			if ($blnHtmlEntities) {
				if (!extension_loaded('xdebug')) $strToReturn = htmlentities($strToReturn);
			} else {
				if (extension_loaded('xdebug')) $strToReturn = strip_tags(html_entity_decode($strToReturn));
			}

			return $strToReturn;
		}
	}

	class QErrorAttribute {
		public $Label;
		public $Contents;
		public $MultiLine;

		public function __construct($strLabel, $strContents, $blnMultiLine) {
			$this->Label = $strLabel;
			$this->Contents = $strContents;
			$this->MultiLine = $blnMultiLine;
		}
	}
?>
