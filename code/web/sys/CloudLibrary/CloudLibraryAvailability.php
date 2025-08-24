<?php /** @noinspection PhpMissingFieldTypeInspection */

class CloudLibraryAvailability extends DataObject {
	public $__table = 'cloud_library_availability';

	public $id;
	public $settingId;
	public $cloudLibraryId;
	public $totalCopies;
	/** @noinspection PhpUnused */
	public $sharedCopies;
	/** @noinspection PhpUnused */
	public $totalLoanCopies;
	/** @noinspection PhpUnused */
	public $totalHoldCopies;
	/** @noinspection PhpUnused */
	public $sharedLoanCopies;
}