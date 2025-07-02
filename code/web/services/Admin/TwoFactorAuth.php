<?php

require_once ROOT_DIR . '/Action.php';
require_once ROOT_DIR . '/services/Admin/ObjectEditor.php';
require_once ROOT_DIR . '/sys/TwoFactorAuthSetting.php';

class Admin_TwoFactorAuth extends ObjectEditor {
	function launch() : void {
		global $interface;
		global $library;
		$objectAction = $_REQUEST['objectAction'] ?? null;
		if ($objectAction == 'recoverAccount') {
			$id = $_REQUEST['id'];
			$interface->assign('id', $id);
			$interface->assign('usernameLabel', str_replace('Your', '', !empty($library->loginFormUsernameLabel) ? $library->loginFormUsernameLabel : 'Name'));
			$this->display('twoFactorAccountRecovery.tpl', 'Account Recovery');
		} else {
			parent::launch();
		}
	}

	function getObjectType(): string {
		return 'TwoFactorAuthSetting';
	}

	function getToolName(): string {
		return 'TwoFactorAuth';
	}

	function getPageTitle(): string {
		return 'Two-Factor Authentication Settings';
	}

	function getAllObjects($page, $recordsPerPage): array {
		$list = [];

		$object = new TwoFactorAuthSetting();
		$object->orderBy($this->getSort());
		$this->applyFilters($object);
		$object->limit(($page - 1) * $recordsPerPage, $recordsPerPage);
		$object->find();
		while ($object->fetch()) {
			$list[$object->id] = clone $object;
		}

		return $list;
	}

	function getDefaultSort(): string {
		return 'name asc';
	}

	function getObjectStructure($context = ''): array {
		return TwoFactorAuthSetting::getObjectStructure($context);
	}

	function getPrimaryKeyColumn(): string {
		return 'id';
	}

	function getIdKeyColumn(): string {
		return 'id';
	}

	function getAdditionalObjectActions($existingObject): array {
		$actions = [];
		if ($existingObject instanceof TwoFactorAuthSetting  && $existingObject->id != '') {
			$actions[] = [
				'text' => 'Recover User Account',
				'url' => '/Admin/TwoFactorAuth?objectAction=recoverAccount&id=' . $existingObject->id,
			];
		}

		return $actions;
	}

	function getBreadcrumbs(): array {
		$breadcrumbs = [];
		$breadcrumbs[] = new Breadcrumb('/Admin/Home', 'Administration Home');
		$breadcrumbs[] = new Breadcrumb('/Admin/Home#primary_configuration', 'Primary Configuration');
		$breadcrumbs[] = new Breadcrumb('/Admin/TwoFactorAuth', 'Two-factor Authentication');
		return $breadcrumbs;
	}

	function getInstructions() : string{
		return 'https://help.aspendiscovery.org/help/users/account';
	}

	function getActiveAdminSection(): string {
		return 'primary_configuration';
	}

	function canView(): bool {
		return UserAccount::userHasPermission('Administer Two-Factor Authentication');
	}

	public function hasMultiStepAddNew() : bool {
		return true;
	}
}