<?php
	class QCliParameterProcessor extends QBaseClass {
		protected $chrShortIdentifierArray = array();
		protected $strLongIdentifierArray = array();

		protected $mixValueArray = array();
		protected $strHelpTextArray = array();
		protected $intParameterTypeArray = array();
		protected $blnFlagArray = array();

		protected $chrShortIdentifierByIndex = array();
		protected $strLongIdentifierByIndex = array();

		protected $strDefaultIdentifierArray = array();
		protected $mixDefaultValueArray = array();
		protected $intDefaultParameterTypeArray = array();
		protected $strDefaultHelpTextArray = array();

		protected $strQcodoCliCommand;
		protected $strHelpTextHeadline;



		/**
		 * @param string $strQcodoCliCommand
		 * @param string $strHelpTextHeadline
		 * @return QCliParameterProcessor
		 */
		public function __construct($strQcodoCliCommand, $strHelpTextHeadline) {
			$this->strQcodoCliCommand = $strQcodoCliCommand;
			$this->strHelpTextHeadline = $strHelpTextHeadline;
		}

		/**
		 * Returns the "Help Text" based on the way this QCliParameterProcessor is set up.
		 * @return string
		 */
		public function GetHelpText() {
			$strToReturn = $this->strHelpTextHeadline . "\r\n";
			$strToReturn .= 'usage:  qcodo ' . $this->strQcodoCliCommand . ' ';
			if (count($this->mixValueArray)) $strToReturn .= '[OPTIONS] ';
			if (count($this->strDefaultIdentifierArray)) $strToReturn .= implode(' ', $this->strDefaultIdentifierArray);
			$strToReturn .= "\r\n\r\n";


			$intMaxIdentifierLength = 16;
			$strPadding = str_repeat(' ', $intMaxIdentifierLength+4);
			$intHelpTextWidth = 120-$intMaxIdentifierLength-4;

			if (count($this->strDefaultIdentifierArray)) {
				$strToReturn .= "required parameters:\r\n";

				// Update MaxIdLength calculation
				foreach ($this->strDefaultIdentifierArray as $strDefaultIdentifier) {
					if (strlen($strDefaultIdentifier) > $intMaxIdentifierLength) $intMaxIdentifierLength = strlen($strDefaultIdentifier);
				}
				$strPadding = str_repeat(' ', $intMaxIdentifierLength+4);
				$intHelpTextWidth = 78-$intMaxIdentifierLength-4;

				foreach ($this->strDefaultIdentifierArray as $intIndex => $strDefaultIdentifier) {
					$strToReturn .= sprintf("  %-" . $intMaxIdentifierLength . "s  %s\r\n",
						$strDefaultIdentifier, QCliParameterProcessor::RenderHelpText($this->strDefaultHelpTextArray[$intIndex], $intHelpTextWidth, $strPadding));
				}
				
				$strToReturn .= "\r\n";
			}

			if (count($this->mixValueArray)) {
				$strToReturn .= "optional parameters:\r\n";

				foreach ($this->mixValueArray as $intIndex => $mixValue) {
					// First, figure out the formal label for the "identifier"
					$strIdentifier = '';
					if (array_key_exists($intIndex, $this->chrShortIdentifierByIndex))
						$strIdentifier .= '-' . $this->chrShortIdentifierByIndex[$intIndex];
					if (array_key_exists($intIndex, $this->strLongIdentifierByIndex)) {
						if ($strIdentifier) $strIdentifier .= ', ';
						$strIdentifier .= '--' . $this->strLongIdentifierByIndex[$intIndex];
					}

					if (array_key_exists($intIndex, $this->intParameterTypeArray))
						$strIdentifier .= '=' . QCliParameterType::$NameArray[$this->intParameterTypeArray[$intIndex]];

					if (!($strHelpText = $this->strHelpTextArray[$intIndex])) {
						$strToReturn .= '  ' . $strIdentifier . "\r\n";
					} else {
						$strHelpText = QCliParameterProcessor::RenderHelpText($strHelpText, $intHelpTextWidth, $strPadding);
						if (strlen($strIdentifier) > $intMaxIdentifierLength)
							$strToReturn .= sprintf("  %s\r\n%s%s\r\n", $strIdentifier, $strPadding, $strHelpText);
						else
							$strToReturn .= sprintf("  %-" . $intMaxIdentifierLength . "s  %s\r\n", $strIdentifier, $strHelpText);
					}
				}

				$strToReturn .= "\r\n";
			}
			return $strToReturn;
		}

		/**
		 * Given a help text, the max width for that help text, and the amount of left-side padding any subsequent line gets,
		 * it will returned the rendered help text with the spacing and linebreaks.
		 * @param string $strHelpText
		 * @param integer $intMaxWidth
		 * @param string $strPadding
		 * @return string
		 */
		public static function RenderHelpText($strHelpText, $intMaxWidth, $strPadding) {
			$strHelpText = wordwrap(trim($strHelpText), $intMaxWidth, "\r\n", true);
			$strHelpText = str_replace("\r\n", "\r\n" . $strPadding, $strHelpText);
			return $strHelpText;
		}

		/**
		 * Adds a CLI Flag parameter to process.  Values are false by default, but can be set to true if the flag is set in the argv.
		 * Examples include things like "--verbose" or "-v", etc.
		 * @param string $chrShortIdentifier
		 * @param string $strLongIdentifier
		 * @param string $strHelpText
		 * @return void
		 */
		public function AddFlagParameter($chrShortIdentifier, $strLongIdentifier, $strHelpText) {
			// Cleanup the Identifiers, and throw in invalid
			try {
				$chrShortIdentifier = QCliParameterProcessor::CleanShortIdentifier($chrShortIdentifier);
				$strLongIdentifier = QCliParameterProcessor::CleanLongIdentifier($strLongIdentifier);
			} catch (QInvalidCastException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Ensure at least one identifier is requested
			if (!$chrShortIdentifier && !$strLongIdentifier)
				throw new QCallerException('No identifiers were specified');

			// Ensure Identifiers are not already in use
			if ($chrShortIdentifier && array_key_exists($chrShortIdentifier, $this->chrShortIdentifierArray))
				throw new QCallerException('Short Identifier already in use: ' . $chrShortIdentifier);
			if ($strLongIdentifier && array_key_exists($strLongIdentifier, $this->strLongIdentifierArray))
				throw new QCallerException('Long Identifier already in use: ' . $strLongIdentifier);

			// Get the ValueIndex for this flag, and set the value to false
			$intIndex = count($this->mixValueArray);
			$this->mixValueArray[$intIndex] = false;
			$this->blnFlagArray[$intIndex] = true;
			$this->strHelpTextArray[$intIndex] = $strHelpText;

			// Set the Identifiers to this ValueIndex
			if ($chrShortIdentifier) {
				$this->chrShortIdentifierArray[$chrShortIdentifier] = $intIndex;
				$this->chrShortIdentifierByIndex[$intIndex] = $chrShortIdentifier;
			}
			if ($strLongIdentifier) {
				$this->strLongIdentifierArray[$strLongIdentifier] = $intIndex;
				$this->strLongIdentifierByIndex[$intIndex] = $strLongIdentifier;
			}
		}

		/**
		 * Adds a CLI Named parameter to process.  Default values can be specified.
		 * Named parameters in CLI calls MUST have values associated with them.  CLI calls can be typically:
		 * 	-i foobar
		 * 	-i=foobar
		 *  -ifoobar
		 *  --identifier foobar
		 *  --identifier=foobar
		 * @param string $chrShortIdentifier
		 * @param string $strLongIdentifier
		 * @param QCliParameterType $intCliParameterType
		 * @param mixed $mixDefaultValue
		 * @param string $strHelpText
		 * @return void
		 */
		public function AddNamedParameter($chrShortIdentifier, $strLongIdentifier, $intCliParameterType, $mixDefaultValue, $strHelpText) {
			// Cleanup the Identifiers, and throw in invalid
			try {
				$chrShortIdentifier = QCliParameterProcessor::CleanShortIdentifier($chrShortIdentifier);
				$strLongIdentifier = QCliParameterProcessor::CleanLongIdentifier($strLongIdentifier);
			} catch (QInvalidCastException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Ensure at least one identifier is requested
			if (!$chrShortIdentifier && !$strLongIdentifier)
				throw new QCallerException('No identifiers were specified');

			// Ensure Identifiers are not already in use
			if ($chrShortIdentifier && array_key_exists($chrShortIdentifier, $this->chrShortIdentifierArray))
				throw new QCallerException('Short Identifier already in use: ' . $chrShortIdentifier);
			if ($strLongIdentifier && array_key_exists($strLongIdentifier, $this->strLongIdentifierArray))
				throw new QCallerException('Long Identifier already in use: ' . $strLongIdentifier);

			// Get the ValueIndex for this flag, and set the value to false
			$intIndex = count($this->mixValueArray);
			$this->mixValueArray[$intIndex] = $mixDefaultValue;
			$this->intParameterTypeArray[$intIndex] = $intCliParameterType;
			$this->strHelpTextArray[$intIndex] = $strHelpText;

			// Set the Identifiers to this ValueIndex
			if ($chrShortIdentifier) {
				$this->chrShortIdentifierArray[$chrShortIdentifier] = $intIndex;
				$this->chrShortIdentifierByIndex[$intIndex] = $chrShortIdentifier;
			}
			if ($strLongIdentifier) {
				$this->strLongIdentifierArray[$strLongIdentifier] = $intIndex;
				$this->strLongIdentifierByIndex[$intIndex] = $strLongIdentifier;
			}
		}

		/**
		 * Adds a default parameter for this CLI call.  DefaultIdentifier will be alphanumeric with underscores in all caps.
		 * Because default parameters are required, there is no default value to specify.
		 * Note that since defualt parameters MUST be passed in, there is no short or long (-x or --xxx) identifiers associated with them.
		 * The identifier specified is simply for internal use.  Processing of default identifiers are done in the order they are added
		 * to the class.  So for example, if default identifiers are added in the following way:
		 * 	$this->AddDefaultParameter('USERNAME', QCliParameterType::String, 'Your Username');
		 * 	$this->AddDefaultParameter('PASSWORD', QCliParameterType::String, 'Your Possword');
		 * 	$this->AddDefaultParameter('PATH_TO_FILE', QCliParameterType::Path, 'Path to the given file');
		 * then the call to the CLI must follow with USERNAME PASSWORD PATH_TO_FILE.
		 * @param string $strDefaultIdentifier
		 * @param QCliParameterType $intCliParameterType
		 * @param string $strHelpText
		 * @return void
		 */		
		public function AddDefaultParameter($strDefaultIdentifier, $intCliParameterType, $strHelpText) {
			// Cleanup the Identifier, and throw in invalid
			try {
				$strDefaultIdentifier = QCliParameterProcessor::CleanDefaultIdentifier($strDefaultIdentifier);
			} catch (QInvalidCastException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
			
			// Ensure DefaultIdentifier is not already in use
			if ($strDefaultIdentifier && array_key_exists($strDefaultIdentifier, $this->strDefaultIdentifierArray))
				throw new QCallerException('DefaultIdentifier already in use: ' . $strDefaultIdentifier);

			// Get the ValueIndex for this flag, and set the value to false
			$intIndex = count($this->mixDefaultValueArray);
			$this->mixDefaultValueArray[$intIndex] = null;
			$this->intDefaultParameterTypeArray[$intIndex] = $intCliParameterType;
			$this->strDefaultHelpTextArray[$intIndex] = $strHelpText;
			$this->strDefaultIdentifierArray[$intIndex] = $strDefaultIdentifier;
		}


		/**
		 * If this is a valid ShortIdentifier character (single letter), it will return it.
		 * If this was null, it will return null.
		 * If it is invalid, it will throw a QInvalidCastException 
		 * @param string $chrShortIdentifier
		 * @return string
		 */
		public static function CleanShortIdentifier($chrShortIdentifier) {
			if (is_null($chrShortIdentifier)) return null;
			if (strlen($chrShortIdentifier) != 1) throw new QInvalidCastException('Invalid Short Identifier: ' . $chrShortIdentifier);
			$intOrd = ord($chrShortIdentifier);
			if (($intOrd >= ord('a')) && ($intOrd <= ord('z')) ||
				($intOrd >= ord('A')) && ($intOrd <= ord('Z')))
				return $chrShortIdentifier;
			throw new QInvalidCastException('Invalid Short Identifier: ' . $chrShortIdentifier);
		}
				
		/**
		 * If this is a valid LongIdentifier string (alphanumeric or hyphen, all lowercase, begins with letter, at least 2 characters long), it will return it.
		 * If this was null, it will return null.
		 * If it is invalid, it will throw a QInvalidCastException 
		 * @param string $strLongIdentifier
		 * @return string
		 */
		public static function CleanLongIdentifier($strLongIdentifier) {
			if (is_null($strLongIdentifier)) return null;
			preg_match('/[A-Za-z][A-Za-z0-9\\-]+/', $strLongIdentifier, $arrMatches);
			if (count($arrMatches) != 1) throw new QInvalidCastException('Invalid Long Identifier: ' . $strLongIdentifier);
			if ($arrMatches[0] != $strLongIdentifier) throw new QInvalidCastException('Invalid Long Identifier: ' . $strLongIdentifier);
			return strtolower($strLongIdentifier);
		}

		/**
		 * If this is a valid DefaultIdentifier string (alphanumeric or underscore, all uppercase, begins with letter), it will return it.
		 * If this was null or invalid, it will throw a QInvalidCastException
		 * @param string $strDefaultIdentifier
		 * @return string
		 */
		public static function CleanDefaultIdentifier($strDefaultIdentifier) {
			if (!strlen($strDefaultIdentifier)) throw new QInvalidCastException('Default Identifier cannot be null');
			preg_match('/[A-Za-z][A-Za-z0-9_]+/', $strDefaultIdentifier, $arrMatches);
			if (count($arrMatches) != 1) throw new QInvalidCastException('Invalid Default Identifier: ' . $strDefaultIdentifier);
			if ($arrMatches[0] != $strDefaultIdentifier) throw new QInvalidCastException('Invalid Default Identifier: ' . $strDefaultIdentifier);
			return strtoupper($strDefaultIdentifier);
		}
	}

	abstract class QCliParameterType extends QBaseClass {
		const String = 1;
		const Integer = 2;
		const Boolean = 3;
		const Path = 4;

		public static $NameArray = array(
			1 => 'string',
			2 => 'integer',
			3 => 'boolean',
			4 => 'path'
		);
	}
?>