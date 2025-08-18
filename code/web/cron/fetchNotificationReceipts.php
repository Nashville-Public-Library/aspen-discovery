<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../bootstrap_aspen.php';

require_once ROOT_DIR . '/sys/Account/UserNotification.php';
require_once ROOT_DIR . '/sys/Notifications/ExpoNotification.php';

require_once ROOT_DIR . '/sys/CronLogEntry.php';
$cronLogEntry = new CronLogEntry();
$cronLogEntry->startTime = time();
$cronLogEntry->name = 'Fetching Notification Receipts';
$cronLogEntry->insert();

$userNotification = new UserNotification();
$userNotification->completed = 0;
$userNotification->error = 0;

$notifications = array_filter($userNotification->fetchAll('receiptId'));

$userNotification = null;

$numProcessed = 0;

$cronLogEntry->notes = "Found " . count($notifications) . " notifications to process";

$expoNotification = new ExpoNotification();
foreach ($notifications as $notification) {
	$expoNotification->getExpoNotificationReceipt($notification);
	$numProcessed++;
}
$expoNotification = null;

$cronLogEntry->endTime = time();
$cronLogEntry->update();

global $aspen_db;
$aspen_db = null;

die();