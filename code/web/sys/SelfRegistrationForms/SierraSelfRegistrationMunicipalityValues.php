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
	public $sierraPCode1;
	public $sierraPCode2;
	public $sierraPCode3;
	public $sierraPCode4;
	public $expirationLength;
	public $expirationPeriod;

	public function getNumericColumnNames(): array {
		return [
			'expirationLength',
			'selfRegAllowed'
		];
	}

	static function getObjectStructure() {
		$sierraPTypes[''] = "None";
		$pCode1Options[''] = "None";
		$pCode2Options[''] = "None";
		$pCode3Options[''] = "None";
		$pCode4Options[''] = "None";
		$metadataOptions = self::getMetadataOptions('patronType,pcode1,pcode2,pcode3,pcode4');
		if (!empty($metadataOptions['patronType'])) {
			$sierraPTypes = array_merge($sierraPTypes, $metadataOptions['patronType']);
		}
		if (!empty($metadataOptions['pcode1'])) {
			$pCode1Options = array_merge($pCode1Options, $metadataOptions['pcode1']);
		}
		if (!empty($metadataOptions['pcode2'])) {
			$pCode2Options = array_merge($pCode2Options, $metadataOptions['pcode2']);
		}
		if (!empty($metadataOptions['pcode3'])) {
			$pCode3Options = array_merge($pCode3Options, $metadataOptions['pcode3']);
		}
		if (!empty($metadataOptions['pcode4'])) {
			$pCode4Options = array_merge($pCode4Options, $metadataOptions['pcode4']);
		}
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
		else if ($name == "sierraPCode3" && $value == '') {
			$value = -1;
		}
		else if ($name == "sierraPCode4" && $value == '') {
			$value = -1;
		}
		parent::__set($name, $value);
	}

	public function update($context = '') {
		if ($this->sierraPType == '') {
			$this->sierraPType = -1;
		}
		if ($this->sierraPCode3 == '') {
			$this->sierraPCode3 = -1;
		}
		if ($this->sierraPCode4 == '') {
			$this->sierraPCode4 = -1;
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