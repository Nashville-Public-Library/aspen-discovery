<?php /** @noinspection PhpMissingFieldTypeInspection */


class CronProcessLogEntry extends DataObject {
	public $__table = 'cron_process_log';   // table name
	public $id;
	public $cronId;
	public $processName;
	public $startTime;
	public $lastUpdate;
	public $endTime;
	public $numErrors;
	public $numUpdated;
	public $numSkipped;
	public $notes;

	/** @noinspection PhpUnused */
	function getElapsedTime() : string {
		if (empty($this->endTime)) {
			return "";
		} else {
			$elapsedTimeMin = ceil(($this->endTime - $this->startTime) / 60);
			if ($elapsedTimeMin < 60) {
				return $elapsedTimeMin . " min";
			} else {
				$hours = floor($elapsedTimeMin / 60);
				$minutes = $elapsedTimeMin - (60 * $hours);
				return "$hours hours, $minutes min";
			}
		}
	}
}
