<?php

class SnapPay_Complete extends Action {
	public function launch() {
		global $interface;
		global $logger;
		global $library;
		$error = true;
		require_once ROOT_DIR . '/sys/Account/UserPayment.php';
		$message = '';
		$emailNotifications = 0; // emailNotifications 0 = Do not send email; 1 = Email errors; 2 = Email all transactions
		require_once ROOT_DIR . '/sys/ECommerce/SnapPaySetting.php';
		$snapPaySetting = new SnapPaySetting();
		$snapPaySetting->id = $library->snapPaySettingId;
		if ($snapPaySetting->find(true)) {
			// determine whether SnapPay settings have email notifications enabled
			$emailNotifications = $snapPaySetting->emailNotifications;
		}
		if ($emailNotifications !== 0) {
			global $serverName;
			require_once ROOT_DIR . '/sys/Email/Mailer.php';
			$mailer = new Mailer();
			$emailNotificationsAddresses = $snapPaySetting->emailNotificationsAddresses;
		}

		// Eliminate requests NOT originating from SnapPay
		// This check is necessary to prevent session hijacking and replay attacks
		// But what James thinks is mostly going on is that users are restoring their browsers with a tab on the SnapPay/Complete page
		$referringUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Unknown';
		if ($snapPaySetting->sandboxMode == 1) {
			$snapPayURL = 'https://stage.snappayglobal.com/';
		} else {
			$snapPayURL = 'https://www.snappayglobal.com/';
		}
		$userPayment = new UserPayment();
		$userPayment->id = $_GET['u']; // Payment Reference ID from the query string
		if ($userPayment->find(true) && $userPayment->completed != 0) {
			// TO DO: add conditional that checks SnapPay for duplicate payments
			// Redirect the user to /MyAccount/Fines, perhaps requiring a login
			header('Location: /MyAccount/Fines');
			return;
		}
		if (!str_contains($referringUrl, $snapPayURL)) {
			$error = true;
			$message = 'Invalid referring URL. The request must originate from SnapPay. Payment Reference ID ' . $_GET['u'];
			$interface->assign('error', $message);
			$logger->log($message, Logger::LOG_ERROR);
			if ($emailNotifications > 0) { // emailNotifications 0 = Do not send email; 1 = Email errors; 2 = Email all transactions
				$mailer->send($emailNotificationsAddresses, "$serverName Error with SnapPay Payment", $message);
			}
			$this->display('paymentResult.tpl', 'Payment Error');
			return;
		}

		// Attempt to restore user session if it has been lost
		// Should follow "Eliminate requests NOT originating from SnapPay" check to avoid promoting session hijacking
		if (!UserAccount::$isLoggedIn) { // if the user is NOT logged in, i.e., the session has been lost...
			global $session;
			require_once ROOT_DIR . '/sys/Session/MySQLSession.php';
			session_name('aspen_session');
			$session = new MySQLSession();
			$incomingSessionId = '';
			if (isset($_POST['udf9']) && preg_match('/^[0-9a-z]{26}$/', $_POST['udf9'])) { // As of 2025 04 21, the aspen_session ID is passed in udf9, but *sometimes* is returned in Nashville's SnapPay configuration in udf0
				$incomingSessionId = $_POST['udf9'];
			} elseif (isset($_POST['udf0']) && preg_match('/^[0-9a-z]{26}$/', $_POST['udf0'])) { // As of 2025 04 21, the aspen_session ID is passed in udf9, but *sometimes* is returned in Nashville's SnapPay configuration in udf0
				$incomingSessionId = $_POST['udf0'];
			}
			if (!empty($incomingSessionId)) {
				$retrievedSessionData = $session->read($incomingSessionId);
				if (!empty($retrievedSessionData) && isset($_POST['customerid'])) {
					if (str_contains($retrievedSessionData, 'activeUserId|i:' . $_POST['customerid'])) {
						$session->write($incomingSessionId, $retrievedSessionData);
						try {
							$_SESSION = unserialize($retrievedSessionData);
							// Destroy the current session
							session_unset();
							session_destroy();
							// Set the new session ID and start the session
							session_id($incomingSessionId);
							session_start();
						} catch (Exception $e) {
							$logger->log('Error restoring session: ' . $e->getMessage(), Logger::LOG_ERROR);
						}
						// Check if an aspen_session cookie exists
						if (isset($_COOKIE['aspen_session']) && $_COOKIE['aspen_session'] !== $incomingSessionId) {
							// Destroy the existing aspen_session cookie with a different value
							setcookie('aspen_session', $_COOKIE['aspen_session'], [
								'expires' => time() - 3600, // Set expiration to a past time
								'path' => '/',            // Cookie is available across the entire domain
								'secure' => true,        // Cookie is sent only over HTTPS
								'httponly' => '',        // Cookie is accessible through HTTP(S) and JavaScript
								'samesite' => ''        // Does NOT prevent the cookie from being sent with cross-site requests
							]);
						}
						// Set the new aspen_session cookie
						setcookie('aspen_session', $incomingSessionId, [ // Based on aspen-discovery\install\php.ini
							'expires' => 0,        // Session cookie, ends when browser closes
							'path' => '/',            // Cookie is available across the entire domain
							'secure' => true,        // Cookie is sent only over HTTPS
							'httponly' => '',        // Cookie is accessible through HTTP(S) and JavaScript
							'samesite' => ''        // Does NOT prevent the cookie from being sent with cross-site requests
						]);
					}
				}
			}
		}
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
			if (empty($_GET['u'])) { // Payment Reference ID from the query string
				$error = true;
				$message = 'No Payment Reference ID was provided in the URL.';
			}
			if (empty($_POST['udf1'])) {
				$error = true;
				$message = 'No Transaction ID was provided from SnapPay.';
			} else {
				if ($_GET['u'] !== $_POST['udf1']) {
					$error = true;
					$message = 'Payment Reference ID from SnapPay does not match Payment Reference ID in the URL. ' . $_GET['u'] . ' !== ' . $_POST['udf1'];
				}
				$params = explode(',', $_POST['hpphmacresponseparameters']);
				$hppHMACParamValue = '';
				foreach ($params as $param) {
					if ($param != 'nonce' && $param != 'timestamp') {
						$hppHMACParamValue .= $_POST[$param];
					}
				}
				$validated = $this->validateSnapPayHMAC($_POST['signature'], $hppHMACParamValue);
				if ($validated != 'Valid signature.') {
					$error = true;
					$message = "Invalid signature returned from SnapPay for Payment Reference ID" . $_GET['u'];
				} else {
					$result = UserPayment::completeSnapPayPayment();
					$message = $result['message'];
					if ($result['error'] === true) {
						$error = true;
					} else {
						$error = false;
					}
				}
			}
			if ($error === true && $result['cancelled'] === true) {
				$interface->assign('error', $message);
				$logger->log($message, Logger::LOG_ERROR);
				if ($emailNotifications === 2) { // emailNotifications 0 = Do not send email; 1 = Email errors; 2 = Email all transactions
					$mailer->send($emailNotificationsAddresses, "$serverName Error with SnapPay Payment", $message);
				}
				$this->display('paymentResult.tpl', 'Payment Cancelled');
			} elseif ($error === true) {
				$interface->assign('error', $message);
				$logger->log($message, Logger::LOG_ERROR);
				if ($emailNotifications > 0) { // emailNotifications 0 = Do not send email; 1 = Email errors; 2 = Email all transactions
					$mailer->send($emailNotificationsAddresses, "$serverName Error with SnapPay Payment", $message);
				}
				$this->display('paymentResult.tpl', 'Payment Error');
			} else {
				if (empty($message)) {
					$message = "SnapPay Payment completed with no message for Payment Reference ID " . $_GET['u'];
				}
				$interface->assign('message', $message);
				$logger->log($message, Logger::LOG_DEBUG);
				if ($emailNotifications === 2) { // emailNotifications 0 = Do not send email; 1 = Email errors; 2 = Email all transactions
					$mailer->send($emailNotificationsAddresses, "$serverName SnapPay Payment", $message);
				}
				$this->display('paymentResult.tpl', 'Payment Completed');
			}
		} else {
			$error = true;
			$message = 'Invalid request method. Only POST requests are allowed. Payment Reference ID ' . $_GET['u'];
			$interface->assign('error', $message);
			$logger->log($message, Logger::LOG_ERROR);
			if ($emailNotifications > 0) { // emailNotifications 0 = Do not send email; 1 = Email errors; 2 = Email all transactions
				$mailer->send($emailNotificationsAddresses, "$serverName Error with SnapPay Payment", $message);
			}
			$this->display('paymentResult.tpl', 'Payment Error');
		}
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
