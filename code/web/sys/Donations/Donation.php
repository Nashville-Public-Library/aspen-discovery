<?php /** @noinspection PhpMissingFieldTypeInspection */

require_once ROOT_DIR . '/sys/Donations/DonationValue.php';
require_once ROOT_DIR . '/sys/Donations/DonationFormFields.php';
require_once ROOT_DIR . '/sys/Donations/DonationEarmark.php';
require_once ROOT_DIR . '/sys/Donations/DonationDedicationType.php';

class Donation extends DataObject {
	public $__table = 'donations';   // table name

	public $id;
	public $paymentId;
	public $firstName;
	public $lastName;
	public $email;
	public $address;
	public $address2;
	public $city;
	public $state;
	public $zip;
	public $anonymous;
	public $donateToLocationId;
	public $donateToLocation;
	public $comments;
	public $dedicate;
	public $dedicateType;
	public $honoreeFirstName;
	public $honoreeLastName;
	public $sendEmailToUser;
	public $donationSettingId;
	public $shouldBeNotified;
	public $notificationFirstName;
	public $notificationLastName;
	public $notificationAddress;
	public $notificationCity;
	public $notificationState;
	public $notificationZip;

	static $_objectStructure = [];
	static function getObjectStructure(string $context = ''): array {
		if (isset(self::$_objectStructure[$context]) && self::$_objectStructure[$context] !== null) {
			return self::$_objectStructure[$context];
		}
		$structure = [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id within the database',
			],
			'dateCompleted' => [
				'property' => 'dateCompleted',
				'type' => 'timestamp',
				'label' => 'Date Completed',
				'description' => 'The date the donation was completed',
				'readOnly' => true,
			],
			'firstName' => [
				'property' => 'firstName',
				'type' => 'text',
				'label' => 'First Name',
				'description' => 'The first name of the person making the donation',
				'readOnly' => true,
			],
			'lastName' => [
				'property' => 'lastName',
				'type' => 'text',
				'label' => 'Last Name',
				'description' => 'The last name of the person making the donation',
				'readOnly' => true,
			],
			'email' => [
				'property' => 'email',
				'type' => 'email',
				'label' => 'Email',
				'description' => 'The email of the person making the donation',
				'readOnly' => true,
			],
			'address' => [
				'property' => 'address',
				'type' => 'text',
				'label' => 'Address',
				'description' => 'The address of the person making the donation',
				'readOnly' => true,
			],
			'address2' => [
				'property' => 'address2',
				'type' => 'text',
				'label' => 'Address 2',
				'description' => 'The address of the person making the donation',
				'readOnly' => true,
			],
			'city' => [
				'property' => 'city',
				'type' => 'text',
				'label' => 'City',
				'description' => 'The city of the person making the donation',
				'readOnly' => true,
			],
			'state' => [
				'property' => 'state',
				'type' => 'text',
				'label' => 'State',
				'description' => 'The state of the person making the donation',
				'readOnly' => true,
			],
			'zip' => [
				'property' => 'zip',
				'type' => 'text',
				'label' => 'Zip',
				'description' => 'The zipcode of the person making the donation',
				'readOnly' => true,
			],
			'anonymous' => [
				'property' => 'anonymous',
				'type' => 'checkbox',
				'label' => 'Anonymous?',
				'description' => 'Whether or not the donor wants to remain anonymous',
				'readOnly' => true,
			],
			'donateToLocation' => [
				'property' => 'donateToLocation',
				'type' => 'text',
				'label' => 'Donate To Location',
				'description' => 'The location where the user wants to send the donation',
				'readOnly' => true,
			],
			'donationValue' => [
				'property' => 'donationValue',
				'type' => 'text',
				'label' => 'Donation Amount',
				'description' => 'The amount donated',
				'readOnly' => true,
			],
			'donationComplete' => [
				'property' => 'donationComplete',
				'type' => 'text',
				'label' => 'Donation Completed',
				'description' => 'Whether or not payment for the donation has been completed',
				'readOnly' => true,
			],
			'comments' => [
				'property' => 'comments',
				'type' => 'text',
				'label' => 'Earmark',
				'description' => 'An earmark the user would like the donation applied to',
				'readOnly' => true,
			],
			'shouldBeDedicated' => [
				'property' => 'shouldBeDedicated',
				'type' => 'checkbox',
				'label' => 'Dedicated?',
				'description' => 'Whether or not the donor wants to dedicate their donation in honor or memory of someone',
				'readOnly' => true,
			],
			'dedicateType' => [
				'property' => 'dedicateType',
				'type' => 'text',
				'label' => 'Dedication Type',
				'description' => 'The location where the user wants to send the donation',
				'readOnly' => true,
				'hideInLists' => true,
			],
			'honoreeFirstName' => [
				'property' => 'honoreeFirstName',
				'type' => 'text',
				'label' => 'Honoree First Name',
				'description' => 'The first name of the person being honored',
				'readOnly' => true,
				'hideInLists' => true,
			],
			'honoreeLastName' => [
				'property' => 'honoreeLastName',
				'type' => 'text',
				'label' => 'Honoree Last Name',
				'description' => 'The last name of the person being honored',
				'readOnly' => true,
				'hideInLists' => true,
			],
			'shouldBeNotified' => [
				'property' => 'shouldBeNotified',
				'type' => 'checkbox',
				'label' => 'Notify someone of gift?',
				'description' => 'Whether or not the donor wants someone to be notified about this gift',
				'readOnly' => true,
			],
			'notificationFirstName' => [
				'property' => 'notificationFirstName',
				'type' => 'text',
				'label' => 'Notification Party First Name',
				'description' => 'The first name of the person to notify about the gift',
				'readOnly' => true,
				'hideInLists' => true,
			],
			'notificationLastName' => [
				'property' => 'notificationLastName',
				'type' => 'text',
				'label' => 'Notification Party Last Name',
				'description' => 'The last name of the person to notify about the gift',
				'readOnly' => true,
				'hideInLists' => true,
			],
			'notificationAddress' => [
				'property' => 'notificationAddress',
				'type' => 'text',
				'label' => 'Notification Party Address',
				'description' => 'The address of the person to notify about the gift',
				'readOnly' => true,
				'hideInLists' => true,
			],
			'notificationCity' => [
				'property' => 'notificationCity',
				'type' => 'text',
				'label' => 'Notification Party Address - City',
				'description' => 'The city of the person to notify about the gift',
				'readOnly' => true,
				'hideInLists' => true,
			],
			'notificationState' => [
				'property' => 'notificationState',
				'type' => 'text',
				'label' => 'Notification Party Address - State',
				'description' => 'The state of the person to notify about the gift',
				'readOnly' => true,
				'hideInLists' => true,
			],
			'notificationZip' => [
				'property' => 'notificationZip',
				'type' => 'text',
				'label' => 'Notification Party Address - Zip',
				'description' => 'The zipcode of the person to notify about the gift',
				'readOnly' => true,
				'hideInLists' => true,
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}

	function __get($name) {
		if ($name == 'donationValue') {
			global $activeLanguage;
			$currencyCode = 'USD';
			$variables = new SystemVariables();
			if ($variables->find(true)) {
				$currencyCode = $variables->currencyCode;
			}

			$currencyFormatter = new NumberFormatter($activeLanguage->locale . '@currency=' . $currencyCode, NumberFormatter::CURRENCY);

			require_once ROOT_DIR . '/sys/Account/UserPayment.php';
			$payment = new UserPayment();
			$payment->id = $this->paymentId;
			if ($payment->find(true)) {
				return $currencyFormatter->formatCurrency(empty($payment->totalPaid) ? 0 : (int)$payment->totalPaid, $currencyCode);
			} else {
				return $currencyFormatter->formatCurrency(0, $currencyCode);
			}
		} elseif ($name == 'donationComplete') {
			require_once ROOT_DIR . '/sys/Account/UserPayment.php';
			$payment = new UserPayment();
			$payment->id = $this->paymentId;
			if ($payment->find(true)) {
				return $payment->completed ? 'true' : 'false';
			} else {
				return 'false';
			}
		} elseif ($name == 'dateCompleted') {
			require_once ROOT_DIR . '/sys/Account/UserPayment.php';
			$payment = new UserPayment();
			$payment->id = $this->paymentId;
			if ($payment->find(true)) {
				return $payment->transactionDate;
			} else {
				return 'Unknown';
			}
		} else {
			return parent::__get($name);
		}
	}

	function getDonationValue($paymentId) {
		require_once ROOT_DIR . '/sys/Account/UserPayment.php';
		$payment = new UserPayment();
		$payment->id = $paymentId;
		if ($payment->find(true)) {
			return $payment->totalPaid;
		}else{
			return 0;
		}
	}

	function getCurrencySymbol() {
		$currencyCode = 'USD';
		$systemVariables = SystemVariables::getSystemVariables();
		if (!empty($systemVariables->currencyCode)) {
			$currencyCode = $systemVariables->currencyCode;
		}
		if ($currencyCode == 'USD') {
			$currencySymbol = '$';
		} elseif ($currencyCode == 'EUR') {
			$currencySymbol = '€';
		} elseif ($currencyCode == 'CAD') {
			$currencySymbol = '$';
		} elseif ($currencyCode == 'GBP') {
			$currencySymbol = '£';
		}
		return $currencySymbol;
	}

	function getDonationFormFields(DonationsSetting $donationSettings) {
		require_once ROOT_DIR . '/sys/Donations/DonationFormFields.php';
		$fieldsToSortByCategory = $donationSettings->getDefaultFormFields();

		$donationFormFields = [];
		if ($fieldsToSortByCategory) {
			foreach ($fieldsToSortByCategory as $formField) {
				if (!array_key_exists($formField->category, $donationFormFields)) {
					$donationFormFields[$formField->category] = [];
				}
				$donationFormFields[$formField->category][] = $formField;
			}
		}
		return $donationFormFields;
	}

	static function getDonationValues($donationSettingId) {
		require_once ROOT_DIR . '/sys/Donations/DonationValue.php';
		$values = new DonationValue();
		$values->donationSettingId = $donationSettingId;

		if ($values->count() == 0) {
			// Load up the default values if library has defined none.
			/** @var DonationValue $defaultValues */
			$defaultValues = DonationValue::getDefaults($donationSettingId);
			$availableValues = [];

			global $configArray;
			foreach ($defaultValues as $index => $donationValue) {
				$value = $donationValue->value;
				if (!isset($configArray['donationValues'][$value]) || $configArray['donationValues'][$value] != false) {
					$availableValues[$value] = $donationValue->value;
				}
			}

		} else {
			$values->orderBy('weight');
			$availableValues = $values->fetchAll('value', 'value');
		}

		return $availableValues;
	}

	static function getLocations() {
		global $configArray;
		$availableLocations = [];
		$locations = [];
		require_once ROOT_DIR . '/sys/LibraryLocation/Location.php';
		$location = new Location();
		$location->showOnDonationsPage = 1;
		$location->find();
		while ($location->fetch()) {
			$locations[] = clone($location);
		}

		foreach ($locations as $index => $donationLocation) {
			$id = $donationLocation->locationId;
			if (!isset($configArray['donationLocations'][$id]) || $configArray['donationLocations'][$id] != false) {
				$availableLocations[$donationLocation->displayName] = $donationLocation->displayName;
			}
		}

		return $availableLocations;
	}

	static function getEarmarks($donationSettingId) {
		require_once ROOT_DIR . '/sys/Donations/DonationEarmark.php';
		$earmarks = new DonationEarmark();
		$earmarks->donationSettingId = $donationSettingId;

		if ($earmarks->count() == 0) {
			// Load up the default values if library has defined none.
			/** @var DonationEarmark $defaultValues */
			$defaultEarmarks = DonationEarmark::getDefaults($donationSettingId);
			$availableEarmarks = [];

			global $configArray;
			foreach ($defaultEarmarks as $index => $donationEarmarkId) {
				$id = $donationEarmarkId->id;
				if (!isset($configArray['donationEarmarks'][$id]) || $configArray['donationEarmarks'][$id] != false) {
					$availableEarmarks[$donationEarmarkId->label] = $id;
				}
			}

		} else {
			$availableEarmarks = $earmarks->fetchAll('label', 'id');

		}

		return $availableEarmarks;
	}

	static function getDedications($donationSettingId) {
		require_once ROOT_DIR . '/sys/Donations/DonationDedicationType.php';
		$dedicationTypes = new DonationDedicationType();
		$dedicationTypes->donationSettingId = $donationSettingId;

		if ($dedicationTypes->count() == 0) {
			// Load up the default values if library has defined none.
			/** @var DonationDedicationType $defaultValues */
			$defaultDedicationTypes = DonationDedicationType::getDefaults($donationSettingId);
			$availableDedicationTypes = [];

			global $configArray;
			foreach ($defaultDedicationTypes as $index => $donationDedicationTypeId) {
				$id = $donationDedicationTypeId->id;
				if (!isset($configArray['donationDedicationTypes'][$id]) || $configArray['donationDedicationTypes'][$id] != false) {
					$availableDedicationTypes[$donationDedicationTypeId->label] = $id;
				}
			}

		} else {
			$availableDedicationTypes = $dedicationTypes->fetchAll('label', 'id');
		}

		return $availableDedicationTypes;
	}

	public function sendReceiptEmail() {
		if ($this->sendEmailToUser == 1 && $this->email) {
			require_once ROOT_DIR . '/sys/Email/Mailer.php';
			$mail = new Mailer();

			$replyToAddress = '';

			$body = '*****This is an auto-generated email response. Please do not reply.*****';

			require_once ROOT_DIR . '/sys/ECommerce/DonationsSetting.php';
			$donationSettings = new DonationsSetting();
			$donationSettings->id = $this->donationSettingId;
			if ($donationSettings->find(true)) {
				$emailTemplate = $donationSettings->donationEmailTemplate;
				$body .= "\r\n\r\n" . $emailTemplate;
			}

			$error = $mail->send($this->email, translate([
				'text' => 'Your Donation Receipt',
				'isPublicFacing' => true,
			]), $body, $replyToAddress);
			if (($error instanceof AspenError)) {
				global $interface;
				$interface->assign('error', $error->getMessage());
			} else {
				$this->sendEmailToUser = 0;
				$this->update();
				return true;
			}
		}
		return false;
	}

	function getPaymentProcessor(): array {
		global $library;
		$clientId = null;
		$showPayLater = null;
		$stripeSecretKey = null;
		$stripePublicKey = null;
		$squareCdnUrl = null;
		$squareApplicationId = null;
		$squareAccessToken = null;
		$squareLocationId = null;
		$deluxeAPIConnectionUrl = null;
		$deluxeRemittanceId = null;
		$deluxeApplicationId = null;
		$apiAuthKey = null;
		$billerId = null;
		$sdkAuthKey = null;
		$sdkClientId = null;
		$sdkClientSecret = null;
		$billerAccountId = null;
		$settleCode = null;
		$merchantCode = null;
		$paymentSite = null;
		$useLineItems = null;
		$baseUrl = null;
		$sdkUrl = null;
		$aspenUrl = null;

		if (UserAccount::isLoggedIn()) {
			$user = UserAccount::getActiveUserObj();
			$homeLibrary = Library::getLibraryForLocation($user->homeLocationId);
			$userId = $user->id;
			if ($homeLibrary == null) {
				$homeLibrary = $library;
				$paymentType = $library->finePaymentType;
			} else {
				$paymentType = $homeLibrary->finePaymentType;
			}

			//PayPal
			if ($paymentType == 2) {
				require_once ROOT_DIR . '/sys/ECommerce/PayPalSetting.php';
				$payPalSetting = new PayPalSetting();
				$payPalSetting->id = $homeLibrary->payPalSettingId;
				if ($payPalSetting->find(true)) {
					$clientId = $payPalSetting->clientId;
					$showPayLater = $payPalSetting->showPayLater;
				}
			}
			// FIS WorldPay data
			if ($paymentType == 7) {
				global $configArray;
				$aspenUrl = $configArray['Site']['url'];

				global $library;
				require_once ROOT_DIR . '/sys/ECommerce/WorldPaySetting.php';
				$worldPaySettings = new WorldPaySetting();
				$worldPaySettings->id = $library->worldPaySettingId;

				$merchantCode = 0;
				$settleCode = 0;
				$paymentSite = "";
				$useLineItems = 0;

				if ($worldPaySettings->find(true)) {
					$merchantCode = $worldPaySettings->merchantCode;
					$settleCode = $worldPaySettings->settleCode;
					$paymentSite = $worldPaySettings->paymentSite;
					$useLineItems = $worldPaySettings->useLineItems;
				}

			}
			// ACI Speedpay data
			if ($paymentType == 8) {
				global $library;
				require_once ROOT_DIR . '/sys/ECommerce/ACISpeedpaySetting.php';
				$aciSpeedpaySettings = new ACISpeedpaySetting();
				$aciSpeedpaySettings->id = $library->aciSpeedpaySettingId;

				if ($aciSpeedpaySettings->find(true)) {
					$baseUrl = 'https://api.acispeedpay.com';
					$sdkUrl = 'cds.officialpayments.com';
					$billerAccountId = $user->ils_barcode;

					if ($aciSpeedpaySettings->sandboxMode == 1) {
						$baseUrl = 'https://sandbox-api.acispeedpay.com';
						$sdkUrl = 'sandbox-cds.officialpayments.com';
					}

					$apiAuthKey = $aciSpeedpaySettings->apiAuthKey;
					$billerId = $aciSpeedpaySettings->billerId;
					$sdkAuthKey = $aciSpeedpaySettings->sdkApiAuthKey;
					$sdkClientId = $aciSpeedpaySettings->sdkClientId;
					$sdkClientSecret = $aciSpeedpaySettings->sdkClientSecret;
				}
			}
			// Certified Payments by Deluxe
			if ($paymentType == 10) {
				global $library;
				require_once ROOT_DIR . '/sys/ECommerce/CertifiedPaymentsByDeluxeSetting.php';
				$deluxeSettings = new CertifiedPaymentsByDeluxeSetting();
				$deluxeSettings->id = $library->deluxeCertifiedPaymentsSettingId;
				if ($deluxeSettings->find(true)) {
					// connection URL to payment portal
					$url = 'https://www.velocitypayment.com/vrelay/verify.do';
					if ($deluxeSettings->sandboxMode == 1 || $deluxeSettings->sandboxMode == "1") {
						$url = 'https://demo.velocitypayment.com/vrelay/verify.do';
					}
					$deluxeAPIConnectionUrl = $url;

					// generate remittance id
					$uid = random_bytes(12);
					$deluxeRemittanceId = bin2hex($uid);

					// application id from deluxe
					$deluxeApplicationId = $deluxeSettings->applicationId;
				}
			}
			// Square
			if ($paymentType == 12) {
				require_once ROOT_DIR . '/sys/ECommerce/SquareSetting.php';
				$squareSetting = new SquareSetting();
				$squareSetting->id = $library->squareSettingId;
				if ($squareSetting->find(true)) {
					$cdnUrl = 'https://web.squarecdn.com/v1/square.js';
					if ($squareSetting->sandboxMode == 1 || $squareSetting->sandboxMode == '1') {
						$cdnUrl = 'https://sandbox.web.squarecdn.com/v1/square.js';
					}
					$squareCdnUrl = $cdnUrl;
					$squareApplicationId = $squareSetting->applicationId;
					$squareAccessToken = $squareSetting->accessToken;
					$squareLocationId = $squareSetting->locationId;
				}
			}
			// Stripe
			if ($paymentType == 13) {
				require_once ROOT_DIR . '/sys/ECommerce/StripeSetting.php';
				$stripeSetting = new StripeSetting();
				$stripeSetting->id = $library->stripeSettingId;
				if ($stripeSetting->find(true)) {
					$stripePublicKey = $stripeSetting->stripePublicKey;
					$stripeSecretKey = $stripeSetting->stripeSecretKey;
				}
			}
		} else {
			$userId = "Guest";
			$paymentType = isset($library) ? $library->finePaymentType : 0;
			if ($paymentType == 2) {
				require_once ROOT_DIR . '/sys/ECommerce/PayPalSetting.php';
				$payPalSetting = new PayPalSetting();
				$payPalSetting->id = $library->payPalSettingId;
				if ($payPalSetting->find(true)) {
					$clientId = $payPalSetting->clientId;
				}
			}
			// Square
			if ($paymentType == 12) {
				require_once ROOT_DIR . '/sys/ECommerce/SquareSetting.php';
				$squareSetting = new SquareSetting();
				$squareSetting->id = $library->squareSettingId;
				if ($squareSetting->find(true)) {
					$cdnUrl = 'https://web.squarecdn.com/v1/square.js';
					if ($squareSetting->sandboxMode == 1 || $squareSetting->sandboxMode == '1') {
						$cdnUrl = 'https://sandbox.web.squarecdn.com/v1/square.js';
					}
					$squareCdnUrl = $cdnUrl;
					$squareApplicationId = $squareSetting->applicationId;
					$squareAccessToken = $squareSetting->accessToken;
					$squareLocationId = $squareSetting->locationId;
				}
			}
			//Stripe
			if ($paymentType == 13) {
				require_once ROOT_DIR . '/sys/ECommerce/StripeSetting.php';
				$stripeSetting = new StripeSetting();
				$stripeSetting->id = $library->stripeSettingId;
				if ($stripeSetting->find(true)) {
					$stripePublicKey = $stripeSetting->stripePublicKey;
					$stripeSecretKey = $stripeSetting->stripeSecretKey;
				}
			}
		}
		$currencyCode = "USD";
		$systemVariables = SystemVariables::getSystemVariables();
		if (!empty($systemVariables->currencyCode)) {
			$currencyCode = $systemVariables->currencyCode;
		}
		return [
			'paymentType' => $paymentType,
			'currencyCode' => $currencyCode,
			'userId' => $userId,
			'aspenUrl' => $aspenUrl,
			'clientId' => $clientId,
			'showPayLater' => $showPayLater,
			'stripePublicKey' => $stripePublicKey,
			'stripeSecretKey' => $stripeSecretKey,
			'squareCdnUrl' => $squareCdnUrl,
			'squareApplicationId' => $squareApplicationId,
			'squareAccessToken' => $squareAccessToken,
			'squareLocationId' => $squareLocationId,
			'deluxeAPIConnectionUrl' => $deluxeAPIConnectionUrl,
			'deluxeRemittanceId' => $deluxeRemittanceId,
			'deluxeApplicationId' => $deluxeApplicationId,
			'billerId' => $billerId,
			'aciHost' => $baseUrl,
			'sdkUrl' => $sdkUrl,
			'sdkAuthKey' => $sdkAuthKey,
			'sdkClientId' => $sdkClientId,
			'sdkClientSecret' => $sdkClientSecret,
			'billerAccountId' => $billerAccountId,
			'settleCode' => $settleCode,
			'merchantCode' => $merchantCode,
			'paymentSite' => $paymentSite,
			'useLineItems' => $useLineItems,
		];
	}

}