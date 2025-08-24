<?php /** @noinspection PhpMissingFieldTypeInspection */


abstract class BaseLogEntry extends DataObject {
	public $numErrors;
	public $startTime;
	public $endTime;
	public $lastUpdate;

	/** @noinspection PhpUnused */
	function getElapsedTime() : string {
		if (!isset($this->endTime) || is_null($this->endTime)) {
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