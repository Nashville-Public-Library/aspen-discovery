<?php /** @noinspection PhpMissingFieldTypeInspection */

class VdxForm extends DataObject {
	public $__table = 'vdx_form';
	public $id;
	public $name;
	public $introText;
	//We always show title
	public $showAuthor;
	public $showPublisher;
	public $showIsbn;
	public $showAcceptFee;
	public $showMaximumFee;
	public $feeInformationText;
	public $showCatalogKey;
	//We always show the Note field.
	//We always show Pickup Library

	protected $_locations;

	/** @noinspection PhpUnusedParameterInspection */
	public static function getObjectStructure($context = ''): array {
		$locationList = Location::getLocationList(!UserAccount::userHasPermission('Administer All VDX Forms'));

		return [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id',
			],
			'name' => [
				'property' => 'name',
				'type' => 'text',
				'label' => 'Name',
				'description' => 'The Name of the Form',
				'maxLength' => 50,
			],
			'introText' => [
				'property' => 'introText',
				'type' => 'textarea',
				'label' => 'Intro Text',
				'description' => 'Introductory Text to be displayed at the top of the form',
				'maxLength' => 5000,
			],
			'showAuthor' => [
				'property' => 'showAuthor',
				'type' => 'checkbox',
				'label' => 'Show Author?',
				'description' => 'Whether or not the user should be prompted to enter the author name',
			],
			'showPublisher' => [
				'property' => 'showPublisher',
				'type' => 'checkbox',
				'label' => 'Show Publisher?',
				'description' => 'Whether or not the user should be prompted to enter the publisher name',
			],
			'showIsbn' => [
				'property' => 'showIsbn',
				'type' => 'checkbox',
				'label' => 'Show ISBN?',
				'description' => 'Whether or not the user should be prompted to enter the ISBN',
			],
			'showAcceptFee' => [
				'property' => 'showAcceptFee',
				'type' => 'checkbox',
				'label' => 'Show Accept Fee?',
				'description' => 'Whether or not the user should be prompted to accept the fee (if any)',
			],
			'showMaximumFee' => [
				'property' => 'showMaximumFee',
				'type' => 'checkbox',
				'label' => 'Show Maximum Fee?',
				'description' => 'Whether or not the user should be prompted for the maximum fee they will pay',
			],
			'feeInformationText' => [
				'property' => 'feeInformationText',
				'type' => 'textarea',
				'label' => 'Fee Information Text',
				'description' => 'Text to be displayed to give additional information about the fees charged.',
				'maxLength' => 5000,
			],
			'showCatalogKey' => [
				'property' => 'showCatalogKey',
				'type' => 'checkbox',
				'label' => 'Show Catalog Key?',
				'description' => 'Whether or not the user should be prompted for the catalog key',
			],

			'locations' => [
				'property' => 'locations',
				'type' => 'multiSelect',
				'listStyle' => 'checkboxSimple',
				'label' => 'Locations',
				'description' => 'Define locations that use this VDX Form',
				'values' => $locationList,
				'hideInLists' => false,
			],
		];
	}

	/**
	 * @return string[]
	 */
	public function getUniquenessFields(): array {
		return ['name'];
	}

	/**
	 * Override the update functionality to save related objects
	 *
	 * @see DB/DB_DataObject::update()
	 */
	public function update($context = '') {
		$ret = parent::update();
		if ($ret !== FALSE) {
			$this->saveLocations();
		}
		return $ret;
	}

	public function insert($context = '') {
		$ret = parent::insert();
		if ($ret !== FALSE) {
			$this->saveLocations();
		}
		return $ret;
	}

	public function delete($useWhere = false, $hardDelete = false) : int {
		$ret = parent::delete($useWhere, $hardDelete);
		if ($ret && !empty($this->id)) {
			$location = new Location();
			$location->vdxFormId = $this->id;
			$location->find();
			while ($location->fetch()) {
				$location->vdxFormId = -1;
				$location->update();
			}
		}
		return $ret;
	}

	public function __get($name) {
		if ($name == "locations") {
			return $this->getLocations();
		} else {
			return parent::__get($name);
		}
	}

	public function saveLocations() {
		if (isset ($this->_locations) && is_array($this->_locations)) {
			$locationList = Location::getLocationList(!UserAccount::userHasPermission('Administer All VDX Forms'));
			foreach ($locationList as $locationId => $displayName) {
				$location = new Location();
				$location->locationId = $locationId;
				$location->find(true);
				if (in_array($locationId, $this->_locations)) {
					if ($location->vdxFormId != $this->id) {
						$location->vdxFormId = $this->id;
						$location->update();
					}
				} else {
					if ($location->vdxFormId == $this->id) {
						$location->vdxFormId = -1;
						$location->update();
					}
				}
			}
			unset($this->_locations);
		}
		return $this->_locations;
	}

	public function __set($name, $value) {
		if ($name == "locations") {
			$this->_locations = $value;
		} else {
			parent::__set($name, $value);
		}
	}

	public function getFormFields(?MarcRecordDriver $marcRecordDriver, ?string $volumeInfo = null): array {
		$fields = [];
		if ($this->introText) {
			$fields['introText'] = [
				'property' => 'introText',
				'type' => 'label',
				'label' => $this->introText,
				'description' => '',
			];
		}
		require_once ROOT_DIR . '/sys/Utils/StringUtils.php';
		$fields['title'] = [
			'property' => 'title',
			'type' => 'text',
			'label' => 'Title',
			'description' => 'The title of the title to be request',
			'maxLength' => 255,
			'required' => true,
			'default' => ($marcRecordDriver != null ? StringUtils::removeTrailingPunctuation($marcRecordDriver->getTitle()) : ''),
		];
		$fields['author'] = [
			'property' => 'author',
			'type' => ($this->showAuthor ? 'text' : 'hidden'),
			'label' => 'Author',
			'description' => 'The author of the title to request',
			'maxLength' => 255,
			'required' => false,
			'default' => ($marcRecordDriver != null ? $marcRecordDriver->getAuthor() : ''),
		];
		$publisher = '';
		if ($marcRecordDriver != null) {
			$publishers = $marcRecordDriver->getPublishers();
			if (count($publishers) > 0) {
				$publisher = reset($publishers);
			}
		}
		$fields['publisher'] = [
			'property' => 'publisher',
			'type' => ($this->showPublisher ? 'text' : 'hidden'),
			'label' => 'Publisher',
			'description' => 'The publisher of the title to request',
			'maxLength' => 255,
			'required' => false,
			'default' => $publisher,
		];
		$fields['isbn'] = [
			'property' => 'isbn',
			'type' => ($this->showIsbn ? 'text' : 'hidden'),
			'label' => 'ISBN',
			'description' => 'The ISBN of the title to request',
			'maxLength' => 20,
			'required' => false,
			'default' => ($marcRecordDriver != null ? $marcRecordDriver->getCleanISBN() : ''),
		];
		if ($marcRecordDriver != null) {
			/** @var File_MARC_Control_Field $oclcNumber */
			$oclcNumber = $marcRecordDriver->getMarcRecord()->getField('001');
			if ($oclcNumber != null) {
				$oclcNumberString = StringUtils::truncate($oclcNumber->getData(), 50);
			} else {
				$oclcNumberString = '';
			}
		} else {
			$oclcNumberString = '';
		}
		$fields['oclcNumber'] = [
			'property' => 'oclcNumber',
			'type' => 'hidden',
			'label' => 'OCLC Number',
			'description' => 'The OCLC Number',
			'maxLength' => 50,
			'required' => false,
			'default' => $oclcNumberString,
		];
		if ($this->showAcceptFee) {
			$fields['feeInformationText'] = [
				'property' => 'feeInformationText',
				'type' => 'label',
				'label' => $this->feeInformationText,
				'description' => '',
			];
			if ($this->showMaximumFee) {
				$fields['maximumFeeAmount'] = [
					'property' => 'maximumFeeAmount',
					'type' => 'currency',
					'label' => 'Maximum Fee ',
					'description' => 'The maximum fee you are willing to pay to have this title transferred to the library.',
					'default' => 0,
					'displayFormat' => '%0.2f',
				];
				$fields['acceptFee'] = [
					'property' => 'acceptFee',
					'type' => 'checkbox',
					'label' => 'I will pay any fees associated with this request up to the maximum amount defined above',
					'description' => '',
				];
			} else {
				$fields['acceptFee'] = [
					'property' => 'acceptFee',
					'type' => 'checkbox',
					'label' => 'I will pay any fees associated with this request',
					'description' => '',
				];
			}
		}
		$user = UserAccount::getLoggedInUser();
		$locations = $user->getValidPickupBranches($user->getCatalogDriver()->accountProfile->recordSource);
		$pickupLocations = [];
		foreach ($locations as $key => $location) {
			if ($location instanceof Location) {
				$pickupLocations[$location->code] = $location->displayName;
			} else {
				if ($key == '0default') {
					$pickupLocations[-1] = $location;
				}
			}
		}
		$fields['pickupLocation'] = [
			'property' => 'pickupLocation',
			'type' => 'enum',
			'values' => $pickupLocations,
			'label' => 'Pickup Location',
			'description' => 'Where you would like to pickup the title',
			'required' => true,
			'default' => $user->getHomeLocationCode(),
		];
		$fields['note'] = [
			'property' => 'note',
			'type' => 'textarea',
			'label' => 'Note',
			'description' => 'Any additional information you want us to have about this request',
			'required' => false,
			'default' => ($volumeInfo == null) ? '' : $volumeInfo,
		];
		$fields['catalogKey'] = [
			'property' => 'catalogKey',
			'type' => (($this->showCatalogKey && $marcRecordDriver != null) ? 'text' : 'hidden'),
			'label' => 'Record Number',
			'description' => 'The record number to be requested',
			'maxLength' => 20,
			'required' => false,
			'default' => ($marcRecordDriver != null ? $marcRecordDriver->getId() : ''),
		];
		return $fields;
	}

	private function getLocations() : ?array {
		if (!isset($this->_locations)) {
			$this->_locations = [];
			if (!empty($this->id)) {
				$obj = new Location();
				$obj->vdxFormId = $this->id;
				$obj->find();
				while ($obj->fetch()) {
					$this->_locations[$obj->locationId] = $obj->locationId;
				}
			}
		}
		return $this->_locations;
	}

	public function getFormFieldsForApi() : array {
		$fields['introText'] = [
			'type' => 'text',
			'property' => 'introText',
			'display' => $this->introText ? 'show' : 'hide',
			'label' => $this->introText,
			'description' => '',
			'required' => false,
			'maxLength' => 255,
		];

		$fields['title'] = [
			'type' => 'input',
			'property' => 'title',
			'display' => 'show',
			'label' => translate([
				'text' => 'Title',
				'isPublicFacing' => true,
			]),
			'description' => translate([
				'text' => 'The title to request',
				'isPublicFacing' => true,
			]),
			'required' => true,
			'maxLength' => 255,
		];

		$fields['author'] = [
			'type' => 'input',
			'property' => 'author',
			'display' => $this->showAuthor ? 'show' : 'hide',
			'label' => translate([
				'text' => 'Author',
				'isPublicFacing' => true,
			]),
			'description' => translate([
				'text' => 'The author of the title to request',
				'isPublicFacing' => true,
			]),
			'required' => false,
			'maxLength' => 255,
		];

		$fields['publisher'] = [
			'type' => 'input',
			'property' => 'publisher',
			'display' => $this->showPublisher ? 'show' : 'hide',
			'label' => translate([
				'text' => 'Publisher',
				'isPublicFacing' => true,
			]),
			'description' => translate([
				'text' => 'The publisher of the title to request',
				'isPublicFacing' => true,
			]),
			'required' => false,
			'maxLength' => 255,
		];

		$fields['isbn'] = [
			'type' => 'input',
			'property' => 'isbn',
			'display' => $this->showIsbn ? 'show' : 'hide',
			'label' => translate([
				'text' => 'ISBN',
				'isPublicFacing' => true,
			]),
			'description' => translate([
				'text' => 'The ISBN of the title to request',
				'isPublicFacing' => true,
			]),
			'required' => false,
			'maxLength' => 20,
		];

		$fields['oclcNumber'] = [
			'type' => 'input',
			'property' => 'oclcNumber',
			'display' => 'hide',
			'label' => translate([
				'text' => 'OCLC Number',
				'isPublicFacing' => true,
			]),
			'description' => translate([
				'text' => 'The OCLC Number',
				'isPublicFacing' => true,
			]),
			'required' => false,
			'maxLength' => 50,
		];

		$fields['feeInformationText'] = [
			'type' => 'text',
			'property' => 'feeInformationText',
			'display' => $this->showAcceptFee ? 'show' : 'hide',
			'label' => $this->feeInformationText,
			'description' => '',
			'required' => false,
			'maxLength' => 255,
		];

		$fields['showMaximumFee'] = [
			'type' => 'number',
			'property' => 'showMaximumFee',
			'display' => $this->showMaximumFee ? 'show' : 'hide',
			'label' => translate([
				'text' => 'Maximum Fee',
				'isPublicFacing' => true,
			]),
			'description' => translate([
				'text' => 'The maximum fee you are willing to pay to have this title transferred to the library.',
				'isPublicFacing' => true,
			]),
			'required' => false,
			'maxLength' => 255,
		];

		$fields['acceptFee'] = [
			'type' => 'checkbox',
			'property' => 'acceptFee',
			'display' => $this->showAcceptFee ? 'show' : 'hide',
			'label' => translate([
				'text' => 'I will pay any fees associated with this request up to the maximum amount defined above',
				'isPublicFacing' => true,
			]),
			'description' => '',
			'required' => false,
			'maxLength' => 255,
		];

		$fields['note'] = [
			'type' => 'textarea',
			'property' => 'note',
			'display' => 'show',
			'label' => translate([
				'text' => 'Note',
				'isPublicFacing' => true,
			]),
			'description' => translate([
				'text' => 'Any additional information you want us to have about this request',
				'isPublicFacing' => true,
			]),
			'required' => false,
			'maxLength' => 255,
		];

		$fields['catalogKey'] = [
			'type' => 'text',
			'property' => 'catalogKey',
			'display' => $this->showCatalogKey ? 'show' : 'hide',
			'label' => translate([
				'text' => 'Record Number',
				'isPublicFacing' => true,
			]),
			'description' => translate([
				'text' => 'The record number to be requested',
				'isPublicFacing' => true,
			]),
			'required' => false,
			'maxLength' => 20,
		];

		require_once ROOT_DIR . '/services/API/UserAPI.php';
		$user = new UserAPI();

		$pickupLocations = 'Unable to get pickup locations for given user';

		$validPickupLocations = $user->getValidPickupLocations();
		if ($validPickupLocations['success']) {
			$pickupLocations = $user->getValidPickupLocations();
			$pickupLocations = $pickupLocations['pickupLocations'];
		}

		$fields['pickupLocation'] = [
			'type' => 'select',
			'property' => 'pickupLocation',
			'display' => 'show',
			'label' => translate([
				'text' => 'Pickup Location',
				'isPublicFacing' => true,
			]),
			'description' => translate([
				'text' => 'Where you would like to pickup the title',
				'isPublicFacing' => true,
			]),
			'required' => true,
			'maxLength' => 255,
			'options' => $pickupLocations,
		];

		return $fields;
	}
}
