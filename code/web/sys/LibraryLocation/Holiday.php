<?php
/** @noinspection PhpMissingFieldTypeInspection */


class Holiday extends DataObject {
	public $__table = 'holiday';
	public $__displayNameColumn = 'displayName';
	public $displayName;
	public $id;                    // int(11)  not_null primary_key auto_increment
	public $libraryId;             // int(11)
	public $date;                  // date
	public $name;                  // varchar(100)


	static $_objectStructure = [];
	static function getObjectStructure(string $context = ''): array {
		if (isset(self::$_objectStructure[$context]) && self::$_objectStructure[$context] !== null) {
			return self::$_objectStructure[$context];
		}
		$libraryList = Library::getLibraryList(false);

		$structure = [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id of the holiday within the database',
			],
			'libraryId' => [
				'property' => 'libraryId',
				'type' => 'enum',
				'values' => $libraryList,
				'label' => 'Library',
				'description' => 'A link to the library',
			],
			'date' => [
				'property' => 'date',
				'type' => 'date',
				'label' => 'Date',
				'description' => 'The date of a holiday.',
				'required' => true,
			],
			'name' => [
				'property' => 'name',
				'type' => 'text',
				'label' => 'Holiday Name',
				'description' => 'The name of a holiday',
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}

	public function fetch(): bool|DataObject|null {
		$result = parent::fetch();
		if (!empty($this->name)) {
			$this->displayName = $this->name . ' (' . $this->date . ')';
		} else {
			$this->displayName = $this->date;
		}
		return $result;
	}
}