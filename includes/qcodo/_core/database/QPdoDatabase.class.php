<?php
	/**
	 * PDO Generic database driver
	 * @abstract
	 * @author Marcos Sánchez [marcosdsanchez at thinkclear dot com dot ar]
	 */
	abstract class QPdoDatabase extends QDatabaseBase {
		const Adapter = 'Generic PDO Adapter (Abstract)';

		/**
		 * @var PDO connection handler
		 * @access protected
		 */
		protected $objPdo;
		/**
		 * @var PDOStatement most recent query result
		 * @access protected
		 */
		protected $objMostRecentResult;


		public function NonQuery($strNonQuery) {
			// Connect if Applicable
			if (!$this->blnConnectedFlag)
				$this->Connect();

			// Log Query (for Profiling, if applicable)
			$this->LogQuery($strNonQuery);

			// Perform the Query
			$objResult = $this->objPdo->query($strNonQuery);
			if ($objResult === false)
				throw new QPdoDatabaseException($this->objPdo->errorInfo(), $this->objPdo->errorCode(), $strNonQuery);
			$this->objMostRecentResult = $objResult;
		}

		public function __get($strName) {
			switch ($strName) {
				case 'AffectedRows':
					return $this->objMostRecentResult->rowCount();
				default:
					try {
						return parent::__get($strName);
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
			}
		}

		public function Close() {
			$this->objPdo = null;
			$this->blnConnectedFlag = false;
		}

		public function TransactionBegin() {
			// Connect if Applicable
			if (!$this->blnConnectedFlag) {
				$this->Connect();
			}
			$this->objPdo->beginTransaction();
		}

		public function TransactionCommit() {
			// Connect if Applicable
			if (!$this->blnConnectedFlag) {
				$this->Connect();
			}
			$this->objPdo->commit();
		}

		public function TransactionRollBack() {
			// Connect if Applicable
			if (!$this->blnConnectedFlag) {
				$this->Connect();
			}
			$this->objPdo->rollback();
		}

	}

	/**
	 * QPdoDatabaseResult
	 *
	 * @abstract
	 */
	abstract class QPdoDatabaseResult extends QDatabaseResultBase {

		/**
		 * @var PDOStatement Query result
		 * @access protected
		 */
		protected $objPdoResult;
		/**
		 * @var PDO Connection object
		 * @access protected
		 */
		protected $objPdo;

		public function __construct($objResult, QPdoDatabase $objDb) {
			$this->objPdoResult = $objResult;
			$this->objPdo = $objDb;
		}

		public function FetchArray() {
			return $this->objPdoResult->fetch();
		}

		public function FetchRow() {
			return $this->objPdoResult->fetch(PDO::FETCH_NUM);
		}

		public function CountRows() {
			return $this->objPdoResult->rowCount();
		}

		public function CountFields() {
			return $this->objPdoResult->columnCount();
		}

		public function Close() {
			$this->objPdoResult = null;
		}

		public function GetRows() {
			$objDbRowArray = array();
			while ($objDbRow = $this->GetNextRow())
				array_push($objDbRowArray, $objDbRow);
			return $objDbRowArray;
		}

	}

	/**
	 * PdoDatabaseException
	 */
	class QPdoDatabaseException extends QDatabaseExceptionBase {

		public function __construct($strMessage, $intNumber, $strQuery) {
			parent::__construct(sprintf("PDO %s", $strMessage[2]), 2);
			$this->intErrorNumber = $intNumber;
			$this->strQuery = $strQuery;
		}

	}
?>
