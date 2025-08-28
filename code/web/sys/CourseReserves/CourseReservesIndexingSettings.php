<?php /** @noinspection PhpMissingFieldTypeInspection */

require_once ROOT_DIR . '/sys/CourseReserves/CourseReserveLibraryMapValue.php';

class CourseReservesIndexingSettings extends DataObject {
	public $__table = 'course_reserves_indexing_settings';    // table name
	public $id;
	public $runFullUpdate;
	/** @noinspection PhpUnused */
	public $lastUpdateOfChangedCourseReserves;
	/** @noinspection PhpUnused */
	public $lastUpdateOfAllCourseReserves;
	/** @var CourseReserveLibraryMapValue[] */
	public $_libraryMappings;

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
			'runFullUpdate' => [
				'property' => 'runFullUpdate',
				'type' => 'checkbox',
				'label' => 'Run Full Update',
				'description' => 'Whether or not a full update of all records should be done on the next pass of indexing',
				'default' => 0,
			],
			'lastUpdateOfChangedCourseReserves' => [
				'property' => 'lastUpdateOfChangedCourseReserves',
				'type' => 'timestamp',
				'label' => 'Last Update of Changed Course Reserves',
				'description' => 'The timestamp when just changes were loaded',
				'default' => 0,
			],
			'lastUpdateOfAllCourseReserves' => [
				'property' => 'lastUpdateOfAllCourseReserves',
				'type' => 'timestamp',
				'label' => 'Last Update of All Course Reserves',
				'description' => 'The timestamp when all course reserves were loaded',
				'default' => 0,
			],

			'libraryMappings' => [
				'property' => 'libraryMappings',
				'type' => 'oneToMany',
				'label' => 'Library Mappings',
				'description' => 'Translation for library names.',
				'keyThis' => 'id',
				'keyOther' => 'settingId',
				'subObjectType' => 'CourseReserveLibraryMapValue',
				'structure' => CourseReserveLibraryMapValue::getObjectStructure($context),
				'sortable' => false,
				'storeDb' => true,
				'allowEdit' => false,
				'canEdit' => false,
				'canAddNew' => true,
				'canDelete' => true,
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}

	public function __get($name) {
		if ($name == "libraryMappings") {
			return $this->getLibraryMappings();
		} else {
			return parent::__get($name);
		}
	}

	public function getLibraryMappings(): ?array {
		if (!isset($this->_libraryMappings)) {
			//Get the list of translation maps
			if ($this->id) {
				$this->_libraryMappings = [];
				$value = new CourseReserveLibraryMapValue();
				$value->settingId = $this->id;
				$value->orderBy('value ASC');
				$value->find();
				while ($value->fetch()) {
					$this->_libraryMappings[$value->id] = clone($value);
				}
			}
		}
		return $this->_libraryMappings;
	}

	public function __set($name, $value) {
		if ($name == "libraryMappings") {
			$this->_libraryMappings = $value;
		} else {
			parent::__set($name, $value);
		}
	}

	/**
	 * Override the update functionality to save the associated translation maps
	 *
	 * @see DB/DB_DataObject::update()
	 */
	public function update(string $context = '') : int|bool {
		$ret = parent::update();
		if ($ret === FALSE) {
			return false;
		} else {
			$this->saveLibraryMappings();
		}
		return true;
	}

	/**
	 * Override the update functionality to save the associated translation maps
	 *
	 * @see DB/DB_DataObject::insert()
	 */
	public function insert(string $context = '') : int|bool {
		$ret = parent::insert();
		if ($ret === FALSE) {
			return false;
		} else {
			$this->saveLibraryMappings();
		}
		return true;
	}

	public function saveLibraryMappings() : void {
		if (isset ($this->_libraryMappings)) {
			foreach ($this->_libraryMappings as $value) {
				if ($value->_deleteOnSave) {
					$value->delete();
				} else {
					if (isset($value->id) && is_numeric($value->id)) {
						$value->update();
					} else {
						$value->settingId = $this->id;
						$value->insert();
					}
				}
			}
			//Clear the translation maps so they are reloaded the next time
			unset($this->_libraryMappings);
		}
	}

}