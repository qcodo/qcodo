<?php
	/**
	 * This EmailServer (and its dependent EmailMessage class) allows the application to send
	 * messages via any accessible SMTP server.
	 * 
	 * The QEmailServer class, specifically, is an abstract class and is NOT meant to be instantiated.
	 * It has one public static method, Send, which takes in a QEmailMessage object.
	 */
	abstract class QEmailServer extends QBaseClass {
		/**
		 * Server Hostname or IP Address of the server running the SMTP service.
		 * Using an IP address is slightly faster, but using a Hostname is easier to manage.
		 * Defaults to "localhost".
		 *
		 * @var string SmtpServer
		 */
		public static $SmtpServer = 'localhost';

		/**
		 * Port of the SMTP Service on the SmtpServer, usually 25
		 *
		 * @var integer SmtpPort
		 */
		public static $SmtpPort = 25;

		/**
		 * IP Address of the Originating Server (e.g. the IP address of this server)
		 * used for the EHLO command in the SMTP protocol.
		 *
		 * @var string OriginatingServerIp
		 */
		public static $OriginatingServerIp = '127.0.0.1';

		/**
		 * Whether or not we are running in Test Mode.  Test Mode allows you
		 * to develop e-mail-based applications without actually having access to
		 * an SMTP server or the Internet.  Instead of messages being sent out,
		 * the messages and corresponding SMTP communication will be saved to disk.
		 *
		 * Alternatively, you can specify an email address, and when specified,
		 * it will send LIVE emails, but always send to the same exact email address.
		 * Any To, CC, or BCC data will be specified in the email a the top of the message.
		 * 
		 * In short, specify "FALSE" on a live enviornment, "TRUE" if you want to
		 * save to disk, or an email address if you want to send all messages to the same
		 * address.
		 * 
		 * @var mixed $TestMode
		 */
		public static $TestMode = false;

		/**
		 * The directory where TestMode e-mail files will be saved to.  The process
		 * running the webserver *must* have write access to this directory.  Default
		 * is "/tmp", which makes sense in unix/linux/mac environments.  Windows users
		 * will likely need to set up their own temp directories.
		 *
		 * @var string $TestModeDirectory
		 */		
		public static $TestModeDirectory = '/tmp';

		/**
		 * Boolean flag signifying whether SMTP's AUTH PLAIN should be used
		 * 
		 * @var bool $AuthPlain
		 */
		public static $AuthPlain = false;

		/**
		 * Boolean flag signifying whether SMTP's AUTH LOGIN should be used
		 * 
		 * @var bool $AuthLogin
		 */
		public static $AuthLogin = false;

		/**
		 * SMTP Username to use for AUTH PLAIN or LOGIN
		 * 
		 * @var string $SmtpUsername
		 */
		public static $SmtpUsername = '';

		/**
		 * SMTP Password to use for AUTH PLAIN or LOGIN
		 * 
		 * @var string $SmtpPassword
		 */
		public static $SmtpPassword = '';

		/**
		 * Encoding Type (if null, will default to the QApplicationBase::$EncodingType)
		 * 
		 * @var string $EncodingType
		 */
		public static $EncodingType = null;

		public static $StartTls = false;

		/**
		 * Uses regular expression matching to return an array of valid e-mail addresses
		 *
		 * @param string $strAddresses Single string containing e-mail addresses and anything else
		 * @return string[] An array of e-mail addresses only, or NULL if none
		 */
		public static function GetEmailAddresses($strAddresses) {
			$strAddressArray = array();

			// Define the ATEXT-based DOT-ATOM pattern which defines the LOCAL-PART of
			// an ADDRESS-SPEC in RFC 2822
			$strDotAtomPattern = "[a-zA-Z0-9\\!\\#\\$\\%\\&\\'\\*\\+\\-\\/\\=\\?\\^\\_\\`\\{\\|\\}\\~\\.]+";

			// Define the Domain pattern, defined by the allowable domain names in the DNS Root Zone of the internet
			// Note that this is stricter than what RFC 2822 allows in DCONTENT, because we assume developers are
			// wanting to send email over the internet, and not using it for a completely closed intranet with a
			// non-DNS Root Zone compliant domain name infrastructure.
			$strDomainPattern = '(?:[a-zA-Z0-9](?:[a-zA-Z0-9\-]*[a-zA-Z0-9])?\.)*[a-zA-Z0-9](?:[a-zA-Z0-9\-]*[a-zA-Z0-9])?';

			// The RegExp Pattern to Use
			$strPattern = sprintf('/%s@%s/', $strDotAtomPattern, $strDomainPattern);

			// See how many address candidates we have
			$strCandidates = explode(',', $strAddresses);

			foreach ($strCandidates as $strCandidate) {
				if (preg_match($strPattern, $strCandidate, $strCandidateArray) &&
					(count($strCandidateArray) == 1)) {
						$strCandidate = $strCandidateArray[0];
						$strParts = explode('@', $strCandidate);

						// Validate String Lengths, and add to AddressArray if Valid
						if (QString::IsLengthBeetween($strCandidate, 3, 256) &&
							QString::IsLengthBeetween($strParts[0], 1, 64) &&
							QString::IsLengthBeetween($strParts[1], 1, 255))
							$strAddressArray[] = $strCandidate;
				}
			}

			if (count($strAddressArray))
				return $strAddressArray;
			else
				return null;
		}

		/**
		 * This will check to see if an email address is considered "Valid" according to RFC 2822.
		 * It utilizes the GetEmailAddresses static method, which does the actual logic work of checking.
		 * @param string $strEmailAddress
		 * @return boolean
		 */
		public static function IsEmailValid($strEmailAddress) {
			$strEmailAddressArray = QEmailServer::GetEmailAddresses($strEmailAddress);
			if (!$strEmailAddressArray) return false;
			return ((count($strEmailAddressArray) == 1) && ($strEmailAddressArray[0] == $strEmailAddress));  
		}

		/**
		 * Actually performs the SMTP Socket connection to send the appropriate commands to the SMTP server.
		 *
		 * Does absolutely no validation -- assumes that the raw data being sent in is valid.  If not,
		 * this will throw a QEmailException exception on any error.
		 * 
		 * @param string $strMailFrom the email address to use for "MAIL FROM"
		 * @param string[] $strRcptToArray the array of email addresse to send to via "RCPT TO"
		 * @param mixed $mixMessageHeader can either be the raw string for the message header or a string-indexed array of header elements
		 * @param string $strMessageBody
		 * @return void
		 */
		public static function SendRawMessage($strMailFrom, $strRcptToArray, $mixMessageHeader, $strMessageBody) {
			self::$objSmtpSocket = null;

			if (QEmailServer::$TestMode === true) {
				// Open up a File Resource to the TestModeDirectory
				$strArray = explode(' ', microtime());
				$strFileName = sprintf('%s/email_%s%s.eml', QEmailServer::$TestModeDirectory, $strArray[1], substr($strArray[0], 1));
				self::$objSmtpSocket = fopen($strFileName, 'w');
				if (!self::$objSmtpSocket)
					throw new QEmailException(sprintf('Unable to open Test SMTP connection to: %s', $strFileName));

				// Clear the Read Buffer
				if (!feof(self::$objSmtpSocket)) fgets(self::$objSmtpSocket, 4096);

				// Write the Connection Command
				fwrite(self::$objSmtpSocket, sprintf("telnet %s %s\r\n", QEmailServer::$SmtpServer, QEmailServer::$SmtpPort));
			} else {
				self::$objSmtpSocket = fsockopen(QEmailServer::$SmtpServer, QEmailServer::$SmtpPort);
				if (!self::$objSmtpSocket)
					throw new QEmailException(sprintf('Unable to open SMTP connection to: %s %s', QEmailServer::$SmtpServer, QEmailServer::$SmtpPort));
			}

			// Connect
			self::ReceiveResponse('220', 'CONNECT');

			// EHLO
			self::SendCommand(sprintf('EHLO %s', QEmailServer::$OriginatingServerIp));
			self::ReceiveResponse('250', 'EHLO');

			// Start TLS
			if (QEmailServer::$StartTls && (QEmailServer::$TestMode !== true)) {
				self::SendCommand('STARTTLS');
				self::ReceiveResponse('220', 'STARTTLS');
				if	(!stream_socket_enable_crypto(self::$objSmtpSocket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
					throw new Exception('Unable to start TLS Connection');
				}

				self::SendCommand(sprintf('EHLO %s', QEmailServer::$OriginatingServerIp));
				self::ReceiveResponse('250', 'EHLO (TLS)');
			}

			// AUTH PLAIN
			if (QEmailServer::$AuthPlain) {
				$strAuthorization = base64_encode(QEmailServer::$SmtpUsername . "\0" . QEmailServer::$SmtpUsername . "\0" . QEmailServer::$SmtpPassword);
				self::SendCommand(sprintf('AUTH PLAIN %s', $strAuthorization));
				self::ReceiveResponse('235', 'AUTH PLAIN');
			}

			// AUTH LOGIN
			if (QEmailServer::$AuthLogin) {
				$strUsername = base64_encode(QEmailServer::$SmtpUsername);
				$strPassword = base64_encode(QEmailServer::$SmtpPassword);

				self::SendCommand('AUTH LOGIN');
				self::ReceiveResponse('334', 'AUTH LOGIN');
				
				self::SendCommand($strUsername);
				self::ReceiveResponse('334', 'AUTH LOGIN - USERNAME');
				
				self::SendCommand($strPassword);
				self::ReceiveResponse('235', 'AUTH LOGIN - PASSWORD');
			}

			// MAIL FROM
			self::SendCommand(sprintf('MAIL FROM:<%s>', $strMailFrom));
			self::ReceiveResponse('250', 'MAIL FROM');

			// RCPT TO
			foreach ($strRcptToArray as $strRcptTo) {
				self::SendCommand(sprintf('RCPT TO:<%s>', $strRcptTo));
				self::ReceiveResponse('250', 'RCPT TO for ' . $strRcptTo);
			}

			// DATA
			self::SendCommand('DATA');
			self::ReceiveResponse('354', 'DATA');

			// Header
			if (is_array($mixMessageHeader)) {
				foreach ($mixMessageHeader as $strName => $mixValue) {
					if (is_array($mixValue)) {
						foreach ($mixValue as $strValue) {
							self::SendData(sprintf("%s: %s\r\n", $strName, $strValue));
						}
					} else {
						self::SendData(sprintf("%s: %s\r\n", $strName, $mixValue));
					}
				}
			} else {
				self::SendData(trim($mixMessageHeader) . "\r\n");
			}
			self::SendData("\r\n");

			// Body
			self::SendData(str_replace("\n.", "\n..", trim($strMessageBody)));
					
			// Message End
			self::SendData("\r\n.\r\n");
			self::ReceiveResponse('250', 'DATA FINISH');

			// QUIT
			self::SendCommand('QUIT');

			// Clear Buffer and Close Resource
			if (!feof(self::$objSmtpSocket)) fgets(self::$objSmtpSocket);
			fclose(self::$objSmtpSocket);
			
			if (QEmailServer::$TestMode === true) chmod($strFileName, 0777);
		}

		protected static $objSmtpSocket;
		protected static function SendCommand($strMessage) {
			fputs(self::$objSmtpSocket, $strMessage . "\r\n");
		}
		protected static function SendData($strData) {
			fputs(self::$objSmtpSocket, $strData);
		}
		protected static function ReceiveResponse($strExpectedStatusCode, $strCurrentAction) {
			if (self::$TestMode !== true) {
				$strResponse = '000-';
				$strFullResponse = null;
				while (!feof(self::$objSmtpSocket) && (substr($strResponse, 3, 1) == '-')) {
					$strResponse = fgets(self::$objSmtpSocket, 4096);
					$strFullResponse .= $strResponse;
				}

				$strExpectedStatusCode = trim($strExpectedStatusCode) . ' ';
				if (substr($strResponse, 0, 4) != $strExpectedStatusCode) {
					throw new QEmailException(sprintf('Unexpected Response from SMTP Server on %s: %s', $strCurrentAction, $strFullResponse));
				}
			}
		}

		/**
		 * Extracts all email addresses (using the to, cc and bcc fields) from a QEmailMessage object
		 * @param QEmailMessage $objMessage
		 * @return array
		 * @throws QEmailException
		 */
		public static function GetRecipientEmailAddressArray(QEmailMessage $objMessage) {
			// Setup RCPT TO Addresses
			$strAddressToArray = QEmailServer::GetEmailAddresses($objMessage->To);
			if (!$strAddressToArray || !count($strAddressToArray)) throw new QEmailException(sprintf('Not a valid To address: %s', $objMessage->To));

			$strAddressCcArray = QEmailServer::GetEmailAddresses($objMessage->Cc);
			if (!$strAddressCcArray) $strAddressCcArray = array();

			$strAddressBccArray = QEmailServer::GetEmailAddresses($objMessage->Bcc);
			if (!$strAddressBccArray) $strAddressBccArray = array();

			$strAddressCcBccArray = array_merge($strAddressCcArray, $strAddressBccArray);
			return array_merge($strAddressToArray, $strAddressCcBccArray);
		}
		
		/**
		 * Uses SendRawMessage to sends a message out via SMTP according to the server, ip, etc. preferences
		 * as set up on the class.  Takes in a QEmailMessage object to calculate the appropriate fields
		 * to SendRawMesage.
		 *
		 * Will throw a QEmailException exception on any error.
		 *
		 * @param QEmailMessage $objMessage Message to Send
		 * @return void
		 */
		public static function Send(QEmailMessage $objMessage) {
			// Alter $objMessage if we are in a TestMode where we are redirecting messages
			if ((self::$TestMode !== false) && (self::$TestMode !== true)) {
				$objMessage = clone($objMessage);
				$strTopText = null;
				$strTopHtml = null;

				if ($objMessage->From) {
					$strTopText .= 'From: ' . $objMessage->From . "\r\n";
					$strTopHtml .= 'From: <strong>' . QApplicationBase::HtmlEntities($objMessage->From) . "</strong><br/>\r\n";
				}
			
				if ($objMessage->To) {
					$strTopText .= 'To: ' . $objMessage->To . "\r\n";
					$strTopHtml .= 'To: <strong>' . QApplicationBase::HtmlEntities($objMessage->To) . "</strong><br/>\r\n";
				}
			
				if ($objMessage->Cc) {
					$strTopText .= 'Cc: ' . $objMessage->Cc . "\r\n";
					$strTopHtml .= 'Cc: <strong>' . QApplicationBase::HtmlEntities($objMessage->Cc) . "</strong><br/>\r\n";
				}
			
				if ($objMessage->Bcc) {
					$strTopText .= 'Bcc: ' . $objMessage->Bcc . "\r\n";
					$strTopHtml .= 'Bcc: <strong>' . QApplicationBase::HtmlEntities($objMessage->Bcc) . "</strong><br/>\r\n";
				}

				if ($objMessage->Body)
					$objMessage->Body = sprintf("%s\r\n\r\n**********************************************\r\n\r\n%s", trim($strTopText), trim($objMessage->Body));

				if ($objMessage->HtmlBody)
					$objMessage->HtmlBody = sprintf("%s<br/><hr/><br/><br/>%s", trim($strTopHtml), trim($objMessage->HtmlBody));

				$objMessage->From = self::$TestMode;
				$objMessage->To = self::$TestMode;
				$objMessage->Cc = null;
				$objMessage->Bcc = null;
			}

			// Set Up Fields
			$strAddressArray = QEmailServer::GetEmailAddresses($objMessage->From);
			if (!$strAddressArray || (count($strAddressArray) != 1)) throw new QEmailException(sprintf('Not a valid From address: %s', $objMessage->From));
			$strMailFrom = $strAddressArray[0];

			if(strlen($objMessage->ExclusiveRecipient)) {
				$strRcptToArray = array($objMessage->ExclusiveRecipient);
			} else {
				$strRcptToArray = QEmailServer::GetRecipientEmailAddressArray($objMessage);
			}
			$strMessageArray = $objMessage->CalculateMessageHeaderAndBody(self::$EncodingType);
			self::SendRawMessage($strMailFrom, $strRcptToArray, $strMessageArray[0], $strMessageArray[1]);
		}
	}

	class QEmailException extends QCallerException {}
	
	class QEmailAttachment extends QBaseClass {
		protected $strFilePath;
		protected $strMimeType;
		protected $strFileName;
		protected $strEncodedFileData;
		protected $strContentId;

		public function __construct($strFilePath, $strSpecifiedMimeType = null, $strSpecifiedFileName = null) {
			// Set File Path
			if (!is_file(realpath($strFilePath)))
				throw new QCallerException('File Not Found: ' . $strFilePath);
			$this->strFilePath = realpath($strFilePath);


			// Set the File MIME Type -- if Explicitly Set, use it
			if ($strSpecifiedMimeType)
				$this->strMimeType = $strSpecifiedMimeType;
			// otherwise, use QMimeType to determine
			else
				$this->strMimeType = QMimeType::GetMimeTypeForFile($this->strFilePath);


			// Set the File Name -- if explicitly set, use it
			if ($strSpecifiedFileName)
				$this->strFileName = $strSpecifiedFileName;
			// Otherwise, use basename() to determine
			else
				$this->strFileName = basename($this->strFilePath);


			// Read file into a Base64 Encoded Data Stream
			$strFileContents = file_get_contents($this->strFilePath, false);
			$this->strEncodedFileData = chunk_split(base64_encode($strFileContents));
		}

		public function SetContentId($strContentId) {
			$this->strContentId = $strContentId;
		}

		public function __get($strName) {
			switch ($strName) {
				case 'FilePath': return $this->strFilePath;
				case 'MimeType': return $this->strMimeType; 
				case 'FileName': return $this->strFileName;
				case 'EncodedFileData': return $this->strEncodedFileData;
				case 'ContentId': return $this->strContentId;
				case 'InlineUrl': return 'cid:' . $this->strContentId;
				default:
					try {
						return parent::__get($strName);
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
			}
		}
	}

/**
 * @property boolean ExclusiveRecipient
 * @property string To
 */
class QEmailMessage extends QBaseClass {
		protected $strFrom;
		protected $strTo;
		protected $strSubject;
		protected $strBody;
		protected $strHtmlBody;
		protected $strExclusiveRecipient;

		protected $strCc;
		protected $strBcc;
		protected $objFileArray = array();
		protected $objInlineArray = array();

		protected $strHeaderArray;
		protected $strHeaderKeyArray;

		public function AddAttachment(QEmailAttachment $objFile) {						
			$this->objFileArray[$objFile->FileName] = $objFile;
		}

		public function AddInline(QEmailAttachment $objFile) {
			$strContentId = md5(microtime());
			$this->objInlineArray[$strContentId] = $objFile;
			$objFile->SetContentId($strContentId);
		}

		public function Attach($strFilePath, $strSpecifiedMimeType = null, $strSpecifiedFileName = null) {
			$this->AddAttachment(new QEmailAttachment($strFilePath, $strSpecifiedMimeType, $strSpecifiedFileName));
		}

		public function RemoveAttachment($strFileName) {
			if (array_key_exists($strName, $this->objFileArray))
				unset($this->objFileArray[$strName]);
		}

		/**
		 * Sets an item in the Header.  This will OVERWRITE any existing header item of the same name (if applicable).
		 * @param string $strName
		 * @param string $strValue
		 * @return void
		 */
		public function SetHeader($strName, $strValue) {
			$strName = trim($strName);
			$strValue = trim($strValue);
			$this->strHeaderArray[strtolower($strName)] = $strValue;
			$this->strHeaderKeyArray[strtolower($strName)] = $strName;
		}

		/**
		 * Returns the value for a given email Header, or NULL if none exists
		 * @param string $strName
		 * @return string
		 */
		public function GetHeader($strName) {
			$strName = trim($strName);
			if (array_key_exists(strtolower($strName), $this->strHeaderArray))
				return $this->strHeaderArray[strtolower($strName)];
			return null;
		}

		/**
		 * Returns the Key for a given email Header with the user-defined uppercase/lowercase specification, or NULL if none exists
		 * @param string $strName
		 * @return string
		 */
		public function GetHeaderKey($strName) {
			$strName = trim($strName);
			if (array_key_exists(strtolower($strName), $this->strHeaderArray))
				return $this->strHeaderArray[strtolower($strName)];
			return null;
		}

		/**
		 * Removes the specified email header from this message object (if applicable)
		 * @param string $strName
		 * @return string
		 */
		public function RemoveHeader($strName) {
			$strName = trim($strName);
			if (array_key_exists(strtolower($strName), $this->strHeaderArray))
				unset($this->strHeaderArray[strtolower($strName)]);
			if (array_key_exists(strtolower($strName), $this->strHeaderKeyArray))
				unset($this->strHeaderKeyArray[strtolower($strName)]);
		}

		public function __construct($strFrom = null, $strTo = null, $strSubject = null, $strBody = null) {
			$this->strFrom = $strFrom;
			$this->strTo = $strTo;

			// We must cleanup the Subject and Body -- use the Property to set
			$this->Subject = $strSubject;
			$this->Body = $strBody;

			// Setup Header Array
			$this->strHeaderArray = array();
			$this->strHeaderKeyArray = array();
			$this->SetHeader('X-Mailer', 'Qcodo v' . QCODO_VERSION);
		}

		/**
		 * Used by CalculateHeaderAndBody to set up additional headers for MIME and content encoding.
		 * This will return the a string array with two items:
		 * 	0 - the MIME Boundary
		 *  1 - the MIME Alternate Boundary
		 * @param string $strEncodingType
		 * @return string[]
		 */
		protected function SetupMimeHeaders($strEncodingType) {
			// Clear any old Content-Type Header (if applicable) and additional MIME information
			$this->RemoveHeader('MIME-Version');
			$this->RemoveHeader('Content-Type');
			$this->RemoveHeader('Content-Transfer-Encoding');
			
			// Setup MIME Boundaries
			if ($this->HasInline) {
				$strBoundary = sprintf('qcodo_alt_%s', strtolower(md5(microtime())));
				$strAltBoundary = sprintf('qcodo_rel_%s', strtolower(md5(microtime())));
				$this->SetHeader('MIME-Version', '1.0');
				$this->SetHeader('Content-Type', sprintf('multipart/alternative; boundary="%s"', $strBoundary));
			} else if ($this->HasFiles || $this->HtmlBody) {
				$strBoundary = sprintf('qcodo_mixed_%s', strtolower(md5(microtime())));
				$strAltBoundary = sprintf('qcodo_alt_%s', strtolower(md5(microtime())));
				$this->SetHeader('MIME-Version', '1.0');
				$this->SetHeader('Content-Type', sprintf('multipart/mixed; boundary="%s"', $strBoundary));
			} else {
				$strBoundary = null;
				$strAltBoundary = null;
				$this->SetHeader('Content-Type', sprintf('text/plain; charset="%s"', $strEncodingType));
				$this->SetHeader('Content-Transfer-Encoding', 'quoted-printable');
			}

			return array($strBoundary, $strAltBoundary);
		}

		protected function CalculateMessageHeader() {
			$strHeader = null;
			foreach ($this->strHeaderArray as $strKey => $strValue) {
				// TODO: Add Line Breaking functionality
				$strHeader .= $this->strHeaderKeyArray[$strKey] . ': ' . $strValue . "\r\n";
			}
			$strHeader = trim($strHeader);

			return $strHeader;
		}

		protected function CalculateMessageBody($strEncodingType, $strBoundary, $strAltBoundary) {
			// Messages with Inline Imgaes with HTML are treated differently
			if ($this->HasInline) {
				// Message Body Explanation (for non-MIME based Email Readers)
				$strBody = "This is a multipart message in MIME format.\r\n\r\n";
				
				// Add Primary Boundary Marker
				$strBody .= sprintf("--%s\r\n", $strBoundary);

				// Provide PlainText Version of Email
				$strBody .= sprintf("Content-Type: text/plain; charset=\"%s\"\r\n", $strEncodingType);
				$strBody .= sprintf("Content-Transfer-Encoding: quoted-printable\r\n\r\n");
				$strBody .= QString::QuotedPrintableEncode($this->Body);
				$strBody .= "\r\n\r\n";

				// Add Primary Boundary Marker
				$strBody .= sprintf("--%s\r\n", $strBoundary);

				// Add Alternate Boundary Marker
				$strBody .= sprintf("Content-Type: multipart/related;\r\n type=\"text/html\";\r\n boundary=\"%s\"\r\n\r\n", $strAltBoundary);

				// Provide Html Version of Email
				$strBody .= sprintf("--%s\r\n", $strAltBoundary);
				$strBody .= sprintf("Content-Type: text/html; charset=\"%s\"\r\n", $strEncodingType);
				$strBody .= sprintf("Content-Transfer-Encoding: quoted-printable\r\n\r\n");
				$strBody .= QString::QuotedPrintableEncode($this->HtmlBody);
				$strBody .= "\r\n\r\n";

				// Add Inline Images
				foreach ($this->objInlineArray as $objFile) {
					$strBody .= sprintf("--%s\r\n", $strAltBoundary);
					$strBody .= sprintf("Content-Type: %s; name=\"%s\"\r\n", $objFile->MimeType, $objFile->FileName);
					$strBody .= sprintf("Content-Length: %s\r\n", strlen($objFile->EncodedFileData));
					$strBody .= sprintf("Content-Disposition: inline; filename=\"%s\"\r\n", $objFile->FileName);
					$strBody .= sprintf("Content-Id: <%s>\r\n", $objFile->ContentId);
					$strBody .= "Content-Transfer-Encoding: base64\r\n\r\n";
					$strBody .= $objFile->EncodedFileData;
					$strBody .= "\r\n\r\n";
				}

				// Close Alternate Boundary Marker
				$strBody .= sprintf("--%s--\r\n\r\n", $strAltBoundary);

				// Close Primary Boundary Marker
				$strBody .= sprintf("--%s--\r\n", $strBoundary);

			// Messages with HTML and/or FileAttachments are treated differently than simple, plain-text messages
			} else if ($this->HasFiles || $this->HtmlBody)  {
				// Message Body Explanation (for non-MIME based Email Readers)
				$strBody = "This is a multipart message in MIME format.\r\n\r\n";

				// Add Primary Boundary Marker
				$strBody .= sprintf("--%s\r\n", $strBoundary);

				// Add Alternate Boundary Marker
				$strBody .= sprintf("Content-Type: multipart/alternative;\r\n boundary=\"%s\"\r\n\r\n", $strAltBoundary);

				// Provide PlainText Version of Email
				$strBody .= sprintf("--%s\r\n", $strAltBoundary);
				$strBody .= sprintf("Content-Type: text/plain; charset=\"%s\"\r\n", $strEncodingType);
				$strBody .= sprintf("Content-Transfer-Encoding: quoted-printable\r\n\r\n");
				$strBody .= QString::QuotedPrintableEncode($this->Body);
				$strBody .= "\r\n\r\n";

				// Provide Html Version of Email (if applicable)
				if ($this->HtmlBody) {
					$strBody .= sprintf("--%s\r\n", $strAltBoundary);
					$strBody .= sprintf("Content-Type: text/html; charset=\"%s\"\r\n", $strEncodingType);
					$strBody .= sprintf("Content-Transfer-Encoding: quoted-printable\r\n\r\n");
					$strBody .= QString::QuotedPrintableEncode($this->HtmlBody);
					$strBody .= "\r\n\r\n";
				}

				// Close Alternate Boundary Marker
				$strBody .= sprintf("--%s--\r\n\r\n", $strAltBoundary);

				// Add File Attachments (if applicable)
				if ($this->HasFiles) {
					foreach ($objArray = $this->FileArray as $objFile) {
						$strBody .= sprintf("--%s\r\n", $strBoundary);
						$strBody .= sprintf("Content-Type: %s; name=\"%s\"\r\n", $objFile->MimeType, $objFile->FileName);
						$strBody .= sprintf("Content-Length: %s\r\n", strlen($objFile->EncodedFileData));
						$strBody .= sprintf("Content-Disposition: attachment; filename=\"%s\"\r\n", $objFile->FileName);
						$strBody .= "Content-Transfer-Encoding: base64\r\n\r\n";
						$strBody .= $objFile->EncodedFileData;
						$strBody .= "\r\n\r\n";
					}
				}

				// Close Primary Boundary Marker
				$strBody .= sprintf("--%s--\r\n", $strBoundary);

			// Plain-Text Version of the Body for Plain-Text Message Only
			} else {
				$strBody = QString::QuotedPrintableEncode($this->Body);
			}

			return $strBody;
		}

		/**
		 * Given the way this object is set up, it will return two-index string array containing the correct
		 * SMTP Message Header and Message Body for this object.
		 * 
		 * This will make changes, cleanup and any additional setup to the HeaderArray in order to complete its task
		 * 
		 * @param string $strEncodingType the encoding type to use (if null, then it uses QApplicationBase's)
		 * @param QDateTime $dttSendDate the optional QDateTime to use for the Date field or NULL if you want to use Now()
		 * @return string[] index 0 is the Header and index 1 is the Body
		 */
		public function CalculateMessageHeaderAndBody($strEncodingType = null, QDateTime $dttSendDate = null) {
			// Quick Check
			if ($this->HasInline && !$this->strHtmlBody) throw new QCallerException('Cannot have Inline File Attachments but no HTML Body');

			// Setup Headers
			$this->RemoveHeader('Message-Id');
			$this->SetHeader('From', $this->From);
			$this->SetHeader('To', $this->To);

			if ($dttSendDate)
				$this->SetHeader('Date', $dttSendDate->ToString(QDateTime::FormatRfc5322));
			else
				$this->SetHeader('Date', QDateTime::NowToString(QDateTime::FormatRfc5322));

			// Setup Encoding Type (default to QApplicationBase's if not specified)
			if (!$strEncodingType) $strEncodingType = QApplicationBase::$EncodingType;

			// Additional "Optional" Headers
			if ($this->Subject) {
				// Encode to UTF8 Subject if Applicable
				if (QString::IsContainsUtf8($this->Subject)) {
					$strSubject = QString::QuotedPrintableEncode($this->Subject);
					$strSubject = str_replace("=\r\n", "", $strSubject);
					$strSubject = str_replace('?', '=3F', $strSubject);
					$this->SetHeader('Subject', sprintf("=?%s?Q?%s?=", $strEncodingType, $strSubject));
				} else {
					$this->SetHeader('Subject', $this->Subject);
				}
			}
			if ($this->Cc) $this->SetHeader('Cc', $this->Cc);

			// Setup for MIME and Content Encoding
			$strBoundaryArray = $this->SetupMimeHeaders($strEncodingType);
			$strBoundary = $strBoundaryArray[0];
			$strAltBoundary = $strBoundaryArray[1];

			// Generate MessageHeader
			$strHeader = $this->CalculateMessageHeader();

			// Generate MessageBody
			$strBody = $this->CalculateMessageBody($strEncodingType, $strBoundary, $strAltBoundary);

			return array($strHeader, $strBody);
		}

		public function __get($strName) {
			switch ($strName) {
				case 'From': return $this->strFrom;
				case 'To': return $this->strTo;
				case 'Subject': return $this->strSubject;
				case 'Body': return $this->strBody;
				case 'HtmlBody': return $this->strHtmlBody;
				case 'ExclusiveRecipient': return $this->strExclusiveRecipient;

				case 'Cc': return $this->strCc;
				case 'Bcc': return $this->strBcc;

				case 'FileArray': return $this->objFileArray;
				case 'HasFiles': return (count($this->objFileArray) > 0) ? true : false;
				case 'HasInline': return (count($this->objInlineArray) > 0) ? true : false;
				
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
			try {
				switch ($strName) {
					case 'From': return ($this->strFrom = QType::Cast($mixValue, QType::String));
					case 'To': return ($this->strTo = QType::Cast($mixValue, QType::String));
					case 'ExclusiveRecipient':
						$strEmailAddressesArray = QEmailServer::GetEmailAddresses(QType::Cast($mixValue, QType::String));
						if(!$strEmailAddressesArray || (count($strEmailAddressesArray) != 1)) {
							throw new QCallerException(sprintf('Unable to discern a single email address from the input string for ExclusiveRecipient: %s', $mixValue));
						}
						return ($this->strExclusiveRecipient = $strEmailAddressesArray[0]);
					case 'Subject':
						$strSubject = trim(QType::Cast($mixValue, QType::String));
						$strSubject = str_replace("\r", "", $strSubject);
						$strSubject = str_replace("\n", " ", $strSubject);
						return ($this->strSubject = $strSubject);
					case 'Body':
						$strBody = QType::Cast($mixValue, QType::String);
						$strBody = str_replace("\r", "", $strBody);
						$strBody = str_replace("\n", "\r\n", $strBody);
						return ($this->strBody = $strBody);
					case 'HtmlBody':
						$strHtmlBody = QType::Cast($mixValue, QType::String);
						$strHtmlBody = str_replace("\r", "", $strHtmlBody);
						$strHtmlBody = str_replace("\n", "\r\n", $strHtmlBody);
						return ($this->strHtmlBody = $strHtmlBody);

					case 'Cc': return ($this->strCc = QType::Cast($mixValue, QType::String));
					case 'Bcc': return ($this->strBcc = QType::Cast($mixValue, QType::String));

					default: return (parent::__set($strName, $mixValue));
				}
			} catch (QInvalidCastException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}
	}
?>