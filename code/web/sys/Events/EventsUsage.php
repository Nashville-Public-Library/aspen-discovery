<?php /** @noinspection PhpMissingFieldTypeInspection */


class EventsUsage extends DataObject {
	public $__table = 'events_usage';
	public $id;
	public $type;
	public $source;
	public $identifier;
	public $year;
	public $month;
	public $timesViewedInSearch;
	public $timesUsed;

	public function getUniquenessFields(): array {
		return [
			'type',
			'source',
			'identifier',
			'year',
			'month',
		];
	}
}