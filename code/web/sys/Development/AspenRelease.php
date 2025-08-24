<?php /** @noinspection PhpMissingFieldTypeInspection */

class AspenRelease extends DataObject {
	public $__table = 'aspen_release';
	public $id;
	public $name;
	public $releaseDateTest;
	public $releaseDate;
	public $_relatedTasks;
	public $_totalStoryPoints;

	static $_objectStructure = [];
	static function getObjectStructure(string $context = ''): array {
		if (isset(self::$_objectStructure[$context]) && self::$_objectStructure[$context] !== null) {
			return self::$_objectStructure[$context];
		}
		require_once ROOT_DIR . '/sys/Development/DevelopmentTask.php';
		$developmentTaskStructure = DevelopmentTask::getObjectStructure($context);
		unset($developmentTaskStructure['releaseId']);
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
				'maxLength' => 10,
				'required' => true,
				'canBatchUpdate' => false,
			],
			'releaseDateTest' => [
				'property' => 'releaseDateTest',
				'type' => 'date',
				'label' => 'Release Date to Test Servers',
				'description' => 'The official release to Test and Implementation servers',
			],
			'releaseDate' => [
				'property' => 'releaseDate',
				'type' => 'date',
				'label' => 'Release Date to Production Servers',
				'description' => 'The official release to live servers',
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
				'description' => 'A list of all tasks assigned to this release',
				'keyThis' => 'id',
				'keyOther' => 'releaseId',
				'subObjectType' => 'DevelopmentTask',
				'structure' => $developmentTaskStructure,
				'sortable' => false,
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
					$this->_totalStoryPoints += $task->storyPoints;
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
			$this->saveOneToManyOptions($this->_relatedTasks, 'releaseId');
			unset($this->_relatedTasks);
		}
	}

	/**
	 * @return ?DevelopmentTask[]
	 */
	private function getRelatedTasks(): ?array {
		if (!isset($this->_relatedTasks) && $this->id) {
			require_once ROOT_DIR . '/sys/Development/DevelopmentTask.php';
			$this->_relatedTasks = [];
			$task = new DevelopmentTask();
			$task->releaseId = $this->id;
			$task->find();
			while ($task->fetch()) {
				$this->_relatedTasks[$task->id] = clone($task);
			}
		}
		return $this->_relatedTasks;
	}

	static function getReleasesList() : array {
		$today = new DateTime('now');
		$today = $today->format('Y-m-d');
		$activeVersion = false;
		$release = new AspenRelease();
		$release->orderBy('releaseDate ASC');
		$release->find();
		$releases = [];
		while($release->fetch()) {
			if(!$activeVersion && $release->releaseDate < $today) {
				$activeVersion = $release->name;
			} else {
				if(version_compare($release->name, $activeVersion, '>=') && $release->releaseDate < $today) {
					$activeVersion = $release->name;
				}
			}

			$releases[$release->name]['id'] = $release->id;
			$releases[$release->name]['version'] = $release->name;
			$releases[$release->name]['name'] = $release->name;
			$releases[$release->name]['date'] = $release->releaseDate;
			$releases[$release->name]['dateTesting'] = $release->releaseDateTest;
			$releases[$release->name]['isActive'] = false;
		}

		if($activeVersion) {
			$releases[$activeVersion]['isActive'] = true;
			$releases[$activeVersion]['name'] .= ' (Active)';
		}

		usort($releases, function ($a, $b) {
			return $a['isActive'] != true;
		});

		return $releases;
	}
}