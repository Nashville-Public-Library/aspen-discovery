<?php

require_once ROOT_DIR . '/services/MyAccount/MyAccount.php';

class MyAccount_Fines extends MyAccount
{
	function launch()
	{
		global $interface;
		global $configArray;

// TODO: get account profile -> ils instead of config.ini
		$ils = $configArray['Catalog']['ils'];
		$interface->assign('showDate', $ils == 'Koha' || $ils == 'Horizon' || $ils == 'CarlX' || $ils == 'Symphony');
		$interface->assign('showReason', true);

		$interface->setFinesRelatedTemplateVariables();

		$showSystem = false;

		if (UserAccount::isLoggedIn()) {
			global $offlineMode;
			if (!$offlineMode) {
				$currencyCode = 'USD';
				$systemVariables = SystemVariables::getSystemVariables();

				if (!empty($systemVariables->currencyCode)) {
					$currencyCode = $systemVariables->currencyCode;
				}
				$interface->assign('currencyCode', $currencyCode);

				// Get My Fines
				$user = UserAccount::getLoggedInUser();
				$interface->assign('profile', $user);
				$userLibrary = $user->getHomeLibrary();
				$fines = $user->getFines();

				$useOutstanding = $user->getCatalogDriver()->showOutstandingFines();
				$interface->assign('showOutstanding', $useOutstanding);

				if ($userLibrary->finePaymentType == 2) {
					$clientId = $userLibrary->payPalClientId;
					$interface->assign('payPalClientId', $clientId);
				}

				// MSB payment result message
				if ($userLibrary->finePaymentType == 3) {
					if (!empty($_REQUEST['id'])) {
						require_once ROOT_DIR . '/sys/Account/UserPayment.php';
						$payment = new UserPayment();
						$payment->id = $_REQUEST['id'];
						$finePaymentResult = new stdClass();
						if ($payment->find(true)) {
							if ($payment->completed == 1) {
								$finePaymentResult->success = true;
								$finePaymentResult->message = translate(['text' => 'patron_payment_success', 'defaultText' => 'Your payment was processed successfully, thank you.']);
							} elseif ($payment->completed == 9) {
								$finePaymentResult->success = false;
								$finePaymentResult->message = translate(['text' => 'patron_payment_fail_1', 'defaultText' => 'Your payment was processed, but failed to update the Library system. Library staff have been alerted to this problem.']);
							} else { // i.e., $payment->completed == 0
								$finePaymentResult->success = false;
								$finePaymentResult->message = translate(['text' => 'patron_payment_fail_2', 'defaultText' => 'Your payment has not completed processing.']);
							}
						} else {
							$finePaymentResult->success = false;
							$finePaymentResult->message = translate(['text' => 'patron_payment_fail_3', 'defaultText' => 'Your payment was processed, but did not match library records. Please contact the library with your receipt.']);
						}
						$interface->assign('finePaymentResult', $finePaymentResult);
					}
				}

				$separateFinesBySystem = $user->getCatalogDriver()->separateFinesBySystem();

				$userAccountLabel = [];
				$fineTotalsVal = [];
				$outstandingTotalVal = [];
				// Get Account Labels, Add Up Totals
				if ($separateFinesBySystem !== true) {
					foreach ($fines as $userId => $finesDetails) {
						$userAccountLabel[$userId] = $user->getUserReferredTo($userId)->getNameAndLibraryLabel();
						$total = $totalOutstanding = 0;
						foreach ($finesDetails as $fine) {
							$amount = $fine['amountVal'];
							if (is_numeric($amount)) $total += $amount;
							if ($useOutstanding && $fine['amountOutstandingVal']) {
								$outstanding = $fine['amountOutstandingVal'];
								if (is_numeric($outstanding)) $totalOutstanding += $outstanding;
							}
							if (!empty($fine['system'])){
								$showSystem = true;
							}
						}
						$fineTotalsVal[$userId] = $total;
						if ($useOutstanding) {
							$outstandingTotalVal[$userId] = $totalOutstanding;
						}
					}
				} else {
					$userXSystem = [];
					foreach ($fines as $userId => $finesDetails) {
						foreach ($finesDetails as $k => $v) {
							if (!empty($finesDetails[$k]['system'])){
								$showSystem = true;
							}
							$finesDetails[$k]['userId'] = $userId;
							$userXSystem_key = $finesDetails[$k]['system'] . '-' . $userId;
							$userXSystem[$userXSystem_key][] = $finesDetails[$k];
						}
					}
					$fines = $userXSystem;
					foreach ($fines as $userXSystemId => $finesDetails) {
						$total = $totalOutstanding = 0;
						$fineTotalsVal[$userXSystemId] = $total;
						if ($useOutstanding) {
							$outstandingTotalVal[$userId] = $totalOutstanding;
						}
						foreach ($finesDetails as $fine) {
							$amount = $finesDetails[$k]['amountVal'];
							if (is_numeric($amount)) $total += $amount;
							if ($useOutstanding && $finesDetails[$k]['amountOutstandingVal']) {
								$outstanding = $fine['amountOutstandingVal'];
								if (is_numeric($outstanding)) $totalOutstanding += $outstanding;
							}
						}

						preg_match('/^(.*)-(\d+$)/', $userXSystemId, $matches);
						$system = $matches[1];
						$userId = $matches[2];
						$userAccountLabel[$userXSystemId] = $user->getUserReferredTo($userId)->displayName . ' : ' . $system;

						$fineTotalsVal[$userXSystemId] = $total;
						if ($useOutstanding) {
							$outstandingTotalVal[$userXSystemId] = $totalOutstanding;
						}
					}
				}

				$interface->assign('finesToPay', $userLibrary->finesToPay);
				$interface->assign('userXSystemFines', $fines);
				$interface->assign('userAccountLabel', $userAccountLabel);
				$interface->assign('fineTotalsVal', $fineTotalsVal);
				if ($useOutstanding) {
					$interface->assign('outstandingTotalVal', $outstandingTotalVal);
				}
			}
		}
		$interface->assign('showSystem', $showSystem);
		$this->display('fines.tpl', 'My Fines');
	}

	function getBreadcrumbs(): array
	{
		$breadcrumbs = [];
		$breadcrumbs[] = new Breadcrumb('/MyAccount/Home', 'My Account');
		$breadcrumbs[] = new Breadcrumb('', 'My Fines');
		return $breadcrumbs;
	}
}
