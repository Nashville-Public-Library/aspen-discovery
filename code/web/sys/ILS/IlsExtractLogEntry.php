<?php /** @noinspection PhpMissingFieldTypeInspection */

require_once ROOT_DIR . '/sys/BaseLogEntry.php';

class IlsExtractLogEntry extends BaseLogEntry {
	public $__table = 'ils_extract_log';   // table name
	public $id;
	public $indexingProfile;
	public $notes;
	/** @noinspection PhpUnused */
	public $isFullUpdate;
	/** @noinspection PhpUnused */
	public $numRegrouped;
	/** @noinspection PhpUnused */
	public $numChangedAfterGrouping;
	/** @noinspection PhpUnused */
	public $currentId;
	public $numProducts;
	/** @noinspection PhpUnused */
	public $numRecordsWithInvalidMarc;
	public $numErrors;
	public $numAdded;
	public $numDeleted;
	public $numUpdated;
	public $numSkipped;
	/** @noinspection PhpUnused */
	public $numInvalidRecords;
}
