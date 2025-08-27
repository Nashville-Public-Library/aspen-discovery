<?php /** @noinspection PhpMissingFieldTypeInspection */

class DevelopmentSprint extends DataObject {
	public $__table = 'development_sprint';
	public $id;
	public $name;
	public $startDate;
	public $endDate;
	public $active;

	public $_relatedTasks;
	public $_totalStoryPoints;


	static $_objectStructure = [];
	static function getObjectStructure(string $context = ''): array {
		if (isset(self::$_objectStructure[$context]) && self::$_objectStructure[$context] !== null) {
			return self::$_objectStructure[$context];
		}
		require_once ROOT_DIR . '/sys/Development/TaskSprintLink.php';
		$taskSprintLinkStructure = TaskSprintLink::getObjectStructure($context);
		unset($taskSprintLinkStructure['sprintId']);

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
				'description' => 'The name of the release',
				'maxLength' => 255,
				'required' => true,
				'canBatchUpdate' => false,
			],
			'startDate' => [
				'property' => 'startDate',
				'type' => 'date',
				'label' => 'Start Date',
				'description' => 'The first day of the sprint',
			],
			'endDate' => [
				'property' => 'endDate',
				'type' => 'date',
				'label' => 'End Date',
				'description' => 'The last day of the sprint',
			],
			'active' => [
				'property' => 'active',
				'type' => 'checkbox',
				'label' => 'Active',
				'description' => 'If the sprint is still active',
				'default' => true,
			],
			'totalStoryPoints' => [
				'property' => 'totalStoryPoints',
				'type' => 'label',
				'label' => 'Total Story Points',
				'description' => 'The total number of story points assigned to the release',
			],
			'relatedTasks' => [
				'property' => 'relatedTasks',
				'type' => 'oneToMany',
				'label' => 'Related Tasks',
				'description' => 'A list of all tasks assigned to this sprint',
				'keyThis' => 'id',
				'keyOther' => 'sprintId',
				'subObjectType' => 'TaskSprintLink',
				'structure' => $taskSprintLinkStructure,
				'sortable' => true,
				'storeDb' => true,
				'allowEdit' => true,
				'canEdit' => true,
				'additionalOneToManyActions' => [],
				'hideInLists' => true,
				'canAddNew' => true,
				'canDelete' => true,
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}

	public function __get($name) {
		if ($name == 'totalStoryPoints') {
			if (!isset($this->_totalStoryPoints) && $this->id) {
				$this->_totalStoryPoints = 0;
				$relatedTasks = $this->getRelatedTasks();
				foreach ($relatedTasks as $task) {
					$this->_totalStoryPoints += $task->getTask()->storyPoints;
				}
			}
			return $this->_totalStoryPoints;
		} elseif ($name == 'relatedTasks') {
			return $this->getRelatedTasks();
		} else {
			return parent::__get($name);
		}
	}

	public function __set($name, $value) {
		if ($name == "relatedTasks") {
			$this->_relatedTasks = $value;
		} else {
			parent::__set($name, $value);
		}
	}

	public function update(string $context = '') : int|bool {
		$ret = parent::update();
		if ($ret !== FALSE) {
			$this->saveRelatedTasks();
		}
		return $ret;
	}

	public function insert(string $context = '') : int|bool {
		$ret = parent::insert();
		if ($ret !== FALSE) {
			$this->saveRelatedTasks();
		}
		return $ret;
	}

	public function saveRelatedTasks() : void {
		if (isset ($this->_relatedTasks) && is_array($this->_relatedTasks)) {
			$this->saveOneToManyOptions($this->_relatedTasks, 'sprintId');
			unset($this->_relatedTasks);
		}
	}

	/**
	 * @return ?TaskEpicLink[]
	 */
	private function getRelatedTasks(): ?array {
		if (!isset($this->_relatedTasks) && $this->id) {
			require_once ROOT_DIR . '/sys/Development/TaskSprintLink.php';
			$this->_relatedTasks = [];
			$task = new TaskSprintLink();
			$task->sprintId = $this->id;
			$task->orderBy('weight asc');
			$task->find();
			while ($task->fetch()) {
				$this->_relatedTasks[$task->id] = clone($task);
			}
		}
		return $this->_relatedTasks;
	}
}