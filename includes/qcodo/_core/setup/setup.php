<?php
	class QcodoSetup {
		protected $stdin;
		protected $vendorPath;

		const DefaultRootRelativeDirectory  = '..' . DIRECTORY_SEPARATOR;
		const DefaultApplicationName = 'MyApplication';
		const DefaultApplicationDirectory = 'application';

		protected $rootPath;
		protected $rootRelativeDirectory;
		protected $vendorRelativeDirectoryFromRoot;

		protected $applicationName;

		protected $applicationPath;
		protected $applicationDirectory;
		protected $rootRelativeDirectoryFromApplication;

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

		protected function GetRelativeReverseDirectory($path, $relativeDirectory) {
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

			return implode(DIRECTORY_SEPARATOR, array_reverse($reverseDirectoryArray));
		}

		protected function ExecuteCreateApplicationDirectories() {
			if (!is_dir($this->applicationPath)) mkdir($this->applicationPath);

			foreach (array('configuration', 'handlers', 'managers', 'models', 'models' . DIRECTORY_SEPARATOR . 'generated') as $subdirectory) {
				$path = $this->applicationPath . DIRECTORY_SEPARATOR . $subdirectory;
				if (!is_dir($path)) mkdir($path);
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

			$this->vendorRelativeDirectoryFromRoot = $this->GetRelativeReverseDirectory($this->vendorPath, $this->rootRelativeDirectory);
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
			$this->rootRelativeDirectoryFromApplication = $this->GetRelativeReverseDirectory($this->rootPath, $this->applicationDirectory);
			printf("Application directory path is [%s]\r\n", $this->applicationPath);
		}
	}

	$qcodoSetup = new QcodoSetup();
	$qcodoSetup->Run();
