<?php /** @noinspection PhpMissingFieldTypeInspection */

class ComponentEpicLink extends DataObject {
	public $__table = 'component_development_epic_link';
	public $id;
	public $epicId;
	public $componentId;

	static $_objectStructure = [];
	static function getObjectStructure(string $context = ''): array {
		if (isset(self::$_objectStructure[$context]) && self::$_objectStructure[$context] !== null) {
			return self::$_objectStructure[$context];
		}
		$componentList = [];
		require_once ROOT_DIR . '/sys/Support/TicketComponentFeed.php';
		$component = new TicketComponentFeed();
		$component->orderBy('name');
		$component->find();
		while ($component->fetch()) {
			$componentList[$component->id] = $component->name;
		}

		$epicList = [];
		require_once ROOT_DIR . '/sys/Development/DevelopmentEpic.php';
		$epic = new DevelopmentEpic();
		$epic->whereAdd('privateStatus NOT IN (9, 10)');
		$epic->orderBy('name ASC');
		$epic->find();
		while ($epic->fetch()) {
			$epicList[$epic->id] = $epic->name;
		}

		$structure = [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id',
			],
			'epicId' => [
				'property' => 'epicId',
				'type' => 'enum',
				'values' => $epicList,
				'label' => 'Epic',
				'description' => 'The epic related to the component',
				'required' => true,
			],
			'componentId' => [
				'property' => 'componentId',
				'type' => 'enum',
				'values' => $componentList,
				'label' => 'Task',
				'description' => 'The component related to the ticket',
				'required' => true,
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}

	public function getEditLink(string $context): string {
		if ($context == 'relatedEpics') {
			return '/Development/Epics?objectAction=edit&id=' . $this->epicId;
		} else {
			return '/Support/TicketComponentFeed?objectAction=edit&id=' . $this->componentId;
		}
	}
}