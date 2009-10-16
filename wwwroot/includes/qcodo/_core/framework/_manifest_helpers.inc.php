<?php
	class QDirectoryToken {
		public $Token;
		public $RelativeFlag;
		public $CoreFlag;
		
		public function GetFullPath() {
			return ($this->RelativeFlag) ? __DOCROOT__ . constant($this->Token) : constant($this->Token);
		}

		public function GetRelativePathForFile($strFullPath) {
			$strDirectory = realpath($this->GetFullPath());
			$strFullPath = realpath($strFullPath);
			if (substr($strFullPath, 0, strlen($strDirectory)) == $strDirectory)
				return substr($strFullPath, strlen($strDirectory) + 1);
			else
				throw new Exception('Cannot calculate relative path in ' . $this->Token . ' for file: ' . $strFullPath);
		}
	}

	class QFileInManifest {
		public $Inode;
		public $DirectoryToken;
		public $Path;
		public $Md5;
		public $DirectoryTokenObject;

		public function GetFullPath() {
			return $this->DirectoryTokenObject->GetFullPath() . '/' . $this->Path;
		}
	}

	/* The following functions are used as QUpdateUtility error handlers for OS-level errors while trying
	 * to perform updates/deletes/overwrites/saves/socket connections, etc.
	 * (e.g. cannot connect, or permission denied, file locked, etc.)
	 */
	function QUpdateUtilityErrorHandler($intErrorNumber, $strErrorString, $strErrorFile, $intErrorLine) {
		QUpdateUtility::Error('Could not connect to Qcodo Update webservice at ' . QUpdateUtility::ServiceUrl . ' (' . $strErrorString . ')');
	}

	function QUpdateUtilityFileSystemErrorHandler($intErrorNumber, $strErrorString, $strErrorFile, $intErrorLine) {
		QUpdateUtility::$PrimaryInstance->strAlertArray[count(QUpdateUtility::$PrimaryInstance->strAlertArray)] =
			sprintf('%s while trying to download and save %s', $strErrorString, QUpdateUtility::$CurrentFilePath);
	}

	function QUpdateUtilityFileSystemErrorHandlerForDelete($intErrorNumber, $strErrorString, $strErrorFile, $intErrorLine) {
		QUpdateUtility::$PrimaryInstance->strAlertArray[count(QUpdateUtility::$PrimaryInstance->strAlertArray)] =
			sprintf('%s while trying to delete %s', $strErrorString, QUpdateUtility::$CurrentFilePath);
	}

	function QUpdateUtilityFileSystemErrorHandlerForRename($intErrorNumber, $strErrorString, $strErrorFile, $intErrorLine) {
		QUpdateUtility::$PrimaryInstance->strAlertArray[count(QUpdateUtility::$PrimaryInstance->strAlertArray)] =
			sprintf('%s while trying to rename %s', $strErrorString, QUpdateUtility::$CurrentFilePath);
	}
?>