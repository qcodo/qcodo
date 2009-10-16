<?php
	require(dirname(__FILE__) . "/_manifest_helpers.inc.php");

	class QPackageManager extends QBaseClass {
		protected $strPackageName;
		protected $strUsername;
		protected $strPassword;
		protected $blnLive;
		protected $blnForce;
		
		protected $strSettingsFilePath;
		protected $objDirectoryArray;
		protected $objFileArrayByInode;
		protected $objManifestXml;

		protected $intSeenInode;
		protected $objNewFileArray;
		protected $objChangedFileArray;

		protected $blnVersionMatch = false;
		protected $blnValidCredential = false;
		protected $blnValidPackage = false;

		protected $strManifestVersion;
		protected $strManifestVersionType;
		protected $strCurrentStableVersion;
		protected $strCurrentDevelopmentVersion;

		const QpmServiceEndpoint = 'http://qpm.qcodo.dev/1_0.php';

		/**
		 * In addition to any files OR folders that start with a period ("."), QPM will ignore any
		 * folders that are named in the following IgnoreFolderArray
		 * @var boolean[]
		 */
		protected $blnIgnoreFolderArray = array(
			'cvs' => true,
			'svn' => true
		);

		public function __construct($strPackageName, $strUsername, $strPassword, $blnLive, $blnForce, $strSettingsFilePath) {
			$this->strPackageName = $strPackageName;
			$this->strUsername = $strUsername;
			$this->strPassword = $strPassword;
			$this->blnLive = $blnLive;
			$this->blnForce = $blnForce;
			$this->strSettingsFilePath = $strSettingsFilePath;

			$this->SetupSettings();
			$this->SetupManifestXml();
			$this->SetupDirectoryArray();
			$this->SetupFileArray();
			$this->CheckVersion();
			$this->CheckCredentialsAndPackage();
		}

		protected function SetupSettings() {
			// If they specified it, make sure it exists
			if ($this->strSettingsFilePath && !is_file($this->strSettingsFilePath)) {
				throw new Exception('QPM Settings XML file does not exist: ' . $this->strSettingsFilePath);
			}

			// If they didn't specify it, then check to see if the default location one exists
			if (!$this->strSettingsFilePath) {
				if (is_file(__DEVTOOLS_CLI__ . '/settings_qpm.xml'))
					$this->strSettingsFilePath = __DEVTOOLS_CLI__ . '/settings_qpm.xml';
				else
					return;
			}

			// Let's parse the file
			try {
				$objXml = @(new SimpleXMLElement(file_get_contents($this->strSettingsFilePath)));
				if (is_null($this->strUsername)) $this->strUsername = (string) $objXml->qcodoWebsite['username'];
				if (is_null($this->strPassword)) $this->strPassword = (string) $objXml->qcodoWebsite['password'];
			} catch (Exception $objExc) {
				throw new Exception('QPM Settings XML file is not valid: ' . $this->strSettingsFilePath);
			}
		}


		protected function SetupManifestXml() {
			$this->objManifestXml = new SimpleXMLElement(file_get_contents(__QCODO_CORE__ . '/manifest.xml'));
		}


		protected function SetupDirectoryArray() {
			$this->objDirectoryArray = array();
			foreach ($this->objManifestXml->directories->directory as $objDirectoryXml) {
				$objToken = new QDirectoryToken();
				$objToken->Token = (string) $objDirectoryXml['token'];
				$objToken->CoreFlag = (string) $objDirectoryXml['coreFlag'];
				$objToken->RelativeFlag = (string) $objDirectoryXml['relativeFlag'];
				$this->objDirectoryArray[$objToken->Token] = $objToken;
			}
		}


		protected function SetupFileArray() {
			$this->objFileArrayByInode = array();
			foreach ($this->objManifestXml->files->file as $objFileXml) {
				$objFileInManifest = new QFileInManifest();
				$objFileInManifest->DirectoryToken = (string) $objFileXml['directoryToken'];
				$objFileInManifest->Path = (string) $objFileXml['path'];
				$objFileInManifest->Md5 = (string) $objFileXml['md5'];

				$objFileInManifest->DirectoryTokenObject = $this->objDirectoryArray[$objFileInManifest->DirectoryToken];

				$objFileInManifest->Inode = fileinode($objFileInManifest->GetFullPath());
				if ($objFileInManifest->Inode)
					$this->objFileArrayByInode[$objFileInManifest->Inode] = $objFileInManifest;
			}
		}

		public function CheckVersion() {
			$this->strManifestVersion = (string) $this->objManifestXml->version;
			$this->strManifestVersionType = (string) $this->objManifestXml->type;
			$this->strCurrentStableVersion = trim(file_get_contents(QPackageManager::QpmServiceEndpoint . '/GetCurrentQcodoVersion'));
			$this->strCurrentDevelopmentVersion = trim(file_get_contents(QPackageManager::QpmServiceEndpoint . '/GetCurrentQcodoVersion?dev=1'));

			if (($this->strManifestVersion != $this->strCurrentStableVersion) && ($this->strManifestVersion != $this->strCurrentDevelopmentVersion)) {
				$this->blnVersionMatch = false;
			} else {
				$this->blnVersionMatch = true;
			}
		}

		public function CheckCredentialsAndPackage() {
			$intPersonId = trim(file_get_contents(QPackageManager::QpmServiceEndpoint . '/Login?u=' . urlencode($this->strUsername) . '&p=' . urlencode($this->strPassword)));
			$intPackageId = trim(file_get_contents(QPackageManager::QpmServiceEndpoint . '/GetPackageId?name=' . urlencode($this->strPackageName)));
			if ($intPersonId) $this->blnValidCredential = true;
			if ($intPackageId) $this->blnValidPackage = true;
		}


		public function GetInvalidCredentialOrPackageErrorText() {
			$strToReturn = null;
			if ((!$this->blnValidCredential) || (!$this->blnValidPackage)) {
				$strToReturn .= "error(s):\r\n";

				if (!$this->blnValidCredential)
					$strToReturn .= '  cannot log in to QPM service with username "' . $this->strUsername . '"' . "\r\n";

				if (!$this->blnValidPackage)
					$strToReturn .= '  package does not exist: "' . $this->strPackageName . '"' . "\r\n";

				$strToReturn .= "\r\n";
			}
			return $strToReturn;
		}

		public function GetVersionMismatchWarningText() {
			$strToReturn = null;
			if (!$this->blnVersionMatch) {
				$strToReturn .= "notice on out-of-date Qcodo version:\r\n";
				$strToReturn .= "  The local install of Qcodo is not the most up-to-date version:\r\n";
				$strToReturn .= sprintf("    local install: v%s (%s)\r\n", $this->strManifestVersion, $this->strManifestVersionType);
				$strToReturn .= sprintf("    Qcodo release: v%s (Development)\r\n", $this->strCurrentDevelopmentVersion);
				$strToReturn .= sprintf("                   v%s (Stable)\r\n", $this->strCurrentStableVersion);

				$strText =
					'You can still upload your QPM package, but note that because the installation ' .
					'is older than what is currently available to the community, it may limit the ' .
					'interest and/or comptability for this QPM package.';
				$strText = wordwrap($strText, 76, "\r\n");
				$strText = '  ' . str_replace("\r\n", "\r\n  ", $strText);
				$strToReturn .= $strText . "\r\n\r\n";

				$strText =
					'If you still want to proceed with uploading this QPM, be sure to specify the ' .
					'"-f" or "--force" flag to override this warning.';
				$strText = wordwrap($strText, 76, "\r\n");
				$strText = '  ' . str_replace("\r\n", "\r\n  ", $strText);
				$strToReturn .= $strText . "\r\n\r\n";
			}
			return $strToReturn;
		}

		public function GetNonLiveText() {
			$strToReturn = null;
			if (!$this->blnLive) {
				$strToReturn .= "notice on non-live mode:\r\n";

				$strText =
					'This is only a report of what WOULD be uploaded.  To actually execute the upload(s) ' .
					'live mode, be sure to specify the "-l" or "--live" flag.';
				$strText = wordwrap($strText, 76, "\r\n");
				$strText = '  ' . str_replace("\r\n", "\r\n  ", $strText);
				$strToReturn .= $strText . "\r\n\r\n";

				$strToReturn .= "new files to be included in this QPM package:\r\n";
				foreach ($this->objNewFileArray as $objFile) {
					$strToReturn .= sprintf("  %-16s  %s\r\n", $objFile->DirectoryToken, $objFile->Path);
				}
				$strToReturn .= "\r\n";

				$strToReturn .= "changed files to be included in this QPM package:\r\n";
				foreach ($this->objChangedFileArray as $objFile) {
					$strToReturn .= sprintf("  %-16s  %s\r\n", $objFile->DirectoryToken, $objFile->Path);
				}
				$strToReturn .= "\r\n";
			}
			return $strToReturn;
		}

		public function PerformUpload() {
			$this->intSeenInode = array();
			$this->objNewFileArray = array();
			$this->objChangedFileArray = array();

			foreach ($this->objDirectoryArray as $objDirectoryToken) {
				// Figure out the actual Path of the directory
				$strPath = $objDirectoryToken->GetFullPath();

				// Make sure it exists
				if (is_dir($strPath)) {
					$this->ProcessDirectory($strPath, $objDirectoryToken);
				}
			}

			print 'Qcodo Package Manager (QPM) Uploader Tool v' . QCODO_VERSION . "\r\n\r\n";
			if ((!$this->blnValidCredential) || (!$this->blnValidPackage)) {
				print $this->GetInvalidCredentialOrPackageErrorText();
			}

			if (!$this->blnVersionMatch) {
				print $this->GetVersionMismatchWarningText();
			}

			if (!$this->blnLive) {
				print $this->GetNonLiveText();
			} else if (($this->blnVersionMatch || $this->blnForce) &&
						($this->blnValidCredential) &&
						($this->blnValidPackage)) {
				print "status:\r\n";
			}
		}

		/**
		 * Given the path of a directory, process all the directories and files in it that have NOT been seen in SeenInode.
		 * Assumes: the path is a valid directory that exists and has NOT been SeenInode
		 * @param string $strPath
		 * @return void
		 */
		protected function ProcessDirectory($strPath, QDirectoryToken $objDirectoryToken) {
			$intInode = fileinode($strPath);
			$this->intSeenInode[$intInode] = true;

			$objDirectory = opendir($strPath);
			while ($strName = readdir($objDirectory)) {
				// Only Process Files/Folders that do NOT start with a single "."
				if (QString::FirstCharacter($strName) != '.') {
					// Put Together the Entire Full Path of the File in Question
					$strFullPath = $strPath . '/' . $strName;

					// Process if it's a file
					if (is_file($strFullPath)) {
						$this->ProcessFile($strFullPath, $objDirectoryToken);

					// Process if it's a directory
					} else if (is_dir($strFullPath)) {
						// Only continue if we haven't visited it and it's not a folder that we are ignoring
						$intInode = fileinode($strFullPath);
						if (!array_key_exists($intInode, $this->intSeenInode) && !array_key_exists(strtolower($strName), $this->blnIgnoreFolderArray))
							$this->ProcessDirectory($strFullPath, $objDirectoryToken);

					// It's neither a file nor a directory?!
					} else {
						throw new Exception('Not a valid file or folder: ' . $strFullPath);
					}
				}
			}
		}

		protected function ProcessFile($strFullPath, QDirectoryToken $objDirectoryToken) {
			// Calculate the iNode and ensure we haven't visited it yet
			$intInode = fileinode($strFullPath);
			if (array_key_exists($intInode, $this->intSeenInode)) throw new Exception('Somehow already visited file: ' . $strFullPath);
			$this->intSeenInode[$intInode] = true;

			// Calculate the MD5
			$strMd5 = md5_file($strFullPath);

			// Does this File Exist in the Manifest
			if (array_key_exists($intInode, $this->objFileArrayByInode)) {
				// Ensure that the FileInManifest Matches the Directory we are in
				$objFile = $this->objFileArrayByInode[$intInode];
				if ($objFile->DirectoryToken != $objDirectoryToken->Token)
					throw new Exception('Mismatched Directory Token: ' . $strFullPath);
				if ($objFile->Path != $objDirectoryToken->GetRelativePathForFile($strFullPath))
					throw new Exception('Mismatched File Path: ' . $strFullPath);

				// Do the MD5's match?
				if ($strMd5 != $objFile->Md5) {
					// NO -- we have an ALTERED FILE
					$objChangedFile = clone($objFile);
					$objChangedFile->Md5 = $strMd5;
					$this->objChangedFileArray[] = $objChangedFile;
				}
			} else {
				// Does NOT exist in Manifest -- it is NEW
				$objNewFile = new QFileInManifest();
				$objNewFile->Inode = $intInode;
				$objNewFile->DirectoryToken = $objDirectoryToken->Token;
				$objNewFile->DirectoryTokenObject = $objDirectoryToken;
				$objNewFile->Path = $objDirectoryToken->GetRelativePathForFile($strFullPath);
				$objNewFile->Md5 = $strMd5;
				$this->objNewFileArray[] = $objNewFile;
			}
		}
	}
?>