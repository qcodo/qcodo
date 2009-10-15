<?php
	class QPackageManager extends QBaseClass {
		protected $strPackageName;
		protected $strUsername;
		protected $strPassword;
		protected $blnLive;
		protected $blnForce;
		
		protected $strSettingsFilePath;
		
		public function __construct($strPackageName, $strUsername, $strPassword, $blnLive, $blnForce, $strSettingsFilePath) {
			$this->strPackageName = $strPackageName;
			$this->strUsername = $strUsername;
			$this->strPassword = $strPassword;
			$this->blnLive = $blnLive;
			$this->blnForce = $blnForce;
			$this->strSettingsFilePath = $strSettingsFilePath;
			
			$this->SetupSettings();
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

		public function PerformUpload() {
			
		}
	}
?>