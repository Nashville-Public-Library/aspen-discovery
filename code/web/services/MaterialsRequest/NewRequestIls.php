<?php

require_once ROOT_DIR . '/services/MyAccount/MyAccount.php';
require_once ROOT_DIR . "/sys/MaterialsRequests/MaterialsRequest.php";

class MaterialsRequest_NewRequestIls extends MyAccount {

	function launch() {
		global $interface;
		global $library;

		$user = UserAccount::getActiveUserObj();
		$patronId = empty($_REQUEST['patronId']) ? $user->id : $_REQUEST['patronId'];
		$patron = $user->getUserReferredTo($patronId);
		
		$interface->assign('patronId', $patronId);

		$interface->assign('newMaterialsRequestSummary', $library->newMaterialsRequestSummary);

		$catalogConnection = CatalogFactory::getCatalogConnectionInstance();

		$isElegible = $catalogConnection->patronEligibleForILLRequests($patron)['isEligible'];
		if (!$isElegible) {
			$interface->assign('module', 'Error');
			$interface->assign('action', 'Handle403');
			require_once ROOT_DIR . "/services/Error/Handle403.php";
			$actionClass = new Error_Handle403();
			$actionClass->launch();
			die();
		}
		
		if (isset($_REQUEST['submit'])) {
			$result = $catalogConnection->processMaterialsRequestForm($patron);
			if ($result['success']) {
				header('Location: /MaterialsRequest/IlsRequests?patronId=' . $patronId);
				exit;
			} else {
				global $interface;
				$interface->assign('errors', [$result['message']]);
			}
		}
		$requestForm = $catalogConnection->getNewMaterialsRequestForm($patron);

		$this->display($requestForm, 'Materials Request');
	}

	function getBreadcrumbs(): array {
		$breadcrumbs = [];
		$breadcrumbs[] = new Breadcrumb('/MyAccount/Home', 'Your Account');
		$breadcrumbs[] = new Breadcrumb('', 'New Materials Request');
		return $breadcrumbs;
	}
}