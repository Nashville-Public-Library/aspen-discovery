<?php /** @noinspection PhpMissingFieldTypeInspection */

class MaterialsRequestHoldCandidate extends DataObject {
	public $__table = 'materials_request_hold_candidate';
	public $id;
	public $requestId;
	public $source;
	public $sourceId;

	protected $_recordDriver;

	public function getTitle() : string {
		$recordDriver = $this->getRecordDriver();
		if ($recordDriver !== false) {
			return $recordDriver->getTitle();
		}else{
			return 'Unknown';
		}
	}

	public function getAuthor() : string {
		$recordDriver = $this->getRecordDriver();
		if ($recordDriver !== false) {
			if (method_exists($recordDriver, 'getAuthor')) {
				return $recordDriver->getAuthor();
			}else{
				return '';
			}
		}else{
			return '';
		}
	}

	public function getFormat() : string {
		$recordDriver = $this->getRecordDriver();
		if ($recordDriver !== false) {
			if (method_exists($recordDriver, 'getPrimaryFormat')) {
				return $recordDriver->getPrimaryFormat();
			}else{
				return '';
			}
		}else{
			return 'Unknown';
		}
	}

	/** @noinspection PhpUnused */
	public function getLink() : string {
		$recordDriver = $this->getRecordDriver();
		if ($recordDriver !== false) {
			return $recordDriver->getLinkUrl();
		}else{
			return '';
		}
	}

	public function getBookcoverUrl() : string {
		$recordDriver = $this->getRecordDriver();
		if ($recordDriver !== false) {
			return $recordDriver->getBookcoverUrl();
		}else{
			return '';
		}
	}

	public function __toString() {
		return $this->source . ':' . $this->sourceId . ' ' . $this->getTitle() . ' ' . $this->getAuthor() . ' (' . $this->getFormat() . ')';
	}

	public function getRecordDriver() : RecordInterface|false {
		if ($this->_recordDriver == null) {
			if ($this->source == 'ils') {
				require_once ROOT_DIR . '/RecordDrivers/MarcRecordDriver.php';
				$this->_recordDriver = new MarcRecordDriver($this->sourceId);
				if (!$this->_recordDriver->isValid()) {
					$this->_recordDriver = false;
				}
			} elseif ($this->source == 'axis360') {
				require_once ROOT_DIR . '/RecordDrivers/Axis360RecordDriver.php';
				$this->_recordDriver = new Axis360RecordDriver($this->sourceId);
				if (!$this->_recordDriver->isValid()) {
					$this->_recordDriver = false;
				}
			} elseif ($this->source == 'palace_project') {
				require_once ROOT_DIR . '/RecordDrivers/PalaceProjectRecordDriver.php';
				$this->_recordDriver = new PalaceProjectRecordDriver($this->sourceId);
				if (!$this->_recordDriver->isValid()) {
					$this->_recordDriver = false;
				}
			} elseif ($this->source == 'cloud_library') {
				require_once ROOT_DIR . '/RecordDrivers/CloudLibraryRecordDriver.php';
				$this->_recordDriver = new CloudLibraryRecordDriver($this->sourceId);
				if (!$this->_recordDriver->isValid()) {
					$this->_recordDriver = false;
				}
			} elseif ($this->source == 'hoopla') {
				require_once ROOT_DIR . '/RecordDrivers/HooplaRecordDriver.php';
				$this->_recordDriver = new HooplaRecordDriver($this->sourceId);
				if (!$this->_recordDriver->isValid()) {
					$this->_recordDriver = false;
				}
			} elseif ($this->source == 'overdrive') {
				require_once ROOT_DIR . '/RecordDrivers/OverDriveRecordDriver.php';
				$this->_recordDriver = new OverDriveRecordDriver($this->sourceId);
				if (!$this->_recordDriver->isValid()) {
					$this->_recordDriver = false;
				}
			} else {
				$this->_recordDriver = false;
			}
		}
		return $this->_recordDriver;
	}
}