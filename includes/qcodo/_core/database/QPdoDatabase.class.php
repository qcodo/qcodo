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
        if (!$this->blnConnectedFlag) $this->Connect();

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
    }

    
    protected function LogQuery($strQuery) {
        if ($this->blnEnableProfiling) {
            // Dereference-ize Backtrace Information
            $objDebugBacktrace = debug_backtrace();

            // Get Rid of Unnecessary Backtrace Info
            $intLength = count($objDebugBacktrace);
            for ($intIndex = 0; $intIndex < $intLength; $intIndex++) {
                if (($intIndex < 2) || ($intIndex > 3))
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

    public function TransactionBegin() {
        $this->objPdo->beginTransaction();
    }

    public function TransactionCommit() {
        $this->objPdo->commit();
    }

    public function TransactionRollBack() {
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
 * 
 * @abstract
 */
abstract class QPdoDatabaseException extends QDatabaseExceptionBase {
    public function __construct($strMessage, $intNumber, $strQuery) {
        parent::__construct(sprintf("PDO %s", $strMessage[2]), 2);
        $this->intErrorNumber = $intNumber;
        $this->strQuery = $strQuery;
    }
}

?>
