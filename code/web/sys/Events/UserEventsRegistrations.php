<?php /** @noinspection PhpMissingFieldTypeInspection */


class UserEventsRegistrations extends DataObject {
	public $__table = 'user_events_registrations';
	public $id;
	public $userId;
	public $barcode;
	public $sourceId;
	/** @noinspection PhpUnused */
	public $waitlist;

	public function getUniquenessFields(): array {
		return [
			'userId',
			'sourceId',
		];
	}

}
