<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../bootstrap_aspen.php';

require_once ROOT_DIR . '/sys/MaterialsRequests/MaterialsRequestHoldCandidateGenerator.php';
require_once ROOT_DIR . '/sys/CronLogEntry.php';
$cronLogEntry = new CronLogEntry();
$cronLogEntry->startTime = time();
$cronLogEntry->name = 'Generate Material Request Hold Candidates';
$cronLogEntry->insert();

generateMaterialsRequestsHoldCandidates();

$cronLogEntry->endTime = time();
$cronLogEntry->update();