
<?php

class Hoopla_AJAX extends Action {
	function launch() {
		global $timer;
		header('Content-type: application/json');
		header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past

		$method = (isset($_GET['method']) && !is_array($_GET['method'])) ? $_GET['method'] : '';
		if (method_exists($this, $method)) {
			$timer->logTime("Starting method $method");

			echo json_encode($this->$method());
		} else {
			echo json_encode(['error' => 'invalid_method']);
		}
	}

	/** @noinspection PhpUnused */
	function getCheckOutPrompts() {
		$user = UserAccount::getLoggedInUser();
		$id = $_REQUEST['id'];
		if (strpos($id, ':') !== false) {
			[
				,
				$id,
			] = explode(':', $id);
		}
		$hooplaType = $_REQUEST['hooplaType'];
		if ($user) {
			$hooplaUsers = $user->getRelatedEcontentUsers('hoopla');

			require_once ROOT_DIR . '/Drivers/HooplaDriver.php';
			$driver = new HooplaDriver();

			if ($id) {
				global $interface;
				$interface->assign('hooplaId', $id);

				//TODO: need to determine what happens to cards without a Hoopla account
				$hooplaUserStatuses = [];
				foreach ($hooplaUsers as $tmpUser) {
					$checkOutStatus = $driver->getAccountSummary($tmpUser);
					$hooplaUserStatuses[$tmpUser->id] = $checkOutStatus;
				}

				if (count($hooplaUsers) > 1) {
					// For multiple users, show the checkout prompt according to the hooplaType
					$interface->assign('hooplaUsers', $hooplaUsers);
					$interface->assign('hooplaUserStatuses', $hooplaUserStatuses);
					$interface->assign('hooplaType', $hooplaType);

					return [
						'title' => translate([
							'text' => 'Hoopla Check Out',
							'isPublicFacing' => true,
						]),
						'body' => $interface->fetch('Hoopla/ajax-checkout-prompt.tpl'),
						'buttons' => '<button class="btn btn-primary" type= "button" title="Check Out" onclick="return AspenDiscovery.Hoopla.checkOutHooplaTitle(\'' . $id . '\');">' . translate([
								'text' => 'Check Out',
								'isPublicFacing' => true,
							]) . '</button>',
					];
				} elseif (count($hooplaUsers) == 1) {
					// Single user
					$hooplaUser = reset($hooplaUsers);
					if ($hooplaUser->id != $user->id) {
						$interface->assign('hooplaUser', $hooplaUser); // Display the account name when not using the main user
					}
					$checkOutStatus = $hooplaUserStatuses[$hooplaUser->id];
					if (!$checkOutStatus) {
						// Always get the checkout status, not sure if this is  still needed?
						require_once ROOT_DIR . '/RecordDrivers/HooplaRecordDriver.php';
						$hooplaRecord = new HooplaRecordDriver($id);

						// Base Hoopla Title View Url
						$accessLink = $hooplaRecord->getAccessLink();
						$hooplaRegistrationUrl = $accessLink['url'];
						$hooplaRegistrationUrl .= (parse_url($hooplaRegistrationUrl, PHP_URL_QUERY) ? '&' : '?') . 'showRegistration=true'; // Add Registration URL parameter

						return [
							'title' => translate([
								'text' => 'Create Hoopla Account',
								'isPublicFacing' => true,
							]),
							'body' => $interface->fetch('Hoopla/ajax-hoopla-single-user-checkout-prompt.tpl'),
							'buttons' => '<button id="theHooplaButton" class="btn btn-default" type="button" title="Check Out" onclick="return AspenDiscovery.Hoopla.checkOutHooplaTitle(\'' . $id . '\', ' . $hooplaUser->id . ')">' . translate([
									'text' => 'I registered, Check Out now',
									'isPublicFacing' => true,
								]) . '</button>' . '<a class="btn btn-primary" role="button" href="' . $hooplaRegistrationUrl . '" target="_blank" title="Register at Hoopla" aria-label="Register at Hoopla ('.translate(['text' => 'opens in a new window', 'isPublicFacing' => true, 'inAttribute' => true]) .')" onclick="$(\'#theHooplaButton+a,#theHooplaButton\').toggleClass(\'btn-primary btn-default\');">' . translate([
									'text' => 'Register at Hoopla',
									'isPublicFacing' => true,
								]) . '</a>',
						];
					}
					if ($hooplaUser->hooplaCheckOutConfirmation && $hooplaType == 'Instant') {
						// Instant titles require a prompt to show the remaining checkouts
						$interface->assign('hooplaPatronStatus', $checkOutStatus);
						return [
							'title' => translate([
								'text' => 'Confirm Hoopla Check Out',
								'isPublicFacing' => true,
							]),
							'body' => $interface->fetch('Hoopla/ajax-hoopla-single-user-checkout-prompt.tpl'),
							'buttons' => '<button class="btn btn-primary" type="button" title="Check Out" onclick="return AspenDiscovery.Hoopla.checkOutHooplaTitle(\'' . $id . '\', ' . $hooplaUser->id . ')">' . translate([
									'text' => 'Check Out',
									'isPublicFacing' => true,
								]) . '</button>',
						];
					} else {
						// Flex titles can be checked out directly
						return [
							'flexDirectCheckout' => true,
							'patronId' => $hooplaUser->id,
							'id' => $id,
							'hooplaType' => $hooplaType
						];
					}
				} else {
					// No Hoopla Account Found, give the user an error message
					$invalidAccountMessage = translate([
						'text' => 'The barcode or library for this account is not valid for Hoopla. Please contact your local library for more information.',
						'isPublicFacing' => true,
					]);
					global $logger;
					$logger->log('No valid Hoopla account was found to check out a Hoopla title.', Logger::LOG_ERROR);
					return [
						'title' => translate([
							'text' => 'Invalid Hoopla Account',
							'isPublicFacing' => true,
						]),
						'body' => '<p class="alert alert-danger">' . $invalidAccountMessage . '</p>',
						'buttons' => '',
					];
				}
			} else {
				return [
					'title' => translate([
						'text' => 'Error',
						'isPublicFacing' => true,
					]),
					'body' => translate([
						'text' => 'Item to checkout was not provided.',
						'isPublicFacing' => true,
					]),
					'buttons' => '',
				];
			}
		} else {
			return [
				'title' => translate([
					'text' => 'Error',
					'isPublicFacing' => true,
				]),
				'body' => translate([
						'text' => 'You must be logged in to checkout an item.',
						'isPublicFacing' => true,
					]) . '<script>Globals.loggedIn = false;  AspenDiscovery.Hoopla.getCheckOutPrompts(\'' . $id . '\')</script>',
				'buttons' => '',
			];
		}

	}

	/** @noinspection PhpUnused */
	function getHoldPrompts() {
		$user = UserAccount::getLoggedInUser();
		if ($user) {
			$id = $_REQUEST['id'];
			$hooplaUsers = $user->getRelatedEcontentUsers('hoopla');

			global $interface;
			$interface->assign('hooplaId', $id);

			$driver = new HooplaDriver();
			$holdQueueSize = $driver->getHoldQueueSize($id);
			$interface->assign('holdQueueSize', $holdQueueSize);
			if (count($hooplaUsers) > 1) {
				$interface->assign('hooplaUsers', $hooplaUsers);
				$interface->assign('holdQueueSize', $holdQueueSize);
				return [
					'success' => true,
					'promptNeeded' => true,
					'promptTitle' => translate(['text' => 'Place Hoopla Flex Hold', 'isPublicFacing' => true]),
					'prompts' => $interface->fetch('Hoopla/ajax-hold-prompt.tpl'),
					'buttons' => '<button class="btn btn-primary" onclick="return AspenDiscovery.Hoopla.doHold($(\'#patronId\').val(), \'' . $id . '\');">' . translate(['text' => 'Place Hold', 'isPublicFacing' => true]) . '</button>'
				];
			} else if (count($hooplaUsers) == 1) {
				$hooplaUser = reset($hooplaUsers);
				if ($hooplaUser->hooplaHoldQueueSizeConfirmation) {
					return [
						'success' => true,
						'promptNeeded' => true,
						'promptTitle' => translate(['text' => 'Confirm Hoopla Flex Hold', 'isPublicFacing' => true]),
						'prompts' => translate([
							'text' => 'There are currently %1% people waiting for this title. Would you like to place a hold?',
							1 => $holdQueueSize,
							'isPublicFacing' => true
						]),
						'buttons' => '<button class="btn btn-primary" onclick="return AspenDiscovery.Hoopla.doHold(\'' . $hooplaUser->id . '\', \'' . $id . '\');">' .
							translate(['text' => 'Place Hold', 'isPublicFacing' => true]) . '</button>'
					];
				} else {
					return [
						'success' => true,
						'promptNeeded' => false,
						'patronId' => reset($hooplaUsers)->id
					];
				}
			} else {
				return [
					'success' => false,
					'message' => translate(['text' => 'No valid Hoopla account found.', 'isPublicFacing' => true])
				];
			}
		}
		return ['success' => false, 'message' => 'You must be logged in to place holds'];

	}

	/** @noinspection PhpUnused */
	function placeHold() {
		$user = UserAccount::getLoggedInUser();
		if ($user) {
			$patronId = $_REQUEST['patronId'];
			$id = $_REQUEST['id'];
			$patron = $user->getUserReferredTo($patronId);

			if ($patron) {
				require_once ROOT_DIR . '/Drivers/HooplaDriver.php';
				$driver = new HooplaDriver();
				$result = $driver->placeHold($patron, $id);
				return $result;
			} else {
				return [
					'success' => false,
					'message' => translate(['text' => 'Invalid patron selected', 'isPublicFacing' => true])
				];
			}
		}
		return ['success' => false, 'message' => 'You must be logged in to place holds'];
	}

	function cancelHold() {
		$user = UserAccount::getLoggedInUser();
		$id = $_REQUEST['recordId'];
		if ($user) {
			$patronId = $_REQUEST['patronId'];
			$patron = $user->getUserReferredTo($patronId);
			if ($patron) {
				require_once ROOT_DIR . '/Drivers/HooplaDriver.php';
				$driver = new HooplaDriver();
				return $driver->cancelHold($patron, $id);
			} else {
				return [
					'success' => false,
					'message' => translate([
						'text' => 'Sorry, it looks like you don\'t have permissions to cancel holds for that user.',
						'isPublicFacing' => true,
					]),
				];
			}
		} else {
			return [
				'success' => false,
				'message' => translate([
					'text' => 'You must be logged in to cancel holds.',
					'isPublicFacing' => true,
				]),
			];
		}
	}

	/** @noinspection PhpUnused */
	function checkOutHooplaTitle() {
		$user = UserAccount::getLoggedInUser();
		if ($user) {
			$patronId = !empty($_REQUEST['patronId']) ? $_REQUEST['patronId'] : $user->id;

			$hooplaType = $_REQUEST['hooplaType'];
			$patron = $user->getUserReferredTo($patronId);
			if ($patron) {
				global $interface;
				if ($patron->id != $user->id) {
					$interface->assign('hooplaUser', $patron); // Display the account name when not using the main user
				}

				$id = $_REQUEST['id'];
				require_once ROOT_DIR . '/Drivers/HooplaDriver.php';
				$driver = new HooplaDriver();
				$result = $driver->checkOutTitle($patron, $id);
				if (!empty($_REQUEST['stopHooplaConfirmation'])) {
					$patron->hooplaCheckOutConfirmation = 0;
					$patron->update();
				}
				if ($result['success']) {
					$checkOutStatus = $driver->getAccountSummary($patron);
					$interface->assign('hooplaPatronStatus', $checkOutStatus);
					$interface->assign('hooplaType', $hooplaType);
					$title = empty($result['title']) ? translate([
						'text' => "Title checked out successfully",
						'isPublicFacing' => true,
					]) : translate([
						'text' => "%1% checked out successfully",
						1 => $result['title'],
						'isPublicFacing' => true,
					]);
					/** @noinspection HtmlUnknownTarget */
					return [
						'success' => true,
						'title' => $title,
						'message' => $interface->fetch('Hoopla/hoopla-checkout-success.tpl'),
						'buttons' => '<a class="btn btn-primary" href="/MyAccount/CheckedOut" role="button">' . translate([
								'text' => 'View My Check Outs',
								'isPublicFacing' => true,
							]) . '</a>',
					];
				} else {
					return $result;
				}
			} else {
				return [
					'success' => false,
					'message' => translate([
						'text' => 'Sorry, it looks like you don\'t have permissions to checkout titles for that user.',
						'isPublicFacing' => true,
					]),
				];
			}
		} else {
			return [
				'success' => false,
				'message' => translate([
					'text' => 'You must be logged in to checkout an item.',
					'isPublicFacing' => true,
				]),
			];
		}
	}

	/** @noinspection PhpUnused */
	function returnCheckout() {
		$user = UserAccount::getLoggedInUser();
		if ($user) {
			$patronId = $_REQUEST['patronId'];
			$patron = $user->getUserReferredTo($patronId);
			if ($patron) {
				$id = $_REQUEST['id'];
				require_once ROOT_DIR . '/Drivers/HooplaDriver.php';
				$driver = new HooplaDriver();
				return $driver->returnCheckout($patron, $id);
			} else {
				return [
					'success' => false,
					'message' => translate([
						'text' => 'Sorry, it looks like you don\'t have permissions to return titles for that user.',
						'isPublicFacing' => true,
					]),
				];
			}
		} else {
			return [
				'success' => false,
				'message' => translate([
					'text' => 'You must be logged in to return an item.',
					'isPublicFacing' => true,
				]),
			];
		}
	}

	/** @noinspection PhpUnused */
	function getLargeCover() {
		global $interface;

		$id = $_REQUEST['id'];
		$interface->assign('id', $id);

		return [
			'title' => translate([
				'text' => 'Cover Image',
				'isPublicFacing' => true,
			]),
			'modalBody' => $interface->fetch("Hoopla/largeCover.tpl"),
			'modalButtons' => "",
		];
	}

	function getStaffView() {
		$result = [
			'success' => false,
			'message' => translate([
				'text' => 'Unknown error loading staff view',
				'isPublicFacing' => true,
			]),
		];
		$id = $_REQUEST['id'];
		require_once ROOT_DIR . '/RecordDrivers/HooplaRecordDriver.php';
		$recordDriver = new HooplaRecordDriver($id);
		if ($recordDriver->isValid()) {
			global $interface;
			$interface->assign('recordDriver', $recordDriver);
			$result = [
				'success' => true,
				'staffView' => $interface->fetch($recordDriver->getStaffView()),
			];
		} else {
			$result['message'] = translate([
				'text' => 'Could not find that record',
				'isPublicFacing' => true,
			]);
		}
		return $result;
	}

	function getBreadcrumbs(): array {
		return [];
	}
}