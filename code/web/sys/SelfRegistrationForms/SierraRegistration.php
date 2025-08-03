<?php
class SierraRegistration extends DataObject {
	public $__table = 'self_registration_sierra';
	public $id;
	public $patronId;
	public $_name;
	public $_address;
	public $_phone;
	public $_email;
	public $_birthDate;
	public $_expirationDate;
	public $barcode;
	public $sierraPType;
	public $sierraPCode1;
	public $sierraPCode2;
	public $sierraPCode3;
	public $sierraPCode4;
	public $locationId;
	public $libraryId;
	public $approved;
	public $_note;
	protected $_sierraData;
	protected $_sierraUpdate = [];

	static function getObjectStructure($context = ''): array {
		$sierraPTypes[''] = "None";
		$pCode1Options[''] = "None";
		$pCode2Options[''] = "None";
		$pCode3Options[''] = "None";
		$pCode4Options[''] = "None";
		$metadataOptions = self::getMetadataOptions('patronType,pcode1,pcode2,pcode3,pcode4');
		if (!empty($metadataOptions['patronType'])) {
			$sierraPTypes = $sierraPTypes + $metadataOptions['patronType'];
		}
		if (!empty($metadataOptions['pcode1'])) {
			$pCode1Options = $pCode1Options + $metadataOptions['pcode1'];
		}
		if (!empty($metadataOptions['pcode2'])) {
			$pCode2Options = $pCode2Options + $metadataOptions['pcode2'];
		}
		if (!empty($metadataOptions['pcode3'])) {
			$pCode3Options = $pCode3Options + $metadataOptions['pcode3'];
		}
		if (!empty($metadataOptions['pcode4'])) {
			$pCode4Options = $pCode4Options + $metadataOptions['pcode4'];
		}

		return [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id within the database',
			],
			'name' => [
				'property' => 'name',
				'type' => 'label',
				'label' => 'Name',
				'description' => "The patron's name",
			],
			'address' => [
				'property' => 'address',
				'type' => 'label',
				'label' => 'Address',
				'description' => "The patron's address",
				'hideInLists' => true,
			],
			'phone' => [
				'property' => 'phone',
				'type' => 'label',
				'label' => 'Phone',
				'description' => "The patron's phone number",
				'hideInLists' => true,
			],
			'email' => [
				'property' => 'email',
				'type' => 'label',
				'label' => 'Email',
				'description' => "The patron's email address",
				'hideInLists' => true,
			],
			'birthDate' => [
				'property' => 'birthDate',
				'type' => 'label',
				'label' => 'Birth Date',
				'description' => "The patron's birth date",
				'hideInLists' => true,
			],
			'expirationDate' => [
				'property' => 'expirationDate',
				'type' => 'label',
				'label' => 'Expiration Date',
				'description' => "The patron's expiration date",
				'hideInLists' => true,
			],
			'dateRegistered' => [
				'property' => 'dateRegistered',
				'type' => 'label',
				'label' => 'Date Registered',
				'description' => 'The date of self registration',
			],
			'libraryName' => [
				'property' => 'libraryName',
				'type' => 'label',
				'label' => 'Library',
				'description' => 'The library the patron registered at',
			],
			'locationName' => [
				'property' => 'locationName',
				'type' => 'label',
				'label' => 'Location',
				'description' => 'The location the patron registered at',
			],
			'barcode' => [
				'property' => 'barcode',
				'type' => 'text',
				'label' => 'Barcode',
				'description' => 'The patron barcode',
			],
			'sierraPType' => [
				'property' => 'sierraPType',
				'type' => 'enum',
				'label' => 'Sierra PType',
				'values' => $sierraPTypes,
				'description' => 'The PType to automatically apply',
				'default' => '',
			],
			'sierraPCode1' => [
				'property' => 'sierraPCode1',
				'type' => 'enum',
				'label' => 'Sierra PCode1',
				'values' => $pCode1Options,
				'description' => 'The PCode 1 to automatically apply',
				'default' => '',
			],
			'sierraPCode2' => [
				'property' => 'sierraPCode2',
				'type' => 'enum',
				'label' => 'Sierra PCode2',
				'values' => $pCode2Options,
				'description' => 'The PCode 2 to automatically apply',
				'default' => '',
			],
			'sierraPCode3' => [
				'property' => 'sierraPCode3',
				'type' => 'enum',
				'label' => 'Sierra PCode3',
				'values' => $pCode3Options,
				'description' => 'The PCode 3 to automatically apply',
				'default' => '',
			],
			'sierraPCode4' => [
				'property' => 'sierraPCode4',
				'type' => 'enum',
				'label' => 'Sierra PCode4',
				'values' => $pCode4Options,
				'description' => 'The PCode 4 to automatically apply',
				'default' => '',
			],
			'note' => [
				'property' => 'note',
				'type' => 'text',
				'label' => 'Note',
				'description' => 'Patron account note',
				'hideInLists' => true,
			],
			'approved' => [
				'property' => 'approved',
				'type' => 'checkbox',
				'label' => 'Approved',
				'default' => 1,
				'hideInLists' => true,
				'hiddenByDefault' => true,
			]
		];
	}

	public function update($context = '') {
		$this->approved = 1;
		$this->_changedFields[] = 'approved';
		if ($this->sierraPType == '') {
			$this->sierraPType = -1;
		}
		if ($this->sierraPCode3 == '') {
			$this->sierraPCode3 = -1;
		}
		if ($this->sierraPCode4 == '') {
			$this->sierraPCode4 = -1;
		}
		if (!empty($this->_changedFields)) {
			self::getSierraData();
			$sierraFields = ['barcode', 'sierraPType', 'sierraPCode1', 'sierraPCode2', 'sierraPCode3', 'sierraPCode4'];
			foreach ($this->_changedFields as $changedField) {
				if (in_array($changedField, $sierraFields)) {
					$this->setSierraData($changedField, $this->{$changedField});
				}
			}
			$this->setSierraData('note', $this->_note);
			$this->updatePatronInSierra();
		}
		$ret = parent::update();
		return $ret;
	}

	public function __set($name, $value) {
		if ($name == "note") {
			$this->_note = $value;
		} else {
			if ($name == "sierraPType" && $value == '') {
				$value = -1;
			} else if ($name == "sierraPCode3" && $value == '') {
				$value = -1;
			} else if ($name == "sierraPCode4" && $value == '') {
				$value = -1;
			}
			parent::__set($name, $value);
		}
	}

	public function __get($name) {
		$sierraFields = ['name', 'address', 'phone', 'email', 'birthDate', 'expirationDate', 'note'];
		if (in_array($name, $sierraFields)) {
			return $this->readSierraData($name);
		} else if ($name == 'libraryName') {
			if (!empty($this->libraryId)) {
				$library = new Library();
				$library->libraryId = $this->libraryId;
				if ($library->find(true)) {
					$this->_data[$name] = $library->displayName;
				}
				$library->__destruct();
				return $this->_data[$name] ?? null;
			} else {
				return null;
			}
		} else if ($name == 'locationName') {
			if (!empty($this->locationId)) {
				$location = new Location();
				$location->locationId = $this->locationId;
				if ($location->find(true)) {
					$this->_data[$name] = $location->displayName;
				}
				$location->__destruct();
				return $this->_data[$name] ?? null;
			} else {
				return null;
			}
		}
		else {
			return parent::__get($name);
		}
	}

	private function readSierraData($name) {
		if ($this->_sierraData === null) {
			$this->getSierraData();
		}
		if (!empty($this->_sierraData)) {
			if ($name == "name") {
				return $this->_sierraData->names[0];
			} else if ($name == "address" && !empty($this->_sierraData->addresses[0])) {
				if (is_array($this->_sierraData->addresses[0]->lines)) {
					return implode(", ", $this->_sierraData->addresses[0]->lines);
				} else {
					return $this->_sierraData->addresses[0]->lines;
				}
			} else if ($name == "phone" && !empty($this->_sierraData->phones[0])) {
				return $this->_sierraData->phones[0]->number;
			} else if ($name == "email" && !empty($this->_sierraData->emails[0])) {
				return $this->_sierraData->emails[0];
			} else if ($name == "note") {
				$note = '';
				foreach ($this->_sierraData->varFields as $varField) {
					if ($varField->fieldTag == 'x') {
						$note .= $varField->content . " ";
						break;
					}
				}
				global $library;
				$user = UserAccount::getLoggedInUser();
				$note .= translate([
					'text' => "Patron verified by %1% at %2% on %3%.",
					1 => $user->displayName,
					2 => $library->displayName,
					3 => date('m/d/Y \a\t g:i a'),
					'isAdminFacing' => true,
				]);
				$this->_note = $note;
				return $note;
			} else {
				if (!empty($this->_sierraData->{$name})) {
					return $this->_sierraData->{$name};
				}
			}
		} else {
			return '';
		}
		return '';
	}

	private function setSierraData($name, $value) {
		if ($name == "barcode") {
			$this->_sierraUpdate['barcodes'] = [$value];
		}
		if ($name == "sierraPType" && $value !== -1) {
			$this->_sierraUpdate['patronType'] = (int)$value;
		}
		if ($name == "sierraPCode1") {
			$this->_sierraUpdate['patronCodes']['pcode1'] = $value;
		}
		if ($name == "sierraPCode2") {
			$this->_sierraUpdate['patronCodes']['pcode2'] = $value;
		}
		if ($name == "sierraPCode3" && $value !== -1) {
			$this->_sierraUpdate['patronCodes']['pcode3'] = (int)$value;
		}
		if ($name == "sierraPCode4" && $value !== -1) {
			$this->_sierraUpdate['patronCodes']['pcode4'] = (int)$value;
		}
		if ($name == "note") {
			$this->_sierraUpdate['varFields'][] = ['fieldTag' => 'x', 'content' => $value];
		}
	}

	private function getSierraData(): void {
		global $library;
		$accountProfile = $library->getAccountProfile();
		$catalogDriverName = trim($accountProfile->driver);
		$catalogDriver = null;
		if (!empty($catalogDriverName)) {
			$catalogDriver = CatalogFactory::getCatalogConnectionInstance($catalogDriverName, $accountProfile);
		}
		if ($catalogDriver->driver instanceof Sierra && !empty($this->patronId)) {
			$data = $catalogDriver->driver->getPatronsByIdList([$this->patronId]);
			if ($data) {
				$this->_sierraData = $data->entries[0];
			} else {
				$this->_sierraData = [];
			}
		} else {
			$this->_sierraData = [];
		}
	}

	private function updatePatronInSierra() {
		global $library;
		$accountProfile = $library->getAccountProfile();
		$catalogDriverName = trim($accountProfile->driver);
		$catalogDriver = null;
		if (!empty($catalogDriverName)) {
			$catalogDriver = CatalogFactory::getCatalogConnectionInstance($catalogDriverName, $accountProfile);
		}
		if ($catalogDriver->driver instanceof Sierra) {
			return $catalogDriver->driver->updatePatronRegistration($this->_sierraUpdate, $this->_sierraData->id);
		} else {
			return [];
		}
	}

	public static function getMetadataOptions($field) {
		global $library;
		$accountProfile = $library->getAccountProfile();
		$catalogDriverName = trim($accountProfile->driver);
		$catalogDriver = null;
		if (!empty($catalogDriverName)) {
			$catalogDriver = CatalogFactory::getCatalogConnectionInstance($catalogDriverName, $accountProfile);
		}
		if ($catalogDriver->driver instanceof Sierra) {
			return $catalogDriver->driver->getPatronMetadataOptions($field);
		} else {
			return [];
		}
	}
}