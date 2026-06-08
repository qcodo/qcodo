<?php

namespace Qcodo\Handlers\Console;
use Qcodo\Handlers;

/**
 * Only kept for backward compatbility reasons.  Recommendation is to use trait directly.
 */
class ScheduledTasks extends Handlers\Console {
	use HasScheduledTasks;
}
