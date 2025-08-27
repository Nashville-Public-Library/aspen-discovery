<?php /** @noinspection PhpMissingFieldTypeInspection */

class NonGroupedRecord extends DataObject {
	public $__table = 'nongrouped_records';
	public $id;
	public $source;
	public $recordId;
	public $notes;

	static $_objectStructure = [];
	static function getObjectStructure(string $context = ''): array {
		if (isset(self::$_objectStructure[$context]) && self::$_objectStructure[$context] !== null) {
			return self::$_objectStructure[$context];
		}

		global $indexingProfiles;
		global $sideLoadSettings;
		$availableSources = [];
		foreach ($indexingProfiles as $profile) {
			$availableSources[$profile->name] = $profile->name;
		}
		foreach ($sideLoadSettings as $profile) {
			$availableSources[$profile->name] = $profile->name;
		}
		$availableSources['axis360'] = 'Boundless';
		$availableSources['cloud_library'] = 'cloudLibrary';
		$availableSources['hoopla'] = 'Hoopla';
		$availableSources['overdrive'] = 'Overdrive';
		$availableSources['palace_project'] = 'Palace Project';

		$structure =  [
			[
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id of the merged grouped work in the database',
				'storeDb' => true,
			],
			[
				'property' => 'source',
				'type' => 'enum',
				'values' => $availableSources,
				'label' => 'Source of the Record Id',
				'description' => 'The source of the record to avoid merging.',
				'default' => 'ils',
				'storeDb' => true,
				'required' => true,
			],
			[
				'property' => 'recordId',
				'type' => 'text',
				'size' => 36,
				'maxLength' => 36,
				'label' => 'Record Id',
				'description' => 'The id of the record that should not be merged.',
				'storeDb' => true,
				'required' => true,
			],
			[
				'property' => 'notes',
				'type' => 'text',
				'size' => 255,
				'maxLength' => 255,
				'label' => 'Notes',
				'description' => 'Notes related to the record.',
				'storeDb' => true,
				'required' => true,
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}

}