<?php
$serverName = $argv[1];
echo("Starting update of cron for $serverName\n");

// Add the snapPayReconciliation.php cron job to the crontab_settings.txt file
$fhnd = fopen("/usr/local/aspen-discovery/sites/$serverName/conf/crontab_settings.txt", 'r');
if ($fhnd) {
    $newContents = '';
    $lines = [];
    $foundCronEntry = false;

    while ($line = fgets($fhnd)) {
        $lines[] = $line;
        if (strpos($line, 'snapPayReconciliation.php') !== false) {
            $foundCronEntry = true;
        }
    }
    fclose($fhnd);

    if (!$foundCronEntry) {
        // Add the cron job entry - run every 15 minutes
        $lines[] = "*/15 * * * * root php /usr/local/aspen-discovery/code/web/cron/snapPayReconciliation.php $serverName\n";

        // Add a blank line at the end for Debian
        if (strpos($lines[count($lines) - 1], 'Debian needs a blank line at the end of cron') === false) {
            $lines[] = "# Debian needs a blank line at the end of cron\n";
        }

        $newContents = implode('', $lines);
        file_put_contents("/usr/local/aspen-discovery/sites/$serverName/conf/crontab_settings.txt", $newContents);
        echo("- Added snapPayReconciliation.php cron job\n");
    } else {
        echo("- snapPayReconciliation.php cron job already exists\n");
    }
} else {
    echo("- Could not find cron settings file\n");
}

echo("Finished update of cron for $serverName\n");
