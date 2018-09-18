<?php

namespace Qcodo\Handlers;
use QApplicationBase;
use Exception;

abstract class Console extends Base {
	/**
	 * @var string[] the command line arguments that were passed in
	 */
	protected $argumentArray;

	abstract public function run();

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
			self::ExecuteConsoleError('the script [' . $argumentArray[1] . '] is not a valid script name');
		}

		$classPath = sprintf('%s\\Handlers\\Console\\%s', QApplicationBase::$application->rootNamespace, $scriptNameParts[0]);
		if (class_exists($classPath)) {
			$handler = new $classPath();
			if (array_key_exists(1, $scriptNameParts)) {
				$methodName = $scriptNameParts[1];
			} else {
				$methodName = 'run';
			}

			$handler->argumentArray = $argumentArray;

			if (!method_exists($handler, $methodName)) {
				self::ExecuteConsoleError('the handler [' . $scriptNameParts[0] . '] does not have a method [' . $methodName . ']');
			}

			$handler->$methodName();

		} else {
			if (strpos($_SERVER['argv'][1], '.cli.php') === false)
				$scriptFilename = $_SERVER['argv'][1] . '.cli.php';
			else
				$scriptFilename = $_SERVER['argv'][1];

			if (file_exists($path = __VENDOR__ . '/qcodo/qcodo/cli/' . $scriptFilename)) {
				QApplicationBase::$application->scriptName = $scriptFilename;
				require($path);
			} else {
				self::ExecuteConsoleError('the script [' . $_SERVER['argv'][1] . '] does not exist');
			}
		}
	}

	protected static function ExecuteConsoleError($errorMessage) {
		fwrite(STDERR, 'error: ' . trim($errorMessage) . PHP_EOL);
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
}