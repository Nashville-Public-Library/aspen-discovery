<?php
require_once ROOT_DIR . '/sys/DB/DataObject.php';
require_once ROOT_DIR . '/Drivers/Sierra.php';

class SierraSelfRegistrationMunicipalityValues extends DataObject {
	public $__table = 'sierra_self_reg_municipality_values';
	public $id;
	public $selfRegistrationFormId;
	public $municipality;
	public $municipalityType;
	public $selfRegAllowed;
	public $sierraPType;
	public $sierraResidence;
	public $sierraLibOfReg;
	public $expirationLength;
	public $expirationPeriod;

	public function getNumericColumnNames(): array {
		return [
			'weight',
			'expirationLength',
			'selfRegAllowed'
		];
	}

	static function getObjectStructure($fieldValues = null) {
		$sierraPTypes[''] = "None";
		$sierraPTypes = array_merge($sierraPTypes, self::getMetadataOptions('patronType'));
		$sierraHomeLibraries[''] = "None";
		$sierraHomeLibraries = array_merge($sierraHomeLibraries, self::getMetadataOptions('homeLibraryCode'));
		return [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id',
			],
			'municipality' => [
				'property' => 'municipality',
				'type' => 'text',
				'label' => 'Municipality Name',
				'description' => 'The name of a city, county, or state',
				'required' => true,
			],
			'municipalityType' => [
				'property' => 'municipalityType',
				'type' => 'enum',
				'label' => 'Municipality Type',
				'values' => [
					'city' => 'City',
					'county' => 'County',
					'state' => 'State',
				],
				'description' => 'The type of municipality',
				'default' => '0',
			],
			'selfRegAllowed' => [
				'property' => 'selfRegAllowed',
				'type' => 'checkbox',
				'label' => 'Self Registration Allowed?',
				'description' => 'Whether or not the municipality allows self registration',
				'default' => '1',
			],
			'sierraPType' => [
				'property' => 'sierraPType',
				'type' => 'enum',
				'label' => 'Sierra PType',
				'values' => $sierraPTypes,
				'description' => 'The PType to automatically apply',
				'default' => '',
			],
			'sierraResidence' => [
				'property' => 'sierraResidence',
				'type' => 'enum',
				'label' => 'Sierra Residence',
				'values' => [
					'' => '',
					'yes' => 'Yes',
					'no' => 'No',
				],
				'description' => 'The Residence to automatically apply',
				'default' => '',
			],
			'sierraLibOfReg' => [
				'property' => 'sierraLibOfReg',
				'type' => 'enum',
				'label' => 'Sierra Lib of Reg',
				'values' => $sierraHomeLibraries,
				'description' => 'The Lib of Reg to automatically apply',
				'default' => '',
			],
			'expirationLength' => [
				'property' => 'expirationLength',
				'type' => 'integer',
				'label' => 'Expiration Length',
				'description' => 'How many days, months, or years before expiration',
				'default' => 0,
			],
			'expirationPeriod' => [
				'property' => 'expirationPeriod',
				'type' => 'enum',
				'label' => 'Expiration Period',
				'values' => [
					'days' => 'Days',
					'months' => 'Months',
					'years' => 'Years',
				],
				'description' => 'The type of municipality',
				'default' => '0',
			],
		];
	}

	public function __set($name, $value) {
		if ($name == "sierraPType" && $value == '') {
			$value = -1;
		}
		parent::__set($name, $value);
	}

	public function update($context = '') {
		if ($this->sierraPType == '') {
			$this->sierraPType = -1;
		}
		$ret = parent::update();
		return $ret;
	}

	public static function getMetadataOptions($field) {
		global $library;
		$user = UserAccount::getActiveUserObj();
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