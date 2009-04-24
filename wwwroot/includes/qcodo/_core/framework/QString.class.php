<?php
	/**
	 * An abstract utility class to handle string manipulation.  All methods
	 * are statically available.
	 */
	abstract class QString {
		/**
		 * This faux constructor method throws a caller exception.
		 * The String object should never be instantiated, and this constructor
		 * override simply guarantees it.
		 *
		 * @return void
		 */
		public final function __construct() {
			throw new CallerException('String should never be instantiated.  All methods and variables are publically statically accessible.');
		}

		/**
		 * Returns the first character of a given string, or null if the given
		 * string is null.
		 * @param string $strString 
		 * @return string the first character, or null
		 */
		public final static function FirstCharacter($strString) {
			if (strlen($strString) > 0)
				return substr($strString, 0 , 1);
			else
				return null;
		}

		/**
		 * Returns the last character of a given string, or null if the given
		 * string is null.
		 * @param string $strString 
		 * @return string the last character, or null
		 */
		public final static function LastCharacter($strString) {
			$intLength = strlen($strString);
			if ($intLength > 0)
				return substr($strString, $intLength - 1);
			else
				return null;
		}

		/**
		 * Truncates the string to a given length, adding elipses (if needed).
		 * @param string $strString string to truncate
		 * @param integer $intMaxLength the maximum possible length of the string to return (including length of the elipse)
		 * @return string the full string or the truncated string with eplise
		 */
		public final static function Truncate($strText, $intMaxLength) {
			if (strlen($strText) > $intMaxLength)
				return substr($strText, 0, $intMaxLength - 3) . "...";
			else
				return $strText;
		}

		/**
		 * Escapes the string so that it can be safely used in as an Xml Node (basically, adding CDATA if needed)
		 * @param string $strString string to escape
		 * @return string the XML Node-safe String
		 */
		public final static function XmlEscape($strString) {
			if ((strpos($strString, '<') !== false) ||
				(strpos($strString, '&') !== false)) {
				$strString = str_replace(']]>', ']]]]><![CDATA[>', $strString);
				$strString = sprintf('<![CDATA[%s]]>', $strString);
			}

			return $strString;
		}

		/**
		 * Obfuscates an email so that it can be outputted as HTML to the page.
		 * @param string $strEmail the email address to obfuscate
		 * @return string the HTML of the obfuscated Email address
		 */
		public static function ObfuscateEmail($strEmail) {
			$strEmail = QApplication::HtmlEntities($strEmail);
			$strEmail = str_replace('@', '<strong style="display: none;">' . md5(microtime()) . '</strong>&#064;<strong style="display: none;">' . md5(microtime()) . '</strong>', $strEmail);
			$strEmail = str_replace('.', '<strong style="display: none;">' . md5(microtime()) . '</strong>&#046;<strong style="display: none;">' . md5(microtime()) . '</strong>', $strEmail);
			return $strEmail;
		}
	}
?>