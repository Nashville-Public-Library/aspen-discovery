<?php /** @noinspection PhpMissingFieldTypeInspection */

class PageDefaults extends DataObject {
	public $__table = 'user_page_defaults';
	public $id;
	public $userId;
	public $module;
	public $action;
	public $objectId;
	public $pageSize;
	public $pageSort;

	public static function getPageDefaultsForUser(int $userId, string $module, string $action, ?int $objectId) : ?PageDefaults {
		$pageDefaults = new PageDefaults();
		$pageDefaults->userId = $userId;
		$pageDefaults->module = $module;
		$pageDefaults->action = $action;
		if ($objectId !== null) {
			$pageDefaults->objectId = $objectId;
		}
		if ($pageDefaults->find(true)) {
			return $pageDefaults;
		}else{
			return null;
		}
	}

	public static function updatePageDefaultsForUser(int $userId, string $module, string $action, ?int $objectId, ?string $defaultPageSize, ?string $defaultSort) : void {
		$pageDefaults = new PageDefaults();
		$pageDefaults->userId = $userId;
		$pageDefaults->module = $module;
		$pageDefaults->action = $action;
		if ($objectId !== null) {
			$pageDefaults->objectId = $objectId;
		}
		if ($pageDefaults->find(true)) {
			$updateDefaults = false;
			if ($pageDefaults->pageSize != $defaultPageSize && $defaultPageSize !== null) {
				$pageDefaults->pageSize = $defaultPageSize;
				$updateDefaults = true;
			}
			if ($pageDefaults->pageSort != $defaultSort && $defaultSort !== null) {
				$pageDefaults->pageSort = $defaultSort;
				$updateDefaults = true;
			}
			if ($updateDefaults) {
				$pageDefaults->update();
			}
		}else{
			$pageDefaults->pageSize = $defaultPageSize;
			$pageDefaults->pageSort = $defaultSort;
			$pageDefaults->insert();
		}
	}
}