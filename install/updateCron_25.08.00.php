<?php

if (count($_SERVER['argv']) > 1) {
	$serverName = $_SERVER['argv'][1];
	// Check to see if the update already exists properly.
	$fhnd = fopen('/usr/local/aspen-discovery/sites/' . $serverName . '/conf/crontab_settings.txt', 'r');
	if ($fhnd) {
		$lines = [];
		$insertPurgeSoftDeleted = true;
		$purgeSoftDeletedInserted = false;
		while (($line = fgets($fhnd)) !== false) {
			// Detect if the cron job is already present.
			if (str_contains($line, 'purgeSoftDeleted.php')) {
				$insertPurgeSoftDeleted = false;
				$line = "59 23 * * * root php /usr/local/aspen-discovery/code/web/cron/purgeSoftDeleted.php $serverName\n";
			}
			// Insert before Debian end-of-file marker.
			if ($insertPurgeSoftDeleted && str_contains($line, 'Debian needs a blank line at the end of cron')) {
				if (!empty($lines) && trim(end($lines)) !== '') {
					$lines[] = "\n";
				}
				$lines[] = "#######################################\n";
				$lines[] = "# Purge Soft-Deleted Database Objects #\n";
				$lines[] = "#######################################\n";
				$lines[] = "59 23 * * * root php /usr/local/aspen-discovery/code/web/cron/purgeSoftDeleted.php $serverName\n";
				$lines[] = "\n";
				$purgeSoftDeletedInserted = true;
			}
			$lines[] = $line;
		}
		fclose($fhnd);

		// Fallback: If marker was not found, add at the end.
		if ($insertPurgeSoftDeleted && !$purgeSoftDeletedInserted) {
			if (!empty($lines) && trim(end($lines)) !== '') {
				$lines[] = "\n";
			}
			$lines[] = "#######################################\n";
			$lines[] = "# Purge Soft-Deleted Database Objects #\n";
			$lines[] = "#######################################\n";
			$lines[] = "59 23 * * * root php /usr/local/aspen-discovery/code/web/cron/purgeSoftDeleted.php $serverName\n";
			$lines[] = "\n";
			$purgeSoftDeletedInserted = true;
		}

		// Write the file only if the new cron job was inserted.
		if ($purgeSoftDeletedInserted) {
			$newContent = implode('', $lines);
			file_put_contents('/usr/local/aspen-discovery/sites/' . $serverName . '/conf/crontab_settings.txt', $newContent);
		}
	} else {
		echo '- Could not find cron settings file.' . PHP_EOL;
	}
} else {
	echo 'Must provide server name as first argument.' . PHP_EOL;
	exit();
}