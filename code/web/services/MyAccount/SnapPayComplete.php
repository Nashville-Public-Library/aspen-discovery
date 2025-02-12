<?php
require_once ROOT_DIR . '/services/MyAccount/MyAccount.php';

class MyAccount_SnapPayComplete extends MyAccount {
	public function launch() {
		global $interface;
		global $logger;
		global $serverName;
		$error = '';
		$message = '';
		require_once ROOT_DIR . '/sys/Email/Mailer.php';
		$mailer = new Mailer();
		require_once ROOT_DIR . '/sys/SystemVariables.php';
		$systemVariables = SystemVariables::getSystemVariables();
		if (empty($_REQUEST['udf1'])) {
			$error = 'No Transaction ID was provided';
		} else {
			$paymentId = $_REQUEST['udf1'];
			$hppHMACParamValue = '';
			$params = explode(',', $_REQUEST['hpphmacresponseparameters']);
			foreach ($params as $param) {
				if ($param != 'nonce' && $param != 'timestamp') {
					$hppHMACParamValue .= $_REQUEST[$param];
				}
			}
			$validated = $this->validateSnapPayHMAC($_REQUEST['signature'], $hppHMACParamValue);
			if ($validated != 'Valid signature.') {
				$error = 'Invalid signature';
			} else {
				require_once ROOT_DIR . '/sys/Account/UserPayment.php';
				$result = UserPayment::completeSnapPayPayment();
				if ($result['error'] === true) {
					$error = $result['message'];
					$logger->log($error, Logger::LOG_ERROR);
					if (!empty($systemVariables->errorEmail)) {
						$mailer->send($systemVariables->errorEmail, "$serverName Error with online payment", $error);
					}
				} else {
					$message = $result['message'];
					$logger->log($message, Logger::LOG_DEBUG);
				}
			}
		}
		$interface->assign('error', $error);
		$interface->assign('message', $message);
		$this->display('paymentCompleted.tpl');
	}

	function validateSnapPayHMAC(string $signatureFromSnapPay, $hppHMACParamValue): string {
		global $library;
		require_once ROOT_DIR . '/sys/ECommerce/SnapPaySetting.php';
		$snapPaySetting = new SnapPaySetting();
		$snapPaySetting->id = $library->snapPaySettingId;
		if ($snapPaySetting->find(true)) {
			$hmacHeader = '';
			try {
				$secretkey = $snapPaySetting->apiAuthenticationCode;
				// Retrieve the actual signature, nonce, and timestamp from the response
				$rawAuthzHeader = mb_convert_encoding(base64_decode($signatureFromSnapPay), 'ISO-8859-1');
				$authorizationHeaderArray = explode(':', $rawAuthzHeader);
				if ($authorizationHeaderArray) {
					$incomingBase64Signature = $authorizationHeaderArray[0];
					$nonce = $authorizationHeaderArray[1];
					$timestamp = $authorizationHeaderArray[2];
					// Concatenate hppHMACParamValue, nonce, and timestamp
					$data = sprintf("%s%s%s", $hppHMACParamValue, $nonce, $timestamp);
					$signature = mb_convert_encoding($data, 'UTF-8');
					// Decode the secret key from Base64
					$secretKeyByteArray = base64_decode($secretkey);
					// Compute the HMAC SHA-256 hash
					$hmac = hash_hmac('sha256', $signature, $secretKeyByteArray, true);
					$convertedInputString = base64_encode($hmac);
					// Compare the computed signature with the incoming signature
					$isValid = hash_equals($incomingBase64Signature, $convertedInputString);
					$hmacHeader = $isValid ? "Valid signature." : "Invalid signature.";
				}
			} catch (Exception $e) {
				$hmacHeader = $e->getMessage();
			}
			return $hmacHeader;
		}
	}
	function getBreadcrumbs(): array {
		$breadcrumbs = [];
		$breadcrumbs[] = new Breadcrumb('/MyAccount/Home', 'Your Account');
		$breadcrumbs[] = new Breadcrumb('/MyAccount/Fines', 'Your Fines');
		$breadcrumbs[] = new Breadcrumb('', 'Payment Completed');
		return $breadcrumbs;
	}
}
