<?php /** @noinspection PhpMissingFieldTypeInspection */


class QuickPollOption extends DataObject {
	public $__table = 'web_builder_quick_poll_option';
	public $id;
	public $pollId;
	public $weight;
	public $label;

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
			'pollId' => [
				'property' => 'pollId',
				'type' => 'label',
				'label' => 'Poll',
				'description' => 'The parent Quick Poll',
			],
			'label' => [
				'property' => 'label',
				'type' => 'text',
				'label' => 'Label',
				'description' => 'A label for the option',
				'size' => '40',
				'maxLength' => 100,
				'required' => true,
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}
}