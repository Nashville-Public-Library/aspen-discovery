<?php /** @noinspection PhpMissingFieldTypeInspection */


class UserPalaceProjectUsage extends DataObject {
	public $__table = 'user_palace_project_usage';
	public $id;
	public $instance;
	public $userId;
	public $year;
	public $month;
	public $usageCount; //Number of holds/clicks to online for sideloads

	public function getUniquenessFields(): array {
		return [
			'instance',
			'userId',
			'year',
			'month',
		];
	}

	public function toArray($includeRuntimeProperties = true, $encryptFields = false): array {
		$return = parent::toArray($includeRuntimeProperties, $encryptFields);
		unset($return['userId']);
		return $return;
	}

	public function okToExport(array $selectedFilters): bool {
		$okToExport = parent::okToExport($selectedFilters);
		if (in_array($this->instance, $selectedFilters['instances'])) {
			$okToExport = true;
		}
		if ($okToExport) {
			$okToExport = false;
			$user = new User();
			$user->id = $this->userId;
			if ($user->find(true)) {
				if ($user->homeLocationId == 0 || in_array($user->homeLocationId, $selectedFilters['locations'])) {
					$okToExport = true;
				}
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