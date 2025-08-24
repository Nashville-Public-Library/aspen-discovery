<?php /** @noinspection PhpMissingFieldTypeInspection */

require_once ROOT_DIR . '/sys/CourseReserves/CourseReserveLibraryMapValue.php';

class SeriesIndexingSettings extends DataObject {
	public $__table = 'series_indexing_settings';    // table name
	public $id;
	public $runFullUpdate;
	/** @noinspection PhpUnused */
	public $lastUpdateOfChangedSeries;
	/** @noinspection PhpUnused */
	public $lastUpdateOfAllSeries;

	static $_objectStructure = [];
	static function getObjectStructure(string $context = ''): array {
		if (isset(self::$_objectStructure[$context]) && self::$_objectStructure[$context] !== null) {
			return self::$_objectStructure[$context];
		}
		$structure = [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id',
			],
			'runFullUpdate' => [
				'property' => 'runFullUpdate',
				'type' => 'checkbox',
				'label' => 'Run Full Update',
				'description' => 'Whether or not a full update of all records should be done on the next pass of indexing',
				'default' => 0,
			],
			'lastUpdateOfChangedSeries' => [
				'property' => 'lastUpdateOfChangedSeries',
				'type' => 'timestamp',
				'label' => 'Last Update of Changed Series',
				'description' => 'The timestamp when just changes were loaded',
				'default' => 0,
			],
			'lastUpdateOfAllCourseReserves' => [
				'property' => 'lastUpdateOfAllSeries',
				'type' => 'timestamp',
				'label' => 'Last Update of All Series',
				'description' => 'The timestamp when all course reserves were loaded',
				'default' => 0,
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}
}