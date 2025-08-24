<?php /** @noinspection PhpMissingFieldTypeInspection */
require_once ROOT_DIR . '/sys/Events/LibraryEventsSetting.php';
require_once ROOT_DIR . '/sys/Events/EventsBranchMapping.php';


/**
 * Settings for Communico integration
 */
class CommunicoSetting extends DataObject {
	public $__table = 'communico_settings';
	public $id;
	public $name;
	public $baseUrl;
	public /** @noinspection PhpUnused */
		$clientId;
	public /** @noinspection PhpUnused */
		$clientSecret;
	public $eventsInLists;
	/** @noinspection PhpUnused */
	public $lastUpdateOfAllEvents;
	public $bypassAspenEventPages;
	public $registrationModalBody;
	public $registrationModalBodyApp;
	public $username;
	public $password;
	public $numberOfDaysToIndex;

	private $_libraries;
	private $_locationMap;


	static $_objectStructure = [];
	static function getObjectStructure(string $context = ''): array {
		if (isset(self::$_objectStructure[$context]) && self::$_objectStructure[$context] !== null) {
			return self::$_objectStructure[$context];
		}
		$libraryList = Library::getLibraryList(!UserAccount::userHasPermission('Administer Communico Settings'));

		$branchMapStructure = EventsBranchMapping::getObjectStructure($context);

		/** @noinspection HtmlRequiredAltAttribute */
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
				'description' => 'A name for the settings',
			],
			'baseUrl' => [
				'property' => 'baseUrl',
				'type' => 'url',
				'label' => 'Base URL (i.e. https://attend.yoursite.com/events)',
				'description' => 'The URL for the site',
			],
			'clientId' => [
				'property' => 'clientId',
				'type' => 'text',
				'label' => 'Client Key',
				'description' => 'Client Key',
			],
			'clientSecret' => [
				'property' => 'clientSecret',
				'type' => 'storedPassword',
				'label' => 'Client Secret',
				'description' => 'Client Secret',
				'maxLength' => 36,
				'hideInLists' => true,
			],
			'numberOfDaysToIndex' => [
				'property' => 'numberOfDaysToIndex',
				'type' => 'integer',
				'label' => 'Number of Days To Index',
				'description' => 'The number of days into the future to index',
				'default' => 365,
				'minimum' => 30,
			],
			'eventsInLists' => [
				'property' => 'eventsInLists',
				'type' => 'enum',
				'label' => 'Events in Lists',
				'description' => 'Allow/Disallow certain users to add events to lists',
				'values' => [
					'1' => 'Allow staff to add events to lists',
					'2' => 'Allow all users to add events to lists',
					'0' => 'Do not allow adding events to lists',
				],
				'default' => '1',
			],
			'bypassAspenEventPages' => [
				'property' => 'bypassAspenEventPages',
				'type' => 'checkbox',
				'label' => 'Bypass event pages in Aspen',
				'description' => 'Whether or not a user will be redirected to an Aspen event page or the page for the native event platform.',
				'default' => 0,
			],
			'lastUpdateOfAllEvents' => [
				'property' => 'lastUpdateOfAllEvents',
				'type' => 'timestamp',
				'label' => 'Last Full Index (clear to force a new full index)',
				'description' => 'When all events were last indexed',
			],
			'registrationModalBody' => [
				'property' => 'registrationModalBody',
				'type' => 'html',
				'label' => 'Registration Modal Body',
				'description' => 'The body of the modal for event registration information',
				'allowableTags' => '<p><em><i><strong><b><a><ul><ol><li><h1><h2><h3><h4><h5><h6><h7><pre><code><hr><table><tbody><tr><th><td><caption><img><br><div><span><sub><sup>',
				'hideInLists' => true,
			],
			'registrationModalBodyApp' => [
				'property' => 'registrationModalBodyApp',
				'type' => 'textarea',
				'label' => 'Registration Information to Show in Aspen LiDA',
				'description' => 'The body of the modal for event registration in Aspen LiDA',
				'hideInLists' => true,
				'maxLength' => 500,
				'note' => '500 character limit. HTML is not allowed.',
			],

			'libraries' => [
				'property' => 'libraries',
				'type' => 'multiSelect',
				'listStyle' => 'checkboxSimple',
				'label' => 'Libraries',
				'description' => 'Define libraries that use these settings',
				'values' => $libraryList,
				'hideInLists' => true,
			],

			'locationMappingSection' => [
				'property' => 'locationMappingSection',
				'type' => 'section',
				'label' => 'Location Mapping',
				'properties' => [
					'locationMap' => [
						'property' => 'locationMap',
						'type' => 'oneToMany',
						'label' => 'Location Map',
						'description' => 'The mapping of library location names for Aspen and events.',
						'keyThis' => 'id',
						'subObjectType' => 'EventsBranchMapping',
						'structure' => $branchMapStructure,
						'storeDb' => true,
						'sortable' => false,
						'allowEdit' => false,
						'canEdit' => false,
						'canAddNew' => false,
						'canDelete' => false,
					],
				],
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}

	/**
	 * Override the update functionality to save related objects
	 *
	 * @see DB/DB_DataObject::update()
	 */
	public function update(string $context = '') : int|bool {
		$ret = parent::update();
		if ($ret !== FALSE) {
			$this->saveLibraries();
			$this->saveLocationMap();
		}
		return $ret;
	}

	/**
	 * Override the insert functionality to save the related objects
	 *
	 * @see DB/DB_DataObject::insert()
	 */
	public function insert(string $context = '') : int|bool {
		$ret = parent::insert();
		if ($ret !== FALSE) {
			$this->saveLibraries();
		}
		return $ret;
	}

	public function __get($name) {
		if ($name == "libraries") {
			return $this->getLibraries();
		} if ($name == "locationMap") {
			return $this->getLocationMap();
		}else {
			return parent::__get($name);
		}
	}

	public function __set($name, $value) {
		if ($name == "libraries") {
			$this->_libraries = $value;
		} else {
			parent::__set($name, $value);
		}
	}

	public function delete(bool $useWhere = false, bool $hardDelete = false) : bool|int {
		$ret = parent::delete($useWhere, $hardDelete);
		if ($ret && !empty($this->id)) {
			$this->clearLibraries();
		}
		return $ret;
	}

	public function getLibraries() : ?array {
		if (!isset($this->_libraries) && $this->id) {
			$this->_libraries = [];
			$library = new LibraryEventsSetting();
			$library->settingSource = 'communico';
			$library->settingId = $this->id;
			$library->find();
			while ($library->fetch()) {
				$this->_libraries[$library->libraryId] = $library->libraryId;
			}
		}
		return $this->_libraries;
	}

	public function getLocationMap() : array {
		if (!isset($this->_locationMap)) {
			//Get the list of translation maps
			$this->_locationMap = [];
			$locationMap = new EventsBranchMapping();
			$locationMap->orderBy('id');
			$locationMap->find();
			while ($locationMap->fetch()) {
				$this->_locationMap[$locationMap->id] = clone($locationMap);
			}
		}
		return $this->_locationMap;
	}

	public function saveLibraries() : void {
		if (isset($this->_libraries) && is_array($this->_libraries)) {
			$this->clearLibraries();

			foreach ($this->_libraries as $libraryId) {
				$libraryEventSetting = new LibraryEventsSetting();

				$libraryEventSetting->settingSource = 'communico';
				$libraryEventSetting->settingId = $this->id;
				$libraryEventSetting->libraryId = $libraryId;
				$libraryEventSetting->insert();
			}
			unset($this->_libraries);
		}
	}

	public function saveLocationMap() : void {
		if (isset($this->_locationMap)) {
			foreach ($this->_locationMap as $location) {
				$locationMap = new EventsBranchMapping();
				$locationMap->locationId = $location->locationId;
				if ($locationMap->find(true)){
					$locationMap->eventsLocation = $location->eventsLocation;
					$locationMap->update();
				}
			}
			unset($this->_locationMap);
		}
	}

	private function clearLibraries() : void {
		//Delete links to the libraries
		$libraryEventSetting = new LibraryEventsSetting();
		$libraryEventSetting->settingSource = 'communico';
		$libraryEventSetting->settingId = $this->id;
		$libraryEventSetting->delete(true);
	}
}