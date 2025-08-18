<?php

class EBSCOhostDatabase extends DataObject {
	public $__table = 'ebscohost_database';
	public $id;
	public $searchSettingId;
	public $shortName;
	public $displayName;
	public $hasDateAndRelevancySorting;
	public $allowSearching;
	public $searchByDefault;
	public $showInExploreMore;
	public $showInCombinedResults;
	public $logo;

	public function getNumericColumnNames(): array {
		return [
			'searchSettingId',
			'allowSearching',
			'searchByDefault',
			'showInExploreMore',
			'showInCombinedResults',
			'hasDateAndRelevancySorting',
		];
	}

	public static function getObjectStructure($context = '') {
		return [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id',
			],
			'shortName' => [
				'property' => 'shortName',
				'type' => 'text',
				'label' => 'Short Name',
				'maxLength' => 50,
				'description' => 'The Short Name from EBSCO',
				'required' => true,
				'readOnly' => true,
			],
			'displayName' => [
				'property' => 'displayName',
				'type' => 'text',
				'label' => 'Display Name',
				'description' => 'The Display Name from EBSCO',
				'readOnly' => true,
			],
			'hasDateAndRelevancySorting' => [
				'property' => 'hasDateAndRelevancySorting',
				'type' => 'checkbox',
				'label' => 'Has Date and Relevancy Sorts',
				'description' => 'If the database results can be sorted by date and relevancy',
				'readOnly' => true,
			],
			'allowSearching' => [
				'property' => 'allowSearching',
				'type' => 'checkbox',
				'label' => 'Allow Searching',
				'description' => 'If the database can be searched',
			],
			'searchByDefault' => [
				'property' => 'searchByDefault',
				'type' => 'checkbox',
				'label' => 'Search By Default',
				'description' => 'If the database is searched by default when searching everything',
			],
			'showInExploreMore' => [
				'property' => 'showInExploreMore',
				'type' => 'checkbox',
				'label' => 'Show in Explore More',
				'description' => 'If the database is shown in Explore More',
			],
			'showInCombinedResults' => [
				'property' => 'showInCombinedResults',
				'type' => 'checkbox',
				'label' => 'Show in Combined Results',
				'description' => 'If the database is shown in Combined Results',
			],
			'logo' => [
				'property' => 'logo',
				'type' => 'image',
				'label' => 'Logo',
				'description' => 'A logo for the database for use in explore more (max width 400px).',
				'maxWidth' => 400,
				'hideInLists' => true,
			],
		];
	}

	public function getEditLink($context): string {
		return '/EBSCO/EBSCOhostDatabases?objectAction=edit&id=' . $this->id;
	}

	public function update($context = '') {
		$this->clearDefaultCovers();
		$ret = parent::update();

		return $ret;
	}

	public function insert($context = '') {
		$this->clearDefaultCovers();
		$ret = parent::insert();

		return $ret;
	}

	public function delete($useWhere = false, $hardDelete = false) : int {
		$this->deleted = 1;
		$this->clearDefaultCovers();
		return $this->update();
	}

	private function clearDefaultCovers() {
		require_once ROOT_DIR . '/sys/Covers/BookCoverInfo.php';
		$covers = new BookCoverInfo();
		$covers->reloadAllDefaultCovers();
	}

}