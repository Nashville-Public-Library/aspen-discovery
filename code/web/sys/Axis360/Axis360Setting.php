<?php /** @noinspection PhpMissingFieldTypeInspection */

require_once ROOT_DIR . '/sys/Axis360/Axis360Scope.php';

class Axis360Setting extends DataObject {
	public $__table = 'axis360_settings';    // table name
	public $id;
	public $name;
	public $apiUrl;
	/** @noinspection PhpUnused */
	public $userInterfaceUrl;
	public $vendorUsername;
	public $vendorPassword;
	public $libraryPrefix;
	public $runFullUpdate;
	/** @noinspection PhpUnused */
	public $lastUpdateOfChangedRecords;
	/** @noinspection PhpUnused */
	public $lastUpdateOfAllRecords;

	private $_scopes;

	static $_objectStructure = [];
	static function getObjectStructure(string $context = ''): array {
		if (isset(self::$_objectStructure[$context]) && self::$_objectStructure[$context] !== null) {
			return self::$_objectStructure[$context];
		}
		$axis360ScopeStructure = Axis360Scope::getObjectStructure($context);
		unset($axis360ScopeStructure['settingId']);

		$structure = [
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
				'description' => 'A name for the setting to distinguish it from others when many are defined in an instance.',
				'maxLength' => 100
			],
			'apiUrl' => [
				'property' => 'apiUrl',
				'type' => 'url',
				'label' => 'url',
				'description' => 'The URL to the API',
			],
			'userInterfaceUrl' => [
				'property' => 'userInterfaceUrl',
				'type' => 'url',
				'label' => 'User Interface URL',
				'description' => 'The URL where the Patron can access the catalog',
			],
			'vendorUsername' => [
				'property' => 'vendorUsername',
				'type' => 'text',
				'label' => 'Vendor Username',
				'description' => 'The Vendor Username provided by Axis360 when registering',
			],
			'vendorPassword' => [
				'property' => 'vendorPassword',
				'type' => 'storedPassword',
				'label' => 'Vendor Password',
				'description' => 'The Vendor Password provided by Axis360 when registering',
				'hideInLists' => true,
			],
			'libraryPrefix' => [
				'property' => 'libraryPrefix',
				'type' => 'text',
				'label' => 'Library Prefix',
				'description' => 'The Library Prefix to use with the API',
			],
			'runFullUpdate' => [
				'property' => 'runFullUpdate',
				'type' => 'checkbox',
				'label' => 'Run Full Update',
				'description' => 'Whether or not a full update of all records should be done on the next pass of indexing',
				'default' => 0,
			],
			'lastUpdateOfChangedRecords' => [
				'property' => 'lastUpdateOfChangedRecords',
				'type' => 'timestamp',
				'label' => 'Last Update of Changed Records',
				'description' => 'The timestamp when just changes were loaded',
				'default' => 0,
			],
			'lastUpdateOfAllRecords' => [
				'property' => 'lastUpdateOfAllRecords',
				'type' => 'timestamp',
				'label' => 'Last Update of All Records',
				'description' => 'The timestamp when all records were loaded',
				'default' => 0,
			],
			'scopes' => [
				'property' => 'scopes',
				'type' => 'oneToMany',
				'label' => 'Scopes',
				'description' => 'Define scopes for the settings',
				'keyThis' => 'id',
				'keyOther' => 'settingId',
				'subObjectType' => 'Axis360Scope',
				'structure' => $axis360ScopeStructure,
				'sortable' => false,
				'storeDb' => true,
				'allowEdit' => true,
				'canEdit' => true,
				'additionalOneToManyActions' => [],
				'canAddNew' => true,
				'canDelete' => true,
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}

	public function __toString() {
		return 'Library ' . $this->libraryPrefix . ' (' . $this->apiUrl . ')';
	}

	public function update(string $context = '') : int|bool {
		$ret = parent::update();
		if ($ret !== FALSE) {
			$this->saveScopes();
		}
		return $ret;
	}

	public function insert(string $context = '') : int|bool {
		$ret = parent::insert();
		if ($ret !== FALSE) {
			if (empty($this->_scopes)) {
				$this->_scopes = [];
				$allScope = new Axis360Scope();
				$allScope->settingId = $this->id;
				$allScope->name = "All Records";
				$this->_scopes[] = $allScope;
			}
			$this->saveScopes();
		}
		return $ret;
	}

	public function saveScopes() : void {
		if (isset ($this->_scopes) && is_array($this->_scopes)) {
			$this->saveOneToManyOptions($this->_scopes, 'settingId');
			unset($this->_scopes);
		}
	}

	public function __get($name) {
		if ($name == "scopes") {
			if (!isset($this->_scopes) && $this->id) {
				$this->_scopes = [];
				$scope = new Axis360Scope();
				$scope->settingId = $this->id;
				$scope->find();
				while ($scope->fetch()) {
					$this->_scopes[$scope->id] = clone($scope);
				}
			}
			return $this->_scopes;
		} else {
			return parent::__get($name);
		}
	}

	public function __set($name, $value) {
		if ($name == "scopes") {
			$this->_scopes = $value;
		} else {
			parent::__set($name, $value);
		}
	}
}