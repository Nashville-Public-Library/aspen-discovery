<?php /** @noinspection PhpMissingFieldTypeInspection */

require_once ROOT_DIR . '/sys/BaseLogEntry.php';

class CloudLibraryExportLogEntry extends BaseLogEntry {
	public $__table = 'cloud_library_export_log';   // table name
	public $id;
	public $settingId;
	public $notes;
	public $numProducts;
	public $numAdded;
	public $numDeleted;
	public $numUpdated;
	/** @noinspection PhpUnused */
	public $numAvailabilityChanges;
	/** @noinspection PhpUnused */
	public $numMetadataChanges;
	/** @noinspection PhpUnused */
	public $numRegrouped;
	/** @noinspection PhpUnused */
	public $numInvalidRecords;
}
