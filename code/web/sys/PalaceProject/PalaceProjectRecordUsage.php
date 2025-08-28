<?php /** @noinspection PhpMissingFieldTypeInspection */


class PalaceProjectRecordUsage extends DataObject {
	public $__table = 'palace_project_record_usage';
	public $id;
	public $instance;
	public $palaceProjectId;
	public $year;
	public $month;
	public $timesHeld;
	public $timesCheckedOut;

	public function getUniquenessFields(): array {
		return [
			'instance',
			'palaceProjectId',
			'year',
			'month',
		];
	}

	public function okToExport(array $selectedFilters): bool {
		$okToExport = parent::okToExport($selectedFilters);
		if (in_array($this->instance, $selectedFilters['instances'])) {
			$okToExport = true;
		}
		return $okToExport;
	}
}