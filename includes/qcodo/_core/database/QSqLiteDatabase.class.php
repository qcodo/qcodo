<?php
	class QSqLiteDatabase extends QDatabaseBase {
		const Adapter = 'SQLite3 Database Adapter';
		
		protected $objSqLite;		

		protected $strEscapeIdentifierBegin = '`';
		protected $strEscapeIdentifierEnd = '`';
		
		public function SqlLimitVariablePrefix($strLimitInfo) {
			// MySQL uses Limit by Suffixes (via a LIMIT clause)
			// Prefix is not used, therefore, return null
			return null;
		}

		public function SqlLimitVariableSuffix($strLimitInfo) {
			// Setup limit suffix (if applicable) via a LIMIT clause 
			if (strlen($strLimitInfo)) {
				if (strpos($strLimitInfo, ';') !== false)
					throw new Exception('Invalid Semicolon in LIMIT Info');
				if (strpos($strLimitInfo, '`') !== false)
					throw new Exception('Invalid Backtick in LIMIT Info');
				return "LIMIT $strLimitInfo";
			}

			return null;
		}

		public function SqlSortByVariable($strSortByInfo) {
			// Setup sorting information (if applicable) via a ORDER BY clause
			if (strlen($strSortByInfo)) {
				if (strpos($strSortByInfo, ';') !== false)
					throw new Exception('Invalid Semicolon in ORDER BY Info');
				if (strpos($strSortByInfo, '`') !== false)
					throw new Exception('Invalid Backtick in ORDER BY Info');

				return "ORDER BY $strSortByInfo";
			}
			
			return null;
		}

		public function Connect() {
			// Connect to the Database Server
			$this->objSqLite = new SQLite3($this->Database, SQLITE3_OPEN_READWRITE);

			if (!$this->objSqLite)
				throw new QSqLiteDatabaseException("Unable to connect to Database", -1, null);

			if ($this->objSqLite->lastErrorCode())
				throw new QSqLiteDatabaseException($this->objSqLite->lastErrorMsg(), $this->objSqLite->lastErrorCode(), 'Connect()');

			$this->blnConnectedFlag = true;
		}

		public function __get($strName) {
			switch ($strName) {
				case 'AffectedRows':
					return $this->objSqLite->changes();
				default:
					try {
						return parent::__get($strName);
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
			}
		}

		public function Query($strQuery) {
			if (!$this->blnConnectedFlag) $this->Connect();

			// Log Query (for Profiling, if applicable)
			$this->LogQuery($strQuery);

			// Perform the Query
			$objResult = $this->objSqLite->query($strQuery);

			if (!$objResult || $this->objSqLite->lastErrorCode())
				throw new QSqLiteDatabaseException($$this->objSqLite->lastErrorCode(), $this->objSqLite->lastErrorMsg(), $strQuery);

			// Return the Result
			$objSqLiteDatabaseResult = new QSqLiteDatabaseResult($objResult, $this);
			return $objSqLiteDatabaseResult;
		}

		public function NonQuery($strNonQuery) {
			if (!$this->blnConnectedFlag) $this->Connect();

			// Log Query (for Profiling, if applicable)
			$this->LogQuery($strNonQuery);

			// Perform the Query	
			$objResult = $this->objSqLite->Query($strNonQuery);

			if (!$objResult || $this->objSqLite->lastErrorCode())
				throw new QSqLiteDatabaseException($$this->objSqLite->lastErrorCode(), $this->objSqLite->lastErrorMsg(), $strNonQuery);
		}

		public function GetTables() {
			$objResult = $this->Query("SELECT name FROM sqlite_master WHERE type = 'table' AND name != 'sqlite_sequence'");
			$strToReturn = array();
			while ($strRowArray = $objResult->FetchRow()) {
				array_push($strToReturn, $strRowArray[0]);
			}
			return $strToReturn;
		}
		
		public function GetFieldsForTable($strTableName) {
			$objResult = $this->Query(sprintf('PRAGMA table_info (%s%s%s)', $this->strEscapeIdentifierBegin, $strTableName, $this->strEscapeIdentifierEnd));
			$objArrayToReturn = array();
			while($objField = $objResult->FetchRow()) {
				array_push($objArrayToReturn, new QSqLiteDatabaseField($objField, $this, $strTableName));
			}
			return $objArrayToReturn;
		}

		public function InsertId($strTableName = null, $strColumnName = null) {
			return $this->objSqLite->lastInsertRowid();
		}

		public function Close() {
			$this->objSqLite->close();
		}
		
		public function TransactionBegin() {
			// Set to AutoCommit
			$this->NonQuery('BEGIN');
		}

		public function TransactionCommit() {
			$this->NonQuery('COMMIT');
		}

		public function TransactionRollback() {
			$this->NonQuery('ROLLBACK;');
			// Set to AutoCommit
			$this->NonQuery('COMMIT');
		}

		public function GetIndexesForTable($strTableName) {
			return $this->ParseForIndexes($strTableName);
		}

		public function GetForeignKeysForTable($strTableName) {
			// An array with the following items are returned from foreign_key_list:
			//	0:	FK Number
			//	1:	? (usually "0")
			//	2:	related table name
			//	3:	the column that is the FK
			//	4:	related table's column name
			//	5:	? (usually "RESTRICT")
			//	6:	? (usually "RESTRICT")
			//	7:	? (usually "NONE")
			$objForeignKeyArray = array();
			$objForeignKeyListResult = $this->Query(sprintf('PRAGMA foreign_key_list (%s%s%s)', $this->strEscapeIdentifierBegin, $strTableName, $this->strEscapeIdentifierEnd));
			while($objForeignKeyRow = $objForeignKeyListResult->FetchRow()) {
				$strColumnName = $objForeignKeyRow[3];
				$strRelatedTableName = $objForeignKeyRow[2];
				$strRelatedColumnName = $objForeignKeyRow[4];
				$strKeyName = sprintf('fk_%s_%s_to_%s_%s', $strTableName, $strColumnName, $strRelatedTableName, $strRelatedColumnName);
				$objForeignKey = new QDatabaseForeignKey($strKeyName, array($strColumnName), $strRelatedTableName, array($strRelatedColumnName));
				$objForeignKeyArray[] = $objForeignKey;
			}

			return $objForeignKeyArray;
		}

		private function ParseForIndexes($strTableName) {
			$objIndexArray = array();

			// IndexList returns rows with the following values:
			//	0:	Index Number
			//	1:	Index Name
			//	2:	Unique?
			$objIndexListResult = $this->Query(sprintf('PRAGMA index_list (%s%s%s)', $this->strEscapeIdentifierBegin, $strTableName, $this->strEscapeIdentifierEnd));
			while ($objIndexListRow = $objIndexListResult->FetchRow()) {
				$blnUnique = $objIndexListRow[2];
				$strColumnArray = array();

				// IndexInfo returns rows with the following values:
				//	0:	Column Number within this Index
				//	1:	Column Number within the Table itself
				//	2:	Column Name
				$objIndexInfoResult = $this->Query(sprintf('PRAGMA index_info (%s%s%s)', $this->strEscapeIdentifierBegin, $objIndexListRow[1], $this->strEscapeIdentifierEnd));
				while ($objIndexInfoRow = $objIndexInfoResult->FetchRow()) {
					$strColumnArray[] = $objIndexInfoRow[2];
				}

				if (count($strColumnArray) > 0) {
					$objIndex = new QDatabaseIndex($objIndexListRow[1], false, $blnUnique, $strColumnArray);
					$objIndexArray[] = $objIndex;
				}
			}

			// This will parse out the PRAGMA resultset which has the following indexes
			// 	0:	column position
			// 	1:	name
			// 	2:	data type
			// 	3:	Not Null?
			// 	4:	Default Value
			// 	5:	PK?
			$objTableInfoResult = $this->Query(sprintf('PRAGMA table_info (%s%s%s)', $this->strEscapeIdentifierBegin, $strTableName, $this->strEscapeIdentifierEnd));
			$strColumnArray = array();
			while ($objTableInfoRow = $objTableInfoResult->FetchRow()) {
				if ($objTableInfoRow[5]) {
					$strColumnArray[] = $objTableInfoRow[1];
				}
			}
			
			if (count($strColumnArray)) {
				$objIndex = new QDatabaseIndex($strTableName . '_PK_Index', true, true, $strColumnArray);
				$objIndexArray[] = $objIndex;
			}

			return $objIndexArray;
		}
	}

	class QSqLiteDatabaseException extends QDatabaseExceptionBase {
		public function __construct($strMessage, $intNumber, $strQuery) {
			parent::__construct(sprintf("SqLite Error: %s", $strMessage), 2);
			$this->intErrorNumber = $intNumber;
			$this->strQuery = $strQuery;
		}
	}

	class QSqLiteDatabaseResult extends QDatabaseResultBase {
		protected $objSqLiteResult;
		protected $objDb;

		public function __construct(SQLite3Result $objResult, QSqLiteDatabase $objDb) {
			$this->objSqLiteResult = $objResult;
			$this->objDb = $objDb;
		}

		public function FetchArray() {
			return $this->objSqLiteResult->fetchArray(SQLITE3_ASSOC);
		}

		public function FetchFields() {
			throw new QCallerException('Not Implemented');
		}

		public function FetchField() {
			throw new QCallerException('Not Implemented');
		}

		public function FetchRow() {
			return $this->objSqLiteResult->fetchArray(SQLITE3_NUM);
		}

		public function CountRows() {
			$intCount = 0;
			while ($this->objSqLiteResult->FetchArray(SQLITE3_NUM)) $intCount++;
			$this->objSqLiteResult->Reset();

			return $intCount;
		}

		public function CountFields() {
			return $this->objSqLiteResult->NumColumns();
		}

		public function Close() {
			unset($this->objSqLiteResult);
		}

		public function GetNextRow() {
			$strColumnArray = $this->FetchArray();
			
			if ($strColumnArray)
				return new QSqLiteDatabaseRow($strColumnArray);
			else
				return null;
		}

		public function GetRows() {
			$objDbRowArray = array();
			while ($objDbRow = $this->GetNextRow())
				array_push($objDbRowArray, $objDbRow);
			return $objDbRowArray;
		}
	}

	class QSqLiteDatabaseRow extends QDatabaseRowBase {
		protected $strColumnArray;

		public function __construct($strColumnArray) {
			$this->strColumnArray = $strColumnArray;
		}

		public function GetColumn($strColumnName, $strColumnType = null) {
			if (array_key_exists($strColumnName, $this->strColumnArray)) {
				if (is_null($this->strColumnArray[$strColumnName]))
					return null;

				switch ($strColumnType) {
					case QDatabaseFieldType::Bit:
						// Account for single bit value
						$chrBit = $this->strColumnArray[$strColumnName];
						if ((strlen($chrBit) == 1) && (ord($chrBit) == 0))
							return false;

						// Otherwise, use PHP conditional to determine true or false
						return ($this->strColumnArray[$strColumnName]) ? true : false;

					case QDatabaseFieldType::Blob:
					case QDatabaseFieldType::Char:
					case QDatabaseFieldType::VarChar:
						return QType::Cast($this->strColumnArray[$strColumnName], QType::String);

					case QDatabaseFieldType::Date:
					case QDatabaseFieldType::DateTime:
					case QDatabaseFieldType::Time:
						return new QDateTime($this->strColumnArray[$strColumnName]);

					case QDatabaseFieldType::Float:
						return QType::Cast($this->strColumnArray[$strColumnName], QType::Float);

					case QDatabaseFieldType::Integer:
						return QType::Cast($this->strColumnArray[$strColumnName], QType::Integer);

					default:
						return $this->strColumnArray[$strColumnName];
				}
			} else
				return null;
		}

		public function ColumnExists($strColumnName) {
			return array_key_exists($strColumnName, $this->strColumnArray);
		}

		public function GetColumnNameArray() {
			return $this->strColumnArray;
		}
	}

	class QSqLiteDatabaseField extends QDatabaseFieldBase {
		/**
		 * This will parse out the PRAGMA resultset which has the following indexes
		 * 	0:	column position
		 * 	1:	name
		 * 	2:	data type
		 * 	3:	Not Null?
		 * 	4:	Default Value
		 * 	5:	PK?
		 * @param stringp[ $mixFieldData
		 * @param QSqLiteDatabase $objDb
		 * @param string $strTableName
		 * @return QSqLiteDatabaseField
		 */
		public function __construct($mixFieldData, $objDb = null, $strTableName = null) {
			$this->strName = $mixFieldData[1];
			$this->strOriginalName = $mixFieldData[1];

			$this->strTable = $strTableName;
			$this->strOriginalTable = $strTableName;
			$this->strDefault = $mixFieldData[4];

			// SQLite Does NOT Support Lengths
			$this->intMaxLength = null;

			$this->blnNotNull = $mixFieldData[3];
			$this->blnPrimaryKey = $mixFieldData[5];

			// By Default, any PK Integer column is AutoIncrement ("identity") so long as it is not a FK
			$this->blnIdentity = false;
			if ($this->blnPrimaryKey && ($mixFieldData[2] == "INTEGER")) {
				$this->blnIdentity = true;
				$objFkListResult = $objDb->Query(sprintf('PRAGMA foreign_key_list(`%s`);', $strTableName));
				while ($objFkListRow = $objFkListResult->FetchRow()) {
					if ($objFkListRow[3] == $this->strName) $this->blnIdentity = false;
				}
			}
			// Check for Unique
			$this->blnUnique = $this->blnPrimaryKey;

			// IndexList returns rows with the following values:
			//	0:	Index Number
			//	1:	Index Name
			//	2:	Unique?
			if (!$this->blnUnique) {
				$objIndexListResult = $objDb->Query(sprintf('PRAGMA index_list(`%s`);', $this->strTable));
				while ($objIndexListRow = $objIndexListResult->FetchRow()) {
					if ($objIndexListRow[2]) {
						$objIndexInfoResult = $objDb->Query(sprintf('PRAGMA index_info(`%s`);', $objIndexListRow[1]));
						if ($objIndexInfoResult->CountRows() == 1) {
							// IndexInfo returns rows with the following values:
							//	0:	Column Number within this Index
							//	1:	Column Number within the Table itself
							//	2:	Column Name
							while ($objIndexInfoRow = $objIndexInfoResult->FetchRow()) {
								if ($objIndexInfoRow[2] == $this->strName)
									$this->blnUnique = true;
							}
						}
					}
				}
			}

			// Cleanup Field Type
			$strFieldType = explode('(', $mixFieldData[2]);
			$strFieldType = trim($strFieldType[0]);
			$this->SetFieldType($strFieldType);
		}

		protected function SetFieldType($strSqLiteFieldType) {
			switch ($strSqLiteFieldType) {
				case "TINYINT":
				case "BOOLEAN":
					$this->strType = QDatabaseFieldType::Bit;
					break;
				case "INTEGER":
				case "INT":
				case "BIGINT":
				case "SMALLINT":
				case "MEDIUMINT":
					$this->strType = QDatabaseFieldType::Integer;
					break;
				case "FLOAT":
				case "DECIMAL":
					$this->strType = QDatabaseFieldType::Float;
					break;
				case "DOUBLE":
					// NOTE: PHP does not offer full support of double-precision floats.
					// Value will be set as a VarChar which will guarantee that the precision will be maintained.
					//    However, you will not be able to support full typing control (e.g. you would
					//    not be able to use a QFloatTextBox -- only a regular QTextBox)
					$this->strType = QDatabaseFieldType::VarChar;
					break;
				case "TIMESTAMP":
					// System-generated Timestamp values need to be treated as plain text
					$this->strType = QDatabaseFieldType::VarChar;
					$this->blnTimestamp = true;
					break;
				case "DATE":
					$this->strType = QDatabaseFieldType::Date;
					break;
				case "TIME":
					$this->strType = QDatabaseFieldType::Time;
					break;
				case "DATETIME":
					$this->strType = QDatabaseFieldType::DateTime;
					break;
				case "TINYBLOB":
				case "MEDIUMBLOB":
				case "LONGBLOB":
				case "BLOB":
					$this->strType = QDatabaseFieldType::Blob;
					break;
				case "VARCHAR":
				case "TEXT":
				case "LONGTEXT":
				case "MEDIUMTEXT":
					$this->strType = QDatabaseFieldType::VarChar;
					break;
				case "CHAR":
					$this->strType = QDatabaseFieldType::Char;
					break;
				case "YEAR":
					$this->strType = QDatabaseFieldType::Integer;
					break;
				default:
					throw new Exception("Unable to determine SqLite Database Field Type: " . $strSqLiteFieldType);
					break;
			}
		}
	}
?>