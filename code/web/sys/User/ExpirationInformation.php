<?php

class ExpirationInformation {
	public int $expirationDate = 0; //Expiration Date in time since epoch

	private ?bool $_expired = null;
	private ?bool $_expireClose = null;

	private function loadExpirationInfo() : void {
		if ($this->expirationDate > 0) {
			$timeNow = time();
			$this->_expired = false;
			$timeToExpire = $this->expirationDate - $timeNow;
			if ($timeToExpire <= 30 * 24 * 60 * 60) {
				if ($timeToExpire <= 0) {
					$this->_expired = true;
				}
				$this->_expireClose = true;
			} else {
				$this->_expireClose = false;
			}
		} else {
			$this->_expired = false;
			$this->_expireClose = false;
		}
	}

	public function isExpired() : bool {
		if ($this->_expired === null) {
			$this->loadExpirationInfo();
		}
		return $this->_expired;
	}

	public function isExpirationClose() : bool {
		if ($this->_expireClose === null) {
			$this->loadExpirationInfo();
		}
		return $this->_expireClose;
	}

	public function expiresOn() : string {
		if (empty($this->expirationDate)) {
			return '';
		}else{
			return date('M j, Y', $this->expirationDate);
		}
	}
}