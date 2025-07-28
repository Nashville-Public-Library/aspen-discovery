<?php

use JetBrains\PhpStorm\NoReturn;

require_once ROOT_DIR . '/services/Admin/ObjectEditor.php';
require_once ROOT_DIR . '/sys/ObjectRestoration.php';

/**
 * Displays every soft-deleted row across a curated set of classes
 * and lets privileged staff restore or permanently purge them.
 *
 * To add a new object for restoration:
 * 1. Ensure the target DataObject implements `supportsSoftDelete()` returning
 *    `true` and that its table contains `deleted` (TINYINT(1)), `dateDeleted` (INT),
 *    and `deletedBy` (INT(11)) columns.  The base `DataObject` already handles
 *    the logic.
 * 2. Append an entry to `self::$managedClasses` below:
 *        `'NewClass' => [
 *            'titleColumn' => 'title',
 *            'classFile'   => '/sys/Path/To/NewClass.php',
 *        ],`
 *    Use the path from `ROOT_DIR` so the class can be lazy-required.
 * 3. Modify the target DataObject's `delete()` method to prevent deletions of
 *    object dependencies unless `$useWhere` is `true`, which indicates a hard deletion.
 *    If the target DataObject has no `delete()`, you may have to override `purgeExpired`
 *    for proper hard-deletion cleanup (e.g., ImageUpload).
 * 4. The batch-purge cron and UI will pick it up the next time the admin page is loaded.
 */
class Admin_ObjectRestorations extends ObjectEditor {
	private static array $managedClasses = [
		'UserList'              => ['titleColumn' => 'title', 'classFile' => '/sys/UserLists/UserList.php'],
		'BasicPage'             => ['titleColumn' => 'title', 'classFile' => '/sys/WebBuilder/BasicPage.php'],
		'PortalPage'            => ['titleColumn' => 'title', 'classFile' => '/sys/WebBuilder/PortalPage.php'],
		'CustomForm'            => ['titleColumn' => 'title', 'classFile' => '/sys/WebBuilder/CustomForm.php'],
		'CustomWebResourcePage' => ['titleColumn' => 'title', 'classFile' => '/sys/WebBuilder/CustomWebResourcePage.php'],
		'WebResource'           => ['titleColumn' => 'name',  'classFile' => '/sys/WebBuilder/WebResource.php'],
		'ImageUpload'           => ['titleColumn' => 'title', 'classFile' => '/sys/File/ImageUpload.php'],
		'FileUpload'            => ['titleColumn' => 'title', 'classFile' => '/sys/File/FileUpload.php'],
		'Placard'               => ['titleColumn' => 'title', 'classFile' => '/sys/LocalEnrichment/Placard.php'],
	];
	private array $cachedRows;

	public function __construct() {
		parent::__construct();
		$this->cachedRows = $this->buildRows();
	}

	function getObjectType(): string { return 'ObjectRestoration'; }
	function getToolName(): string  { return 'ObjectRestorations'; }
	function getModule(): string    { return 'Admin'; }
	function getPageTitle(): string { return 'Restore Deleted Objects'; }
	function getDefaultSort(): string { return 'deletedOn desc'; }
	function getPrimaryKeyColumn(): string { return 'compositeId'; }
	function getIdKeyColumn(): string { return 'compositeId'; }
	function canView(): bool { return UserAccount::userHasPermission('Administer Object Restoration'); }
	function canAddNew(): bool { return false; }
	function canDelete(): bool { return true; }
	function canEdit(DataObject $object): bool { return false; }
	function canCopy(): bool { return false; }
	function canBatchDelete(): bool { return false; }
	function canBatchEdit(): bool { return false; }
	function canCompare(): bool { return true; }
	protected function showHistoryLinks(): bool { return false; }
	protected function showEditButtons(): bool { return false; }
	public function canActiveUserEdit() : bool { return false; }
	public function canExportToCSV() : bool { return false; }

	function getAllObjects($page, $recordsPerPage): array {
		$offset = ($page - 1) * $recordsPerPage;
		return array_slice($this->cachedRows, $offset, $recordsPerPage, true);
	}
	function getNumObjects(): int { return count($this->cachedRows); }

	function getObjectStructure($context = ''): array {
		// For comparison, use the actual object's structure instead of ObjectRestoration's minimal structure.
		if ($context === 'compare' && isset($_REQUEST['selectedObject'])) {
			$selectedObjects = array_keys($_REQUEST['selectedObject']);
			if (!empty($selectedObjects)) {
				$id = $selectedObjects[0];
				if (str_contains($id, '_')) {
					[$objectType, $actualId] = explode('_', $id, 2);
					if (class_exists($objectType)) {
						$managedClasses = self::$managedClasses;
						if (isset($managedClasses[$objectType])) {
							require_once ROOT_DIR . self::$managedClasses[$objectType]['classFile'];
							$tempObject = new $objectType();
							$structure = $tempObject->getObjectStructure($context);
							$structure['dateDeleted'] = [
								'property' => 'dateDeleted',
								'type' => 'timestamp',
								'label' => 'Deleted',
								'hideInLists' => true,
							];
							$structure['deletedBy'] = [
								'property' => 'deletedBy',
								'type' => 'label',
								'label' => 'Deleted By',
								'hideInLists' => true,
							];
							return $structure;
						}
					}
				}
			}
		}
		return ObjectRestoration::getObjectStructure($context);
	}

	/**
	 * Scan all managed classes for rows where `deleted = 1` and `dateDeleted > 0`.
	 *
	 * Because the list is virtual, build it once per request and cache the
	 * resulting `ObjectRestoration` objects in `$this->cachedRows`.
	 *
	 * @return ObjectRestoration[] Keyed by composite ID `<Class>_<pk>`.
	 */
	private function buildRows(): array {
		$rows = [];
		foreach (self::$managedClasses as $class => $info) {
			// Lazy-load class definition only when iterating.
			require_once ROOT_DIR . $info['classFile'];
			$sample = new $class();
			// Select only necessary columns for faster lookup.
			$sample->selectAdd();
			$sample->selectAdd($sample->getPrimaryKey());
			$sample->selectAdd($info['titleColumn']);
			$sample->selectAdd('dateDeleted');
			$sample->selectAdd('deletedBy');
			if ($class === 'UserList') {
				$sample->selectAdd('user_id');
			}
			if (!method_exists($sample, 'supportsSoftDelete') || !$sample->supportsSoftDelete()) continue;

			$sample->_includeDeleted = true;
			$sample->deleted = 1;
			$sample->whereAdd('dateDeleted > 0');
			$sample->find();
			while ($sample->fetch()) {
				$rows[$class . '_' . $sample->getPrimaryKeyValue()] = $this->createRestorationItem($sample, $class, $info['titleColumn']);
			}
		}

		return $rows;
	}

	/**
	 * Convert a real soft-deleted DataObject into an `ObjectRestoration`
	 * virtual object that the list can display.
	 *
	 * @param DataObject $orig Original soft-deleted object.
	 * @param string $class Class name (same as object type).
	 * @param string $titleColumn Column holding the human-readable title.
	 * @return ObjectRestoration Populated virtual row.
	 */
	private function createRestorationItem(DataObject $orig, string $class, string $titleColumn): ObjectRestoration {
		$item = new ObjectRestoration();
		$item->objectType = $class;
		$item->id = $orig->getPrimaryKeyValue();
		$item->compositeId = $class . '_' . $item->id;
		$item->title = $orig->$titleColumn ?? '';

		// User info enrich (only for UserList).
		if ($class === 'UserList' && property_exists($orig, 'user_id') && !empty($orig->user_id)) {
			require_once ROOT_DIR . '/sys/Account/User.php';
			$user = new User();
			$user->id = $orig->user_id;
			if ($user->find(true)) {
				$item->userInfo = trim($user->getBarcode() . ' - ' . $user->getDisplayName(), ' -');
			} else {
				$item->userInfo = 'User ID: ' . $orig->user_id;
			}
		}

		if (!empty($orig->dateDeleted)) {
			$item->deletedOn = date('Y-m-d H:i', $orig->dateDeleted);
			$expires = $orig->dateDeleted + 2592000; // 30 days
			$item->daysRemaining = max(0, floor(($expires - time()) / 86400));
			if ($item->daysRemaining <= 0) {
				$item->daysRemaining = 'Final Day!';
			}
		}

		if (!empty($orig->deletedBy)) {
			require_once ROOT_DIR . '/sys/Account/User.php';
			$deletingUser = new User();
			$deletingUser->id = $orig->deletedBy;
			if ($deletingUser->find(true)) {
				$item->deletedBy = trim($deletingUser->getBarcode() . ' - ' . $deletingUser->getDisplayName(), ' -');
			} else {
				$item->deletedBy = 'User ID: ' . $orig->deletedBy;
			}
		} else {
			$item->deletedBy = 'Unknown';
		}

		return $item;
	}

	/**
	 * Restore a single object from the "recycle-bin" back to an active state.
	 */
	#[NoReturn] public function restore(): void {
		$compositeId = $_REQUEST['id'] ?? '';
		$user = UserAccount::getActiveUserObj();
		if (str_contains($compositeId, '_')) {
			[$class, $id] = explode('_', $compositeId, 2);
			if (class_exists($class)) {
				$obj = new $class();
				$pk = $obj->getPrimaryKey();
				$obj->$pk = $id;
				$obj->_includeDeleted = true;
				if ($obj->find(true)) {
					if ($obj->restore()) {
						$user->updateMessage = "Restored $class #$id.";
						$user->updateMessageIsError = false;
					} else {
						$user->updateMessage = "Failed to restore $class #$id.";
						$user->updateMessageIsError = true;
					}
					$user->update();
				}
			}
		}
		header('Location: /Admin/ObjectRestorations');
		exit;
	}

	#[NoReturn] public function history(): void {
		$this->showHistory();
	}

	/**
	 * Permanently delete one soft-deleted row (hard delete).
	 *
	 * @noinspection PhpUnused
	 */
	#[NoReturn] public function hardDeleteSingle(): void {
		$compositeId = $_REQUEST['id'] ?? '';
		$user = UserAccount::getActiveUserObj();
		if (str_contains($compositeId, '_')) {
			[$class, $id] = explode('_', $compositeId, 2);
			if (class_exists($class)) {
				$deleteObj = new $class();
				$pk = $deleteObj->getPrimaryKey();
				$deleteObj->$pk = $id;
				$deleteObj->_includeDeleted = true;

				if ($deleteObj->find(true)) {
					if ($deleteObj->delete(true)) {
						$user->updateMessage = "Permanently deleted $class #$id.";
						$user->updateMessageIsError = false;
					} else {
						$user->updateMessage = "Failed to delete $class #$id.";
						$user->updateMessageIsError = true;
					}
				} else {
					$user->updateMessage = "Could not find $class #$id for deletion.";
					$user->updateMessageIsError = true;
				}
				$user->update();
			}
		}
		header('Location: /Admin/ObjectRestorations');
		exit;
	}

	/**
	 * Permanently delete many (or all) soft-deleted rows.
	 * If no checkboxes are selected, assume "delete all".
	 *
	 * @noinspection PhpUnused
	 */
	#[NoReturn] public function batchHardDelete(): void {
		$selected = $_REQUEST['selectedObject'] ?? [];
		if (empty($selected)) {
			$selected = array_keys($this->cachedRows);
		}

		$deletedCnt = 0;
		foreach ($selected as $compositeId => $unused) {
			$key = is_numeric($compositeId) ? $unused : $compositeId;
			if (!str_contains($key, '_')) continue;
			[$class, $id] = explode('_', $key, 2);
			if (!class_exists($class)) continue;
			$obj = new $class();
			$pk = $obj->getPrimaryKey();
			$obj->$pk = $id;
			$obj->_includeDeleted = true;
			if ($obj->find(true)) {
				if ($obj->delete(true)) $deletedCnt++;
			}
		}

		$user = UserAccount::getActiveUserObj();
		$user->updateMessage = "Permanently deleted $deletedCnt object(s).";
		$user->updateMessageIsError = false;
		$user->update();

		header('Location: /Admin/ObjectRestorations');
		exit;
	}

	/**
	 * ObjectEditor override that retrieves an object even if it is soft-deleted
	 * so the history and comparison screens still work.
	 *
	 * @param $id
	 * @return ?DataObject
	 */
	function getExistingObjectById($id): ?DataObject {
		if (!str_contains($id, '_')) {
			return null;
		}
		[$objectType, $actualId] = explode('_', $id, 2);
		if (!class_exists($objectType)) { return null; }

		$object = new $objectType();
		$pk = $object->getPrimaryKey();
		$object->$pk = $actualId;
		$object->_includeDeleted = true;
		if ($object->find(true)) {
			if (isset(self::$managedClasses[$objectType])) {
				$titleColumn = self::$managedClasses[$objectType]['titleColumn'];
				// Map labeling fields (e.g., name) to title for comparison display.
				if ($titleColumn !== 'title' && isset($object->$titleColumn)) {
					$object->title = $object->$titleColumn;
				}
			}
			return $object;
		}
		return null;
	}

	/**
	 * Override to handle special property value formatting for ObjectRestoration fields.
	 *
	 * @param $property
	 * @param $propertyValue
	 * @param $propertyType
	 * @return string
	 */
	public function getPropertyValue($property, $propertyValue, $propertyType): string {
		if ($property['property'] === 'deletedBy' && !empty($propertyValue)) {
			require_once ROOT_DIR . '/sys/Account/User.php';
			$deletingUser = new User();
			$deletingUser->id = $propertyValue;
			if ($deletingUser->find(true)) {
				return trim($deletingUser->getBarcode() . ' - ' . $deletingUser->getDisplayName(), ' -');
			} else {
				return 'User ID: ' . $propertyValue;
			}
		}

		if ($property['property'] === 'dateDeleted' && !empty($propertyValue)) {
			return date('Y-m-d H:i', $propertyValue);
		}

		$result = parent::getPropertyValue($property, $propertyValue, $propertyType);
		return $result ?? '';
	}

	public function customListActions(): array {
		$selectedOnclick = "return AspenDiscovery.Admin.recycleBinDelete('selected');";
		$allOnclick = "return AspenDiscovery.Admin.recycleBinDelete('all');";

		return [
			[
				'label' => '<i class="fas fa-trash"></i> Permanently Delete Selected',
				'action' => 'batchHardDelete',
				'class' => 'btn-danger',
				'onclick' => $selectedOnclick,
			],
			[
				'label' => '<i class="fas fa-trash"></i> Permanently Delete All',
				'action' => 'batchHardDelete',
				'class' => 'btn-danger',
				'onclick' => $allOnclick,
			],
		];
	}

	/**
	 * ObjectEditor override because ObjectRestoration is a virtual object, so the standard
	 * SQL-based filtering/sorting in the parent viewExistingObjects() doesn't apply.
	 *
	 * @param $structure
	 */
	public function viewExistingObjects($structure): void {
		global $interface;
		$interface->assign('instructions', $this->getListInstructions());
		$interface->assign('sortableFields', $this->getSortableFields($structure));
		$sort = $_REQUEST['sort'] ?? $this->getDefaultSort();
		$interface->assign('sort', $sort);
		$filterFields = $this->getFilterFields($structure);
		$interface->assign('filterFields', $filterFields);
		$appliedFilters = $this->getAppliedFilters($filterFields);
		$interface->assign('appliedFilters', $appliedFilters);
		$interface->assign('hiddenFields', $this->getHiddenFields());

		$rows = $this->cachedRows;
		foreach ($appliedFilters as $filter) {
			$field = $filter['field']['property'];
			$type = $filter['filterType'] ?? 'contains';
			$value = (string)($filter['filterValue'] ?? '');
			$value2 = (string)($filter['filterValue2'] ?? '');

			$timestamp1 = null;
			$timestamp2 = null;
			if (in_array($type, ['beforeTime', 'afterTime', 'betweenTimes'])) {
				if ($value !== '') {
					$timestamp1 = strtotime($value);
				}
				if ($value2 !== '') {
					$timestamp2 = strtotime($value2);
				}
			}

			foreach ($rows as $key => $item) {
				$fieldValueRaw = $item->$field;

				if (in_array($type, ['beforeTime', 'afterTime', 'betweenTimes'])) {
					// Handle both numeric timestamps and formatted date strings gracefully.
					if (is_numeric($fieldValueRaw)) {
						$fieldTimestamp = (int)$fieldValueRaw;
					} else {
						$fieldTimestamp = strtotime((string)$fieldValueRaw);
					}
				}

				$fieldValue = (string)$fieldValueRaw;
				$match = match ($type) {
					'matches' => ($fieldValue === $value),
					'startsWith' => (stripos($fieldValue, $value) === 0),
					'beforeTime' => ($timestamp2 !== null && isset($fieldTimestamp) && $fieldTimestamp < $timestamp2),
					'afterTime' => ($timestamp1 !== null && isset($fieldTimestamp) && $fieldTimestamp > $timestamp1),
					'betweenTimes' => (
						$timestamp1 !== null && $timestamp2 !== null && isset($fieldTimestamp) &&
						$fieldTimestamp > $timestamp1 && $fieldTimestamp < $timestamp2
					),
					default => (stripos($fieldValue, $value) !== false),
				};
				if (!$match) {
					unset($rows[$key]);
				}
			}
		}

		[$fieldName, $direction] = array_pad(explode(' ', $sort), 2, 'desc');
		uasort($rows, function($a, $b) use ($fieldName, $direction) {
			$valA = $a->$fieldName;
			$valB = $b->$fieldName;
			if ($valA == $valB) return 0;
			$cmp = ($valA < $valB) ? -1 : 1;
			return (strtolower($direction) === 'desc') ? -$cmp : $cmp;
		});

		$page = isset($_REQUEST['page']) && is_numeric($_REQUEST['page']) ? (int)$_REQUEST['page'] : 1;
		$recordsPerPage = isset($_REQUEST['pageSize']) ? (int)$_REQUEST['pageSize'] : $this->getDefaultRecordsPerPage();
		$totalItems = count($rows);
		$offset = ($page - 1) * $recordsPerPage;
		$pagedRows = array_slice($rows, $offset, $recordsPerPage, true);

		if ($this->supportsPagination()) {
			$options = [
				'totalItems' => $totalItems,
				'perPage' => $recordsPerPage,
				'canChangeRecordsPerPage' => true,
				'canJumpToPage' => true,
			];
			$pager = new Pager($options);
			$interface->assign('pageLinks', $pager->getLinks());
		}

		$interface->assign('dataList', $pagedRows);
		$interface->assign('showQuickFilterOnPropertiesList', $this->showQuickFilterOnPropertiesList());
		$interface->setTemplate('../Admin/propertiesList.tpl');
	}

	/**
	 * Return a list of class names actively managed by the "recycle-bin."
	 *
	 * @return string[] Class names.
	 */
	public static function getManagedClasses(): array {
		foreach (self::$managedClasses as $className => $info) {
			require_once ROOT_DIR . $info['classFile'];
		}
		return array_keys(self::$managedClasses);
	}

	function getActiveAdminSection(): string {
		return 'system_admin';
	}

	public function getSortableFields($structure): array {
		$fields = parent::getSortableFields($structure);
		unset($fields['Key']);
		return $fields;
	}

	public function getFilterFields($structure): array {
		$fields = parent::getFilterFields($structure);
		unset($fields['compositeId']);
		return $fields;
	}

	function getBreadcrumbs(): array {
		return [
			new Breadcrumb('/Admin/Home','Administration Home'),
			new Breadcrumb('/Admin/Home#system_admin', 'System Administration'),
			new Breadcrumb('','Restore Deleted Objects')
		];
	}
}