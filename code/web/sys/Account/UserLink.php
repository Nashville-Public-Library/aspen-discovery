<?php /** @noinspection PhpMissingFieldTypeInspection */

class UserLink extends DataObject {
	public $id;
	public $primaryAccountId;
	public $linkedAccountId;

	public $__table = 'user_link';    // table name

	public function getUniquenessFields(): array {
		return [
			'primaryAccountId',
			'linkedAccountId',
		];
	}

	public function okToExport(array $selectedFilters): bool {
		$okToExport = parent::okToExport($selectedFilters);

		$primaryAccountOkToExport = false;
		$user = new User();
		$user->id = $this->primaryAccountId;
		if ($user->find(true)) {
			if ($user->homeLocationId == 0 || in_array($user->homeLocationId, $selectedFilters['locations'])) {
				$primaryAccountOkToExport = true;
			}
		}

		$linkedAccountOkToExport = false;
		$user = new User();
		$user->id = $this->linkedAccountId;
		if ($user->find(true)) {
			if ($user->homeLocationId == 0 || in_array($user->homeLocationId, $selectedFilters['locations'])) {
				$linkedAccountOkToExport = true;
			}
		}

		if ($linkedAccountOkToExport && $primaryAccountOkToExport) {
			$okToExport = true;
		}

		return $okToExport;
	}

	public function toArray($includeRuntimeProperties = true, $encryptFields = false): array {
		$return = parent::toArray($includeRuntimeProperties, $encryptFields);
		unset($return['primaryAccountId']);
		unset($return['linkedAccountId']);
		return $return;
	}

	public function getLinksForJSON(): array {
		$links = parent::getLinksForJSON();
		$user = new User();
		$user->id = $this->primaryAccountId;
		if ($user->find(true)) {
			$links['primaryAccount'] = $user->ils_barcode;
		}
		$user = new User();
		$user->id = $this->linkedAccountId;
		if ($user->find(true)) {
			$links['linkedAccount'] = $user->ils_barcode;
		}
		return $links;
	}

	public function loadEmbeddedLinksFromJSON($jsonData, $mappings, string $overrideExisting = 'keepExisting') : void {
		parent::loadEmbeddedLinksFromJSON($jsonData, $mappings, $overrideExisting);
		if (isset($jsonData['primaryAccount'])) {
			$username = $jsonData['primaryAccount'];
			$user = new User();
			$user->ils_barcode = $username;
			if ($user->find(true)) {
				$this->primaryAccountId = $user->id;
			}

			$username = $jsonData['linkedAccount'];
			$user = new User();
			$user->ils_barcode = $username;
			if ($user->find(true)) {
				$this->linkedAccountId = $user->id;
			}
		}
	}
}