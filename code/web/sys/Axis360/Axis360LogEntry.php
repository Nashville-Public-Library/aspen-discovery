<?php /** @noinspection PhpMissingFieldTypeInspection */

require_once ROOT_DIR . '/sys/BaseLogEntry.php';

class Axis360LogEntry extends BaseLogEntry {
	public $__table = 'axis360_export_log';   // table name
	public $id;
	public $settingId;
	public $notes;
	public $numProducts;
	public $numErrors;
	public $numAdded;
	public $numDeleted;
	public $numUpdated;
	public $numSkipped;
	public $numInvalidRecords;
}
