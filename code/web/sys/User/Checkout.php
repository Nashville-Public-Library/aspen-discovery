<?php /** @noinspection PhpMissingFieldTypeInspection */
require_once ROOT_DIR . '/sys/User/CircEntry.php';

class Checkout extends CircEntry {
	public $__table = 'user_checkout';
	public $shortId;
	public $itemId;
	public $itemIndex;
	public $renewalId;
	public $barcode;
	public $title2;
	public $callNumber;
	public $volume;
	public $checkoutDate;
	public $dueDate;
	public $renewCount;
	public $renewIndicator;
	public $renewalDate;
	public $canRenew;
	public $renewError;
	public $autoRenew;
	public $autoRenewError;
	public $maxRenewals;
	public $fine;
	public $returnClaim;
	public $holdQueueLength;
	public $isIll;
	public $outOfHoldGroupMessage;
	public $ilsStatus;
	public $showFineButton;

	//For OverDrive
	public $collectionName;
	public $allowDownload;
	public $overdriveRead;
	public $overdriveReadUrl;
	public $overdriveListen;
	public $overdriveListenUrl;
	public $overdriveVideo;
	public $overdriveVideoUrl;
	public $overdriveMagazine;
	public $formatSelected;
	public $selectedFormatName;
	public $selectedFormatValue;
	public $canReturnEarly;
	public $isSupplemental;
	public $supplementalMaterials; //This gets serialized when saved and loaded
	public $formats; //This gets serialized when saved and loaded

	public $downloadUrl;

	//For Axis360
	public $accessOnlineUrl;
	public $transactionId;

	//For Palace Project
	public $earlyReturnUrl;

	//Calculate in realtime
	public $_overdue = null;
	public $_daysUntilDue = null;

	public function getNumericColumnNames(): array {
		return [
			'userId',
			'canRenew',
			'checkoutDate',
			'dueDate',
			'renewCount',
			'autoRenew',
			'maxRenewals',
			'fine',
			'holdQueueLength',
			'allowDownload',
			'overdriveRead',
			'overdriveListen',
			'overdriveVideo',
			'overdriveMagazine',
			'formatSelected',
			'canReturnEarly',
			'isSupplemental',
			'isIll',
			'showFineButton',
		];
	}

	public function getSerializedFieldNames(): array {
		return [
			'supplementalMaterials',
			'formats',
		];
	}

	public function getDaysUntilDue() {
		if ($this->_daysUntilDue == null) {
			if ($this->dueDate) {
				// use the same time of day to calculate days until due, in order to avoid errors with rounding
				$dueDate = strtotime('midnight', $this->dueDate);
				$today = strtotime('midnight');
				$daysUntilDue = ceil(($dueDate - $today) / (24 * 60 * 60));
				$overdue = $daysUntilDue < 0;
				$this->_overdue = $overdue;
				$this->_daysUntilDue = $daysUntilDue;
			} else {
				$this->_overdue = false;
				$this->_daysUntilDue = '';
			}
		}
		return $this->_daysUntilDue;
	}

	/** @noinspection PhpUnused */
	public function isOverdue() {
		if ($this->_overdue == null) {
			$this->getDaysUntilDue();
		}
		return $this->_overdue;
	}

	/** @noinspection PhpUnused */
	public function getFormattedRenewalDate() {
		if (!empty($this->renewalDate)) {
			return date('D M jS', $this->renewalDate);
		} else {
			return '';
		}
	}

	public function getArrayForAPIs() {
		$checkout = $this->toArray();
		if ($checkout['type'] == 'ils') {
			$checkout['checkoutSource'] = 'ILS';
		} elseif ($checkout['type'] == 'cloud_library') {
			$checkout['checkoutSource'] = 'CloudLibrary';
		} elseif ($checkout['type'] == 'axis360') {
			$checkout['checkoutSource'] = 'Axis360';
		} elseif ($checkout['type'] == 'palace_project') {
			$checkout['checkoutSource'] = 'Palace Project';
		} elseif ($checkout['type'] == 'hoopla') {
			$checkout['checkoutSource'] = 'Hoopla';
			$checkout['hooplaId'] = $checkout['sourceId'];
			$checkout['hooplaUrl'] = $checkout['accessOnlineUrl'];
			require_once ROOT_DIR . '/Drivers/HooplaDriver.php';
		} elseif ($checkout['type'] == 'overdrive') {
			global $configArray;
			$checkout['checkoutSource'] = 'OverDrive';
			$checkout['overDriveId'] = $checkout['sourceId'];
			$checkout['expiresOn'] = date(DateTime::ISO8601, $checkout['dueDate']);
			$checkout['overdriveRead'] = $checkout['overdriveRead'] == 1;
			$checkout['formatSelected'] = $checkout['formatSelected'] == 1;
			$checkout['allowDownload'] = $checkout['allowDownload'] == 1;
			$checkout['overdriveListen'] = $checkout['overdriveListen'] == 1;
			$checkout['earlyReturn'] = $checkout['canReturnEarly'] == 1;
			$checkout['format'] = $this->getPrimaryFormat();
			$checkout['recordUrl'] = $configArray['Site']['url'] . $this->getLinkUrl();
		}
		$checkout['id'] = $checkout['sourceId'];
		$checkout['ratingData'] = $this->getRatingData();
		$checkout['coverUrl'] = $this->getCoverUrl();
		$checkout['link'] = $this->getLinkUrl();
		$checkout['linkUrl'] = $this->getLinkUrl();
		$checkout['title_sort'] = $this->getSortTitle();
		$checkout['renewalDate'] = date('D M jS', $checkout['renewalDate']);
		$checkout['overdue'] = $this->isOverdue();
		$checkout['daysUntilDue'] = $this->getDaysUntilDue();
		$checkout['checkoutDate'] = (int)$checkout['checkoutDate'];
		$checkout['dueDate'] = (int)$checkout['dueDate'];
		$checkout['user'] = $this->getUserName();
		$checkout['fullId'] = $checkout['source'] . ':' . $checkout['recordId'];
		if (isset($checkout['canRenew'])) {
			/** @noinspection SpellCheckingInspection */
			$checkout['canrenew'] = $checkout['canRenew'] == 1;
			$checkout['canRenew'] = $checkout['canRenew'] == 1;
		}
		if (isset($checkout['itemId'])) {
			/** @noinspection SpellCheckingInspection */
			$checkout['itemid'] = $checkout['itemId'];
			$checkout['renewMessage'] = '';
		}
		$checkout['barcode'] = $this->barcode;
		return $checkout;
	}

	private function performPreSaveChecks() {
		require_once ROOT_DIR . '/sys/Utils/StringUtils.php';
		if (strlen($this->title) > 500) {
			$this->title = StringUtils::trimStringToLengthAtWordBoundary($this->title, 500, true);
		}
		if (strlen($this->title2) > 500) {
			$this->title2 = StringUtils::trimStringToLengthAtWordBoundary($this->title2, 500, true);
		}
		if (strlen($this->author) > 500) {
			$this->author = StringUtils::trimStringToLengthAtWordBoundary($this->author, 500, true);
		}
		if (strlen($this->callNumber) > 100) {
			$this->callNumber = StringUtils::trimStringToLengthAtWordBoundary($this->callNumber, 100, true);
		}
	}

	public function insert(string $context = '') : int|bool {
		$this->performPreSaveChecks();
		return parent::insert();
	}

	public function update(string $context = '') : int|bool {
		$this->performPreSaveChecks();
		return parent::update();
	}

	public function getReplacementCost() : float {
		require_once ROOT_DIR . '/sys/ReplacementCost.php';
		$replacementCosts = ReplacementCost::getReplacementCostsByFormat();

		$replacementCostForCheckout = 0;
		$useDefaultReplacementCost = true;
		//Check to see if the title has a replacement cost in the record
		$recordDriver = $this->getRecordDriver();
		if ($recordDriver instanceof MarcRecordDriver) {
			$indexingProfile = $recordDriver->getIndexingProfile();
			if (!empty($indexingProfile->replacementCostSubfield) && $indexingProfile->replacementCostSubfield != ' ' && !empty($indexingProfile->itemRecordNumber) && $indexingProfile->itemRecordNumber != ' '){
				//We need the full MARC record to get all the data
				$marcRecord = $recordDriver->getMarcRecord();
				if ($marcRecord) {
					$itemFields = $marcRecord->getFields($indexingProfile->itemTag);
					/** @var File_MARC_Data_Field $itemField */
					foreach ($itemFields as $itemField) {
						$recordNumberMatches = false;
						if (!empty($indexingProfile->itemRecordNumber)) {
							$itemRecordNumber = $itemField->getSubfield($indexingProfile->itemRecordNumber);
							$recordNumberMatches = (!empty($itemRecordNumber) && ($itemRecordNumber->getData() == $this->itemId));
						}
						$barcodeMatches = false;
						if (!empty($indexingProfile->barcode)) {
							$itemBarcode = $itemField->getSubfield($indexingProfile->barcode);
							$barcodeMatches = (!empty($itemBarcode) && ($itemBarcode->getData() == $this->barcode));
						}
						$replacementCost = $itemField->getSubfield($indexingProfile->replacementCostSubfield);
						if (!empty($replacementCost) && ($recordNumberMatches || $barcodeMatches)) {
							$replacementCost = $replacementCost->getData();
							//Remove dollar signs if they are in the field.
							require_once ROOT_DIR . '/sys/Utils/StringUtils.php';
							$replacementCost = str_replace(StringUtils::getCurrencySymbol(), '', $replacementCost);
							if ($replacementCost > 0 && is_numeric($replacementCost)) {
								$replacementCostForCheckout = $replacementCost;
								$useDefaultReplacementCost = false;
							}
							break;
						}
					}
				}
				$marcRecord = null;
			}
		}
		$lowerFormat = strtolower($this->format);
		if ($useDefaultReplacementCost && array_key_exists($lowerFormat, $replacementCosts)) {
			$replacementCostForCheckout = $replacementCosts[$lowerFormat];
		}
		return $replacementCostForCheckout;
	}
}
