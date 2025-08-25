<?php /** @noinspection PhpMissingFieldTypeInspection */

class VdxSetting extends DataObject {
	public $__table = 'vdx_settings';
	public $id;
	public $name;
	public $baseUrl;
	public $submissionEmailAddress;
	public $patronKey;
	public $reqVerifySource;

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
			'name' => [
				'property' => 'name',
				'type' => 'text',
				'label' => 'Name',
				'description' => 'The Name of the Hold Group',
				'maxLength' => 50,
			],
			'baseUrl' => [
				'property' => 'baseUrl',
				'type' => 'url',
				'label' => 'Base Url',
				'description' => 'The URL for the VDX System',
				'maxLength' => 255,
			],
			'submissionEmailAddress' => [
				'property' => 'submissionEmailAddress',
				'type' => 'text',
				'label' => 'Submission Email Address',
				'description' => 'The Address where new submissions are sent (separate multiple addresses with semi-colons)',
				'maxLength' => 255,
			],
			'patronKey' => [
				'property' => 'patronKey',
				'type' => 'text',
				'label' => 'Patron Key',
				'description' => 'The PatronKey to be sent as part of the request',
				'maxLength' => 50,
			],
			'reqVerifySource' => [
				'property' => 'reqVerifySource',
				'type' => 'text',
				'label' => 'Request Verification Source',
				'description' => 'The Name of the Hold Group',
				'maxLength' => 50,
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}

	/**
	 * @return string[]
	 */
	public function getUniquenessFields(): array {
		return ['name'];
	}
}