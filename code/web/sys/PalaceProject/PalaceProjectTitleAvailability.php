<?php /** @noinspection PhpMissingFieldTypeInspection */

class PalaceProjectTitleAvailability extends DataObject {
	public $__table = 'palace_project_title_availability';
	public $id;
	public $titleId;
	public $collectionId;
	/** @noinspection PhpUnused */
	public $lastSeen;
	public $deleted;
	public $borrowLink;
	public $needsHold;
	public $previewLink;

	public function getNumericColumnNames(): array {
		return ['deleted', 'needsHold'];
	}
}