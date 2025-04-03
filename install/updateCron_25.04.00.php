<?php
if (count($_SERVER['argv']) > 1) {
	$serverName = $_SERVER['argv'][1];
	//Check to see if the update already exists properly.
	$fhnd = fopen("/usr/local/aspen-discovery/sites/$serverName/conf/crontab_settings.txt", 'r');
	if ($fhnd) {
		$lines = [];
		$insertSection = true;
		$sectionInserted = false;
		while (($line = fgets($fhnd)) !== false) {
			if (strpos($line, 'talpaWorksCron') > 0) {
				$insertSection = false;
			}
			if (strpos($line, 'Debian needs a blank line at the end of cron') > 0) {
				if ($insertSection) {
					//Add these before the end of the file in debian
					$lines[] = "####################################\n";
					$lines[] = "# Update grouped works for TalpaAI #\n";
					$lines[] = "####################################\n";
					$lines[] = "0 1 * * * root php /usr/local/aspen-discovery/code/web/cron/talpaWorksCron.php $serverName\n";
					$sectionInserted = true;
				}
			}
			$lines[] = $line;
		}
		fclose($fhnd);

		if ($insertSection && !$sectionInserted) {
			//Add at the end for everything else
			$lines[] = "######################################\n";
			$lines[] = "# Dismiss old Year in Review messages #\n";
			$lines[] = "######################################\n";
			$lines[] = "0 3 * * * root php /usr/local/aspen-discovery/code/web/cron/dismissYearInReviewMessages.php $serverName\n";
		}
		if ($insertSection) {
			$newContent = implode('', $lines);
			file_put_contents("/usr/local/aspen-discovery/sites/$serverName/conf/crontab_settings.txt", $newContent);
		}
	} else {
		echo("- Could not find cron settings file\n");
	}

} else {
	echo 'Must provide servername as first argument';
	exit();
}
