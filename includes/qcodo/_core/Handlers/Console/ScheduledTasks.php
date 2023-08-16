<?php

namespace Qcodo\Handlers\Console;
use \Exception;
use Qcodo\Handlers;

class ScheduledTasks extends Handlers\Console {
	public function ExecuteMinutely()	{ $this->Execute('minutely'); }
	public function ExecuteHourly()		{ $this->Execute('hourly'); }
	public function ExecuteDaily()		{ $this->Execute('daily'); }
	public function ExecuteNightly()	{ $this->Execute('nightly'); }
	public function ExecuteMorningly()	{ $this->Execute('morningly'); }
	public function ExecuteWeekly()		{ $this->Execute('weekly'); }
	public function ExecuteBiweekly()	{ $this->Execute('biweekly'); }
	public function ExecuteMonthly()	{ $this->Execute('monthly'); }
	public function ExecuteBimonthly()	{ $this->Execute('bimonthly'); }
	public function ExecuteQuarterly()	{ $this->Execute('quarterly'); }

	private function Execute($type) {
		if (!$this->isConsoleProcessUnique()) throw new Exception('Already Running: ' . $this->argumentArray[1]);

		$methodNameArray = $this->GetMethodArrayFor($type);

		// Go thru each method...
		$foundFlag = false;
		foreach ($methodNameArray as $methodName) {
			// We found at least one!
			$foundFlag = true;

			switch ($pid = pcntl_fork()) {
				case -1:
					throw new Exception('Fork Failed: ' . $this->argumentArray[1] . ' ' . $methodName);

				case 0:
					$this->$methodName();
					exit();

				default:
					pcntl_waitpid($pid, $status);
					break;
			}
		}

		if (!$foundFlag) {
			throw new Exception('No Scheduled Tasks Found for Type: ' . $type);
		}
	}

	private function GetMethodArrayFor($type) {
		$methodNameArray = array();
		foreach ($this->reflectionClass->getMethods() as $reflectionMethod) {
			// Get docComment for this method
			if (!($docComment = $reflectionMethod->getDocComment())) continue;

			// Ensure that this method is "uses" the type
			if (!strpos($docComment, '@uses ' . $type)) continue;

			$methodNameArray[] = $reflectionMethod->name;
		}

		return $methodNameArray;
	}

	public function ListFor($type) {
		printf("List of [%s] methods in [%s]:\n", $type, $this->reflectionClass->getShortName());
		foreach ($this->GetMethodArrayFor($type) as $methodName) {
			printf("    %s::%s\n", $this->reflectionClass->getShortName(), $methodName);
		}
	}
}
