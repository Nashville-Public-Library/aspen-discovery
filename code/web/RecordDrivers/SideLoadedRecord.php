<?php

require_once ROOT_DIR . '/RecordDrivers/ExternalEContentDriver.php';

class SideLoadedRecord extends BaseEContentDriver {
	/**
	 * Constructor.  We build the object using data from the Side-loaded records stored on disk.
	 * Will be similar to a MarcRecord with slightly different functionality
	 *
	 * @param array|File_MARC_Record|string $record
	 * @param GroupedWork $groupedWork ;
	 * @access  public
	 */
	public function __construct($record, $groupedWork = null) {
		parent::__construct($record, $groupedWork);
	}

	function getRecordUrl() {
		$recordId = $this->getUniqueID();

		/** @var SideLoad[] $sideLoadSettings */ global $sideLoadSettings;
		$indexingProfile = $sideLoadSettings[strtolower($this->profileType)];

		return "/{$indexingProfile->recordUrlComponent}/$recordId";
	}

	public function getMoreDetailsOptions() {
		global $interface;

		$isbn = $this->getCleanISBN();

		//Load table of contents
		$tableOfContents = $this->getTableOfContents();
		$interface->assign('tableOfContents', $tableOfContents);

		//Get Related Records to make sure we initialize items
		$recordInfo = $this->getGroupedWorkDriver()->getRelatedRecord($this->getIdWithSource());
		if ($recordInfo != null) {
			//Get copies for the record
			$this->assignCopiesInformation();

			$interface->assign('items', $recordInfo->getItemSummary());
		}

		//Load more details options
		$moreDetailsOptions = $this->getBaseMoreDetailsOptions($isbn);

		if ($recordInfo != null) {
			$moreDetailsOptions['copies'] = [
				'label' => 'Copies',
				'body' => $interface->fetch('ExternalEContent/view-items.tpl'),
				'openByDefault' => true,
			];
		}

		$moreDetailsOptions['moreDetails'] = [
			'label' => 'More Details',
			'body' => $interface->fetch('ExternalEContent/view-more-details.tpl'),
		];

		$this->loadSubjects();
		$moreDetailsOptions['subjects'] = [
			'label' => 'Subjects',
			'body' => $interface->fetch('Record/view-subjects.tpl'),
		];
		$moreDetailsOptions['citations'] = [
			'label' => 'Citations',
			'body' => $interface->fetch('Record/cite.tpl'),
		];
		if ($interface->getVariable('showStaffView')) {
			$moreDetailsOptions['staff'] = [
				'label' => 'Staff View',
				'body' => $interface->fetch($this->getStaffView()),
			];
		}

		return $this->filterAndSortMoreDetailsOptions($moreDetailsOptions);
	}

	public function getRecordType() {
		return $this->profileType;
	}

	function isEContentHoldable($locationCode, $eContentFieldData) {
		return false;
	}

	function isLocalItem($locationCode, $eContentFieldData) {
		return true;
	}

	function isLibraryItem($locationCode, $eContentFieldData) {
		return true;
	}

	function isItemAvailable($itemId, $totalCopies) {
		return true;
	}

	function isValidForUser($locationCode, $eContentFieldData) {
		return true;
	}

	function getSharing($locationCode, $eContentFieldData) {
		return '';
	}

	function getEContentFormat($fileOrUrl, $iType) {
		// TODO: Implement getEContentFormat() method.
		return '';
	}

	function createActionsFromUrls($relatedUrls, $itemInfo = null, $variationId = 'any') : array {
		global $sideLoadSettings;
		$sideLoad = $sideLoadSettings[strtolower($this->profileType)];

		global $configArray;
		$actions = [];
		$i = 0;
		foreach ($relatedUrls as $urlInfo) {
			//Revert to access online per Karen at CCU.  If people want to switch it back, we can add a per library switch
			$useDefaultLabel = false;
			if ($sideLoad->useLinkTextForButtonLabel && $itemInfo != null) {
				if ($itemInfo instanceof Grouping_Item) {
					$title = $itemInfo->shelfLocation;
				}else{
					$title = $itemInfo['shelfLocation'];
				}
				if (empty($title)) {
					$useDefaultLabel = true;
				}else{
					$title = translate([
						'text' => $title,
						'isPublicFacing' => true,
						'isAdminEnteredData' => true,
					]);
				}
			}else {
				$useDefaultLabel = true;
			}
			if ($useDefaultLabel) {
				$title = translate([
					'text' => $sideLoad->accessButtonLabel,
					'isPublicFacing' => true,
					'isAdminEnteredData' => true,
				]);
			}
			$action = $configArray['Site']['url'] . '/' . $this->getModule() . '/AccessOnline?id=' . $this->id . "&index=$i";
			if ($itemInfo != null) {
				$action .= '&itemId=' . $itemInfo->itemId;
			}
			$fileOrUrl = isset($urlInfo['url']) ? $urlInfo['url'] : $urlInfo['file'];
			if (strlen($fileOrUrl) > 0) {
				$actions[] = [
					'url' => $action,
					'redirectUrl' => $fileOrUrl,
					'title' => $title,
					'requireLogin' => false,
					'target' => '_blank',
					'itemId' => $itemInfo->itemId,
					'index' => $i
				];
				$i++;
			}
		}

		return $actions;
	}

	public function isShowStatus() {
		/** @var SideLoad[] $sideLoadSettings */ global $sideLoadSettings;
		$indexingProfile = $sideLoadSettings[strtolower($this->profileType)];
		return $indexingProfile->showStatus;
	}
}