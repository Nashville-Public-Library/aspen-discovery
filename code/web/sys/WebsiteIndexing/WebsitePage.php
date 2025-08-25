<?php /** @noinspection PhpMissingFieldTypeInspection */


class WebsitePage extends DataObject {
	public $__table = 'website_pages';
	public $id;
	public $websiteId;
	public $url;
	public $deleted;
	/** @noinspection PhpUnused */
	public $deleteReason;

	static $_objectStructure = [];
	static function getObjectStructure(string $context = ''): array {
		if (isset(self::$_objectStructure[$context]) && self::$_objectStructure[$context] !== null) {
			return self::$_objectStructure[$context];
		}

		$websites = [];
		$websiteSettings = new WebsiteIndexSetting();
		$websiteSettings->orderBy('name');
		$websiteSettings->find();
		while ($websiteSettings->fetch()) {
			$websites[$websiteSettings->id] = $websiteSettings->name;
		}
		$structure = [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id',
			],
			'websiteId' => [
				'property' => 'websiteId',
				'type' => 'enum',
				'values' => $websites,
				'label' => 'Website',
				'description' => 'The Website for the page',
				'required' => true,
				'readOnly' => true,
			],
			'url' => [
				'property' => 'url',
				'type' => 'url',
				'label' => 'Page URL',
				'description' => 'The URL to the page',
				'maxLength' => 255,
				'required' => true,
				'readOnly' => true,
			],
			'deleted' => [
				'property' => 'deleted',
				'type' => 'checkbox',
				'label' => 'Deleted?',
				'description' => 'Whether or not the page is deleted.',
				'required' => false,
				'readOnly' => true,
			],
			'deleteReason' => [
				'property' => 'deleteReason',
				'type' => 'text',
				'label' => 'Deletion Reason',
				'description' => 'The reason the page was deleted',
				'maxLength' => 255,
				'required' => false,
				'readOnly' => true,
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}

	public function getNumericColumnNames(): array {
		return [
			'id',
			'deleted',
		];
	}
}