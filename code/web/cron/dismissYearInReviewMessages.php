<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../bootstrap_aspen.php';

require_once ROOT_DIR . '/sys/Account/UserMessage.php';
require_once ROOT_DIR . '/sys/YearInReview/YearInReviewSetting.php';

require_once ROOT_DIR . '/sys/CronLogEntry.php';
$cronLogEntry = new CronLogEntry();
$cronLogEntry->startTime = time();
$cronLogEntry->name = 'Dismiss Year In Review Messages';
$cronLogEntry->insert();

$today = strtotime("today");
$tomorrow = strtotime("tomorrow");
$yearInReviewSetting  = new YearInReviewSetting();
$yearInReviewSetting->whereAdd('endDate >= ' . $today);
$yearInReviewSetting->whereAdd('endDate < ' . $tomorrow);
$yearInReviewSetting->find();
$numDismissed = 0;
if ($yearInReviewSetting->fetch()) {
	$userMessage = new UserMessage();
	$userMessage->messageType = 'yearInReview';
	$userMessage->isDismissed = 0;
	$userMessage->find();
	while ($userMessage->fetch()) {
		$userMessage->isDismissed = 1;
		$userMessage->update();
		$numDismissed++;
	}
}

$cronLogEntry->notes .= "<br/>Dismissed $numDismissed Year In Review messages";

$cronLogEntry->endTime = time();
$cronLogEntry->update();

global $aspen_db;
$aspen_db = null;

die();
