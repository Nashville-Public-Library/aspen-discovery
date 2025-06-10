<?php
require_once __DIR__ . '/../bootstrap.php';
require_once ROOT_DIR . '/sys/ECommerce/SnapPaySetting.php';
require_once ROOT_DIR . '/services/SnapPay/SnapPayReconciliationService.php';

global $logger;

$logger->log('Starting SnapPay reconciliation process', Logger::LOG_NOTICE);

// Get all SnapPay settings with automated reconciliation enabled
$snapPaySetting = new SnapPaySetting();
$snapPaySetting->enableAutomatedReconciliation = 1;
$snapPaySetting->find();

$reconciliationService = new SnapPayReconciliationService();

while ($snapPaySetting->fetch()) {
    $logger->log("Processing reconciliation for SnapPay setting: {$snapPaySetting->name}", Logger::LOG_NOTICE);

    try {
        $results = $reconciliationService->reconcileTransactions($snapPaySetting);

        if ($results['success']) {
            $logger->log("Reconciliation successful: {$results['message']}", Logger::LOG_NOTICE);

            // Update the last reconciliation time
            $snapPaySettingToUpdate = new SnapPaySetting();
            $snapPaySettingToUpdate->id = $snapPaySetting->id;
            if ($snapPaySettingToUpdate->find(true)) {
                $snapPaySettingToUpdate->lastReconciliationTime = time();
                $snapPaySettingToUpdate->update();
                $logger->log("Updated last reconciliation time for {$snapPaySetting->name}", Logger::LOG_NOTICE);
            }
        } else {
            $logger->log("Reconciliation failed: {$results['message']}", Logger::LOG_ERROR);
        }

        // Log any errors
        foreach ($results['errors'] as $error) {
            $logger->log("Reconciliation error: {$error}", Logger::LOG_ERROR);
        }

        // Send email notifications if configured
        if ($snapPaySetting->emailNotifications > 0 && 
            (!$results['success'] || $snapPaySetting->emailNotifications == 2)) {

            require_once ROOT_DIR . '/sys/Email/Mailer.php';
            $mailer = new Mailer();

            $subject = $results['success'] ? 
                "SnapPay Reconciliation Completed" : 
                "SnapPay Reconciliation Failed";

            $body = "SnapPay Reconciliation Results:\n\n";
            $body .= "Setting: {$snapPaySetting->name}\n";
            $body .= "Status: " . ($results['success'] ? "Success" : "Failed") . "\n";
            $body .= "Message: {$results['message']}\n";
            $body .= "Transactions Found: {$results['transactions_found']}\n";
            $body .= "Transactions Processed: {$results['transactions_processed']}\n";
            $body .= "Time: " . date('Y-m-d H:i:s', $results['timestamp']) . "\n";

            if ($results['success']) {
                $body .= "Last Reconciliation Time: " . date('Y-m-d H:i:s', $snapPaySetting->lastReconciliationTime) . "\n";
            }

            if (!empty($results['errors'])) {
                $body .= "\nErrors:\n";
                foreach ($results['errors'] as $error) {
                    $body .= "- {$error}\n";
                }
            }

            $mailer->send($snapPaySetting->emailNotificationsAddresses, $subject, $body);
        }

    } catch (Exception $e) {
        $logger->log("Exception during reconciliation: " . $e->getMessage(), Logger::LOG_ERROR);
    }
}

$logger->log('SnapPay reconciliation process completed', Logger::LOG_NOTICE);
