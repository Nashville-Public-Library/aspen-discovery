<?php
/** @noinspection PhpMissingFieldTypeInspection */

require_once ROOT_DIR . '/sys/Indexing/RecordToInclude.php';

class LibraryRecordToInclude extends RecordToInclude {
	public $__table = 'library_records_to_include';
	public $__displayNameColumn = 'location';
	public $libraryId;

	static function getObjectStructure($context = ''): array {
		$library = new Library();
		$library->orderBy('displayName');
		if (!UserAccount::userHasPermission('Administer All Libraries')) {
			$homeLibrary = Library::getPatronHomeLibrary();
			$library->libraryId = $homeLibrary->libraryId;
		}
		$library->find();
		while ($library->fetch()) {
			$libraryList[$library->libraryId] = $library->displayName;
		}

		$structure = parent::getObjectStructure($context);
		$structure['libraryId'] = [
			'property' => 'libraryId',
			'type' => 'enum',
			'values' => $libraryList,
			'label' => 'Library',
			'description' => 'The id of a library',
		];

		return $structure;
	}
}