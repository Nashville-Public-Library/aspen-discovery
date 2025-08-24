<?php /** @noinspection PhpMissingFieldTypeInspection */

class IlsRecord extends DataObject {
	public $__table = 'ils_records';    // table name
	public $id;
	public $ilsId;
	/** @noinspection PhpUnused */
	public $checksum;
	/** @noinspection PhpUnused */
	public $dateFirstDetected;
	public $deleted;
	public $dateDeleted;
	/** @noinspection PhpUnused */
	public $suppressedNoMarcAvailable;
	public $source;
	public $sourceData;
	public $lastModified;
	public $suppressed;
	/** @noinspection PhpUnused */
	public $suppressionNotes;

	public function getNumericColumnNames(): array {
		return [
			'suppressed',
			'deleted',
			'dateFirstDetected',
			'dateDeleted',
			'suppressedNoMarcAvailable',
		];
	}

	public function getCompressedColumnNames(): array {
		return ['sourceData'];
	}
}