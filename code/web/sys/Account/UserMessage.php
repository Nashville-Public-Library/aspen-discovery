<?php /** @noinspection PhpMissingFieldTypeInspection */

class UserMessage extends DataObject {
	public $__table = 'user_messages';
	public $id;
	public $userId;
	public $messageType;
	public $messageLevel;
	public $message;
	public $isDismissed;
	public $action1Title;
	public $action1;
	public $action2Title;
	public $action2;
	public $addendum;
	public $relatedObjectId;

	public function getNumericColumnNames(): array {
		return [
			'isDismissed',
			'userId',
		];
	}

	public function toArray($includeRuntimeProperties = true, $encryptFields = false): array {
		$return = parent::toArray($includeRuntimeProperties, $encryptFields);
		unset($return['userId']);
		return $return;
	}

	public function okToExport(array $selectedFilters): bool {
		$okToExport = parent::okToExport($selectedFilters);
		$user = new User();
		$user->id = $this->userId;
		if ($user->find(true)) {
			if ($user->homeLocationId == 0 || in_array($user->homeLocationId, $selectedFilters['locations'])) {
				$okToExport = true;
			}
		}
		return $okToExport;
	}

	public function getLinksForJSON(): array {
		$links = parent::getLinksForJSON();
		$user = new User();
		$user->id = $this->userId;
		if ($user->find(true)) {
			$links['user'] = $user->ils_barcode;
		}
		return $links;
	}

	public function loadEmbeddedLinksFromJSON($jsonData, $mappings, string $overrideExisting = 'keepExisting') : void {
		parent::loadEmbeddedLinksFromJSON($jsonData, $mappings, $overrideExisting);
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

