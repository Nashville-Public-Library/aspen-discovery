<?php /** @noinspection PhpMissingFieldTypeInspection */


class RecordOwned extends DataObject {
	public $id;
	public $indexingProfileId;
	public $location;
	public /** @noinspection PhpUnused */
		$locationsToExclude;
	public $subLocation;
	public /** @noinspection PhpUnused */
		$subLocationsToExclude;

	static $_objectStructure = [];
	static function getObjectStructure(string $context = ''): array {
		if (isset(self::$_objectStructure[$context]) && self::$_objectStructure[$context] !== null) {
			return self::$_objectStructure[$context];
		}

		$indexingProfiles = [];
		require_once ROOT_DIR . '/sys/Indexing/IndexingProfile.php';
		$indexingProfile = new IndexingProfile();
		$indexingProfile->orderBy('name');
		$indexingProfile->find();
		while ($indexingProfile->fetch()) {
			$indexingProfiles[$indexingProfile->id] = $indexingProfile->name;
		}
		$structure = [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id',
			],
			'indexingProfileId' => [
				'property' => 'indexingProfileId',
				'type' => 'enum',
				'values' => $indexingProfiles,
				'label' => 'Indexing Profile Id',
				'description' => 'The Indexing Profile this map is associated with',
			],
			'location' => [
				'property' => 'location',
				'type' => 'regularExpression',
				'label' => 'Location (Regex)',
				'description' => 'A regular expression for location codes to include',
				'maxLength' => '100',
				'required' => true,
				'forcesReindex' => true,
			],
			'locationsToExclude' => [
				'property' => 'locationsToExclude',
				'type' => 'regularExpression',
				'label' => 'Locations to Exclude (Regex)',
				'description' => 'A regular expression for location codes to exclude',
				'maxLength' => '200',
				'required' => false,
				'forcesReindex' => true,
			],
			'subLocation' => [
				'property' => 'subLocation',
				'type' => 'regularExpression',
				'label' => 'Sub Location (Regex)',
				'description' => 'A regular expression for sublocation codes to include',
				'maxLength' => '100',
				'required' => false,
				'forcesReindex' => true,
			],
			'subLocationsToExclude' => [
				'property' => 'subLocationsToExclude',
				'type' => 'regularExpression',
				'label' => 'Sub Locations to Exclude (Regex)',
				'description' => 'A regular expression for sublocation codes to exclude',
				'maxLength' => '200',
				'required' => false,
				'forcesReindex' => true,
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}
}