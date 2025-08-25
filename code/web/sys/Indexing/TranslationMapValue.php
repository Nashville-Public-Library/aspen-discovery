<?php /** @noinspection PhpMissingFieldTypeInspection */

class TranslationMapValue extends DataObject {
	public $__table = 'translation_map_values';    // table name
	public $id;
	public $translationMapId;
	public $value;
	public $translation;

	public function __toString() {
		return "$this->value => $this->translation";
	}

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
				'description' => 'The unique id within the database',
			],
			'translationMapId' => [
				'property' => 'translationMapId',
				'type' => 'foreignKey',
				'label' => 'Translation Map Id',
				'description' => 'The Translation Map this is associated with',
			],
			'value' => [
				'property' => 'value',
				'type' => 'text',
				'label' => 'Value',
				'description' => 'The value to be translated',
				'maxLength' => '50',
				'required' => true,
				'forcesReindex' => true,
			],
			'translation' => [
				'property' => 'translation',
				'type' => 'text',
				'label' => 'Translation',
				'description' => 'The translated value',
				'maxLength' => '255',
				'required' => false,
				'forcesReindex' => true,
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}
}