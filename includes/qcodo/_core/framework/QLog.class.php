<?php
	/**
	 * Class defines the static methods used to log information, alerts, etc.
	 */
	class QLog extends QBaseClass {
		/**
		 * The current log level (by default, defined in configuration.inc.php
		 * @var integer
		 */
		public static $MinimumLogLevel = QCODO_LOG_LEVEL;

		/**
		 * The extension to use for the log file
		 * @var string
		 */
		public static $Extension = '.log.txt';

		/**
		 * The maximum width (in character length) per line in the log file
		 * @var integer
		 */
		public static $LogFileWidth = 138;

		/**
		 * Whether or not to display Horizontal Dividers in the log file
		 * @var boolean
		 */
		public static $HorizontalDividers = false;

		/**
		 * This will log a message to the Qcodo Log.  Location of the log file is defined in __QCODO_LOG__
		 * 
		 * By default, this will log a "Normal" level log entry in the "default" Qcodo log file, which is
		 * located at __QCODO_LOG__/default.log.txt
		 * 
		 * Either parameter can be overridden.
		 * 
		 * @param string $strMessage
		 * @param integer $intLogLevel
		 * @param string $strLogModule
		 * @return void
		 */
		public static function Log($strMessage, $intLogLevel = QLogLevel::Normal, $strLogModule = 'default') {
			// Cancel out if log level is too low
			if ($intLogLevel > self::$MinimumLogLevel) return;

			// Setup Log Path
			if (!defined('__QCODO_LOG__')) throw new QCallerException('__QCODO_LOG__ must be defined before running QLog::Log');

			// Cancel out if log path is null
			if (!__QCODO_LOG__) return;

			// Create the Log Directory if it does NOT yet exist
			if (!is_dir(__QCODO_LOG__)) QApplication::MakeDirectory(__QCODO_LOG__, 0777);

			// Setup the Line
			$strLine = sprintf("%5s | %s | %s | %s\r\n",
				getmypid(),
				QLogLevel::$NameArray[$intLogLevel],
				QDateTime::Now()->NowToString(QDateTime::FormatIso),
				self::FormatMessage($strMessage));

			// Open the File for Writing
			$strLogFilePath = __QCODO_LOG__ . '/' . $strLogModule . self::$Extension;
			$objFile = fopen($strLogFilePath, 'a');

			// Write the Line
			fwrite($objFile, $strLine);
			fclose($objFile);
		}

		/**
		 * Used to log a Data Class object.
		 * 
		 * This can NOT be used to log a QForm or QControl object.
		 * 
		 * @param object $objObject
		 * @param integer $intLogLevel
		 * @param string $strLogModule
		 * @return void
		 */
		public static function LogObject($objObject, $intLogLevel = QLogLevel::Normal, $strLogModule = 'default') {
			if ($objObject instanceof QForm) {
				$objObject = unserialize(serialize($objObject));
				$strMessage = $objObject->PrepForVarExport(false);
			} else if ($objObject instanceof QControl) {
				$objObject = unserialize(serialize($objObject));
				$strMessage = $objObject->PrepForVarExport(false);
			}

			ob_start();
			var_dump($objObject);
			$strMessage = ob_get_clean();
			if (extension_loaded('xdebug')) $strMessage = strip_tags(html_entity_decode($strMessage));

			try {
				self::Log($strMessage, $intLogLevel, $strLogModule);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Internally used function to properly format (with linebreaks and indentation) the message
		 * @param string $strMessage
		 * @return string
		 */
		protected static function FormatMessage($strMessage) {
			$strMessage = trim($strMessage);
			$strMessage = str_replace("\r", "", $strMessage);

			$strToReturn = null;
			foreach (explode("\n", $strMessage) as $strSegment) {
				$strToReturn .= chunk_split($strSegment, self::$LogFileWidth - 37, "\n");
			}

			// Trim the last LF
			$strToReturn = substr($strToReturn, 0, strlen($strToReturn) - 1);

			// Create the Output
			$strToReturn = str_replace("\n", "\r\n" . str_repeat(' ', 35) . '| ', $strToReturn);

			// Add Horizontal Dividers (if applicable)
			if (self::$HorizontalDividers)
				$strToReturn .= "\r\n" . str_repeat('=', self::$LogFileWidth);

			return $strToReturn;
		}
	}

	abstract class QLogLevel extends QBaseClass {
		// These constants are can be used for QLog::$MinimumLogLevel
		// but should NOT be used for QLog::Log();
		const None		= 0;
		const All		= 6;

		// These constants can be used anywhere
		const Critical	= 1;
		const High		= 2;
		const Normal	= 3;
		const Low		= 4;
		const Info		= 5;

		public static $NameArray = array(
			QLogLevel::Critical	=> 'CRIT',
			QLogLevel::High		=> 'HIGH',
			QLogLevel::Normal	=> 'NORM',
			QLogLevel::Low		=> 'LOW ',
			QLogLevel::Info		=> 'INFO'
		);
	}