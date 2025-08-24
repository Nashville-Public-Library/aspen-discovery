<?php /** @noinspection PhpMissingFieldTypeInspection */

class TaskEpicLink extends DataObject {
	public $__table = 'development_task_epic_link';
	public $id;
	public $epicId;
	public $taskId;
	public $weight;

	private $_task;

	static $_objectStructure = [];
	static function getObjectStructure(string $context = ''): array {
		if (isset(self::$_objectStructure[$context]) && self::$_objectStructure[$context] !== null) {
			return self::$_objectStructure[$context];
		}
		$taskList = [];
		require_once ROOT_DIR . '/sys/Development/DevelopmentTask.php';
		$task = new DevelopmentTask();
		$task->find();
		while ($task->fetch()) {
			$taskList[$task->id] = "$task->name ($task->storyPoints)";
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
			'weight' => [
				'property' => 'weight',
				'type' => 'numeric',
				'label' => 'Weight',
				'weight' => 'Defines how items are sorted.  Lower weights are displayed higher.',
				'required' => true,
			],
			'epicId' => [
				'property' => 'epicId',
				'type' => 'enum',
				'values' => $epicList,
				'label' => 'Epic',
				'description' => 'The epic related to the task',
				'required' => true,
			],
			'taskId' => [
				'property' => 'taskId',
				'type' => 'enum',
				'values' => $taskList,
				'label' => 'Task',
				'description' => 'The task related to the epic',
				'required' => true,
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}

	public function getTask(): ?DevelopmentTask {
		if (is_null($this->_task) && !empty($this->taskId)) {
			require_once ROOT_DIR . '/sys/Development/DevelopmentTask.php';
			$this->_task = new DevelopmentTask();
			$this->_task->id = $this->taskId;
			$this->_task->find(true);
		}
		return $this->_task;
	}

	/** @noinspection PhpUnusedParameterInspection */
	public function getEditLink(string $context): string {
		return '/Development/Tasks?objectAction=edit&id=' . $this->taskId;
	}
}