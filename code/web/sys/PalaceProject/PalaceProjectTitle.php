<?php /** @noinspection PhpMissingFieldTypeInspection */

class PalaceProjectTitle extends DataObject {
	public $id;
	public $palaceProjectId;
	public $title;
	/** @noinspection PhpUnused */
	public $rawChecksum;
	public $rawResponse;
	/** @noinspection PhpUnused */
	public $dateFirstDetected;

	public $__table = 'palace_project_title';

	public function getCompressedColumnNames(): array {
		return ['rawResponse'];
	}

}