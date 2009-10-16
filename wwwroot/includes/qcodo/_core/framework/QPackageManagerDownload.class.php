<?php
	class QPackageManagerDownload extends QPackageManager {
		protected $strPackageVersion;
		protected $strPackageVersionType;
		protected $strQpmXml;
		protected $objQpmXml;

		public function __construct($strPackageName, $strUsername, $blnLive, $blnForce) {
			$this->strPackageName = trim(strtolower($strPackageName));
			$this->strUsername = trim(strtolower($strUsername));
			$this->blnLive = $blnLive;
			$this->blnForce = $blnForce;

			$this->SetupManifestXml();
			$this->SetupDirectoryArray();
			$this->SetupFileArray();
			$this->SetupManifestVersion();
		}

		public function PerformDownload() {
			$strEndPoint = sprintf('%s/DownloadPackage?name=%s&u=%s&gz=', QPackageManager::QpmServiceEndpoint, $this->strPackageName, $this->strUsername); 
			if (function_exists('gzdecode')) {
				$strQpmXmlCompressed = file_get_contents($strEndPoint . '1');
				$this->strQpmXml = gzdecode($strQpmXmlCompressed);
			} else {
				$this->strQpmXml = trim(file_get_contents($strEndPoint . '0'));
			}
			
			if (!$this->strQpmXml) throw new Exception(sprintf('package not found: %s/%s', $this->strUsername, $this->strPackageName));

			$this->objQpmXml = new SimpleXMLElement($this->strQpmXml);
			print ((string) $this->objQpmXml->package['name']) . "\r\n";
			print ((string) $this->objQpmXml->package['user']) . "\r\n";
		}
	}
?>