<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../bootstrap_aspen.php';

require_once ROOT_DIR . '/sys/Notifications/ExpoNotification.php';
require_once ROOT_DIR . '/sys/LocalEnrichment/LiDANotification.php';
require_once ROOT_DIR . '/sys/CronLogEntry.php';
$cronLogEntry = new CronLogEntry();
$cronLogEntry->startTime = time();
$cronLogEntry->name = 'Send LiDA Notifications';
$cronLogEntry->insert();

$allNotifications = new LiDANotification();
$allNotifications->sent = 0;
$notifications = $allNotifications->fetchAll('id');
$allNotifications->__destruct();
$allNotifications = null;

$numNotificationsSent = 0;
$cronLogEntry->notes = "Found " . count($notifications) . " notifications to process";
foreach ($notifications as $notification) {
	$tokens = [];
	$notificationToSend = new LiDANotification();
	$notificationToSend->id = $notification;
	if ($notificationToSend->find(true)) {
		$now = time();
		if ($now - $notificationToSend->sendOn > 0) {
			$expirationTime = $notificationToSend->sendOn + (7 * 24 * 60 * 60);
			if (!empty($notificationToSend->expiresOn)) {
				$expirationTime = $notificationToSend->expiresOn;
			}
			$tokens = $notificationToSend->getEligibleUsers();
			foreach ($tokens as $token => $user) {
				$body = [
					'to' => $user['token'],
					'title' => $notificationToSend->title,
					'body' => strip_tags(html_entity_decode($notificationToSend->message)),
					'categoryId' => 'libraryAlert',
					'channelId' => 'libraryAlert',
					'expiration' => $expirationTime,
				];

				if ($notificationToSend->linkType == 1 || $notificationToSend->linkType == "1") {
					$body['data'] = [
						'url' => urlencode($notificationToSend->ctaUrl),
					];
				} else {
					require_once ROOT_DIR . '/sys/AspenLiDA/LocationSetting.php';
					$body['data'] = [
						'url' => urlencode(LocationSetting::getDeepLinkByName($notificationToSend->deepLinkPath, $notificationToSend->deepLinkId ?? '')),
					];
				}

				$expoNotification = new ExpoNotification();
				$expoNotification->sendExpoPushNotification($body, $user['token'], $user['uid'], "custom_notification");
				$expoNotification = null;
				$numNotificationsSent++;
			}
			$tokens = null;

			$notificationToSend->sent = 1;
			$notificationToSend->expiresOn = $expirationTime;
			$notificationToSend->update();
		}
	}
	$notificationToSend->__destruct();
	$notificationToSend = null;
}

$cronLogEntry->notes .= "<br/>Sent $numNotificationsSent notifications";
$cronLogEntry->endTime = time();
$cronLogEntry->update();

global $aspen_db;
$aspen_db = null;

die();