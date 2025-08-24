<?php

require_once ROOT_DIR . '/sys/DB/LibraryLinkedObject.php';

abstract class DB_LibraryLocationLinkedObject extends DB_LibraryLinkedObject {
	/**
	 * @return ?int[]
	 */
	public abstract function getLocations(): ?array;

	public function okToExport(array $selectedFilters): bool {
		$okToExport = parent::okToExport($selectedFilters);
		$selectedLibraries = $selectedFilters['locations'];
		foreach ($selectedLibraries as $locationId) {
			if (array_key_exists($locationId, $this->getLocations())) {
				$okToExport = true;
				break;
			}
		}
		return $okToExport;
	}

	public function getLinksForJSON(): array {
		$links = parent::getLinksForJSON();
		$allLocations = Location::getLocationListAsObjects(false);

		$locations = $this->getLocations();

		$links['locations'] = [];
		foreach ($locations as $locationId) {
			if (array_key_exists($locationId, $allLocations)) {
				$location = $allLocations[$locationId];
				$links['locations'][$locationId] = $location->code;
			}
		}
		return $links;
	}

	public function loadRelatedLinksFromJSON($jsonData, $mappings, string $overrideExisting = 'keepExisting'): bool {
		$result = parent::loadRelatedLinksFromJSON($jsonData, $mappings);
		if (array_key_exists('locations', $jsonData)) {
			$allLocations = Location::getLocationListAsObjects(false);
			$locations = [];
			foreach ($jsonData['locations'] as $ilsCode) {
				if (array_key_exists($ilsCode, $mappings['locations'])) {
					$ilsCode = $mappings['locations'][$ilsCode];
				}
				foreach ($allLocations as $tmpLocation) {
					if ($tmpLocation->code == $ilsCode) {
						$locations[$tmpLocation->locationId] = $tmpLocation->locationId;
						break;
					}
				}
			}
			$this->_locations = $locations;
			$result = true;
		}
		return $result;
	}

	public function toArray($includeRuntimeProperties = true, $encryptFields = false): array {
		//Unset locations since they will be added as links
		$return = parent::toArray($includeRuntimeProperties, $encryptFields);
		unset($return['locations']);
		return $return;
	}
}
