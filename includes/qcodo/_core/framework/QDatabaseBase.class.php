<?php
	/**
	 * Every database adapter must implement the following 5 classes (all which are abstract):
	 * * DatabaseBase
	 * * DatabaseFieldBase
	 * * DatabaseResultBase
	 * * DatabaseRowBase
	 * * DatabaseExceptionBase
	 *
	 * This Database library also has the following classes already defined, and
	 * Database adapters are assumed to use them internally:
	 * * DatabaseIndex
	 * * DatabaseForeignKey
	 * * DatabaseFieldType (which is an abstract class that solely contains constants)
	 */

	/**
	 * Class QDatabaseBase
	 * @property QDatabaseBase $JournalingDatabase
	 * @property string $JournaledById
	 */
	abstract class QDatabaseBase extends QBaseClass {
		// Must be updated for all Adapters
		const Adapter = 'Generic Database Adapter (Abstract)';

		// Protected Member Variables for ALL Database Adapters
		protected $intDatabaseIndex;
		protected $blnEnableProfiling;
		protected $strProfileArray;

		protected $objConfigArray;
		protected $blnConnectedFlag = false;

		protected $objJournalingDatabase;

		protected $strEscapeIdentifierBegin = '"';
		protected $strEscapeIdentifierEnd = '"';

		// Abstract Methods that ALL Database Adapters MUST implement
		abstract public function Connect();

		/**
		 * @param $strQuery
		 * @return QDatabaseResultBase
		 */
		abstract public function Query($strQuery);
		abstract public function NonQuery($strNonQuery);

		abstract public function GetTables();
		abstract public function InsertId($strTableName = null, $strColumnName = null);

		abstract public function GetFieldsForTable($strTableName);
		abstract public function GetIndexesForTable($strTableName);
		abstract public function GetForeignKeysForTable($strTableName);

		abstract public function TransactionBegin($strTransactionIdentifier = null);
		abstract public function TransactionCommit();
		abstract public function TransactionRollBack();

		abstract public function SqlLimitVariablePrefix($strLimitInfo);
		abstract public function SqlLimitVariableSuffix($strLimitInfo);
		abstract public function SqlSortByVariable($strSortByInfo);

		abstract public function Close();

		public function __get($strName) {
			switch ($strName) {
				case 'EscapeIdentifierBegin':
					return $this->strEscapeIdentifierBegin;
				case 'EscapeIdentifierEnd':
					return $this->strEscapeIdentifierEnd;
				case 'EnableProfiling':
					return $this->blnEnableProfiling;
				case 'AffectedRows':
					return -1;

				case 'JournalingDatabase':
					return $this->objJournalingDatabase;

				case 'JournaledById':
					if (!array_key_exists('staticproperty', $this->objConfigArray)) return null;
					if (!($strStaticPropertyName = $this->objConfigArray['staticproperty'])) return null;
					return QApplication::$$strStaticPropertyName;

				case 'Adapter':
					$strConstantName = get_class($this) . '::Adapter';
					return constant($strConstantName) . ' (' . $this->objConfigArray['adapter'] . ')';
				case 'Server':
				case 'Port':
				case 'Database':
				case 'Username':
				case 'Password':
				case 'StaticProperty':
					return $this->objConfigArray[strtolower($strName)];

				case 'Timezone':
					$key = strtolower($strName);
					if (array_key_exists($key, $this->objConfigArray)) return $this->objConfigArray[$key];
					return null;

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
			switch ($strName) {
				case 'JournalingDatabase':
					return ($this->objJournalingDatabase = QType::Cast($mixValue, 'QDatabaseBase'));

				default:
					try {
						return parent::__set($strName);
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
			}
		}

		/**
		 * Constructs a Database Adapter based on the database index and the configuration array of properties for this particular adapter
		 * Sets up the base-level configuration properties for this database,
		 * namely DB Profiling and Database Index
		 *
		 * @param integer $intDatabaseIndex
		 * @param string[] $objConfigArray configuration array as passed in to the constructor by QApplicationBase::InitializeDatabaseConnections();
		 * @return void
		 */
		public function __construct($intDatabaseIndex, $objConfigArray) {
			// Setup DatabaseIndex
			$this->intDatabaseIndex = $intDatabaseIndex;

			// Save the ConfigArray
			$this->objConfigArray = $objConfigArray;

			// Setup Profiling Array (if applicable)
			$this->blnEnableProfiling = QType::Cast($objConfigArray['profiling'], QType::Boolean);
			if ($this->blnEnableProfiling)
				$this->strProfileArray = array();
		}

		/**
		 * Allows for the enabling of DB profiling while in middle of the script
		 *
		 * @return void
		 */
		public function EnableProfiling() {
			// Only perform profiling initialization if profiling is not yet enabled
			if ($this->blnEnableProfiling == false) {
				$this->blnEnableProfiling = true;
				$this->strProfileArray = array();
			}
		}

		/**
		 * Allows for the disabling of DB profiling while in middle of the script
		 *
		 * @return void
		 */
		public function DisableProfiling() {
			// Turn off profiling only if profiling is enabled
			if ($this->blnEnableProfiling == true) {
				$this->blnEnableProfiling = false;
				$this->strProfileArray = array();
			}
		}

		/**
		 * Checks if any database configured has profiling turned on
		 * @return boolean
		 */
		public static function IsAnyDatabaseProfilingEnabled() {
			$blnEnabled = false;

			foreach (QApplication::$Database as $objDb) {
				if($objDb->EnableProfiling == true)
					$blnEnabled = true;
			}

			return $blnEnabled;
		}

		/**
		 * If EnableProfiling is on, then log the query to the profile array
		 *
		 * @param string $strQuery
		 * @return void
		 */
		protected function LogQuery($strQuery) {
			if ($this->blnEnableProfiling) {
				// Dereference-ize Backtrace Information
				$objDebugBacktrace = debug_backtrace();

				// Get Rid of Unnecessary Backtrace Info
				$intLength = count($objDebugBacktrace);
				for ($intIndex = 0; $intIndex < $intLength; $intIndex++) {
					if ($intIndex > 5)
						$objDebugBacktrace[$intIndex] = 'BackTrace ' . $intIndex;
					else {
						if (array_key_exists('args', $objDebugBacktrace[$intIndex])) {
							$intInnerLength = count($objDebugBacktrace[$intIndex]['args']);
							for ($intInnerIndex = 0; $intInnerIndex < $intInnerLength; $intInnerIndex++)
								if (($objDebugBacktrace[$intIndex]['args'][$intInnerIndex] instanceof QQClause) ||
									($objDebugBacktrace[$intIndex]['args'][$intInnerIndex] instanceof QQCondition))
									$objDebugBacktrace[$intIndex]['args'][$intInnerIndex] = sprintf("[%s]", $objDebugBacktrace[$intIndex]['args'][$intInnerIndex]->__toString());
								else if (is_null($objDebugBacktrace[$intIndex]['args'][$intInnerIndex]))
									$objDebugBacktrace[$intIndex]['args'][$intInnerIndex] = 'null';
								else if (gettype($objDebugBacktrace[$intIndex]['args'][$intInnerIndex]) == 'integer')
									$objDebugBacktrace[$intIndex]['args'][$intInnerIndex] = $objDebugBacktrace[$intIndex]['args'][$intInnerIndex];
								else if (gettype($objDebugBacktrace[$intIndex]['args'][$intInnerIndex]) == 'object')
									$objDebugBacktrace[$intIndex]['args'][$intInnerIndex] = 'Object';
								else
									$objDebugBacktrace[$intIndex]['args'][$intInnerIndex] = sprintf("'%s'", $objDebugBacktrace[$intIndex]['args'][$intInnerIndex]);
						}
					}
				}

				// Push it onto the profiling information array
				array_push($this->strProfileArray, $objDebugBacktrace);
				array_push($this->strProfileArray, $strQuery);
			}
		}

		/**
		 * Properly escapes $mixData to be used as a SQL query parameter.
		 * If IncludeEquality is set (usually not), then include an equality operator.
		 * So for most data, it would just be "=".  But, for example,
		 * if $mixData is NULL, then most RDBMS's require the use of "IS".
		 *
		 * @param mixed $mixData
		 * @param boolean $blnIncludeEquality whether or not to include an equality operator
		 * @param boolean $blnReverseEquality whether the included equality operator should be a "NOT EQUAL", e.g. "!="
		 * @return string the properly formatted SQL variable
		 */
		public function SqlVariable($mixData, $blnIncludeEquality = false, $blnReverseEquality = false) {
			// Are we SqlVariabling a BOOLEAN value?
			if (is_bool($mixData)) {
				// Yes
				if ($blnIncludeEquality) {
					// We must include the inequality

					if ($blnReverseEquality) {
						// Do a "Reverse Equality"

						// Check against NULL, True then False
						if (is_null($mixData))
							return 'IS NOT NULL';
						else if ($mixData)
							return '= 0';
						else
							return '!= 0';
					} else {
						// Check against NULL, True then False
						if (is_null($mixData))
							return 'IS NULL';
						else if ($mixData)
							return '!= 0';
						else
							return '= 0';
					}
				} else {
					// Check against NULL, True then False
					if (is_null($mixData))
						return 'NULL';
					else if ($mixData)
						return '1';
					else
						return '0';
				}
			}

			// Check for Equality Inclusion
			if ($blnIncludeEquality) {
				if ($blnReverseEquality) {
					if (is_null($mixData))
						$strToReturn = 'IS NOT ';
					else
						$strToReturn = '!= ';
				} else {
					if (is_null($mixData))
						$strToReturn = 'IS ';
					else
						$strToReturn = '= ';
				}
			} else
				$strToReturn = '';

			// Check for NULL Value
			if (is_null($mixData))
				return $strToReturn . 'NULL';

			// Check for NUMERIC Value
			if (is_integer($mixData) || is_float($mixData))
				return $strToReturn . sprintf('%s', $mixData);

			// Check for DATE Value
			if ($mixData instanceof QDateTime) {
				if ($mixData->IsTimeNull())
					return $strToReturn . sprintf("'%s'", $mixData->__toString('YYYY-MM-DD'));
				else {
					if ($this->Timezone) {
						$originalDateTimeZone = $mixData->getTimezone();
						$mixData->setTimezone(new DateTimeZone($this->Timezone));
						$strToReturn .= sprintf("'%s'", $mixData->__toString(QDateTime::FormatIso));
						$mixData->setTimezone($originalDateTimeZone);
						return $strToReturn;
					}
					return $strToReturn . sprintf("'%s'", $mixData->__toString(QDateTime::FormatIso));
				}
			}

			// Assume it's some kind of string value
			return $strToReturn . sprintf("'%s'", addslashes($mixData));
		}

		public function PrepareStatement($strQuery, $mixParameterArray) {
			foreach ($mixParameterArray as $strKey => $mixValue) {
				if (is_array($mixValue)) {
					$strParameters = array();
					foreach ($mixValue as $mixParameter)
						array_push($strParameters, $this->Database->SqlVariable($mixParameter));
					$strQuery = str_replace(chr(QQNamedValue::DelimiterCode) . '{' . $strKey . '}', implode(',', $strParameters) . ')', $strQuery);
				} else {
					$strQuery = str_replace(chr(QQNamedValue::DelimiterCode) . '{=' . $strKey . '=}', $this->SqlVariable($mixValue, true, false), $strQuery);
					$strQuery = str_replace(chr(QQNamedValue::DelimiterCode) . '{!' . $strKey . '!}', $this->SqlVariable($mixValue, true, true), $strQuery);
					$strQuery = str_replace(chr(QQNamedValue::DelimiterCode) . '{' . $strKey . '}', $this->SqlVariable($mixValue), $strQuery);
				}
			}

			return $strQuery;
		}

		/**
		 * Generate an RFC 9562 version 7 (Unix-time-ordered) UUID.
		 *
		 * No dependencies. Requires 64-bit PHP (PHP_INT_SIZE === 8).
		 *
		 * Layout, per RFC 9562 section 5.7:
		 *   unix_ts_ms  48 bits   milliseconds since the Unix epoch
		 *   version      4 bits   0111
		 *   rand_a      12 bits   sub-millisecond precision (RFC "method 3")
		 *   variant      2 bits   10
		 *   rand_b      62 bits   random
		 *
		 * Using rand_a for sub-millisecond precision rather than random bits matches
		 * MariaDB's own uuid_v7() implementation and makes ids from a single process
		 * strictly increasing, not merely increasing per millisecond.
		 *
		 * REQUIRES MariaDB >= 10.11.5. Earlier 10.11.x byte-swaps every UUID on the
		 * assumption it is a v1, which destroys v7 ordering. Check with:
		 *   SELECT VERSION();
		 */
		public function Uuid7() {
			static $lastMs = 0;
			static $lastSub = -1;

			[$usec, $sec] = explode(' ', microtime());
			$micros = (int) $sec * 1000000 + (int) round(((float) $usec) * 1000000);
			$ms  = intdiv($micros, 1000);
			$sub = intdiv(($micros % 1000) * 4096, 1000);  // 0..999 scaled to 12 bits

			// Keep ids strictly increasing even if the clock stalls or steps backwards.
			if ($ms < $lastMs) {
				$ms  = $lastMs;
				$sub = $lastSub + 1;
			} elseif ($ms === $lastMs && $sub <= $lastSub) {
				$sub = $lastSub + 1;
			}
			if ($sub > 0x0FFF) {   // more than 4096 ids in one millisecond
				$ms++;
				$sub = 0;
			}
			$lastMs  = $ms;
			$lastSub = $sub;

			$rand = random_bytes(8);
			$rand[0] = chr((ord($rand[0]) & 0x3F) | 0x80);  // variant 10
			$hex = bin2hex($rand);

			return sprintf(
				'%08x-%04x-%04x-%s-%s',
				($ms >> 16) & 0xFFFFFFFF,  // unix_ts_ms, high 32 bits
				$ms & 0xFFFF,              // unix_ts_ms, low 16 bits
				0x7000 | $sub,             // version 7 | rand_a (sub-ms)
				substr($hex, 0, 4),        // variant | rand_b high bits
				substr($hex, 4, 12)        // rand_b remainder
			);
		}

		/**
		 * Displays the OutputProfiling results, plus a link which will popup the details of the profiling.
		 *
		 * @return void
		 */
		public function OutputProfiling() {
			if ($this->blnEnableProfiling) {
				print json_encode($this->strProfileArray);
			} else {
				_p('<form></form><b>Profiling was not enabled for this database connection (#' . $this->intDatabaseIndex . ').</b>  To enable, ensure that ENABLE_PROFILING is set to TRUE.', false);
			}
		}
	}

	abstract class QDatabaseFieldBase extends QBaseClass {
		protected $strName;
		protected $strOriginalName;
		protected $strOriginalType;
		protected $strTable;
		protected $strOriginalTable;
		protected $strDefault;
		protected $intMaxLength;

		// Bool
		protected $blnIdentity;
		protected $blnUuid;
		protected $blnNotNull;
		protected $blnPrimaryKey;
		protected $blnIndexed;
		protected $blnUnique;
		protected $blnTimestamp;
		protected $blnUnsigned;

		protected $strType;

		public function __get($strName) {
			switch ($strName) {
				case "Name":
					return $this->strName;
				case "OriginalName":
					return $this->strOriginalName;
				case "OriginalType":
					return $this->strOriginalType;
				case "Table":
					return $this->strTable;
				case "OriginalTable":
					return $this->strOriginalTable;
				case "Default":
					return $this->strDefault;
				case "MaxLength":
					return $this->intMaxLength;
				case "Identity":
					return $this->blnIdentity;
				case "Uuid":
					return $this->blnUuid;
				case "NotNull":
					return $this->blnNotNull;
				case "PrimaryKey":
					return $this->blnPrimaryKey;
				case "Indexed":
					return $this->blnIndexed;
				case "Unique":
					return $this->blnUnique;
				case "Timestamp":
					return $this->blnTimestamp;
				case "Unsigned":
					return $this->blnUnsigned;
				case "Type":
					return $this->strType;
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
	 * Class QDatabaseResultBase
	 * @property QQueryBuilder $QueryBuilder
	 */
	abstract class QDatabaseResultBase extends QBaseClass {
		// Allow to attach a QQueryBuilder object to use the result object as cursor resource for cursor queries.
		protected $objQueryBuilder;

		abstract public function FetchArray();
		abstract public function FetchRow();
		abstract public function FetchField();
		abstract public function FetchFields();
		abstract public function CountRows();
		abstract public function CountFields();

		/**
		 * @return QDatabaseRowBase or null
		 */
		abstract public function GetNextRow();
		abstract public function GetRows();

		abstract public function Close();

		public function __get($strName) {
			switch ($strName) {
				case 'QueryBuilder':
					return $this->objQueryBuilder;
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
			switch ($strName) {
				case 'QueryBuilder':
					try {
						return ($this->objQueryBuilder = QType::Cast($mixValue, 'QQueryBuilder'));
					} catch (QInvalidCastException $objExc) {
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

	abstract class QDatabaseRowBase extends QBaseClass {
		abstract public function GetColumn($strColumnName, $strColumnType = null);
		abstract public function ColumnExists($strColumnName);
		abstract public function GetColumnNameArray();
	}

	/**
	 * @property-read integer $ErrorNumber
	 * @property-read string $Query
	 */
	abstract class QDatabaseExceptionBase extends QCallerException {
		protected $intErrorNumber;
		protected $strQuery;

		public function __get($strName) {
			switch ($strName) {
				case "ErrorNumber":
					return $this->intErrorNumber;
				case "Query";
					return $this->strQuery;
				default:
					return parent::__get($strName);
			}
		}
	}

	class QDatabaseForeignKey extends QBaseClass {
		protected $strKeyName;
		protected $strColumnNameArray;
		protected $strReferenceTableName;
		protected $strReferenceColumnNameArray;

		public function __construct($strKeyName, $strColumnNameArray, $strReferenceTableName, $strReferenceColumnNameArray) {
			$this->strKeyName = $strKeyName;
			$this->strColumnNameArray = $strColumnNameArray;
			$this->strReferenceTableName = $strReferenceTableName;
			$this->strReferenceColumnNameArray = $strReferenceColumnNameArray;
		}

		public function __get($strName) {
			switch ($strName) {
				case "KeyName":
					return $this->strKeyName;
				case "ColumnNameArray":
					return $this->strColumnNameArray;
				case "ReferenceTableName":
					return $this->strReferenceTableName;
				case "ReferenceColumnNameArray":
					return $this->strReferenceColumnNameArray;
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

	class QDatabaseIndex extends QBaseClass {
		protected $strKeyName;
		protected $blnPrimaryKey;
		protected $blnUnique;
		protected $strColumnNameArray;

		public function __construct($strKeyName, $blnPrimaryKey, $blnUnique, $strColumnNameArray) {
			$this->strKeyName = $strKeyName;
			$this->blnPrimaryKey = $blnPrimaryKey;
			$this->blnUnique = $blnUnique;
			$this->strColumnNameArray = $strColumnNameArray;
		}

		public function __get($strName) {
			switch ($strName) {
				case "KeyName":
					return $this->strKeyName;
				case "PrimaryKey":
					return $this->blnPrimaryKey;
				case "Unique":
					return $this->blnUnique;
				case "ColumnNameArray":
					return $this->strColumnNameArray;
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

	abstract class QDatabaseFieldType {
		const Blob = "Blob";
		const VarChar = "VarChar";
		const Char = "Char";
		const Integer = "Integer";
		const DateTime = "DateTime";
		const Date = "Date";
		const Time = "Time";
		const Float = "Float";
		const Bit = "Bit";
	}
?>
