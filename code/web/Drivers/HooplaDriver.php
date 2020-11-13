<?php

require_once ROOT_DIR . '/Drivers/AbstractEContentDriver.php';
class HooplaDriver extends AbstractEContentDriver{
	const memCacheKey = 'hoopla_api_access_token';
	/** @var HooplaSetting|null */
	private $hooplaSettings = null;
	public $hooplaAPIBaseURL = 'hoopla-api-dev.hoopladigital.com';
	private $accessToken;
	private $hooplaEnabled = false;

	public function __construct()
	{
		require_once ROOT_DIR . '/sys/Hoopla/HooplaSetting.php';
		try{
			$hooplaSettings = new HooplaSetting();
			if ($hooplaSettings->find(true)){
				$this->hooplaEnabled = true;

				$this->hooplaAPIBaseURL = $hooplaSettings->apiUrl;
				$this->hooplaSettings = $hooplaSettings;
				$this->getAccessToken();
			}
		}catch (Exception $e){
			global $logger;
			$logger->log("Could not load Hoopla settings", Logger::LOG_ALERT);
		}
	}

	/**
	 * Clean an assumed Hoopla RecordID to Hoopla ID number
	 * @param $hooplaRecordId
	 * @return string
	 */
	public static function recordIDtoHooplaID($hooplaRecordId)
	{
		if (strpos($hooplaRecordId, ':') !== false) {
			list(,$hooplaRecordId) = explode(':', $hooplaRecordId, 2);
		}
		return preg_replace('/^MWT/', '', $hooplaRecordId);
	}


	// $customRequest is for curl, can be 'PUT', 'DELETE', 'POST'
	private function getAPIResponse($url, $params = null, $customRequest = null, $additionalHeaders = null)
	{
		global $logger;
		$logger->log('Hoopla API URL :' .$url, Logger::LOG_NOTICE);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		$headers  = array(
			'Accept: application/json',
			'Content-Type: application/json',
			'Authorization: Bearer ' . $this->accessToken,
			'Originating-App-Id: Aspen Discovery',
		);
		if (!empty($additionalHeaders) && is_array($additionalHeaders)) {
			$headers = array_merge($headers, $additionalHeaders);
		}
		if (empty($customRequest)) {
			curl_setopt($ch, CURLOPT_HTTPGET, true);
		} elseif ($customRequest == 'POST') {
			curl_setopt($ch, CURLOPT_POST, true);
		}
		else {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $customRequest);
		}

		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		global $instanceName;
		if (stripos($instanceName, 'localhost') !== false) {
			// For local debugging only
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);

		}
		if ($params !== null) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
		}
		$json = curl_exec($ch);

		if (!$json && curl_getinfo($ch, CURLINFO_HTTP_CODE) == 401) {
			$logger->log('401 Response in getAPIResponse. Attempting to renew access token', Logger::LOG_WARNING);
			$this->renewAccessToken();
			return false;
		}

		$logger->log("Hoopla API response\r\n$json", Logger::LOG_DEBUG);
		curl_close($ch);

		if ($json !== false && $json !== 'false') {
			return json_decode($json);
		} else {
			$logger->log('Curl problem in getAPIResponse', Logger::LOG_WARNING);
			return false;
		}
	}

	/**
	 * Simplified CURL call for returning a title. Success is determined by receiving a http status code of 204
	 * @param $url
	 * @return bool
	 */
	private function getAPIResponseReturnHooplaTitle($url)
	{
		$ch = curl_init();
		$headers  = array(
			'Authorization: Bearer ' . $this->accessToken,
			'Originating-App-Id: Aspen Discovery',
		);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		global $instanceName;
		if (stripos($instanceName, 'localhost') !== false) {
			// For local debugging only
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		}

		curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);
		return $http_code == 204;
	}


	private static $hooplaLibraryIdsForUser;

	/**
	 * @param User $user
     *
     * @return false|int
	 */
	public function getHooplaLibraryID($user) {
		if ($this->hooplaEnabled) {
			if (isset(self::$hooplaLibraryIdsForUser[$user->id])) {
				return self::$hooplaLibraryIdsForUser[$user->id]['libraryId'];
			} else {
				$library                                               = $user->getHomeLibrary();
				$hooplaID                                              = $library->hooplaLibraryID;
				self::$hooplaLibraryIdsForUser[$user->id]['libraryId'] = $hooplaID;
				return $hooplaID;
			}
		}
		return false;
	}

	/**
	 * @param User $user
     *
     * @return null|string
	 */
	private function getHooplaBasePatronURL($user) {
		$url = null;
		if ($this->hooplaEnabled) {
			$hooplaLibraryID = $this->getHooplaLibraryID($user);
			$barcode         = $user->getBarcode();
			if (!empty($hooplaLibraryID) && !empty($barcode)) {
				$url = $this->hooplaAPIBaseURL . '/api/v1/libraries/' . $hooplaLibraryID . '/patrons/' . $barcode;
			}
		}
		return $url;
    }

	private $hooplaPatronStatuses = array();
	/**
	 * @param $user User
     *
     * @return false|stdClass
	 */
	public function getAccountSummary($user) {
		if ($this->hooplaEnabled) {
			if (isset($this->hooplaPatronStatuses[$user->id])) {
				return $this->hooplaPatronStatuses[$user->id];
			} else {
				$getPatronStatusURL = $this->getHooplaBasePatronURL($user);
				if (!empty($getPatronStatusURL)) {
					$getPatronStatusURL         .= '/status';
					$hooplaPatronStatusResponse = $this->getAPIResponse($getPatronStatusURL);
					if (!empty($hooplaPatronStatusResponse) && !isset($hooplaPatronStatusResponse->message)) {
						$this->hooplaPatronStatuses[$user->id] = $hooplaPatronStatusResponse;
						return $hooplaPatronStatusResponse;
					} else {
						global $logger;
						$hooplaErrorMessage = empty($hooplaPatronStatusResponse->message) ? '' : ' Hoopla Message :' . $hooplaPatronStatusResponse->message;
						$logger->log('Error retrieving patron status from Hoopla. User ID : ' . $user->id . $hooplaErrorMessage, Logger::LOG_NOTICE);
						$this->hooplaPatronStatuses[$user->id] = false; // Don't do status call again for this user
					}
				}
			}
		}
		return false;
	}

	/**
	 * @param $user User
	 * @return array
	 */
	public function getCheckouts($user)
	{
		$checkedOutItems = array();
		if ($this->hooplaEnabled) {
			$hooplaCheckedOutTitlesURL = $this->getHooplaBasePatronURL($user);
			if (!empty($hooplaCheckedOutTitlesURL)) {
				$hooplaCheckedOutTitlesURL  .= '/checkouts/current';
				$checkOutsResponse = $this->getAPIResponse($hooplaCheckedOutTitlesURL);
				if (is_array($checkOutsResponse)) {
                    $hooplaPatronStatus = null;
					if (count($checkOutsResponse)) { // Only get Patron status if there are checkouts
						$hooplaPatronStatus = $this->getAccountSummary($user);
					}
					foreach ($checkOutsResponse as $checkOut) {
						$hooplaRecordID  = $checkOut->contentId;
						$simpleSortTitle = preg_replace('/^The\s|^A\s/i', '', $checkOut->title); // remove beginning The or A

						$currentTitle = array(
							'checkoutSource' => 'Hoopla',
							'user'           => $user->getNameAndLibraryLabel(),
							'userId'         => $user->id,
							'hooplaId'       => $checkOut->contentId,
							'title'          => $checkOut->title,
							'title_sort'     => empty($simpleSortTitle) ? $checkOut->title : $simpleSortTitle,
							'author'         => isset($checkOut->author) ? $checkOut->author : null,
							'format'         => $checkOut->kind,
							'checkoutDate'   => $checkOut->borrowed,
							'dueDate'        => $checkOut->due,
							'hooplaUrl'      => $checkOut->url
						);

						if ($hooplaPatronStatus != null && isset($hooplaPatronStatus->borrowsRemaining)) {
							$currentTitle['borrowsRemaining'] = $hooplaPatronStatus->borrowsRemaining;
						}

						require_once ROOT_DIR . '/RecordDrivers/HooplaRecordDriver.php';
						$hooplaRecordDriver = new HooplaRecordDriver($hooplaRecordID);
						if ($hooplaRecordDriver->isValid()) {
							// Get Record For other details
							$currentTitle['coverUrl']      = $hooplaRecordDriver->getBookcoverUrl('medium', true);
							$currentTitle['linkUrl']       = $hooplaRecordDriver->getLinkUrl();
							$currentTitle['groupedWorkId'] = $hooplaRecordDriver->getGroupedWorkId();
							$currentTitle['ratingData']    = $hooplaRecordDriver->getRatingData();
							$currentTitle['title_sort']    = $hooplaRecordDriver->getTitle();
							$currentTitle['author']        = $hooplaRecordDriver->getPrimaryAuthor();
							$currentTitle['format']        = $hooplaRecordDriver->getPrimaryFormat();
						}
						$key = $currentTitle['checkoutSource'] . $currentTitle['hooplaId']; // This matches the key naming scheme in the Overdrive Driver
						$checkedOutItems[$key] = $currentTitle;
					}
				} else {
					global $logger;
					$logger->log('Error retrieving checkouts from Hoopla.', Logger::LOG_ERROR);
				}
			}
		}
		return $checkedOutItems;
	}

	/**
	 * @return string
	 */
	private function getAccessToken()
	{
		if (empty($this->accessToken)) {
			/** @var Memcache $memCache */
			global $memCache;
			$accessToken = $memCache->get(self::memCacheKey);
			if (empty($accessToken)) {
				$this->renewAccessToken();
			} else {
				$this->accessToken = $accessToken;
			}

		}
		return $this->accessToken;
	}

	private function renewAccessToken (){
		if ($this->hooplaEnabled) {
			$url = 'https://' . str_replace(array('http://', 'https://'),'', $this->hooplaAPIBaseURL) . '/v2/token';
			// Ensure https is used

			$username = $this->hooplaSettings->apiUsername;
			$password = $this->hooplaSettings->apiPassword;

			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
			curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, array());

			global $instanceName;
			if (stripos($instanceName, 'localhost') !== false) {
				// For local debugging only
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLINFO_HEADER_OUT, true);
			}
			$response = curl_exec($curl);

			curl_close($curl);

			if ($response) {
				$json = json_decode($response);
				if (!empty($json->access_token)) {
					$this->accessToken = $json->access_token;

					/** @var Memcache $memCache */
					global $memCache;
					global $configArray;
					$memCache->set(self::memCacheKey, $this->accessToken, $configArray['Caching']['hoopla_api_access_token']);

					return true;

				} else {
					global $logger;
					$logger->log('Hoopla API retrieve access token call did not contain an access token', Logger::LOG_ERROR);
				}
			} else {
				global $logger;
				$logger->log('Curl Error in Hoopla API call to retrieve access token', Logger::LOG_ERROR);
			}
		} else {
			global $logger;
			$logger->log('Hoopla API user and/or password not set. Can not retrieve access token', Logger::LOG_ERROR);
		}
		return false;
	}

	/**
     * @param User $user
     * @param string $titleId
	 *
     * @return array
	 */
	public function checkOutTitle($user, $titleId) {
		if ($this->hooplaEnabled) {
			$checkoutURL = $this->getHooplaBasePatronURL($user);
			if (!empty($checkoutURL)) {

				$titleId = self::recordIDtoHooplaID($titleId);
				$checkoutURL      .= '/' . $titleId;
				$checkoutResponse = $this->getAPIResponse($checkoutURL, array(), 'POST');
				if ($checkoutResponse) {
					if (!empty($checkoutResponse->contentId)) {
						$this->trackUserUsageOfHoopla($user);
						$this->trackRecordCheckout($titleId);
						return array(
							'success'   => true,
							'message'   => $checkoutResponse->message,
							'title'     => $checkoutResponse->title,
							'HooplaURL' => $checkoutResponse->url,
							'due'       => $checkoutResponse->due
						);
					} else {
						return array(
							'success' => false,
							'message' => isset($checkoutResponse->message) ? $checkoutResponse->message : 'An error occurred checking out the Hoopla title.'
						);
					}

				} else {
					return array(
						'success' => false,
						'message' => 'An error occurred checking out the Hoopla title.'
					);
				}
			} elseif (!$this->getHooplaLibraryID($user)) {
				return array(
					'success' => false,
					'message' => 'Your library does not have Hoopla integration enabled.'
				);
			} else {
				return array(
					'success' => false,
					'message' => 'There was an error retrieving your library card number.'
				);
			}
		} else {
			return array(
				'success' => false,
				'message' => 'Hoopla integration is not enabled.'
			);
		}
	}

    /**
     * @param string $hooplaId
     * @param User $user
     *
     * @return array
     */
	public function returnCheckout($user, $hooplaId) {
		if ($this->hooplaEnabled) {
            $returnCheckoutURL = $this->getHooplaBasePatronURL($user);
			if (!empty($returnCheckoutURL)) {
				$itemId = self::recordIDtoHooplaID($hooplaId);
                $returnCheckoutURL .= "/$itemId";
				$result = $this->getAPIResponseReturnHooplaTitle($returnCheckoutURL);
				if ($result) {
					return array(
						'success' => true,
						'message' => 'The title was successfully returned.'
					);
				} else {
					return array(
						'success' => false,
						'message' => 'There was an error returning this title.'
					);
				}

			} elseif (!$this->getHooplaLibraryID($user)) {
				return array(
					'success' => false,
					'message' => 'Your library does not have Hoopla integration enabled.'
				);
			} else {
				return array(
					'success' => false,
					'message' => 'There was an error retrieving your library card number.'
				);
			}
		} else {
			return array(
				'success' => false,
				'message' => 'Hoopla integration is not enabled.'
			);
		}
	}

    public function hasNativeReadingHistory()
    {
        return false;
    }

    /**
     * @return boolean true if the driver can renew all titles in a single pass
     */
    public function hasFastRenewAll()
    {
        return false;
    }

    /**
     * Renew all titles currently checked out to the user
     *
     * @param $patron  User
     * @return mixed
     */
    public function renewAll($patron)
    {
        return false;
    }

    /**
     * Renew a single title currently checked out to the user
     *
     * @param $patron     User
     * @param $recordId   string
     * @param $itemId     string
     * @param $itemIndex  string
     * @return mixed
     */
    public function renewCheckout($patron, $recordId, $itemId = null, $itemIndex = null)
    {
        return false;
    }

    /**
     * Get Patron Holds
     *
     * This is responsible for retrieving all holds for a specific patron.
     *
     * @param User $user The user to load transactions for
     *
     * @return array        Array of the patron's holds
     * @access public
     */
    public function getHolds($user)
    {
        return [];
    }

    /**
     * Place Hold
     *
     * This is responsible for both placing holds as well as placing recalls.
     *
     * @param User $patron The User to place a hold for
     * @param string $recordId The id of the bib record
     * @return  array                 An array with the following keys
     *                                result - true/false
     *                                message - the message to display (if item holds are required, this is a form to select the item).
     *                                needsItemLevelHold - An indicator that item level holds are required
     *                                title - the title of the record the user is placing a hold on
     * @access  public
     */
	function placeHold($patron, $recordId, $pickupBranch = null, $cancelDate = null)
    {
        return [
            'result' => false,
            'message' => 'Holds are not implemented for Hoopla'
        ];
    }

    /**
     * Cancels a hold for a patron
     *
     * @param User $patron The User to cancel the hold for
     * @param string $recordId The id of the bib record
     * @return false|array
     */
	function cancelHold($patron, $recordId, $cancelId = null)
    {
        return false;
    }

	/**
	 * @param $user
	 */
	public function trackUserUsageOfHoopla($user): void
	{
		require_once ROOT_DIR . '/sys/Hoopla/UserHooplaUsage.php';
		$userUsage = new UserHooplaUsage();
		$userUsage->userId = $user->id;
		$userUsage->year = date('Y');
		$userUsage->month = date('n');

		if ($userUsage->find(true)) {
			$userUsage->usageCount++;
			$userUsage->update();
		} else {
			$userUsage->usageCount = 1;
			$userUsage->insert();
		}
	}

	/**
	 * @param int $hooplaId
	 */
	public function trackRecordCheckout($hooplaId): void
	{
		require_once ROOT_DIR . '/sys/Hoopla/HooplaRecordUsage.php';
		$recordUsage = new HooplaRecordUsage();
		require_once ROOT_DIR . '/sys/Hoopla/HooplaExtract.php';
		$product = new HooplaExtract();
		$product->hooplaId = $hooplaId;
		if ($product->find(true)) {
			$recordUsage->hooplaId = $product->id;
			$recordUsage->year = date('Y');
			$recordUsage->month = date('n');
			if ($recordUsage->find(true)) {
				$recordUsage->timesCheckedOut++;
				$recordUsage->update();
			} else {
				$recordUsage->timesCheckedOut = 1;
				$recordUsage->insert();
			}
		}
	}
}
