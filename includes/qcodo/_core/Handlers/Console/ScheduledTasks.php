<?php

namespace Qcodo\Handlers\Console;
use PHPUnit\Util\Exception;
use Qcodo\Handlers;

class ScheduledTasks extends Handlers\Console {
	public function Execute($type) {
		if (!$this->isConsoleProcessUnique()) throw new Exception('Already Running: ' . $this->argumentArray[1]);

		// Go thru each method...
		$foundFlag = false;

		foreach ($this->reflectionClass->getMethods() as $reflectionMethod) {
			// Get docComment for this method
			if (!($docComment = $reflectionMethod->getDocComment())) continue;

			// Ensure that this method is "uses" the type
			if (!strpos($docComment, '@uses ' . $type)) continue;

			// We found at least one!
			$foundFlag = true;

			// If we are here, we are going to be processing this method
			$methodName = $reflectionMethod->name;

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
}
