<?php

require_once ROOT_DIR . '/sys/DB/DataObject.php';

class Marriage extends DataObject {
	public $__table = 'marriage';    // table name
	public $__primaryKey = 'marriageId';
	public $marriageId;
	public $personId;
	public $spouseName;
	public $spouseId;
	public $marriageDate;
	public $marriageDateDay;
	public $marriageDateMonth;
	public $marriageDateYear;
	public $comments;

	function id() {
		return $this->marriageId;
	}

	function getNumericColumnNames(): array {
		return [
			'marriageDateDay',
			'marriageDateMonth',
			'marriageDateYear',
		];
	}

	function label() {
		return $this->spouseName . (isset($this->marriageDate) ? (' - ' . $this->marriageDate) : '');
	}

	static function getObjectStructure($context = ''): array {
		$structure = [
			[
				'property' => 'marriageId',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id of the marriage in the database',
				'storeDb' => true,
			],
			[
				'property' => 'personId',
				'type' => 'hidden',
				'label' => 'Person Id',
				'description' => 'The id of the person this marriage is for',
				'storeDb' => true,
			],
			[
				'property' => 'spouseName',
				'type' => 'text',
				'maxLength' => 100,
				'label' => 'Spouse',
				'description' => 'The spouse&apos;s name.',
				'storeDb' => true,
			],
			[
				'property' => 'marriageDate',
				'type' => 'partialDate',
				'label' => 'Date',
				'description' => 'The date of the marriage.',
				'storeDb' => true,
				'propNameMonth' => 'marriageDateMonth',
				'propNameDay' => 'marriageDateDay',
				'propNameYear' => 'marriageDateYear',
			],
			[
				'property' => 'comments',
				'type' => 'textarea',
				'rows' => 10,
				'cols' => 80,
				'label' => 'Comments',
				'description' => 'Information about the marriage.',
				'storeDb' => true,
				'hideInLists' => true,
			],
		];
		return $structure;
	}

	function insert($context = '') {
		$ret = parent::insert();
		//Load the person this is for, and update solr
		if ($this->personId) {
			require_once ROOT_DIR . '/sys/Genealogy/Person.php';
			$person = new Person();
			$person->personId = $this->personId;
			$person->find(true);
			$person->saveToSolr();
		}
		return $ret;
	}

	function update($context = '') {
		$ret = parent::update();
		//Load the person this is for, and update solr
		if ($this->personId) {
			require_once ROOT_DIR . '/sys/Genealogy/Person.php';
			$person = new Person();
			$person->personId = $this->personId;
			$person->find(true);
			$person->saveToSolr();
		}
		return $ret;
	}

	function delete($useWhere = false, $hardDelete = false) : int {
		$personId = $this->personId;
		$ret = parent::delete($useWhere, $hardDelete);
		//Load the person this is for, and update solr
		if ($personId) {
			require_once ROOT_DIR . '/sys/Genealogy/Person.php';
			$person = new Person();
			$person->personId = $this->personId;
			$person->find(true);
			$person->saveToSolr();
		}
		return $ret;
	}
}