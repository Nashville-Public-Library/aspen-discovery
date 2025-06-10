<?php

require_once ROOT_DIR . '/sys/ECommerce/SnapPaySetting.php';
require_once ROOT_DIR . '/sys/Account/UserPayment.php';

class SnapPayReconciliationService {
    /**
     * Check for and process any missing transactions
     * 
     * @param SnapPaySetting $snapPaySetting The SnapPay settings to use
     * @return array Results of the reconciliation process
     */
    public function reconcileTransactions(SnapPaySetting $snapPaySetting): array {
        $results = [
            'success' => false,
            'message' => '',
            'transactions_found' => 0,
            'transactions_processed' => 0,
            'errors' => [],
            'timestamp' => time(), // Add current timestamp
        ];

        try {
            // Determine API URL based on sandbox mode
            $apiBaseUrl = $snapPaySetting->sandboxMode == 1 ?
				'https://restapi-stage.snappayglobal.com/' :
				'https://restapi.snappayglobal.com/';

            // Get transaction history from SnapPay API
            $transactions = $this->getTransactionHistory($snapPaySetting, $apiBaseUrl);

            if (empty($transactions)) {
                $results['message'] = 'No transactions found in SnapPay Transaction History';
                return $results;
            }

            $results['transactions_found'] = count($transactions);

            // Process each transaction
            foreach ($transactions as $transaction) {
                // Skip transactions that don't have a reference ID in udf1 or udf9
                if (empty($transaction['udf1']) && empty($transaction['udf9'])) {
                    $results['errors'][] = "Transaction {$transaction['paymenttransactionid']} has no reference ID";
                    continue;
                }

                // Use udf1 or udf9 as the payment reference ID
                $paymentReferenceId = !empty($transaction['udf1']) ? $transaction['udf1'] : $transaction['udf9'];

                // Check if this transaction exists in our system
                $userPayment = new UserPayment();
                $userPayment->id = $paymentReferenceId;

                if ($userPayment->find(true)) {
                    // If payment exists but is not completed, process it
                    if (!$userPayment->completed) {
                        $this->processTransaction($userPayment, $transaction);
                        $results['transactions_processed']++;
                    }
                } else {
                    $results['errors'][] = "Payment with reference ID {$paymentReferenceId} not found in system";
                }
            }

            $results['success'] = true;
            $results['message'] = "Reconciliation completed: {$results['transactions_processed']} transactions processed";

        } catch (Exception $e) {
            $results['success'] = false;
            $results['message'] = "Error during reconciliation: " . $e->getMessage();
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Get transaction history from SnapPay API
     * 
     * @param SnapPaySetting $snapPaySetting The SnapPay settings to use
     * @param string $apiBaseUrl The base URL for the API
     * @return array Array of transactions
     */
	protected function getTransactionHistory(SnapPaySetting $snapPaySetting, string $apiBaseUrl, &$responseRaw = null, &$httpCode = 0): array {
        // Parse filters from settings
        $filters = json_decode($snapPaySetting->automatedReconciliationFilters, true);
        if (!$filters) {
            $filters = ['status' => 'success'];
        }

        // Add required filter for successful transactions
        $filters['status'] = 'success';

        // Add start date filter if we have a last reconciliation time
        if (!empty($snapPaySetting->lastReconciliationTime) && $snapPaySetting->lastReconciliationTime > 0) {
            // Add 1 second to avoid duplicate transactions
            $startTime = $snapPaySetting->lastReconciliationTime + 1;
            $filters['startdate'] = date('m/d/Y H:i:s', $startTime);
        }

        // Build query parameters
        $queryParams = http_build_query($filters);

        // Set up API request
		$url = $apiBaseUrl . "api/TransactionHistory?" . $queryParams;
		$headers = [
            'Authorization: Basic ' . base64_encode($snapPaySetting->accountId . ':' . $snapPaySetting->apiBasicAuthPassword),
			'AccountId: ' . $snapPaySetting->accountId,
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        // Make API request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		// Always store the raw response in the reference parameter
		$responseRaw = $response;

		if ($httpCode != 200) {
            throw new Exception("API request failed with status code: $httpCode, response: $response");
        }

        $responseData = json_decode($response, true);

        if (!isset($responseData['transactions']) || !is_array($responseData['transactions'])) {
            return [];
        }

        return $responseData['transactions'];
    }

    /**
     * Process a transaction by simulating the SnapPay/Complete endpoint
     * 
     * @param UserPayment $userPayment The user payment record
     * @param array $transaction The transaction data from SnapPay
     * @return bool Success or failure
     */
    private function processTransaction(UserPayment $userPayment, array $transaction): bool {
        // Prepare the payload similar to what would be received from SnapPay
        $_POST = [
            'udf1' => $userPayment->id,
            'paymenttransactionid' => $transaction['paymenttransactionid'],
            'transactionstatus' => 'Y', // Successful transaction
            'transactionamount' => $transaction['transactionamount'],
            'returnmessage' => 'Transaction successful',
            // Add other required fields for HMAC validation if needed
        ];

        // Call the existing method to complete the payment
        $result = UserPayment::completeSnapPayPayment();

        return !$result['error'];
    }
}
