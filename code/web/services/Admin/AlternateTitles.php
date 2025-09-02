<?php

require_once ROOT_DIR . '/sys/Grouping/GroupedWorkAlternateTitle.php';
require_once ROOT_DIR . '/services/Admin/ObjectEditor.php';

class Admin_AlternateTitles extends ObjectEditor {
	function getObjectType(): string {
		return 'GroupedWorkAlternateTitle';
	}

	function getToolName(): string {
		return 'AlternateTitles';
	}

	function getPageTitle(): string {
		return 'Manual Grouping Title/Author Variants';
	}

	function getAllObjects($page, $recordsPerPage): array {
		$object = new GroupedWorkAlternateTitle();
		$object->orderBy($this->getSort());
		$this->applyFilters($object);
		$object->limit(($page - 1) * $recordsPerPage, $recordsPerPage);
		$object->find();
		$objectList = [];
		while ($object->fetch()) {
			$objectList[$object->id] = clone $object;
		}
		return $objectList;
	}

	function getDefaultSort(): string {
		return 'dateAdded desc';
	}

	function getObjectStructure($context = ''): array {
		return GroupedWorkAlternateTitle::getObjectStructure($context);
	}

	function getPrimaryKeyColumn(): string {
		return 'id';
	}

	function getIdKeyColumn(): string {
		return 'id';
	}

	function getInstructions(): string {
		return 'https://help.aspendiscovery.org/help/catalog/groupedworks';
	}

	function getBreadcrumbs(): array {
		$breadcrumbs = [];
		$breadcrumbs[] = new Breadcrumb('/Admin/Home', 'Administration Home');
		$breadcrumbs[] = new Breadcrumb('/Admin/Home#cataloging', 'Catalog / Grouped Works');
		$breadcrumbs[] = new Breadcrumb('/Admin/AlternateTitles', 'Manual Grouping Title/Author Variants');
		return $breadcrumbs;
	}

	function getActiveAdminSection(): string {
		return 'cataloging';
	}

	function canView(): bool {
		return UserAccount::userHasPermission('Manually Group and Ungroup Works');
	}

	function canAddNew() {
		return false;
	}

	function applyFilter($object, $fieldName, $filter) {
		if ($fieldName == 'addedByName') {
			$this->applySpecialFilter($object, $fieldName, $filter, [
				'sourceTable' => 'grouped_work_alternate_titles',
				'sourceField' => 'addedBy',
				'targetClass' => 'User',
				'targetField' => 'id',
				'getCompareValueMethod' => 'getDisplayName',
			]);
		} else {
			parent::applyFilter($object, $fieldName, $filter);
		}
	}
}