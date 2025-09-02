<?php /** @noinspection PhpMissingFieldTypeInspection */

require_once ROOT_DIR . '/sys/BaseLogEntry.php';

class HooplaExportLogEntry extends BaseLogEntry {
	public $__table = 'hoopla_export_log';   // table name
	public $id;
	public $notes;
	public $numProducts;
	public $numErrors;
	public $numAdded;
	public $numDeleted;
	public $numUpdated;
	public $numSkipped;
	/** @noinspection PhpUnused */
	public $numInvalidRecords;
}
