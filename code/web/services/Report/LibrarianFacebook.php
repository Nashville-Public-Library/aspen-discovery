<?php

require_once(ROOT_DIR . '/services/Admin/Admin.php');

class Report_LibrarianFacebook extends Admin_Admin {
	function launch() {
		global $interface;
		global $configArray;
		$user = UserAccount::getLoggedInUser();

		// Get report data
		$data = CatalogFactory::getCatalogConnectionInstance()->getLibrarianFacebookData();
		$interface->assign('reportData', $data);

		$this->display('librarianFacebook.tpl', 'Librarian Facebook');
	}
	function getBreadcrumbs(): array {
		$breadcrumbs = [];
		$breadcrumbs[] = new Breadcrumb('/Admin/Home', 'Administration Home');
		$breadcrumbs[] = new Breadcrumb('/Admin/Home#circulation_reports', 'Circulation Reports');
		$breadcrumbs[] = new Breadcrumb('', 'Librarian Facebook');
		return $breadcrumbs;
	}

	function getActiveAdminSection(): string {
		return 'circulation_reports';
	}

	function canView(): bool {
		return UserAccount::userHasPermission([
			'View Librarian Facebook',
		]);
	}
}