<?php

namespace Qcodo\Handlers;
use QApplicationBase;
use Exception;
use ReflectionMethod;
use ReflectionClass;

abstract class Console extends Base {
	/**
	 * @var string[] the command line arguments that were passed in
	 */
	protected $argumentArray;

	/**
	 * @var array[] a structured array of parameters (indexed by parameterName) of the method being called
	 */
	protected $parameterArray;

	/**
	 * @var ReflectionClass $reflectionClass for this class being called
	 */
	protected $reflectionClass;

	/**
	 * @var ReflectionMethod $reflectionMethod for the method being called
	 */
	protected $reflectionMethod;



	/**
	 * This is the default "Run" method.  It is recommended that you use the default approach to execute the Method List.
	 *
	 * But if needed, this can be overriden by the specific Console handlers.
	 *
	 * It is the method called when no method is specified at run time,
	 * and it must take no parameters
	 *
	 * @return void
	 */
	public function Run() {
		$this->executeMethodList();
	}

	/**
	 * Responsible for performing the actual Run for this CLI
	 * @param string[] $argumentArray
	 */
	public static function RunConsole($argumentArray) {
		if (!QApplicationBase::$application->consoleModeFlag) throw new Exception('Cannot runConsole if not set to console mode');

		// Did we ask for a script to be run?
		if (!is_array($argumentArray) ||
			!array_key_exists(1, $argumentArray) ||
			(substr($argumentArray[1], 0, 1) == '-')) {
			self::ExecuteConsoleWelcome();
		}

		// Figure out the name of the script
		$scriptNameParts = explode('::', trim($argumentArray[1]));
		if (count($scriptNameParts) > 2) {
			// Too many colon delimiters (we only allow one)
			fwrite(STDERR, 'error: the script [' . $argumentArray[1] . '] is not a valid script name' . PHP_EOL);
			exit(1);
		}

		// Figure out full classpath
		if (strtolower($scriptNameParts[0]) == 'qcodo') {
			$classPath = 'Qcodo\\Handlers\\Console\\Qcodo';
			require(dirname(__FILE__) . '/Console/Qcodo.php');
		} else {
			$classPath = sprintf('%s\\Handlers\\Console\\%s', QApplicationBase::$application->rootNamespace, $scriptNameParts[0]);
		}

		// Get the Console Handler for the requested Script
		if (class_exists($classPath)) {
			$handler = new $classPath();
			$handler->argumentArray = $argumentArray;
			$handler->reflectionClass = new ReflectionClass($handler);

			// Figure out the method name
			if (array_key_exists(1, $scriptNameParts)) {
				$methodName = $scriptNameParts[1];
			} else {
				$methodName = 'run';
			}

			// Ensure it exists and get the ReflectionMethod for it
			if (!method_exists($handler, $methodName)) {
				fwrite(STDERR, 'error: the handler [' . $scriptNameParts[0] . '] does not have a method [' . $methodName . ']' . PHP_EOL);
				exit(1);
			}

			$handler->executeRunMethod($methodName);

		// Revert to check against Legacy Qcodo, or report error if not found
		} else {
			if (strpos($_SERVER['argv'][1], '.cli.php') === false)
				$scriptFilename = $_SERVER['argv'][1] . '.cli.php';
			else
				$scriptFilename = $_SERVER['argv'][1];

			if (file_exists($path = __VENDOR__ . '/qcodo/qcodo/cli/' . $scriptFilename)) {
				QApplicationBase::$application->scriptName = $scriptFilename;
				require($path);
			} else {
				fwrite(STDERR, 'error: the script [' . $_SERVER['argv'][1] . '] does not exist' . PHP_EOL);
				exit(1);
			}
		}
	}

	protected function executeRunMethod($methodName) {
		// Get the ReflectionMethod for it
		$this->reflectionMethod = $this->reflectionClass->getMethod($methodName);

		// Get the Parameter Structure
		$this->parameterArray = self::GetParameterStructureArray($this->reflectionMethod);

		// Apply Arguments
		if (count($this->argumentArray) > 2) {

			// Go Straight to Help
			if (count($this->argumentArray) == 3) switch (strtolower($this->argumentArray[2])) {
				case '-h':
				case '--help':
				case '-?':
					$this->executeHelp();
					break;
			}

			// Figure out the values -- Optional Parameters/Flags
			for ($index = 2 ; ($index < count($this->argumentArray)) && (substr($this->argumentArray[$index], 0, 1) == '-') ; $index++) {
				if (substr($this->argumentArray[$index], 0, 2) == '--') {
					$this->parseLongArgument(substr($this->argumentArray[$index], 2), $index);
				} else {
					$this->parseShortArgument(substr($this->argumentArray[$index], 1), $index);
				}
			}

			foreach ($this->parameterArray as $parameterIndex => $array) {
				if ($index < count($this->argumentArray)) {
					if ($array['type'] == 'required') {
						$this->parameterArray[$parameterIndex]['value'] = $this->argumentArray[$index];
						$index++;
					}
				}
			}

			// Too Many Arguments passed in?
			if (count($this->argumentArray) > $index) {
				$this->executeConsoleError('invalid argument [' . $this->argumentArray[$index] . ']');
			}
		}

		// Not enough arguments passed in?
		foreach ($this->parameterArray as $array) {
			if (($array['type'] == 'required') && is_null($array['value']))
				$this->executeConsoleError('missing value for [' . $array['name'] . ']');
		}

		// Get the Values in an Array
		$valueArray = array();
		foreach ($this->parameterArray as $parameter) $valueArray[] = $parameter['value'];;

		// Make the method call
		$this->reflectionMethod->invokeArgs($this, $valueArray);
	}

	protected function executeHelp() {
		printf('usage: %s %s::%s ', $this->argumentArray[0], $this->reflectionClass->getShortName(), $this->reflectionMethod->getName());
		$optionalParameterExists = false;
		$requiredParameterArray = array();

		foreach ($this->parameterArray as $parameter) {
			switch ($parameter['type']) {
				case 'required':
					$requiredParameterArray[] = $parameter['name'];
					break;
				default:
					$optionalParameterExists = true;
					break;
			}
		}

		if ($optionalParameterExists) print '[OPTIONS] ';
		print implode(' ', $requiredParameterArray);

		print PHP_EOL . PHP_EOL;

		// Default Identifier MaxLength and associated HelpText width and padding
		$intMaxIdentifierLength = 16;
		$strPadding = str_repeat(' ', $intMaxIdentifierLength + 4);
		$intHelpTextWidth = 78 - $intMaxIdentifierLength - 4;

		// Printout any required parameters
		if (count($requiredParameterArray)) {
			print 'required parameters:' . PHP_EOL;

			// Update MaxIdLength calculation (if applicable)
			foreach ($this->parameterArray as $parameter) {
				if ($parameter['type'] == 'required') {
					if (strlen($parameter['name']) > $intMaxIdentifierLength) $intMaxIdentifierLength = strlen($parameter['name']);
				}
			}
			$strPadding = str_repeat(' ', $intMaxIdentifierLength + 4);
			$intHelpTextWidth = 78 - $intMaxIdentifierLength - 4;

			// Render the Required Parameters
			foreach ($this->parameterArray as $parameter) {
				if ($parameter['type'] == 'required') {
					printf('  %-' . $intMaxIdentifierLength . 's  %s%s',
						$parameter['name'],
						self::RenderHelpText($parameter['description'], $intHelpTextWidth, $strPadding),
						PHP_EOL
					);
				}
			}

			print PHP_EOL;
		}


		// Printout any optional parameters
		if ($optionalParameterExists) {
			print 'optional parameters:' . PHP_EOL;

			foreach ($this->parameterArray as $name => $parameter) if ($parameter['type'] != 'required') {
				// First, figure out the formal label for the "identifier"
				$strIdentifier = '';

				if ($parameter['shortName'])
					$strIdentifier = '-' . $parameter['shortName'] . ', ';
				$strIdentifier .= '--' . $parameter['name'];

				// For non-flags (actual named parameters) output the parameter type we are expecting
				if ($parameter['type'] != 'flag')
					$strIdentifier .= '=VALUE';

				// Print it out by itself, or include the help text (if applicable)
				if (!$parameter['description']) {
					print('  ' . $strIdentifier . PHP_EOL);
				} else {
					$strHelpText = self::RenderHelpText($parameter['description'], $intHelpTextWidth, $strPadding);
					if (strlen($strIdentifier) > $intMaxIdentifierLength)
						printf("  %s\r\n%s%s\r\n", $strIdentifier, $strPadding, $strHelpText);
					else
						printf("  %-" . $intMaxIdentifierLength . "s  %s\r\n", $strIdentifier, $strHelpText);
				}
			}

			print PHP_EOL;
		}

		exit(0);
	}

	/**
	 * Given a help text, the max width for that help text, and the amount of left-side padding any subsequent line gets,
	 * it will returned the rendered help text with the spacing and linebreaks.
	 * @param string $strHelpText
	 * @param integer $intMaxWidth
	 * @param string $strPadding
	 * @return string
	 */
	protected static function RenderHelpText($strHelpText, $intMaxWidth, $strPadding) {
		$strHelpText = wordwrap(trim((string) $strHelpText), $intMaxWidth, "\r\n", true);
		$strHelpText = str_replace("\r\n", "\r\n" . $strPadding, $strHelpText);
		return $strHelpText;
	}


	protected function parseLongArgument($argument, &$currentIndex) {
		$value = null;

		// Get out any "value" after "=" (if applicable)
		if (($position = strpos($argument, '=')) !== false) {
			$value = substr($argument, $position + 1);
			$identifier = substr($argument, 0, $position);
		} else {
			$identifier = $argument;
		}

		// Ensure identifier exists / Find It
		$parameterIndex = $this->getParameterIndexForName($identifier);
		if (!$parameterIndex)
			$this->executeConsoleError('invalid argument [' . $identifier . ']');

		// Process based on Type
		switch ($this->parameterArray[$parameterIndex]['type']) {
			case 'required':
				$this->executeConsoleError('invalid argument [' . $identifier . ']');
				break;
			case 'flag':
				$this->parameterArray[$parameterIndex]['value'] = true;
				break;
			case 'optional':
				if (is_null($value)) {
					if (!array_key_exists($currentIndex + 1, $this->argumentArray))
						$this->executeConsoleError('invalid argument [' . $identifier . ']');
					$currentIndex++;
					$value = $this->argumentArray[$currentIndex];
					if (substr($value, 0, 1) == '-')
						$this->executeConsoleError('invalid argument [' . $identifier . ']');
				}
				$this->parameterArray[$parameterIndex]['value'] = $value;
				break;
			default:
				throw new Exception('Unhandled Parameter Type: ' . $this->parameterArray[$parameterIndex]['type']);
		}
	}

	/**
	 * @param string $name the name (e.g. "Long Name") of a given parameter
	 * @return string or null if not found
	 */
	protected function getParameterIndexForName($name) {
		foreach ($this->parameterArray as $index => $array)
			if ($array['name'] == $name) return $index;
		return null;
	}

	/**
	 * @param string $shortName the character (e.g. "Short Name") of a given parameter
	 * @return string or null if not found
	 */
	protected function getParameterIndexForShortName($shortName) {
		foreach ($this->parameterArray as $index => $array)
			if (array_key_exists('shortName', $array) &&
				($array['shortName'] == $shortName)) return $index;
		return null;
	}

	protected function parseShortArgumentCluster($identifier) {
		for ($index = 0; $index < strlen($identifier); $index++) {
			$parameterIndex = $this->getParameterIndexForShortName(substr($identifier, $index, 1));
			if (!$parameterIndex) $this->executeConsoleError('invalid argument [' . $identifier . ']');
			$this->parameterArray[$parameterIndex]['value'] = true;
		}
	}

	/**
	 * @param string $argument
	 * @param integer $currentIndex
	 * @return void
	 */
	protected function parseShortArgument($argument, &$currentIndex) {
		$value = null;

		// Get out any "value" after "=" (if applicable)
		if (($position = strpos($argument, '=')) !== false) {
			$value = substr($argument, $position + 1);
			$identifier = substr($argument, 0, $position);
		} else {
			$identifier = $argument;
		}

		// Clustered Flags?
		if (strlen($identifier) > 1) {
			if (!is_null($value)) $this->executeConsoleError('invalid argument [' . $identifier . ']');
			$this->parseShortArgumentCluster($identifier);
			return;
		}

		// Ensure identifier exists / Find It
		$parameterIndex = $this->getParameterIndexForShortName($identifier);
		if (!$parameterIndex)
			$this->executeConsoleError('invalid argument [' . $identifier . ']');

		// Process based on Type
		switch ($this->parameterArray[$parameterIndex]['type']) {
			case 'required':
				$this->executeConsoleError('invalid argument [' . $identifier . ']');
				break;
			case 'flag':
				$this->parameterArray[$parameterIndex]['value'] = true;
				break;
			case 'optional':
				if (is_null($value)) {
					if (!array_key_exists($currentIndex + 1, $this->argumentArray))
						$this->executeConsoleError('invalid argument [' . $identifier . ']');
					$currentIndex++;
					$value = $this->argumentArray[$currentIndex];
					if (substr($value, 0, 1) == '-')
						$this->executeConsoleError('invalid argument [' . $identifier . ']');
				}
				$this->parameterArray[$parameterIndex]['value'] = $value;
				break;
			default:
				throw new Exception('Unhandled Parameter Type: ' . $this->parameterArray[$parameterIndex]['type']);
		}
	}

	/**
	 * Will return an array, indexed by the parameter name itself, of specs for each parameter
	 * @param ReflectionMethod $method
	 * @return string[] indexed by parameter name
	 */
	protected static function GetParameterCommentArray(ReflectionMethod $method) {
		$doc = trim($method->getDocComment());
		$doc = str_replace("\t", " ", $doc);

		$parameterCommentArray = array();

		// Iterate through each line one at a time
		foreach (explode("\n", $doc) as $line) {
			$line = trim($line);

			// Skip this line if it doesn't start with asterisk
			// Otherwise, get rid of the asterisk
			if (substr($line, 0, 1) != '*') continue;
			$line = trim(substr($line, 1));

			// Skip this line if it's not a "@param" line
			// Otherwise, get rid of the tag
			if (substr(strtolower($line), 0, 7) != '@param ') continue;
			$line = trim(substr($line, 7));

			// Skip this line if there is no variable defined
			// Otherwise, let's get this starting with the variable name
			if (strpos($line, '$') === false) continue;
			$line = trim(substr($line, strpos($line, '$') + 1));

			// Figure out the spot of the first space
			$position = strpos($line, ' ');
			if ($position === false) continue;

			$parameterCommentArray[trim(substr($line, 0, $position))] = trim(substr($line, $position + 1));
		}

		return $parameterCommentArray;
	}

	/**
	 * Will return an array of arrays, indexed by the parameter name itself, for the specs for each parameter
	 * @param ReflectionMethod $method
	 * @return array[] indexed by parameterName
	 */
	protected static function GetParameterStructureArray(ReflectionMethod $method) {
		$parameterArray = array();

		foreach ($method->getParameters() as $parameter) {
			if (!$parameter->isDefaultValueAvailable()) {
				$parameterArray[$parameter->getName()] = array(
					'type' => 'required',
					'name' => self::ConvertFromCamelCase($parameter->getName(), true, '_'),
					'description' => null,
					'value' => null
				);
			} else {
				// Validate name of the optional flag/parameter
				if (strtolower($parameter->getName()) == 'help')
					throw new Exception('the optional flag/parameter [' . $parameter->getName() . '] cannot have a name of HELP');
				if ($parameter->getDefaultValue() === true)
					throw new Exception('the optional parameter [' . $parameter->getName() . '] cannot have a default value of TRUE');

				if ($parameter->getDefaultValue() === false)
					$parameterArray[$parameter->getName()] = array(
						'type' => 'flag',
						'name' => self::ConvertFromCamelCase($parameter->getName(), false, '-'),
						'shortName' => null,
						'description' => null,
						'value' => false
					);
				else
					$parameterArray[$parameter->getName()] = array(
						'type' => 'optional',
						'name' => self::ConvertFromCamelCase($parameter->getName(), false, '-'),
						'shortName' => null,
						'description' => null,
						'value' => $parameter->getDefaultValue()
					);
			}
		}

		// Add optional shortName and/or description
		foreach (self::GetParameterCommentArray($method) as $parameterName => $parameterComment) {
			if (array_key_exists($parameterName, $parameterArray)) {
				switch ($parameterArray[$parameterName]['type']) {
					case 'required':
						$parameterArray[$parameterName]['description'] = $parameterComment;
						break;

					case 'flag':
					case 'optional':
						self::UpdateParameterArrayWithShortName($parameterName, $parameterArray, $parameterComment);
						$parameterArray[$parameterName]['description'] = $parameterComment;
						break;

					default:
						throw new Exception('Unhandled Parameter Type: ' . $parameterArray[$parameterName]['type']);
				}
			}
		}

		return $parameterArray;
	}

	protected static function UpdateParameterArrayWithShortName($parameterName, &$parameterArray, &$parameterComment) {
		$matches = array();

		if (preg_match("/^\\([a-zA-Z]\\)/", $parameterComment, $matches)) {
			$match = $matches[0];
			$parameterComment = trim(substr($parameterComment, 3));

			$shortName = substr($match, 1, 1);
			if (array_key_exists($match, $parameterArray))
				throw new Exception('the optional flag/parameter [' . $parameterName . '] has specified a shortName that is already in use');
			foreach ($parameterArray as $parameterToCheck) {
				if (array_key_exists('shortName', $parameterToCheck) && ($shortName == $parameterToCheck['shortName']))
					throw new Exception('the optional flag/parameter [' . $parameterName . '] has specified a shortName that is already in use');
			}

			$parameterArray[$parameterName]['shortName'] = $shortName;
		}
	}

	/**
	 * @param string $camelCase the term to convert from camelcase
	 * @param boolean $uppercaseFlag if false, this will make everything lowercase, if true this will make everything uppercase
	 * @param string $spaceDelimiter the character to use for a space (typically underscore "_" or dash "-")
	 * @return string
	 */
	protected static function ConvertFromCamelCase($camelCase, $uppercaseFlag, $spaceDelimiter) {
		$convertedString = '';
		$length = strlen($camelCase);
		for ($position = 0; $position < $length; $position++) {
			$character = substr($camelCase, $position, 1);
			if ((ord($character) >= ord('A')) && (ord($character) <= ord('Z')))
				$convertedString .= ' ';
			$convertedString .= $character;
		}

		$convertedString = str_replace(' ', $spaceDelimiter, trim($convertedString));
		if ($uppercaseFlag) return strtoupper($convertedString);
		return strtolower($convertedString);
	}

	protected function executeConsoleError($errorMessage) {
		fwrite(STDERR, 'error: ' . trim($errorMessage) . PHP_EOL);
		fwrite(STDERR, sprintf('See "%s %s::%s --help" for more information%s', $this->argumentArray[0], $this->reflectionClass->getShortName(), $this->reflectionMethod->getName(), PHP_EOL));
		exit(1);
	}

	protected static function ExecuteConsoleWelcome() {
		print "Qcodo CLI Runner v" . QCODO_VERSION . "\r\n";
		print "usage: " . $_SERVER['argv'][0] . " SCRIPT [SCRIPT-SPECIFIC ARGS]\r\n";
		print "\r\n";
		print "required parameters:\r\n";
		print "  SCRIPT         the name of the handler in the Handlers/Console directory\r\n";
		print "                 in " . QApplicationBase::$application->rootNamespace . " that you wish to run\r\n";
		print "\r\n";
		print "the following SCRIPTs are included with the Qcodo distribution:\r\n";
		print "  codegen        Code generates your ORM-layer\r\n";
		print "  ws-setup       Creates a new webservice handler for a given URL path and specification doc\r\n";
		print "\r\n";
		print "Other custom scripts can be created as well.\r\n";
		print "\r\n";
		exit(1);
	}

	/**
	 * This will print out the list of Public methods
	 */
	protected function executeMethodList() {
		$class = new \ReflectionClass($this);
		print 'Accessible console methods in [' . $class->getShortName() . ']:' . PHP_EOL;

		foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
			if (!$method->isStatic() &&
				!in_array($method->getDeclaringClass()->getShortName(), array('Console', 'QBaseClass'))) {
				print '    ' . $method->getDeclaringClass()->getShortName() . '::' . $method->getName() . PHP_EOL;
			}
		}

		exit(0);
	}


	/**
	 * Checks to see if this Console process being called is running uniquely.
	 * @return boolean true if this is a uniquely running process, false if there is at least one other process running it
	 */
	protected function isConsoleProcessUnique() {
		// Get Process List
		$resultArray = array();
		exec('ps aux', $resultArray);

		$matchCount = 0;
		$commandString = strtolower(sprintf('%s %s', $this->argumentArray[0], $this->argumentArray[1]));
		foreach ($resultArray as $processLineItem) {
			$processLineItem = strtolower($processLineItem);

			// If not a CommandString match, move to the next processLineItem
			if (strpos($processLineItem, $commandString) === false) continue;

			// Filter out any entries that are there as a wrapper/shell process
			if (strpos($processLineItem, '/dev/null') !== false) continue;

			// We have a match
			$matchCount++;
		}

		// Return the result
		return ($matchCount == 1);
	}

	/**
	 * @return string
	 */
	protected function calculateLockFileName() {
		$name = strtolower($this->argumentArray[1]);
		$name = str_replace('/', '__', $name);
		$name = str_replace('\\', '__', $name);
		$name = str_replace('::', '__', $name);
		$name = str_replace(':', '__', $name);

		$name .= '.lock';

		return $name;
	}

	/**
	 * Sets up a lock file for this process
	 * @return void
	 */
	protected function setupConsoleProcessLockFile() {
		if (!is_dir(__LOCK_FILES__)) QApplicationBase::MakeDirectory(__LOCK_FILES__ . DIRECTORY_SEPARATOR, 0777);

		$path = __LOCK_FILES__ . DIRECTORY_SEPARATOR . $this->calculateLockFileName();
		if (is_file($path)) unlink($path);

		file_put_contents($path, getmypid());
	}

	/**
	 * Checks to see if there is a lock file and if matches the PID for this process.
	 * @return boolean whether or not the lock file is considered valid for this process
	 */
	protected function isConsoleProcessLockFileValid() {
		$path = __LOCK_FILES__ . DIRECTORY_SEPARATOR . $this->calculateLockFileName();
		if (!is_file($path)) return false;

		$pid = null;
		$pid = @file_get_contents($path);
		return ($pid == getmypid());
	}
}
