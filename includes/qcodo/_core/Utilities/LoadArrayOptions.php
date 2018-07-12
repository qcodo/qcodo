<?php

namespace Qcodo\Utilities;
use QBaseClass;
use QQ;
use QQNode;
use QQClause;
use QJsonBaseClass;

class LoadArrayOptions extends QBaseClass {
	public $resultsOrderBy = null;
	public $resultsOrderAscending = null;

	public $resultsLimitOffset = null;
	public $resultsLimitCount = null;
	public $totalCount = null;

	public function __construct($resultsOrderBy = null, $resultsOrderAscending = null, $resultsLimitOffset = null, $resultsLimitCount = null) {
		$this->resultsOrderBy = $resultsOrderBy;
		$this->resultsOrderAscending = $resultsOrderBy;
		$this->resultsLimitOffset = $resultsLimitOffset;
		$this->resultsLimitCount = $resultsLimitCount;
	}

	/**
	 * @param QJsonBaseClass $jsonSchemaObject
	 * @return LoadArrayOptions
	 */
	public static function CreateFromSchemaObject(QJsonBaseClass $jsonSchemaObject) {
		$loadArrayOptions = new LoadArrayOptions();
		if ($jsonSchemaObject->IsPropertySet('resultsOrderBy')) {
			$loadArrayOptions->resultsOrderBy = $jsonSchemaObject->resultsOrderBy;
			$loadArrayOptions->resultsOrderAscending = $jsonSchemaObject->resultsOrderAscending;
		}

		if ($jsonSchemaObject->IsPropertySet('resultsLimitOffset')) {
			$loadArrayOptions->resultsLimitOffset = $jsonSchemaObject->resultsLimitOffset;
			$loadArrayOptions->resultsLimitCount = $jsonSchemaObject->resultsLimitCount;
		}

		return $loadArrayOptions;
	}

	/**
	 * @param QQNode $orderByNode
	 * @return QQClause[]
	 */
	public function GenerateClause(QQNode $orderByNode = null) {
		$clause = array();
		if ($orderByNode) {
			if ($this->resultsOrderAscending === false)
				$clause[] = QQ::OrderBy($orderByNode, false);
			else
				$clause[] = QQ::OrderBy($orderByNode);
		}

		if ($this->resultsLimitOffset || $this->resultsLimitCount) {
			$clause[] = QQ::LimitInfo($this->resultsLimitCount, $this->resultsLimitOffset);
		}

		return $clause;
	}
}