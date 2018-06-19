<?php

class QcodoWebServiceSetup {
	/**
	 * @var resource $stdin
	 */
	protected $stdin;

	const DefaultOpenApiSpecificationRelativePath = '..' . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'swagger.json';
	const DefaultWsRootRelativePath = '..' . DIRECTORY_SEPARATOR . 'public';
	const DefaultUrlDirectory = '/';
	const DefaultViewSpecificationCommand = '/display/swagger';

	protected $openApiSpecificationRelativePath;
	protected $wsRootRelativePath;
	protected $urlDirectory;
	protected $viewSpecificationCommand;

	/**
	 * @var string[] $rootRelativeDirectoryFromApplicationArray
	 */
	protected $applicationRelativeDirectoryFromWsRootArray;

	public function __construct() {
		$this->stdin = fopen('php://stdin', 'r');
	}

	public function Run() {
		$this->ExecuteOpenApiSpecificationRelativePath();
		$this->ExecuteWsRootRelativePath();
		$this->ExecuteUrlDirectory();
		$this->ExecuteViewSpecificationCommand();

		$this->ExecuteCreateWsRootDirectories();
	}

	/**
	 * @param string $path
	 * @param string $relativeDirectory
	 * @return string[]
	 */
	protected function GetRelativeReverseDirectoryArray($path, $relativeDirectory) {
		// Cleanup starting path
		$position = strpos($path, DIRECTORY_SEPARATOR);
		$path = substr($path, $position + 1);
		$pathArray = explode(DIRECTORY_SEPARATOR, $path);

		// Clean relative directory (remove all parent/current references)
		$cleanRelativeDirectoryArray = array();
		foreach (explode(DIRECTORY_SEPARATOR, $relativeDirectory) as $directoryAtom) {
			$directoryAtom = trim($directoryAtom);
			if (strlen($directoryAtom) > 0) {
				switch($directoryAtom) {
					case '.':
						break;
					case '..':
						if (count($cleanRelativeDirectoryArray) && ($cleanRelativeDirectoryArray[count($cleanRelativeDirectoryArray) - 1] != '..'))
							array_pop($cleanRelativeDirectoryArray);
						else
							$cleanRelativeDirectoryArray[] = '..';
						break;
					default:
						$cleanRelativeDirectoryArray[] = $directoryAtom;
						break;
				}
			}
		}

		$reverseDirectoryArray = array();
		foreach ($cleanRelativeDirectoryArray as $directoryAtom) {
			$directoryAtom = trim($directoryAtom);
			if (strlen($directoryAtom) > 0) {
				switch($directoryAtom) {
					case '..':
						$reverseDirectoryArray[] = array_pop($pathArray);
						break;
					default:
						$reverseDirectoryArray[] = '..';
						break;
				}
			}
		}

		return array_reverse($reverseDirectoryArray);
	}

	protected function ExecuteCreateWsRootDirectories() {
		$wsRootDirectory = (__APPLICATION__ . DIRECTORY_SEPARATOR . $this->wsRootRelativePath);
		if (!is_dir($wsRootDirectory)) mkdir($wsRootDirectory);
		$wsRootDirectory = realpath(__APPLICATION__ . DIRECTORY_SEPARATOR . $this->wsRootRelativePath);

		// Figure Out Relative Paths for directories
		$applicationRelativePath = null;
		foreach ($this->applicationRelativeDirectoryFromWsRootArray as $directoryAtom) {
			$applicationRelativePath .= sprintf(" . DIRECTORY_SEPARATOR . '%s'", $directoryAtom);
		}

		// Define Setup Files
		$wsRootFiles = array(
			'.htaccess' => false,
			'ws.php' => array(
				'APPLICATION' => $applicationRelativePath,
				'NAME' => QApplicationBase::$application->rootNamespace
			),
			'ws-settings.php' => array(
				'OPENAPI' => $this->openApiSpecificationRelativePath,
				'WSROOT' => $this->wsRootRelativePath,
				'URLDIRECTORY' => $this->urlDirectory,
				'VIEWSPEC' => is_null($this->viewSpecificationCommand) ? 'NULL' : "'" . $this->viewSpecificationCommand . "'"

			)
		);

		// Execute Setup Files
		$templatePath = dirname(__FILE__) . '/ws_templates';
		foreach ($wsRootFiles as $file => $substitutionArray) {
			if (!is_file($wsRootDirectory . DIRECTORY_SEPARATOR . $file)) {
				$templateFile = $file . '.txt';
				if (substr($templateFile, 0, 1) == '.') $templateFile = substr($templateFile, 1);

				$templateContent = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ws_templates' . DIRECTORY_SEPARATOR . $templateFile);
				if ($substitutionArray) foreach ($substitutionArray as $token => $value) {
					$templateContent = str_replace('%' . $token . '%', $value, $templateContent);
				}
				file_put_contents($wsRootDirectory  . DIRECTORY_SEPARATOR . $file, $templateContent);
			}
		}
	}

	protected function ExecuteOpenApiSpecificationRelativePath() {
		printf("This assumes the Application directory path is [%s]\r\n", __APPLICATION__);

		$this->openApiSpecificationRelativePath = null;
		$pathCandidate = null;
		while (!$this->openApiSpecificationRelativePath) {
			printf("\r\nOpen API Specification document path (relative to the Application directory) [%s]: ", self::DefaultOpenApiSpecificationRelativePath);

			$input = trim(fgets($this->stdin));
			if (!strlen($input)) $input = self::DefaultOpenApiSpecificationRelativePath;

			$pathCandidate = __APPLICATION__ . DIRECTORY_SEPARATOR . $input;
			$pathCandidate = realpath($pathCandidate);
			if ($pathCandidate && !is_file($pathCandidate)) $pathCandidate = null;

			if (!$pathCandidate) {
				printf("error: path not found: [%s]\r\n", $input);
			} else {
				$this->openApiSpecificationRelativePath = $input;
			}
		}

		printf("Open API specification path is [%s]\r\n", $pathCandidate);
	}
	protected function ExecuteWsRootRelativePath() {
		$this->wsRootRelativePath = null;
		$pathCandidate = null;
		while (!$this->wsRootRelativePath) {
			printf("\r\nWebService root in the webserver docroot (relative to the Application directory) [%s]: ", self::DefaultWsRootRelativePath);

			$input = trim(fgets($this->stdin));
			if (!strlen($input)) $input = self::DefaultWsRootRelativePath;

			$pathCandidate = __APPLICATION__ . DIRECTORY_SEPARATOR . $input;

			if (!$pathCandidate) {
				printf("error: path not found: [%s]\r\n", $input);
			} else {
				$this->wsRootRelativePath = $input;
			}
		}

		$this->applicationRelativeDirectoryFromWsRootArray = $this->GetRelativeReverseDirectoryArray(__APPLICATION__, $input);
		printf("WS root path is [%s]\r\n", $pathCandidate);
	}

	protected function ExecuteUrlDirectory() {
		$this->urlDirectory = null;

		printf("\r\nURL Directory (if any) [%s]: ", self::DefaultUrlDirectory);
		$input = trim(fgets($this->stdin));
		if (!strlen($input)) $input = self::DefaultUrlDirectory;

		$input = trim($input);
		while (substr($input, 0, 1) == '/') {
			$input = trim(substr($input, 1));
		}

		while (substr($input, strlen($input) - 1) == '/') {
			$input = trim(substr($input, 0, strlen($input) - 1));
		}

		$input = '/' . $input;
		$this->urlDirectory = $input;
	}

	protected function ExecuteViewSpecificationCommand() {
		$this->viewSpecificationCommand = null;

		printf("\r\nURL Directory (if any) [%s]: ", self::DefaultViewSpecificationCommand);
		$input = trim(fgets($this->stdin));
		if (!strlen($input)) $input = self::DefaultViewSpecificationCommand;

		$input = trim($input);
		while (substr($input, 0, 1) == '/') {
			$input = trim(substr($input, 1));
		}

		while (substr($input, strlen($input) - 1) == '/') {
			$input = trim(substr($input, 0, strlen($input) - 1));
		}

		if ($input) {
			$input = '/' . $input;
		} else {
			$input = null;
		}

		$this->viewSpecificationCommand = $input;
	}
}

$setup = new QcodoWebServiceSetup();
$setup->Run();
