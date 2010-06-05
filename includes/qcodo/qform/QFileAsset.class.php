<?php
	class QFileAsset extends QFileAssetBase {
		protected $strTemporaryUploadPath = '/tmp';
		protected $strUnacceptableFileSizeMessage;
               /**
                * @var integer Store maximum files size in bytes
                */
                protected $intMaxFileSize = null;
		public function __construct($objParentObject, $strControlId = null) {
			parent::__construct($objParentObject, $strControlId);

			// Setup Default Properties
			$this->strTemplate = __QCODO_CORE__ . '/assets/QFileAsset.tpl.php';
			$this->DialogBoxCssClass = 'fileassetDbox';
			$this->UploadText = QApplication::Translate('Upload');
			$this->CancelText = QApplication::Translate('Cancel');
			$this->btnUpload->Text = '<img src="' . __VIRTUAL_DIRECTORY__ . __IMAGE_ASSETS__ . '/add.png" alt="' . QApplication::Translate('Upload') . '" border="0"/> ' . QApplication::Translate('Upload');
			$this->btnDelete->Text = '<img src="' . __VIRTUAL_DIRECTORY__ . __IMAGE_ASSETS__ . '/delete.png" alt="' . QApplication::Translate('Delete') . '" border="0"/> ' . QApplication::Translate('Delete');
			$this->DialogBoxHtml = '<h1>Upload a File</h1><p>Please select a file to upload.</p>';
		}

protected function SetupIconFilePathArray() {
			$this->strIconFilePathArray['swf'] = __DOCROOT__ . __IMAGE_ASSETS__ . '/file_asset_swf.png';
      parent::SetupIconFilePathArray();
		}

    protected function SetFileAssetType($intFileAssetType) {
			switch ($intFileAssetType) {

				case QFileAssetTypeExtended::Flash:
					$this->intFileAssetType = $intFileAssetType;
					$this->strAcceptibleMimeArray = array(
						'application/x-shockwave-flash' => 'swf'
					);
					$this->strUnacceptableMessage = QApplication::Translate('Must be an SWF');
					break;

				default:
          parent::SetFileAssetType($intFileAssetType);
					break;
			}
      
      return $intFileAssetType;
    }

    protected function SetFile($strFile) {
			if (!strlen($strFile)) {
				// No File Selected -- Remove
				$this->strFile = null;
				$this->imgFileIcon->ImagePath = $this->strIconFilePathArray['blank'];
			} else if (!is_file($strFile)) {
				// Invalid File Selected -- Throw Exception
				throw new QCallerException('File Not Found: ' . $strFile);
			} else {
				// Valid File Selected
				$this->strFile = realpath($strFile);

				// On Windows, we must replace all "\" with "/"
				if (substr($this->strFile, 1, 2) == ':\\') {
					$this->strFile = str_replace('\\', '/', $this->strFile);
				}

				// Figure Out File Type, and Display Icon Accordingly
				$strExtension = substr($this->strFile, strrpos($this->strFile, '.') + 1);
				switch (trim(strtolower($strExtension))) {
					case 'swf':
						$this->imgFileIcon->ImagePath = $this->strIconFilePathArray[trim(strtolower($strExtension))];
						break;
					default:
						parent::SetFile($strFile);
						break;
				}
			}

			$this->strFileName = basename($this->strFile);
			return $this->strFile;
		}

    public function dlgFileAsset_Upload() {
      //Max size verification code.
      if($this->intMaxFileSize && $this->dlgFileAsset->flcFileAsset->Size > $this->intMaxFileSize){
        if(!$this->strUnacceptableFileSizeMessage)
          $this->strUnacceptableFileSizeMessage = sprintf(QApplication::Translate('Maximum upload file size reached. Please upload a file less than or equal to %s'), ($this->intMaxFileSize / 1024) . 'Kb' );
        $this->dlgFileAsset->ShowError($this->strUnacceptableFileSizeMessage);
      }
      else
        parent::dlgFileAsset_Upload();
    }

    public function __get($strName) {
			switch ($strName) {
				case 'MaxFileSize': return $this->intMaxFileSize;
				case 'UnacceptableFileSizeMessage': return $this->strUnacceptableFileSizeMessage;

				default:
					try {
						return parent::__get($strName);
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
			}
		}

    public function __set($strName, $mixValue) {
			$this->blnModified = true;

			switch ($strName) {
				case 'MaxFileSize':
					try {
						return ($this->intMaxFileSize = QType::Cast($mixValue, QType::Integer));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'UnacceptableFileSizeMessage':
					try {
						return ($this->strUnacceptableFileSizeMessage = QType::Cast($mixValue, QType::String));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				default:
					try {
						return parent::__set($strName, $mixValue);
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
			}
		}
	}
?>