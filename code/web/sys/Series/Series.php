<?php /** @noinspection PhpMissingFieldTypeInspection */

require_once ROOT_DIR . '/sys/DB/DataObject.php';

class Series extends DataObject {
	public $__table = 'series';
	public $id;
	public $displayName;
	public $_authors; // Get all authors from series members
	public $description;
	public $cover;
	public $audience;
	public $isIndexed;

	public $_seriesMembers; // grouped works and placeholders

	function numTitlesInSeries() {
		require_once ROOT_DIR . '/sys/Series/SeriesMember.php';
		$members = new SeriesMember();
		$members->seriesId = $this->id;
		return $members->count();
	}

	/**
	 * @return array      of list entries
	 */
	function getTitles() {
		require_once ROOT_DIR . '/sys/Series/SeriesMember.php';
		$seriesMember = new SeriesMember();
		$seriesMember->seriesId = $this->id;
		$seriesMember->orderBy('pubDate');

		$seriesMembers = [];
		$idsBySource = [];
		$seriesMember->find();
		while ($seriesMember->fetch()) {
			$source = "GroupedWork";  // All series currently come from groupedWorks
			if (!array_key_exists($source, $idsBySource)) {
				$idsBySource[$source] = [];
			}
			$idsBySource[$source][] = $seriesMember->groupedWorkPermanentId;
			$tmpListEntry = [
				'source' => $source,
				'sourceId' => $seriesMember->groupedWorkPermanentId,
				'title' => $seriesMember->displayName,
				'seriesMemberId' => $seriesMember->id,
				'seriesMember' => clone($seriesMember),
			];

			$seriesMembers[] = $tmpListEntry;
		}
		$seriesMember->__destruct();
		$seriesMember = null;
		return [
			'seriesMembers' => $seriesMembers,
			'idsBySource' => $idsBySource,
		];
	}

	/**
	 * @param int $start position of first list item to fetch (0 based)
	 * @param int $numItems Number of items to fetch for this result
	 * @param string $format The format of the records, valid values are html, summary, recordDrivers, citation
	 * @return array     Array of HTML to display to the user
	 */
	public function getSeriesRecords($start, $numItems, $format) {
		//Get all entries for the list
		$seriesMemberInfo = $this->getTitles();

		//Trim to the number of records we want to return
		if ($numItems > 0) {
			$filteredSeriesMembers = array_slice($seriesMemberInfo['seriesMembers'], $start, $numItems);
		} else {
			$filteredSeriesMembers = $seriesMemberInfo['seriesMembers'];
		}

		$filteredIdsBySource = [];
		foreach ($filteredSeriesMembers as $seriesMember) {
			$source = "GroupedWork";
			if (!array_key_exists($source, $filteredIdsBySource)) {
				$filteredIdsBySource[$source] = [];
			}
			$filteredIdsBySource[$source][] = $seriesMember['sourceId'];
		}

		//Load the actual items from each source
		$listResults = [];
		foreach ($filteredIdsBySource as $sourceType => $sourceIds) {
			$searchObject = SearchObjectFactory::initSearchObject($sourceType);
			if ($searchObject === false) {
				AspenError::raiseError("Unknown Series Member Source $sourceType");
			} else {
				$records = $searchObject->getRecords($sourceIds);
				if ($format == 'html') {
					$listResults = $listResults + $this->getResultListHTML($records, $filteredSeriesMembers, $start);
				} elseif ($format == 'summary') {
					$listResults = $listResults + $this->getResultListSummary($records, $filteredSeriesMembers);
				} elseif ($format == 'recordDrivers') {
					$listResults = $listResults + $this->getResultListRecordDrivers($records, $filteredSeriesMembers);
				} else {
					AspenError::raiseError("Unknown display format $format in getSeriesRecords");
				}
			}
		}

		if ($format == 'html') {
			//Add in non-owned results for anything that is left
			global $interface;
			foreach ($filteredSeriesMembers as $listPosition => $seriesMemberInfo) {
				if (!array_key_exists($listPosition, $listResults)) {
					$interface->assign('recordIndex', $listPosition + 1);
					$interface->assign('resultIndex', $listPosition + $start + 1);
					$interface->assign('seriesMemberId', $seriesMemberInfo['seriesMemberId']);
					if (!empty($seriesMemberInfo['title'])) {
						$interface->assign('deletedEntryTitle', $seriesMemberInfo['title']);
					} else {
						$interface->assign('deletedEntryTitle', '');
					}
					$listResults[$listPosition] = $interface->fetch('MyAccount/deletedListEntry.tpl');
				}
			}
		}

		ksort($listResults);
		krsort($listResults, SORT_NATURAL);
		return $listResults;
	}

	/**
	 * Use the record driver to build an array of HTML displays from the search
	 * results suitable for use while displaying lists
	 *
	 * @access  public
	 * @param RecordInterface[] $records Records retrieved from the getRecords method of a SolrSearcher
	 * @param array $allListEntryIds optional list of IDs to re-order the records by (ie User List sorts)
	 * @param int $startRecord The first record being displayed
	 * @return array Array of HTML chunks for individual records.
	 */
	private function getResultListHTML($records, $allListEntryIds, $startRecord = 0) {
		global $interface;
		$html = [];
		//Reorder the documents based on the list of id's
		foreach ($allListEntryIds as $listPosition => $currentId) {
			// use $IDList as the order guide for the html
			/** @var GroupedWorkDriver|null $current */
			$current = null; // empty out in case we don't find the matching record
			reset($records);
			foreach ($records as $docIndex => $recordDriver) {
				if ($recordDriver->getId() == $currentId['sourceId']) {
					$recordDriver->setListEntryId($currentId['seriesMemberId']);
					$current = $recordDriver;
					break;
				}
			}
			$interface->assign('recordIndex', $listPosition + 1);
			$interface->assign('resultIndex', $listPosition + $startRecord + 1);

			if (!empty($current)) {
				//Get information from list entry
				$interface->assign('seriesMemberId', $current->getListEntryId());

				$interface->assign('recordDriver', $current);
				$html[$listPosition] = $interface->fetch($current->getCourseReserveEntry($this->id));
			}
		}
		return $html;
	}

	private function getResultListSummary($records, $allListEntryIds) {
		$results = [];
		//Reorder the documents based on the list of id's
		foreach ($allListEntryIds as $listPosition => $currentId) {
			// use $IDList as the order guide for the html
			/** @var CourseReservesRecordDriver|null $current */
			$current = null; // empty out in case we don't find the matching record
			reset($records);
			/**
			 * @var int $docIndex
			 * @var CourseReservesRecordDriver $recordDriver
			 */
			foreach ($records as $docIndex => $recordDriver) {
				if ($recordDriver->getId() == $currentId['sourceId']) {
					$current = $recordDriver;
					break;
				}
			}
			if (!empty($current)) {
				$results[$listPosition] = $current->getSummaryInformation();
			}
		}
		return $results;
	}

	private function getResultListRecordDrivers($records, $allListEntryIds) {
		$results = [];
		//Reorder the documents based on the list of id's
		foreach ($allListEntryIds as $listPosition => $currentId) {
			// use $IDList as the order guide for the html
			$current = null; // empty out in case we don't find the matching record
			reset($records);
			/**
			 * @var int $docIndex
			 * @var IndexRecordDriver $recordDriver
			 */
			foreach ($records as $docIndex => $recordDriver) {
				if ($recordDriver->getId() == $currentId['sourceId']) {
					$current = $recordDriver;
					break;
				}
			}
			if (!empty($current)) {
				$results[$listPosition] = $current;
			}
		}
		return $results;
	}


}