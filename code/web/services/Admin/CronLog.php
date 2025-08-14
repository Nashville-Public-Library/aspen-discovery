<?php

require_once ROOT_DIR . '/Action.php';
require_once ROOT_DIR . '/services/Admin/Admin.php';
require_once ROOT_DIR . '/sys/Pager.php';

class Admin_CronLog extends Admin_Admin {
	function launch() : void {
		global $interface;

		$logEntries = [];
		$cronLogEntry = new CronLogEntry();
		if (isset($_REQUEST['showErrorsOnly'])) {
			$interface->assign('showErrorsOnly', true);
			$cronLogEntry->whereAdd('numErrors > 0');
		}
		$total = $cronLogEntry->count();

		$cronLogEntry = new CronLogEntry();
		$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
		$pageSize = isset($_REQUEST['pageSize']) ? $_REQUEST['pageSize'] : 30; // to adjust number of items listed on a page
		$interface->assign('recordsPerPage', $pageSize);
		$interface->assign('page', $page);
		$cronLogEntry->limit(($page - 1) * $pageSize, $pageSize);
		$cronNamesToShow = isset($_REQUEST['cronNamesToShow']) ? $_REQUEST['cronNamesToShow'] : '';

		if (!isSpammySearchTerm($cronNamesToShow)) {
			$interface->assign('cronNamesToShow', $cronNamesToShow);
			$escapedFilter = $cronLogEntry->escape('%' . $cronNamesToShow . '%');
			$cronLogEntry->whereAdd("name LIKE $escapedFilter");
		}
		if (isset($_REQUEST['showErrorsOnly'])) {
			$interface->assign('showErrorsOnly', true);
			$cronLogEntry->whereAdd('numErrors > 0');
		}
		$cronLogEntry->orderBy('startTime DESC');
		$page = $_REQUEST['page'] ?? 1;
		$interface->assign('page', $page);
		$cronLogEntry->limit(($page - 1) * 30, 30);
		$cronLogEntry->find();
		while ($cronLogEntry->fetch()) {
			$logEntries[] = clone($cronLogEntry);
		}
		$interface->assign('logEntries', $logEntries);

		$options = [
			'totalItems' => $total,
			'perPage' => 30,
		];
		$pager = new Pager($options);
		$interface->assign('pageLinks', $pager->getLinks());

		$this->display('cronLog.tpl', 'Cron Log');
	}

	function getBreadcrumbs(): array {
		$breadcrumbs = [];
		$breadcrumbs[] = new Breadcrumb('/Admin/Home', 'Administration Home');
		$breadcrumbs[] = new Breadcrumb('/Admin/Home#system_reports', 'System Reports');
		$breadcrumbs[] = new Breadcrumb('', 'Cron Log');
		return $breadcrumbs;
	}

	function getActiveAdminSection(): string {
		return 'system_reports';
	}

	function canView(): bool {
		return UserAccount::userHasPermission('View System Reports');
	}
}
