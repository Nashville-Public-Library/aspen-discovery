<?php
require_once ROOT_DIR . '/JSON_Action.php';

class Axis360_AJAX extends JSON_Action
{
	function placeHold()
	{
		$user = UserAccount::getLoggedInUser();

		$id = $_REQUEST['id'];
		if ($user) {
			$patronId = $_REQUEST['patronId'];
			$patron = $user->getUserReferredTo($patronId);
			if ($patron) {
				require_once ROOT_DIR . '/Drivers/Axis360Driver.php';
				$driver = new Axis360Driver();
				return $driver->placeHold($patron, $id);
			} else {
				return array('result' => false, 'message' => translate(['text' => 'no_permissions_for_hold', 'defaultText' => 'Sorry, it looks like you don\'t have permissions to place holds for that user.']));
			}
		} else {
			return array('result' => false, 'message' => 'You must be logged in to place a hold.');
		}
	}

	function checkOutTitle()
	{
		$user = UserAccount::getLoggedInUser();
		$id = $_REQUEST['id'];
		if ($user) {
			$patronId = $_REQUEST['patronId'];
			$patron = $user->getUserReferredTo($patronId);
			if ($patron) {
				require_once ROOT_DIR . '/Drivers/Axis360Driver.php';
				$driver = new Axis360Driver();
				$result = $driver->checkoutTitle($patron, $id);
				//$logger->log("Checkout result = $result", Logger::LOG_NOTICE);
				if ($result['success']) {
					/** @noinspection HtmlUnknownTarget */
					$result['title'] = translate("Title Checked Out Successfully");
					$result['buttons'] = '<a class="btn btn-primary" href="/MyAccount/CheckedOut" role="button">' . translate('View My Check Outs') . '</a>';
				} else {
					$result['title'] = translate("Error Checking Out Title");
				}
				return $result;
			} else {
				return array('result' => false, 'title' => translate("Error Checking Out Title"), 'message' => translate(['text' => 'no_permission_to_checkout', 'defaultText' => 'Sorry, it looks like you don\'t have permissions to checkout titles for that user.']));
			}
		} else {
			return array('result' => false, 'title' => translate("Error Checking Out Title"), 'message' => translate('You must be logged in to checkout an item.'));
		}
	}

	/** @noinspection PhpUnused */
	function getHoldPrompts()
	{
		$user = UserAccount::getLoggedInUser();
		global $interface;
		$id = $_REQUEST['id'];
		$interface->assign('id', $id);

		$usersWithAxis360Access = $this->getAxis360Users($user);

		if (count($usersWithAxis360Access) > 1) {
			$promptTitle = 'Axis 360 Hold Options';
			return array(
					'promptNeeded' => true,
					'promptTitle' => $promptTitle,
					'prompts' => $interface->fetch('Axis360/ajax-hold-prompt.tpl'),
					'buttons' => '<input class="btn btn-primary" type="submit" name="submit" value="Place Hold" onclick="return AspenDiscovery.Axis360.processHoldPrompts();">'
			);
		} elseif (count($usersWithAxis360Access) == 1) {
			return array(
					'patronId' => reset($usersWithAxis360Access)->id,
					'promptNeeded' => false,
				);
		} else {
			// No Axis 360 Account Found, let the user create one if they want
			return array(
					'promptNeeded' => true,
					'promptTitle' => 'Create an Account',
					'prompts' => $interface->fetch('Axis360/ajax-create-account-prompt.tpl'),
					'buttons' => '<input class="btn btn-primary" type="submit" name="submit" value="Create Account" onclick="return AspenDiscovery.Axis360.createAccount(\'hold\', \'' . $user->id . '\', \'' . $id . '\');">'
				);
		}
	}

	/** @noinspection PhpUnused */
	function getCheckOutPrompts()
	{
		$user = UserAccount::getLoggedInUser();
		global $interface;
		$id = $_REQUEST['id'];
		$interface->assign('id', $id);
		$interface->assign('checkoutType', 'book');

		$usersWithAxis360Access = $this->getAxis360Users($user);

		if (count($usersWithAxis360Access) > 1) {
			$promptTitle = 'Axis 360 Checkout Options';
			return array(
					'promptNeeded' => true,
					'promptTitle' => $promptTitle,
					'prompts' => $interface->fetch('Axis360/ajax-checkout-prompt.tpl'),
					'buttons' => '<input class="btn btn-primary" type="submit" name="submit" value="Checkout Title" onclick="return AspenDiscovery.Axis360.processCheckoutPrompts();">'
				);
		} elseif (count($usersWithAxis360Access) == 1) {
			return array(
					'patronId' => reset($usersWithAxis360Access)->id,
					'promptNeeded' => false,
				);
		} else {
			// No Axis 360 Account Found, let the user create one if they want
			return array(
					'promptNeeded' => true,
					'promptTitle' => 'Create an Account',
					'prompts' => $interface->fetch('Axis360/ajax-create-account-prompt.tpl'),
					'buttons' => '<input class="btn btn-primary" type="submit" name="submit" value="Create Account" onclick="return AspenDiscovery.Axis360.createAccount(\'checkout\', ' . $user->id . ', ' . $id . ');">'
				);
		}
	}

	function cancelHold()
	{
		$user = UserAccount::getLoggedInUser();
		$id = $_REQUEST['recordId'];
		if ($user) {
			$patronId = $_REQUEST['patronId'];
			$patron = $user->getUserReferredTo($patronId);
			if ($patron) {
				require_once ROOT_DIR . '/Drivers/Axis360Driver.php';
				$driver = new Axis360Driver();
				return $driver->cancelHold($patron, $id);
			} else {
				return array('result' => false, 'message' => 'Sorry, it looks like you don\'t have permissions to cancel holds for that user.');
			}
		} else {
			return array('result' => false, 'message' => 'You must be logged in to cancel holds.');
		}
	}

	function renewCheckout()
	{
		$user = UserAccount::getLoggedInUser();
		$id = $_REQUEST['recordId'];
		if ($user) {
			$patronId = $_REQUEST['patronId'];
			$patron = $user->getUserReferredTo($patronId);
			if ($patron) {
				require_once ROOT_DIR . '/Drivers/Axis360Driver.php';
				$driver = new Axis360Driver();
				return $driver->renewCheckout($patron, $id);
			} else {
				return array('result' => false, 'message' => 'Sorry, it looks like you don\'t have permissions to modify checkouts for that user.');
			}
		} else {
			return array('result' => false, 'message' => 'You must be logged in to renew titles.');
		}
	}

	/** @noinspection PhpUnused */
	function returnCheckout()
	{
		$user = UserAccount::getLoggedInUser();
		$id = $_REQUEST['recordId'];
		if ($user) {
			$patronId = $_REQUEST['patronId'];
			$patron = $user->getUserReferredTo($patronId);
			if ($patron) {
				require_once ROOT_DIR . '/Drivers/Axis360Driver.php';
				$driver = new Axis360Driver();
				return $driver->returnCheckout($patron, $id);
			} else {
				return array('result' => false, 'message' => 'Sorry, it looks like you don\'t have permissions to modify checkouts for that user.');
			}
		} else {
			return array('result' => false, 'message' => 'You must be logged in to return titles.');
		}
	}
	/**
	 * @param User $user
	 * @return User[]
	 */
	private function getAxis360Users(User $user)
	{
		global $interface;
		$users = $user->getRelatedEcontentUsers('axis360');
		$usersWithAxis360Access = [];
		require_once ROOT_DIR . '/Drivers/Axis360Driver.php';
		foreach ($users as $tmpUser) {
			$usersWithAxis360Access[] = $tmpUser;
		}
		$interface->assign('users', $usersWithAxis360Access);
		return $usersWithAxis360Access;
	}

	function getStaffView(){
		$result = [
			'success' => false,
			'message' => 'Unknown error loading staff view'
		];
		$id = $_REQUEST['id'];
		require_once ROOT_DIR . '/RecordDrivers/Axis360RecordDriver.php';
		$recordDriver = new Axis360RecordDriver($id);
		if ($recordDriver->isValid()){
			global $interface;
			$interface->assign('recordDriver', $recordDriver);
			$result = [
				'success' => true,
				'staffView' => $interface->fetch($recordDriver->getStaffView())
			];
		}else{
			$result['message'] = 'Could not find that record';
		}
		return $result;
	}
}