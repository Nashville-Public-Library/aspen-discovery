<?php /** @noinspection PhpMissingFieldTypeInspection */

class UserNotificationToken extends DataObject {
	public $__table = 'user_notification_tokens';
	public $id;
	public $userId;
	public $pushToken;
	public $deviceModel;
	public $notifySavedSearch;
	public $notifyCustom;
	public $notifyAccount;
	public $onboardAppNotifications;

	function getNumericColumnNames(): array {
		return ['id, userId, notifySavedSearch, notifyCustom, notifyAccount, onboardAppNotifications'];
	}

	public static function deleteToken($token): bool {
		$storedToken = new UserNotificationToken();
		$storedToken->pushToken = $token;
		if ($storedToken->find(true)) {
			$storedToken->delete();
			return true;
		} else {
			return false;
		}
	}

}