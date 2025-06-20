<?php

if (count($_SERVER['argv']) > 1) {
	$serverName = $_SERVER['argv'][1];
	// Check to see if the update already exists properly.
	$fhnd = fopen('/usr/local/aspen-discovery/sites/' . $serverName . '/conf/crontab_settings.txt', 'r');
	if ($fhnd) {
		$lines = [];
		$insertFetchIlsMessages = true;
		$fetchIlsMessagesInserted = false;
		while (($line = fgets($fhnd)) !== false) {
			// Detect if the cron job is already present.
			if (str_contains($line, 'fetchILSMessages.php')) {
				$insertFetchIlsMessages = false;
			}
			// Insert before Debian end-of-file marker.
			if ($insertFetchIlsMessages && str_contains($line, 'Debian needs a blank line at the end of cron')) {
				if (!empty($lines) && trim(end($lines)) !== '') {
					$lines[] = "\n";
				}
				$lines[] = "#######################\n";
				$lines[] = "# Fetch ILS Messages #\n";
				$lines[] = "#######################\n";
				$lines[] = "*/40 * * * * root php /usr/local/aspen-discovery/code/web/cron/fetchILSMessages.php $serverName\n";
				$lines[] = "\n";
				$fetchIlsMessagesInserted = true;
			}
			$lines[] = $line;
		}
		fclose($fhnd);

		// Fallback: If marker was not found, add at the end.
		if ($insertFetchIlsMessages && !$fetchIlsMessagesInserted) {
			if (!empty($lines) && trim(end($lines)) !== '') {
				$lines[] = "\n";
			}
			$lines[] = "#######################\n";
			$lines[] = "# Fetch ILS Messages #\n";
			$lines[] = "#######################\n";
			$lines[] = "*/40 * * * * root php /usr/local/aspen-discovery/code/web/cron/fetchILSMessages.php $serverName\n";
			$lines[] = "\n";
			$fetchIlsMessagesInserted = true;
		}

		$fhnd = fopen('/usr/local/aspen-discovery/sites/' . $serverName . '/conf/crontab_settings.txt', 'r');
		$insertSendIlsMessages = true;
		$sendIlsMessagesInserted = false;
		while (($line = fgets($fhnd)) !== false) {
			// Detect if the cron job is already present.
			if (str_contains($line, 'fetchILSMessages.php')) {
				$insertSendIlsMessages = false;
			}
			// Insert before Debian end-of-file marker.
			if ($insertSendIlsMessages && str_contains($line, 'Debian needs a blank line at the end of cron')) {
				if (!empty($lines) && trim(end($lines)) !== '') {
					$lines[] = "\n";
				}
				$lines[] = "#######################\n";
				$lines[] = "# Fetch ILS Messages #\n";
				$lines[] = "#######################\n";
				$lines[] = "*/40 * * * * root php /usr/local/aspen-discovery/code/web/cron/fetchILSMessages.php $serverName\n";
				$lines[] = "\n";
				$sendIlsMessagesInserted = true;
			}
			$lines[] = $line;
		}
		fclose($fhnd);

		// Fallback: If marker was not found, add at the end.
		if ($insertSendIlsMessages && !$sendIlsMessagesInserted) {
			if (!empty($lines) && trim(end($lines)) !== '') {
				$lines[] = "\n";
			}
			$lines[] = "#######################\n";
			$lines[] = "# Fetch ILS Messages #\n";
			$lines[] = "#######################\n";
			$lines[] = "*/40 * * * * root php /usr/local/aspen-discovery/code/web/cron/fetchILSMessages.php $serverName\n";
			$lines[] = "\n";
			$sendIlsMessagesInserted = true;
		}

		// Write the file only if the new cron job was inserted.
		if ($fetchIlsMessagesInserted || $insertSendIlsMessages) {
			$newContent = implode('', $lines);
			file_put_contents('/usr/local/aspen-discovery/sites/' . $serverName . '/conf/crontab_settings.txt', $newContent);
		}
	} else {
		echo '- Could not find cron settings file.' . PHP_EOL;
	}
} else {
	echo 'Must provide servername as first argument.'. PHP_EOL;
	exit();
}
