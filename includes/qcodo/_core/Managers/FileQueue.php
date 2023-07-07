<?php

namespace Qcodo\Managers;
use QApplicationBase;
use QBaseClass;
use Exception;
use stdClass;

abstract class FileQueue extends QBaseClass {
	protected $token = null;
	protected $reportFormat = 'csv';

	const LogException = 0;
	const LogChange = 1;

	/**
	 * @var array indexed by Log Index type ID
	 */
	protected $logArrayByType;

	/**
	 * @var boolean $verboseFlag
	 */
	protected $verboseFlag;

	public function __construct($verboseFlag = false) {
		if (!$this->token) throw new Exception('no FileQueue token is defined');
		$this->verboseFlag = $verboseFlag;

		// Setup Paths
		if (!is_dir($this->GetPathFor('inbox'))) QApplicationBase::MakeDirectory($this->GetPathFor('inbox'), 0777);
		if (!is_dir($this->GetPathFor('error'))) QApplicationBase::MakeDirectory($this->GetPathFor('error'), 0777);
		if (!is_dir($this->GetPathFor('done'))) QApplicationBase::MakeDirectory($this->GetPathFor('done'), 0777);

		// Setup Reporting
		$this->logArrayByType = array();
		$this->logArrayByType[] = array();
		$this->logArrayByType[] = array();
	}

	/**
	 * @param string $message
	 * @return void
	 */
	protected function LogToConsole($message) {
		if (!$this->verboseFlag) return;
		print $message . "\n";
	}

	/**
	 * @param string $item should be inbox, error or done
	 * @param string|null $filename optional, will provide the full path with filename if specified
	 * @return string
	 */
	protected function GetPathFor($item, $filename = null) {
		$return = sprintf('%s/file_assets/%s/%s', __ROOT__, $this->token, $item);
		if (!is_null($filename)) $return .= '/' . $filename;
		return $return;
	}

	/**
	 * @param integer $logTypeId
	 * @param string[]|stdClass $item
	 * @param string $message
	 * @return void
	 */
	protected function LogReportFor($logTypeId, $item, $message) {
		for ($index = 0; $index <= $logTypeId; $index++) {
			if (!array_key_exists($logTypeId, $this->logArrayByType)) $this->logArrayByType[$index] = array();
		}

		switch ($this->reportFormat) {
			case 'csv':
				$this->logArrayByType[$logTypeId][] = array_merge(array($message), $item);
				break;

			default:
				throw new Exception('Unhandled Report Format: ' . $this->reportFormat);
		}
	}

	/**
	 * @param integer $logTypeId
	 * @return string[][]|stdClass|null
	 */
	public function GetReportFor($logTypeId) {
		if (!array_key_exists($logTypeId, $this->logArrayByType)) return null;

		switch ($this->reportFormat) {
			case 'csv':
				if (!count($this->logArrayByType[$logTypeId])) return null;
				return $this->logArrayByType[$logTypeId];

			default:
				throw new Exception('Unhandled Report Format: ' . $this->reportFormat);
		}
	}

	/**
	 * Processes the queue.  It will take the first item readdir() returns (if any), or is a no-op if nothing is in the inbox.
	 *
	 * OR, a specific file in the inbox can optionally be specified.
	 *
	 * @param string|null $filename optional, if not specified, it will take the first file readdir() returns
	 * @return string the filename that was processed (if any) or NULL if none
	 */
	public function ProcessQueue($filename = null) {
		if ($filename) {
			if (!is_file($this->GetPathFor('inbox', $filename))) throw new Exception('File Not Found in Inbox: ' . $filename);
		} else {
			$directory = opendir($this->GetPathFor('inbox'));
			$filename = null;
			while ( ($file = readdir($directory)) && is_null($filename) ) {
				if (substr($file, 0, 1) != '.') $filename = $file;
			}

			if (is_null($filename)) return null;
		}

		$inboxPath = $this->GetPathFor('inbox', $filename);
		$errorPath = $this->GetPathFor('error', $filename);
		$donePath = $this->GetPathFor('done', $filename);

		rename($inboxPath, $errorPath);
		$this->ProcessFile($errorPath);
		rename($errorPath, $donePath);

		return $filename;
	}

	/**
	 * @param string $path
	 * @return void
	 */
	public abstract function ProcessFile($path);
}
