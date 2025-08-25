<?php /** @noinspection PhpMissingFieldTypeInspection */


class ReindexLogEntry extends BaseLogEntry {
	public $__table = 'reindex_log';   // table name
	public $id;
	public $notes;
	/** @noinspection PhpUnused */
	public $numWorksProcessed;
	public $numErrors;
	/** @noinspection PhpUnused */
	public $numInvalidRecords;
}
