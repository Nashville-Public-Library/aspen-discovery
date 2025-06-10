<?php

require_once ROOT_DIR . '/Action.php';
require_once ROOT_DIR . '/services/Admin/Admin.php';
require_once ROOT_DIR . '/services/Admin/ObjectEditor.php';
require_once ROOT_DIR . '/sys/ECommerce/SnapPaySetting.php';
require_once ROOT_DIR . '/services/SnapPay/SnapPayReconciliationService.php';

class Admin_SnapPaySettings extends ObjectEditor {
	function getObjectType(): string {
		return 'SnapPaySetting';
	}

	function getToolName(): string {
		return 'SnapPaySettings';
	}

	function getPageTitle(): string {
		return 'SnapPay Settings';
	}

	function getAllObjects($page, $recordsPerPage): array {
		$list = [];

		$object = new SnapPaySetting();
		$object->orderBy($this->getSort());
		$this->applyFilters($object);
		$object->limit(($page - 1) * $recordsPerPage, $recordsPerPage);
		$object->find();
		while ($object->fetch()) {
			$list[$object->id] = clone $object;
		}

		return $list;
	}

	function getDefaultSort(): string {
		return 'name asc';
	}

	function getObjectStructure($context = ''): array {
		return SnapPaySetting::getObjectStructure($context);
	}

	function getPrimaryKeyColumn(): string {
		return 'id';
	}

	function getIdKeyColumn(): string {
		return 'id';
	}

	function getInstructions(): string {
		return 'https://help.aspendiscovery.org/help/admin/ecommerce';
	}

	function getBreadcrumbs(): array {
		$breadcrumbs = [];
		$breadcrumbs[] = new Breadcrumb('/Admin/Home', 'Administration Home');
		$breadcrumbs[] = new Breadcrumb('/Admin/Home#ecommerce', 'eCommerce');
		$breadcrumbs[] = new Breadcrumb('', 'SnapPay Settings');
		return $breadcrumbs;
	}

	function getActiveAdminSection(): string {
		return 'ecommerce';
	}

	function canView(): bool {
		return UserAccount::userHasPermission('Administer SnapPay');
	}

	/**
	 * Add a "Test API" button to the SnapPay settings page
	 *
	 * @param object $existingObject The SnapPay setting object
	 * @return array Array of additional actions
	 */
	function getAdditionalObjectActions($existingObject): array {
		$actions = [];
		if (!empty($existingObject) && $existingObject->id > 0) {
			$actions[] = [
				'text' => 'Test Transaction History API',
				'url' => '/Admin/SnapPaySettings?objectAction=testTransactionHistoryAPI&id=' . $existingObject->id,
			];
		}
		return $actions;
	}

	/**
	 * Test the SnapPay Transaction History API and display the results
	 */
	function testTransactionHistoryAPI() {
		global $interface;

		// Get the SnapPay setting
		$id = $_REQUEST['id'];
		$snapPaySetting = new SnapPaySetting();
		$snapPaySetting->id = $id;

		if ($snapPaySetting->find(true)) {
			// Create a test service
			$testService = new SnapPayAPITester();

			// Set test parameters
			$startDate = isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : null;
			$endDate = isset($_REQUEST['endDate']) ? $_REQUEST['endDate'] : null;
			$verbose = isset($_REQUEST['verbose']) && $_REQUEST['verbose'] == 'true';

			// Run the test
			$results = $testService->testTransactionHistoryAPI($snapPaySetting, $verbose, $startDate, $endDate);

			// Assign results to the template
			$interface->assign('snapPaySetting', $snapPaySetting);
			$interface->assign('results', $results);
			$interface->assign('startDate', $startDate);
			$interface->assign('endDate', $endDate);
			$interface->assign('verbose', $verbose);

			// Set the template but don't display it
			$interface->setTemplate('snapPayAPITest.tpl');
			$interface->setPageTitle('SnapPay API Test Results');
		} else {
			$interface->assign('error', 'Could not find SnapPay setting with ID ' . $id);
			$interface->setTemplate('../Error/error.tpl');
			$interface->setPageTitle('Error');
		}
	}
}
	/**
 * Class to test the SnapPay Transaction History API
 */
class SnapPayAPITester extends SnapPayReconciliationService {
	/**
	 * Test the connection to the SnapPay Transaction History API
	 *
	 * @param SnapPaySetting $snapPaySetting The SnapPay settings to use
	 * @param bool $verbose Whether to output detailed information
	 * @param string|null $startDate Optional start date in m/d/Y H:i:s format
	 * @param string|null $endDate Optional end date in m/d/Y H:i:s format
	 * @return array Results of the API test
	 */
	public function testTransactionHistoryAPI(SnapPaySetting $snapPaySetting, bool $verbose = false, ?string $startDate = null, ?string $endDate = null): array {
		$results = [
			'success' => false,
			'message' => '',
			'api_url' => '',
			'request_headers' => [],
			'request_params' => [],
			'http_code' => 0,
			'response_raw' => '',
			'transactions' => [],
			'transaction_count' => 0,
			'timestamp' => time(),
		];

		try {
			// Determine API URL based on sandbox mode
			$apiBaseUrl = $snapPaySetting->sandboxMode == 1 ?
				'https://restapi-stage.snappayglobal.com/' :
				'https://restapi.snappayglobal.com/';

			$results['api_url'] = $apiBaseUrl;

			// Parse filters from settings
			$filters = json_decode($snapPaySetting->automatedReconciliationFilters, true);
			if (!$filters) {
				$filters = ['status' => 'success'];
			}

			// Add required filter for successful transactions
			$filters['status'] = 'success';

			// Override date filters if provided
			if ($startDate !== null) {
				$filters['startdate'] = $startDate;
			} elseif (!empty($snapPaySetting->lastReconciliationTime) && $snapPaySetting->lastReconciliationTime > 0) {
				// Add 1 second to avoid duplicate transactions
				$startTime = $snapPaySetting->lastReconciliationTime + 1;
				$filters['startdate'] = date('m/d/Y H:i:s', $startTime);
			}

			if ($endDate !== null) {
				$filters['enddate'] = $endDate;
			}

			// Store request parameters for display
			$results['request_params'] = $filters;

			// Create request headers for display
			$results['request_headers'] = [
				'Authorization: Basic ' . '[CREDENTIALS HIDDEN]',
				'Content-Type: application/json',
				'Accept: application/json'
			];

			// Use parent class method to get transactions
			// We need to temporarily store the filters in the setting object
			$originalFilters = $snapPaySetting->automatedReconciliationFilters;
			$snapPaySetting->automatedReconciliationFilters = json_encode($filters);

			// Capture the API response for verbose mode
			$responseData = null;
			$httpCode = 0;

			try {
				// Call parent method to get transactions
				$transactions = $this->getTransactionHistory($snapPaySetting, $apiBaseUrl, $responseData, $httpCode);

				// Store HTTP code and raw response if in verbose mode
				$results['http_code'] = $httpCode;
				if ($verbose && $responseData !== null) {
					$results['response_raw'] = $responseData;
				}

				$results['transactions'] = $transactions;
				$results['transaction_count'] = count($transactions);
				$results['success'] = true;
				$results['message'] = "Successfully retrieved {$results['transaction_count']} transactions from SnapPay API";
			} finally {
				// Restore original filters
				$snapPaySetting->automatedReconciliationFilters = $originalFilters;
			}

		} catch (Exception $e) {
			$results['success'] = false;
			$results['message'] = "Error during API test: " . $e->getMessage();
		}

		return $results;
	}
}