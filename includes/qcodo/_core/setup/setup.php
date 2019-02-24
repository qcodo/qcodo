<?php

class QcodoSetup {
	/**
	 * @var resource $stdin
	 */
	protected $stdin;
	protected $vendorPath;

	const DefaultRootRelativeDirectory  = '..' . DIRECTORY_SEPARATOR;
	const DefaultApplicationName = 'MyApplication';
	const DefaultApplicationDirectory = 'application';

	protected $rootPath;
	protected $rootRelativeDirectory;

	/**
	 * @var string[] $vendorRelativeDirectoryFromRootArray
	 */
	protected $vendorRelativeDirectoryFromRootArray;

	protected $applicationName;

	protected $applicationPath;
	protected $applicationDirectory;

	/**
	 * @var string[] $rootRelativeDirectoryFromApplicationArray
	 */
	protected $rootRelativeDirectoryFromApplicationArray;

	public function __construct() {
		$this->stdin = fopen('php://stdin', 'r');
		$this->vendorPath = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..');
	}

	public function Run() {
		$this->ExecuteRootPath();
		$this->ExecuteApplicationName();
		$this->ExecuteApplicationDirectory();

		$this->ExecuteCreateApplicationDirectories();
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

	protected function ExecuteCreateApplicationDirectories() {
		if (!is_dir($this->applicationPath)) mkdir($this->applicationPath);

		// Setup Commands
		if (!is_file($path = $this->applicationPath . DIRECTORY_SEPARATOR . 'qcodo')) {
			copy(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'qcodo', $path);
			chmod($path, 0555);
		}
		if (!is_file($path = $this->applicationPath . DIRECTORY_SEPARATOR . 'qcodo.bat')) {
			copy(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'qcodo.bat', $path);
		}

		// Figure Out Relative Paths for directories
		$rootRelativePath = null;
		foreach ($this->rootRelativeDirectoryFromApplicationArray as $directoryAtom) {
			$rootRelativePath .= sprintf(" . DIRECTORY_SEPARATOR . '%s'", $directoryAtom);
		}

		$vendorRelativePath = null;
		foreach ($this->vendorRelativeDirectoryFromRootArray as $directoryAtom) {
			$vendorRelativePath .= sprintf(" . DIRECTORY_SEPARATOR . '%s'", $directoryAtom);
		}

		// Define Setup Directories
		$applicationDirectories = array(
			'bootstrap' => array(
				'BootstrapHelper.php' => array(
					'NAME' => $this->applicationName
				),
				'autoload.php' => array(
					'NAME' => $this->applicationName
				),
				'console.php' => array(
					'NAME' => $this->applicationName
				),
				'webservice.php' => array(
					'NAME' => $this->applicationName
				)
			),
			'configuration' => array(
				'_server_instance.php' => false,
				'_directories.php' => array(
					'ROOT' => $rootRelativePath,
					'VENDOR' => $vendorRelativePath
				),
				'_logs.php' => false,
				'database.php' => false
			),
			'configuration' . DIRECTORY_SEPARATOR . 'codegen' => array(
				'codegen.xml' => false
			),
			'Handlers' => array(),
			'Handlers' . DIRECTORY_SEPARATOR . 'Console' => array(),
			'Handlers' . DIRECTORY_SEPARATOR . 'WebService' => array(),
			'Managers' => array(
				'Application.php' => array(
					'NAME' => $this->applicationName
				)
			),
			'Models' => array(),
			'Models' . DIRECTORY_SEPARATOR . 'Database' => array(),
			'Models' . DIRECTORY_SEPARATOR . 'JsonSchema' => array()
		);

		// Execute Setup Directories
		foreach ($applicationDirectories as $directory => $fileArray) {
			$path = $this->applicationPath . DIRECTORY_SEPARATOR . $directory;
			if (!is_dir($path)) mkdir($path);

			foreach ($fileArray as $file => $substitutionArray) {
				if (!is_file($path . DIRECTORY_SEPARATOR . $file)) {
					$templateContent = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . $file . '.txt');
					if ($substitutionArray) foreach ($substitutionArray as $token => $value) {
						$templateContent = str_replace('%' . $token . '%', $value, $templateContent);
					}
					file_put_contents($path . DIRECTORY_SEPARATOR . $file, $templateContent);
				}
			}
		}
	}

	protected function ExecuteRootPath() {
		printf("This assumes the Vendor directory path is [%s]\r\n", $this->vendorPath);

		$this->rootPath = null;
		while (!$this->rootPath) {
			printf("\r\nRoot directory path (relative to the Vendor directory) [%s]: ", self::DefaultRootRelativeDirectory);

			$input = trim(fgets($this->stdin));
			if (!strlen($input)) $input = self::DefaultRootRelativeDirectory;

			$rootPathCandidate = $this->vendorPath . DIRECTORY_SEPARATOR . $input;
			$this->rootPath = realpath($rootPathCandidate);
			if ($this->rootPath && !is_dir($this->rootPath)) $this->rootPath = null;

			if (!$this->rootPath) {
				printf("error: path not found: [%s]\r\n", $rootPathCandidate);
			} else {
				$this->rootRelativeDirectory = $input;
			}
		}

		$this->vendorRelativeDirectoryFromRootArray = $this->GetRelativeReverseDirectoryArray($this->vendorPath, $this->rootRelativeDirectory);
		printf("Root directory path is [%s]\r\n", $this->rootPath);
	}

	protected function ExecuteApplicationName() {
		$this->applicationName = null;
		while (!$this->applicationName) {
			printf("\r\nApplication namespace [%s]: ", self::DefaultApplicationName);
			$input = trim(fgets($this->stdin));
			if (!strlen($input)) $input = self::DefaultApplicationName;

			$validFlag = true;
			for ($index = 0; $index < strlen($input); $index++) {
				$ord = ord(substr(strtolower($input), $index, 1));
				if (($ord >= ord('a')) && ($ord <= ord('z'))) {
					// This is a letter -- still valid, no change
				} else {
					$validFlag = false;
				}
			}

			if (!$validFlag) {
				printf("error: can only contain letters: [%s]\r\n", $input);
			} else {
				$this->applicationName = $input;
			}
		}
	}

	protected function ExecuteApplicationDirectory() {
		$this->applicationDirectory = null;
		while (!$this->applicationDirectory) {
			printf("\r\nApplication directory path (relative to the Root directory) [%s]: ", self::DefaultApplicationDirectory);

			$input = trim(fgets($this->stdin));
			if (!strlen($input)) $input = self::DefaultApplicationDirectory;

			$this->applicationDirectory = $input;
		}

		$this->applicationPath = $this->rootPath . DIRECTORY_SEPARATOR . $this->applicationDirectory;
		$this->rootRelativeDirectoryFromApplicationArray = $this->GetRelativeReverseDirectoryArray($this->rootPath, $this->applicationDirectory);
		printf("Application directory path is [%s]\r\n", $this->applicationPath);
	}
}

$qcodoSetup = new QcodoSetup();
$qcodoSetup->Run();
