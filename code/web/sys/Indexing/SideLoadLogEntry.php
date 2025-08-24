<?php /** @noinspection PhpMissingFieldTypeInspection */

require_once ROOT_DIR . '/sys/BaseLogEntry.php';

class SideLoadLogEntry extends BaseLogEntry {
	public $__table = 'sideload_log';   // table name
	public $id;
	public $notes;
	/** @noinspection PhpUnused */
	public $numSideLoadsUpdated;
	/** @noinspection PhpUnused */
	public $sideLoadsUpdated;
	public $numProducts;
	public $numErrors;
	public $numAdded;
	public $numDeleted;
	public $numUpdated;
	public $numSkipped;

}
