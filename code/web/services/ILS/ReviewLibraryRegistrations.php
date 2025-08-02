<?php
require_once ROOT_DIR . '/Action.php';
require_once ROOT_DIR . '/services/Admin/ObjectEditor.php';
require_once ROOT_DIR . '/sys/SelfRegistrationForms/SierraRegistration.php';

class ILS_ReviewLibraryRegistrations extends ObjectEditor {
	function getObjectType(): string {
		return 'SierraRegistration';
	}

	function getModule(): string {
		return "ILS";
	}

	function getToolName(): string {
		return 'ReviewLibraryRegistrations';
	}

	function getPageTitle(): string {
		return 'Review Library Registrations';
	}

	function getAllObjects($page, $recordsPerPage): array {
		$object = new SierraRegistration();

		$list = [];

		$object->orderBy($this->getSort());
		$this->applyFilters($object);
		$object->limit(($page - 1) * $recordsPerPage, $recordsPerPage);
		$object->find();
		while ($object->fetch()) {
			$list[$object->id] = clone $object;
		}
		$list = self::getPatronData($list);

		return $list;
	}

	function getDefaultSort(): string {
		return 'dateRegistered desc';
	}

	function getObjectStructure($context = ''): array {
		return SierraRegistration::getObjectStructure($context);
	}

	function getPrimaryKeyColumn(): string {
		return 'id';
	}

	function getIdKeyColumn(): string {
		return 'id';
	}

	function getBreadcrumbs(): array {
		$breadcrumbs = [];
		$breadcrumbs[] = new Breadcrumb('/Admin/Home', 'Administration Home');
		$breadcrumbs[] = new Breadcrumb('/Admin/Home#ils_integration', 'ILS Integration');
		$breadcrumbs[] = new Breadcrumb('/ILS/ReviewLibraryRegistrations', 'Review Library Registrations');
		return $breadcrumbs;
	}

	function getActiveAdminSection(): string {
		return 'ils_integration';
	}

	function canView(): bool {
		return UserAccount::userHasPermission('Administer Self Registration Forms');
	}

	function canAddNew(): bool {
		return false;
	}

	function canCopy(): bool {
		return false;
	}

	function getDefaultFilters(array $filterFields): array {
		return [
			'name' => [
				'fieldName' => 'name',
				'filterType' => 'text',
				'filterValue' => '',
				'field' => $filterFields['name'],
			],
			'sierraPType' => [
				'fieldName' => 'sierraPType',
				'filterType' => 'text',
				'filterValue' => '',
				'field' => $filterFields['sierraPType'],
			],
			'sierraPCode1' => [
				'fieldName' => 'sierraPCode1',
				'filterType' => 'text',
				'filterValue' => '',
				'field' => $filterFields['sierraPCode1'],
			],
			'sierraPCode3' => [
				'fieldName' => 'sierraPCode3',
				'filterType' => 'text',
				'filterValue' => '',
				'field' => $filterFields['sierraPCode3'],
			],
		];
	}

	function getPatronData($list) {
		global $library;
		$accountProfile = $library->getAccountProfile();
		$catalogDriverName = trim($accountProfile->driver);
		$catalogDriver = null;
		if (!empty($catalogDriverName)) {
			$catalogDriver = CatalogFactory::getCatalogConnectionInstance($catalogDriverName, $accountProfile);
		}
		if ($catalogDriver->driver instanceof Sierra) {
			$ids = array_column($list, 'patronId');
			$patrons = $catalogDriver->driver->getPatronsByIdList($ids);
			foreach ($patrons->entries as $i => $patron) {
				$list[$i + 1]->_sierraData = $patron;
			}
			return $list;
		} else {
			return [];
		}
	}

}