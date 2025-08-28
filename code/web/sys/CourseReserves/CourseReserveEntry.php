<?php /** @noinspection PhpMissingFieldTypeInspection */


class CourseReserveEntry extends DataObject {
	public $__table = 'course_reserve_entry';     // table name
	public $id;                              // int(11)  not_null primary_key auto_increment
	public $source;
	public $sourceId;          // int(11)  not_null multiple_key
	public $courseReserveId;                          // int(11)  multiple_key
	public $dateAdded;                       // timestamp(19)  not_null unsigned zerofill binary timestamp
	public $title;

	public function getRecordDriver() : ?RecordInterface{
		if ($this->source == 'GroupedWork') {
			require_once ROOT_DIR . '/RecordDrivers/GroupedWorkDriver.php';
			$recordDriver = new GroupedWorkDriver($this->sourceId);
			if (!$recordDriver->isValid()) {
				return null;
			}
			return $recordDriver;
		} elseif ($this->source == 'OpenArchives') {
			require_once ROOT_DIR . '/RecordDrivers/OpenArchivesRecordDriver.php';
			return new OpenArchivesRecordDriver($this->sourceId);
		} elseif ($this->source == 'Lists') {
			require_once ROOT_DIR . '/RecordDrivers/ListsRecordDriver.php';
			$recordDriver = new ListsRecordDriver($this->sourceId);
			if ($recordDriver->isValid()) {
				return $recordDriver;
			} else {
				return null;
			}
		} elseif ($this->source == 'Genealogy') {
			require_once ROOT_DIR . '/RecordDrivers/PersonRecord.php';
			return new PersonRecord($this->sourceId);
		} elseif ($this->source == 'EbscoEds') {
			require_once ROOT_DIR . '/RecordDrivers/EbscoRecordDriver.php';
			return new EbscoRecordDriver($this->sourceId);
		} else {
			return null;
		}
	}
}
