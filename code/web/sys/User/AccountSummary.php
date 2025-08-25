<?php /** @noinspection PhpMissingFieldTypeInspection */


class AccountSummary extends DataObject {
	public $__table = 'user_account_summary';
	public $id;
	public $source;
	public $userId;
	public $numCheckedOut;
	public $numCheckoutsRemaining; //Currently used for Hoopla Only
	public $numOverdue;
	public $numAvailableHolds;
	public $numUnavailableHolds;
	public $totalFines;
	public $expirationDate;
	public $lastLoaded;
	public $hasUpdatedSavedSearches;

	protected $_materialsRequests;
	protected $_readingHistory;
	protected $_numUpdatedSearches;

	public function getNumericColumnNames(): array {
		return [
			'userId',
			'numCheckedOut',
			'numCheckoutsRemaining',
			'numOverdue',
			'numAvailableHolds',
			'numUnavailableHolds',
			'totalFines',
			'expirationDate',
			'lastLoaded',
			'hasUpdatedSavedSearches',
		];
	}

	function objectHistoryEnabled() : bool {
		return false;
	}

	/**
	 * @return int
	 */
	public function getMaterialsRequests() {
		return $this->_materialsRequests;
	}

	/**
	 * @param int $materialsRequests
	 */
	public function setMaterialsRequests($materialsRequests): void {
		$this->_materialsRequests = $materialsRequests;
	}

	public function getNumHolds() {
		return $this->numAvailableHolds + $this->numUnavailableHolds;
	}

	/**
	 * @return int
	 */
	public function getReadingHistory() {
		return $this->_readingHistory;
	}

	/**
	 * @param int $readingHistory
	 */
	public function setReadingHistory($readingHistory): void {
		$this->_readingHistory = $readingHistory;
	}

	public function setNumUpdatedSearches($numUpdatedSearches): void {
		$this->_numUpdatedSearches = $numUpdatedSearches;
	}

	private $_expired = null;
	private $_expireClose = null;

	private function loadExpirationInfo() {
		if ($this->expirationDate > 0) {
			$timeNow = time();
			$this->_expired = 0;
			$timeToExpire = $this->expirationDate - $timeNow;
			if ($timeToExpire <= 30 * 24 * 60 * 60) {
				if ($timeToExpire <= 0) {
					$this->_expired = 1;
				}
				$this->_expireClose = 1;
			} else {
				$this->_expireClose = 0;
			}
		} else {
			$this->_expired = 0;
			$this->_expireClose = 0;
		}
	}

	public function isExpired() {
		if ($this->_expired === null) {
			$this->loadExpirationInfo();
		}
		return $this->_expired;
	}

	public function isExpirationClose() {
		if ($this->_expireClose === null) {
			$this->loadExpirationInfo();
		}
		return $this->_expireClose;
	}

	public function expiresOn() {
		return date('M j, Y', $this->expirationDate);
	}

	private $_expirationFinesNotice = '';

	public function setExpirationFinesNotice(string $notice) {
		$this->_expirationFinesNotice = $notice;
	}

	private $_expirationNotice = '';

	public function setExpirationNotice(string $notice) {
		$this->_expirationNotice = $notice;
	}

	/**
	 * @return string
	 */
	public function getExpirationNotice(): string {
		return $this->_expirationNotice;
	}

	/**
	 * @return string
	 */
	public function getExpirationFinesNotice(): string {
		return $this->_expirationFinesNotice;
	}

	private $_finesBadge = '';

	public function setFinesBadge(string $notice) {
		$this->_finesBadge = $notice;
	}

	public function toArray($includeRuntimeProperties = true, $encryptFields = false): array {
		$return = parent::toArray($includeRuntimeProperties, $encryptFields);
		$return['expires'] = date('M j, Y', $this->expirationDate);
		$return['expired'] = $this->isExpired();
		$return['expireClose'] = $this->isExpirationClose();
		$return['expirationFinesNotice'] = $this->_expirationFinesNotice;
		$return['expirationNotice'] = $this->_expirationNotice;
		$return['numHolds'] = $this->getNumHolds();
		if ($this->_numUpdatedSearches > 0) {
			$return['savedSearches'] = translate([
				'text' => '%1% Updated',
				1 => $this->_numUpdatedSearches,
				'isPublicFacing' => true,
			]);
		} else {
			$return['savedSearches'] = '';
		}
		$return['finesBadge'] = $this->_finesBadge;
		return $return;
	}

	public function resetCounters() {
		$this->numCheckedOut = 0;
		$this->numCheckoutsRemaining = 0;
		$this->numOverdue = 0;
		$this->numAvailableHolds = 0;
		$this->numUnavailableHolds = 0;
		$this->totalFines = 0;
		$this->expirationDate = 0;
	}
}