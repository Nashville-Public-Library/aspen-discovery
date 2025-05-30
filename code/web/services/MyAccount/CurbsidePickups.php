<?php

require_once ROOT_DIR . '/services/MyAccount/MyAccount.php';
require_once ROOT_DIR . '/sys/CurbsidePickups/CurbsidePickupSetting.php';

class MyAccount_CurbsidePickups extends MyAccount {
	function launch(): void {
		global $interface;
		global $library;
		$user = UserAccount::getActiveUserObj();
		$interface->assign('patronId', $user->id);

		$curbsidePickupSetting = new CurbsidePickupSetting();
		$curbsidePickupSetting->id = $library->curbsidePickupSettingId;
		if ($curbsidePickupSetting->find(true)) {
			$interface->assign('instructionSchedule', $curbsidePickupSetting->instructionSchedule);
			$interface->assign('useNote', $curbsidePickupSetting->useNote);
			$interface->assign('noteLabel', $curbsidePickupSetting->noteLabel);

			$catalog = CatalogFactory::getCatalogConnectionInstance();
			$currentPickups = $catalog->getPatronCurbsidePickups($user);
			$hasPickups = false;
			// Check if the patron has any curbside pickups.
			if (!empty($currentPickups['pickups']) && is_array($currentPickups['pickups'])) {
				// Remove any pickups that have already been delivered.
				$allowedTime = $curbsidePickupSetting->timeAllowedBeforeCheckIn;
				$now = date_create();

				foreach ($currentPickups['pickups'] as $key => &$pickup) {
					if (!empty($pickup['delivered_datetime'])) {
						unset($currentPickups['pickups'][$key]);
						continue;
					}

					// Calculate withinTime based solely on the scheduled time and allowed window.
					$pickupTime = $pickup['scheduled_pickup_datetime'];
					$scheduledTime = date_create($pickupTime);
					$difference = date_diff($now, $scheduledTime);
					$minutes = $difference->days * 24 * 60;
					$minutes += $difference->h * 60;
					$minutes += $difference->i;

					// If now is before scheduled time and within the allowed window,
					// OR if now is after scheduled time.
					if (($scheduledTime > $now && $minutes <= $allowedTime) || ($scheduledTime < $now)) {
						$pickup['withinTime'] = true;
					} else {
						$pickup['withinTime'] = false;
					}

					// Add an explicit isReady flag based on staging status.
					$pickup['isReady'] = !empty($pickup['staged_datetime']);
				}
				unset($pickup); // Break the reference.

				// Reindex array after removals.
				$currentPickups['pickups'] = array_values($currentPickups['pickups']);
				$hasPickups = true;
			}

			$interface->assign('hasPickups', $hasPickups);
			$interface->assign('currentCurbsidePickups', $currentPickups);

			if ($hasPickups) {
				$pickupsByLocation = [];
				foreach ($currentPickups['pickups'] as $pickup) {
					if (!isset($pickupsByLocation)) {
						$pickupsByLocation[$pickup['branchcode']]['code'] = $pickup['branchcode'];
						$pickupsByLocation[$pickup['branchcode']]['count'] += 1;
					} elseif (!in_array($pickup['branchcode'], array_column($pickupsByLocation, 'code'))) {
						$pickupsByLocation[$pickup['branchcode']]['code'] = $pickup['branchcode'];
						$pickupsByLocation[$pickup['branchcode']]['count'] = 1;

						$location = new Location();
						$location->code = $pickup['branchcode'];
						if ($location->find(true)) {
							if ($location->curbsidePickupInstructions) {
								$interface->assign('pickupInstructions', $location->curbsidePickupInstructions);
							} else {
								$interface->assign('pickupInstructions', $curbsidePickupSetting->curbsidePickupInstructions);
							}
						}
					}
				}
			}

			$allHolds = $user->getHolds(false, '', '', 'ils');
			$userHomePickupLocation = $user->getHomeLocation();
			$hasAvailableHolds = !empty($allHolds['available']);
			$interface->assign('allowCheckIn', $curbsidePickupSetting->allowCheckIn);
			$interface->assign('hasHolds', $hasAvailableHolds);
			$interface->assign('availableHolds', count($allHolds['available']));
			$interface->assign('userHomePickupLocation', $userHomePickupLocation);
			$interface->assign('timeAllowedBeforeCheckIn', $curbsidePickupSetting->timeAllowedBeforeCheckIn);

			if ($hasAvailableHolds > 0) {
				$interface->assign('hasHolds', true);
				$holdsByLocation = [];
				foreach ($allHolds['available'] as $hold) {
					$locationCode = null;
					require_once ROOT_DIR . '/sys/LibraryLocation/Location.php';
					$location = new Location();
					$location->locationId = $hold->pickupLocationId;
					if ($location->find(true)) {
						$locationCode = $location->code;
					}
					$isScheduled = false;
					if (isset($pickupsByLocation[$locationCode])) {
						$isScheduled = true;
					}
					if (!isset($holdsByLocation)) {
						$holdsByLocation[$hold->pickupLocationName]['id'] = $hold->pickupLocationId;
						$holdsByLocation[$hold->pickupLocationName]['name'] = $hold->pickupLocationName;
						$holdsByLocation[$hold->pickupLocationName]['code'] = $locationCode;
						$holdsByLocation[$hold->pickupLocationName]['pickupScheduled'] = $isScheduled;
						$holdsByLocation[$hold->pickupLocationName]['holds'][] = $hold;
					} elseif (!in_array($hold->pickupLocationId, array_column($holdsByLocation, 'code'))) {
						$holdsByLocation[$hold->pickupLocationName]['id'] = $hold->pickupLocationId;
						$holdsByLocation[$hold->pickupLocationName]['name'] = $hold->pickupLocationName;
						$holdsByLocation[$hold->pickupLocationName]['code'] = $locationCode;
						$holdsByLocation[$hold->pickupLocationName]['pickupScheduled'] = $isScheduled;
						$holdsByLocation[$hold->pickupLocationName]['holds'][] = $hold;
					}
				}
				$interface->assign('holdsReadyForPickup', $holdsByLocation);
			}

		}

		$this->display('curbsidePickups.tpl', 'Curbside Pickups');
	}

	function getBreadcrumbs(): array {
		$breadcrumbs = [];
		$breadcrumbs[] = new Breadcrumb('/MyAccount/Home', 'Your Account');
		$breadcrumbs[] = new Breadcrumb('', 'Curbside Pickups');
		return $breadcrumbs;
	}
}