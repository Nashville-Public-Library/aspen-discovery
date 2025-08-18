<?php

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../bootstrap_aspen.php';

/**
 * Purge soft-deleted objects that have been in the "recycle-bin" for more than 30 days.
 *
 * This is executed nightly at 23:59 PM server local time
 * so administrators still have the entire "Final Day" to
 * restore objects before automatic removal.
 */
global $logger;
require_once ROOT_DIR . '/services/Admin/ObjectRestorations.php';
$softDeleteClasses = Admin_ObjectRestorations::getManagedClasses();

$totalPurged = 0;
foreach ($softDeleteClasses as $className) {
	if (class_exists($className) && method_exists($className, 'purgeExpired')) {
		$totalPurged += $className::purgeExpired();
	}
}

$logger->log("Soft-delete purge complete: $totalPurged rows permanently removed from the database.", Logger::LOG_DEBUG);