<?php /** @noinspection PhpMissingFieldTypeInspection */

class CloudLibraryProduct extends DataObject {
	public $__table = 'cloud_library_title';

	public $id;
	public $cloudLibraryId;
	public $title;
	public $subTitle;
	public $author;
	public $format;
	/** @noinspection PhpUnused */
	public $rawChecksum;
	public $rawResponse;
	/** @noinspection PhpUnused */
	public $lastChange;
	/** @noinspection PhpUnused */
	public $dateFirstDetected;
	public $deleted;
}