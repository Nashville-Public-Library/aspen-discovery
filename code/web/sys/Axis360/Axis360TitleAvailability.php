<?php /** @noinspection PhpMissingFieldTypeInspection */

class Axis360TitleAvailability extends DataObject {
	public $__table = 'axis360_title_availability';   // table name

	public $id;
	public $settingId;
	public $titleId;
	/** @noinspection PhpUnused */
	public $libraryPrefix;
	public $available;
	/** @noinspection PhpUnused */
	public $ownedQty;
	/** @noinspection PhpUnused */
	public $totalHolds;
} 