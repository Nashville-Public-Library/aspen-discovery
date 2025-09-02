<?php /** @noinspection PhpMissingFieldTypeInspection */


class CronLogEntry extends DataObject {
	public $__table = 'cron_log';
	public $id;
	public $name;
	public $startTime;
	public $lastUpdate;
	public $endTime;
	public $numErrors;
	public $notes;
	private $_processes = null;

	function processes() : array {
		if (is_null($this->_processes)) {
			$this->_processes = [];
			$reindexProcess = new CronProcessLogEntry();
			$reindexProcess->cronId = $this->id;
			$reindexProcess->orderBy('processName');
			$reindexProcess->find();
			while ($reindexProcess->fetch()) {
				$this->_processes[] = clone $reindexProcess;
			}
		}
		return $this->_processes;
	}

	/** @noinspection PhpUnused */
	function getNumProcesses() : int {
		return count($this->processes());
	}

	/** @noinspection PhpUnused */
	function getHadErrors() : bool {
		foreach ($this->processes() as $process) {
			if ($process->numErrors > 0) {
				return true;
			}
		}
		return false;
	}

	/** @noinspection PhpUnused */
	function getElapsedTime() : string {
		if (empty($this->endTime)) {
			return "";
		} else {
			$elapsedTimeMin = ceil(($this->endTime - $this->startTime) / 60);
			if ($elapsedTimeMin < 60) {
				return $elapsedTimeMin . " min";
			} else {
				$hours = floor($elapsedTimeMin / 60);
				$minutes = $elapsedTimeMin - (60 * $hours);
				return "$hours hours, $minutes min";
			}
		}
	}

	private $skipLogging = false;

	/**
	 * Override insert to check if this is a frequent cron job with logging disabled.
	 * If so, sets skipLogging flag and prevents database insertion.
	 */
	public function insert(string $context = '') : int|bool {
		if (!empty($this->name)) {
			require_once ROOT_DIR . '/services/Admin/CronRunner.php';
			require_once ROOT_DIR . '/sys/SystemVariables.php';
			
			$frequentJobs = Admin_CronRunner::getFrequentCronJobs();
			if (in_array($this->name, $frequentJobs)) {
				$systemVariables = SystemVariables::getSystemVariables();
				if (!$systemVariables->logFrequentCrons) {
					$this->skipLogging = true;
					return true;
				}
			}
		}
		
		return parent::insert($context);
	}

	/**
	 * Override update to prevent database operations when logging is disabled.
	 * This handles cases where cron jobs call update() after insert() returned early.
	 */
	public function update(string $context = '') : bool|int {
		if ($this->skipLogging) {
			return true;
		}
		return parent::update($context);
	}

}
