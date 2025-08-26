<?php

require_once ROOT_DIR . '/services/MyAccount/MyAccount.php';
require_once ROOT_DIR . '/sys/Pager.php';

class ReadingHistory extends MyAccount {
	function launch() : void {
		global $interface;
		global $library;

		$interface->assign('showRatings', $library->getGroupedWorkDisplaySettings()->showRatings);

		global $offlineMode;
		if (!$offlineMode) {
			$interface->assign('offline', false);
		}
		$user = UserAccount::getLoggedInUser();

		// Get My Transactions
		if ($user) {
			if (!$user->isReadingHistoryEnabled()) {
				//User shouldn't get here
				$module = 'Error';
				$action = 'Handle404';
				$interface->assign('module', 'Error');
				$interface->assign('action', 'Handle404');
				require_once ROOT_DIR . "/services/Error/Handle404.php";
				$actionClass = new Error_Handle404();
				$actionClass->launch();
				die();
			}
			$interface->assign('profile', $user);

			$linkedUsers = $user->getLinkedUsers();
			if (count($linkedUsers) > 0) {
				array_unshift($linkedUsers, $user);
				$interface->assign('linkedUsers', $linkedUsers);
			}
			$patronId = empty($_REQUEST['patronId']) ? $user->id : $_REQUEST['patronId'];

			$patron = $user->getUserReferredTo($patronId);

			$interface->assign('selectedUser', $patronId); // needs to be set even when there is only one user so that the patronId hidden input gets a value in the reading history form.

			if (isset($_REQUEST['page']) && is_numeric($_REQUEST['page'])) {
				$interface->assign('page', $_REQUEST['page']);
			} else {
				$interface->assign('page', 1);
			}
			if (isset($_REQUEST['readingHistoryFilter'])) {
				$interface->assign('readingHistoryFilter', strip_tags($_REQUEST['readingHistoryFilter']));
			} else {
				$interface->assign('readingHistoryFilter', '');
			}
			$interface->assign('historyActive', $patron->trackReadingHistory);
			$interface->assign('initialReadingHistoryLoaded', $patron->initialReadingHistoryLoaded);
			//Check to see if there is an action to perform.
			if (!empty($_REQUEST['readingHistoryAction'])) {
				//Perform the requested action
				$selectedTitles = $_REQUEST['selected'] ?? [];
				$readingHistoryAction = $_REQUEST['readingHistoryAction'];
				$result = $patron->doReadingHistoryAction($readingHistoryAction, $selectedTitles);
				if (isset($result['message'])) {
					session_start();
					$_SESSION['readingHistoryMessage'] = $result['message'];
					$_SESSION['readingHistoryMessageIsError'] = !$result['success'];
				}

				//redirect back to the current location without the action.
				$newLocation = "/MyAccount/ReadingHistory";
				if (isset($_REQUEST['page']) && $readingHistoryAction != 'deleteAll' && $readingHistoryAction != 'optOut') {
					$params[] = 'page=' . $_REQUEST['page'];
				}
				if (isset($_REQUEST['patronId'])) {
					$params[] = 'patronId=' . $_REQUEST['patronId'];
				}
				if (!empty($params)) {
					$additionalParams = implode('&', $params);
					$newLocation .= '?' . $additionalParams;
				}
				header("Location: $newLocation");
				die();
			}

			session_start();
			if (isset($_SESSION['readingHistoryMessage'])) {
				$interface->assign('updateMessage', $_SESSION['readingHistoryMessage']);
				$interface->assign('updateMessageIsError', $_SESSION['readingHistoryMessageIsError']);
				unset($_SESSION['readingHistoryMessage']);
				unset($_SESSION['readingHistoryMessageIsError']);
			}
		}

		$this->display('readingHistory.tpl', 'Reading History');
	}

	function getBreadcrumbs(): array {
		$breadcrumbs = [];
		$breadcrumbs[] = new Breadcrumb('/MyAccount/Home', 'Your Account');
		$breadcrumbs[] = new Breadcrumb('', 'Your Reading History');
		return $breadcrumbs;
	}
}