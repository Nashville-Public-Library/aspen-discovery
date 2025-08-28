<?php /** @noinspection PhpMissingFieldTypeInspection */

require_once ROOT_DIR . '/sys/WebBuilder/CustomFormSubmissionSelection.php';

class CustomFormSubmission extends DataObject {
	public $__table = 'web_builder_custom_from_submission';
	public $id;
	public $formId;
	public $libraryId;
	public $userId;
	public $dateSubmitted;
	public $submission;
	public $isRead;

	public function getUniquenessFields(): array {
		return [
			'id',
		];
	}

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
			'libraryName' => [
				'property' => 'libraryName',
				'type' => 'label',
				'label' => 'Library',
				'description' => 'The name of the library for the submission',
			],
			'userName' => [
				'property' => 'userName',
				'type' => 'label',
				'label' => 'User Name',
				'description' => 'The name of the user who made the submission',
			],
			'dateSubmitted' => [
				'property' => 'dateSubmitted',
				'type' => 'timestamp',
				'label' => 'Date Submitted',
				'description' => 'The date of the form submission',
			],
			'isRead' => [
				'property' => 'isRead',
				'type' => 'checkbox',
				'label' => 'Mark as Read',
				'description' => 'If the submission has been read, archive it',
			],
			'submission' => [
				'property' => 'submission',
				'type' => 'html',
				'label' => 'Submission contents',
				'description' => 'The information that was submitted by the user',
				'hideInLists' => true,
			],
		];
		if (!empty($_REQUEST['formId'])) {
			$form = new CustomForm();
			$form->id = $_REQUEST['formId'];
			if ($form->find(true)) {
				$customFields = $form->getFormFields();
				foreach ($customFields as $i => $field) {
					$structure['field_' . $i] = [
						'property' => 'field_' . $i,
						'type' => 'label',
						'label' => $field->label,
						'description' => $field->description,
						'readOnly' => true,
					];
				}
			}
		}

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}

	public function __get($name) {
		if (isset($this->_data[$name])) {
			return $this->_data[$name] ?? null;
		} elseif ($name == 'libraryName') {
			$library = new Library();
			$library->libraryId = $this->libraryId;
			if ($library->find(true)) {
				$this->_data[$name] = $library->displayName;
			}
			$library->__destruct();
			return $this->_data[$name] ?? null;
		} elseif ($name == 'userName') {
			$user = new User();
			$user->id = $this->userId;
			if ($user->find(true)) {
				$this->_data[$name] = empty($user->displayName) ? ($user->firstname . ' ' . $user->lastname) : $user->displayName;
			}
			$user->__destruct();
			return $this->_data[$name] ?? null;
		} elseif (str_starts_with($name, 'field_')) {
			if (!array_key_exists($name, $this->_data)) {
				$fieldId = str_replace('field_', '', $name);
				$fieldSelection = new CustomFormSubmissionSelection();
				$fieldSelection->formSubmissionId = $this->id;
				$fieldSelection->submissionFieldId = $fieldId;
				if ($fieldSelection->find(true)) {
					$this->_data[$name] = $fieldSelection->formFieldContent;
				}else{
					$this->_data[$name] = '';
				}
			}

			return $this->_data[$name];
		}
        return parent::__get($name);
	}

	public function okToExport(array $selectedFilters): bool {
		$okToExport = parent::okToExport($selectedFilters);
		if (in_array($this->libraryId, $selectedFilters['libraries'])) {
			$okToExport = true;
		}
		return $okToExport;
	}

	public function toArray($includeRuntimeProperties = true, $encryptFields = false): array {
		$return = parent::toArray($includeRuntimeProperties, $encryptFields);
		unset ($return['libraryId']);

		return $return;
	}

	public function getLinksForJSON(): array {
		$links = parent::getLinksForJSON();
		//library
		$allLibraries = Library::getLibraryListAsObjects(false);
		if (array_key_exists($this->libraryId, $allLibraries)) {
			$library = $allLibraries[$this->libraryId];
			$links['library'] = empty($library->subdomain) ? $library->ilsCode : $library->subdomain;
		}
		//User
		$user = new User();
		$user->id = $this->userId;
		if ($user->find(true)) {
			$links['user'] = $user->ils_barcode;
		}
		return $links;
	}

	public function loadEmbeddedLinksFromJSON($jsonData, $mappings, string $overrideExisting = 'keepExisting') : void {
		parent::loadEmbeddedLinksFromJSON($jsonData, $mappings, $overrideExisting);

		if (isset($jsonData['library'])) {
			$allLibraries = Library::getLibraryListAsObjects(false);
			$subdomain = $jsonData['library'];
			if (array_key_exists($subdomain, $mappings['libraries'])) {
				$subdomain = $mappings['libraries'][$subdomain];
			}
			foreach ($allLibraries as $tmpLibrary) {
				if ($tmpLibrary->subdomain == $subdomain || $tmpLibrary->ilsCode == $subdomain) {
					$this->libraryId = $tmpLibrary->libraryId;
					break;
				}
			}
		}
		if (isset($jsonData['user'])) {
			$username = $jsonData['user'];
			$user = new User();
			$user->ils_barcode = $username;
			if ($user->find(true)) {
				$this->userId = $user->id;
			}
		}
	}
}