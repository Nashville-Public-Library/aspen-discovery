<?php /** @noinspection PhpMissingFieldTypeInspection */

require_once ROOT_DIR . '/sys/DB/DataObject.php';

class IlsHoldSummary extends DataObject {
	public $__table = 'ils_hold_summary';    // table name
	public $id;
	public $ilsId;
	public $numHolds;

	private static $preloadedHoldSummaries = [];
	/**
	 * Preloads hold summaries for a type and identifier using minimal database queries
	 *
	 * @param string $type
	 * @param array $identifiers
	 * @return void
	 */
	static function preloadHoldSummaries(string $type, array $identifiers) : void {
		global $indexingProfiles;
		//Hold Summaries are only for ILS records
		if (!in_array($type, $indexingProfiles)) {
			return;
		}
		foreach ($identifiers as $identifier) {
			if (!array_key_exists($identifier, self::$preloadedHoldSummaries)) {
				self::$preloadedHoldSummaries[$identifier] = null;
			}
		}
		$ilsHoldSummaries = new IlsHoldSummary();
		$ilsHoldSummaries->whereAddIn('ilsId', $identifiers, true);
		$allHoldSummaries = $ilsHoldSummaries->fetchAll();
		foreach ($allHoldSummaries as $holdSummary) {
			self::$preloadedHoldSummaries[$holdSummary->ilsId] = $holdSummary;
		}
	}

	/**
	 * @param string $type
	 * @param string $identifier
	 * @return ?IlsHoldSummary
	 */
	static function getHoldSummaryForRecord(string $type, string $identifier) : ?IlsHoldSummary {
		global $indexingProfiles;
		//Hold Summaries are only for ILS records
		if (!in_array($type, $indexingProfiles)) {
			return null;
		}
		if (array_key_exists($identifier, self::$preloadedHoldSummaries)) {
			return self::$preloadedHoldSummaries[$identifier];
		}else{
			$ilsHoldSummaries = new IlsHoldSummary();
			$ilsHoldSummaries->ilsId = $identifier;
			if ($ilsHoldSummaries->find(true)) {
				return $ilsHoldSummaries;
			} else {
				return null;
			}
		}
	}
} 