<?php /** @noinspection PhpMissingFieldTypeInspection */

require_once ROOT_DIR . '/sys/Account/PType.php';

class LiDANotificationPType extends DataObject {
	public $__table = 'aspen_lida_notifications_ptype';
	public $id;
	public $lidaNotificationId;
	public $patronTypeId;

}
