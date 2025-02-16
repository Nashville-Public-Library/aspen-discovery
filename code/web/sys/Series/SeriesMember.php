<?php /** @noinspection PhpMissingFieldTypeInspection */

require_once ROOT_DIR . '/sys/DB/DataObject.php';

class SeriesMember extends DataObject {
	public $__table = 'series_member';
	public $id;
	public $seriesId;
	public $groupedWorkPermanentId;
	public $isPlaceholder;
	public $displayName;
	public $author;
	public $volume;
	public $pubDate;
	public $cover;

	public function getRecordDriver() {
		require_once ROOT_DIR . '/RecordDrivers/GroupedWorkDriver.php';
		$recordDriver = new GroupedWorkDriver($this->groupedWorkPermanentId);
		if (!$recordDriver->isValid()) {
			return null;
		}
		return $recordDriver;
	}
}