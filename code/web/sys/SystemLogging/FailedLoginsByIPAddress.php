<?php /** @noinspection PhpMissingFieldTypeInspection */

class FailedLoginsByIPAddress extends DataObject {
	public $__table = 'failed_logins_by_ip_address';
	public $id;
	public $ipAddress;
	public $timestamp;

	public static function addFailedLogin() : void {
		try {
			$newLogin = new FailedLoginsByIPAddress();
			$newLogin->ipAddress = IPAddress::getClientIP();
			$newLogin->timestamp = time();
			$newLogin->insert();
		}catch (Exception) {
			//This fails when the table isn't created, ignore it
		}
	}
}