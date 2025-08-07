<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../bootstrap_aspen.php';

require_once ROOT_DIR . '/sys/Session/SharedSession.php';
require_once ROOT_DIR . '/sys/CronLogEntry.php';
$cronLogEntry = new CronLogEntry();
$cronLogEntry->startTime = time();
$cronLogEntry->name = 'Cleanup Shared Sessions';
$cronLogEntry->insert();

$sharedSessions = new SharedSession();

$sessions = array_filter($sharedSessions->fetchAll('sessionId'));
$cronLogEntry->notes = "Found " . count($sessions) . " shared sessions to process";

$sharedSessions = null;

$numProcessed = 0;

$numDeleted = 0;
foreach ($sessions as $session) {
	$sharedSession = new SharedSession();
	$sharedSession->setSessionId($session);
	if($sharedSession->find(true)) {
		$createdOn = $sharedSession->getCreated();
		$oneHourLater = strtotime('+1 hour', $createdOn);
		if($createdOn <= $oneHourLater) {
			$numDeleted++;
			$sharedSession->delete();
		}
	}
	$sharedSession->__destruct();
	$sharedSession = null;
	$numProcessed++;
}
$cronLogEntry->notes .= "<br/>Deleted $numDeleted shared sessions";

$cronLogEntry->endTime = time();
$cronLogEntry->update();

global $aspen_db;
$aspen_db = null;
$configArray = null;

die();

/////// END OF PROCESS ///////