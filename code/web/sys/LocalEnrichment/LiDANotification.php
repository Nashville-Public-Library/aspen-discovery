<?php

require_once ROOT_DIR . '/sys/DB/LibraryLocationLinkedObject.php';
require_once ROOT_DIR . '/sys/LocalEnrichment/LiDANotificationLibrary.php';
require_once ROOT_DIR . '/sys/LocalEnrichment/LiDANotificationLocation.php';
require_once ROOT_DIR . '/sys/LocalEnrichment/LiDANotificationPType.php';

require_once ROOT_DIR . '/sys/Account/User.php';
require_once ROOT_DIR . '/sys/Account/UserNotificationToken.php';
require_once ROOT_DIR . '/sys/AspenLiDA/LocationSetting.php';

class LiDANotification extends DB_LibraryLocationLinkedObject {
	public $__table = 'aspen_lida_notifications';
	public $id;
	public $title;
	public $message;
	public $sendOn;
	public $expiresOn;
	public $ctaUrl;
	public $linkType;
	public $deepLinkPath;
	public $deepLinkId;
	public $sent;

	protected $_libraries;
	protected $_locations;
	protected $_ptypes;

	static function getObjectStructure($context = ''): array {
		$libraryList = Library::getLibraryList(!UserAccount::userHasPermission('Send Notifications to All Libraries'));
		$locationList = Location::getLocationList(!UserAccount::userHasPermission('Send Notifications to All Locations') || UserAccount::userHasPermission('Send Notifications to Home Library Locations'));
		$ptypeList = PType::getPatronTypeList();

		$ctaType = [
			0 => 'A specific screen in the app',
			1 => 'An external website',
		];
		require_once ROOT_DIR . '/sys/AspenLiDA/LocationSetting.php';
		$ctaScreens = LocationSetting::getDeepLinks();
		$messageLimits = "<p>Character limits before being truncated</p><ul><li>iOS: 178 characters (includes both title and message)</li><li>Android (if collapsed, default): 43 characters for message, 39 characters for title</li><li>Android (if expanded): 504 characters for message, 79 characters for title</li></ul>";

		return [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id',
			],
			'title' => [
				'property' => 'title',
				'type' => 'text',
				'label' => 'Title',
				'description' => 'The title of the notification',
				'required' => true,
			],
			'message' => [
				'property' => 'message',
				'type' => 'markdown',
				'label' => 'Message',
				'description' => 'The body of the notification',
				'hideInLists' => true,
				'required' => true,
				'note' => 'HTML tags are not permitted and will be stripped out',
			],
			'messageLimits' => [
				'property' => 'messageLimits',
				'type' => 'label',
				'label' => $messageLimits,
				'hideInLists' => true,
				'canSort' => false,
			],
			'sendOn' => [
				'property' => 'sendOn',
				'type' => 'timestamp',
				'label' => 'Sends on',
				'description' => 'When to send the notification to users',
				'required' => true,
			],
			'expiresOn' => [
				'property' => 'expiresOn',
				'type' => 'timestamp',
				'label' => 'Expires on',
				'description' => 'The time the notification will expire',
				'note' => 'If left blank, expiration will be set to 7 days from send time',
			],
			'linkType' => [
				'property' => 'linkType',
				'type' => 'enum',
				'label' => 'On tap, send user to',
				'values' => $ctaType,
				'default' => 0,
				'onchange' => 'return AspenDiscovery.Admin.getUrlOptions();',
				'hideInLists' => true,
				'canSort' => false,
			],
			'deepLinkPath' => [
				'property' => 'deepLinkPath',
				'type' => 'enum',
				'label' => 'Aspen LiDA Screen',
				'values' => $ctaScreens,
				'default' => 'home',
				'onchange' => 'return AspenDiscovery.Admin.getDeepLinkFullPath();',
				'hideInLists' => true,
				'canSort' => false,
			],
			'deepLinkId' => [
				'property' => 'deepLinkId',
				'type' => 'text',
				'label' => 'Id for Object',
				'hideInLists' => true,
				'canSort' => false,
			],
			'ctaUrl' => [
				'property' => 'ctaUrl',
				'type' => 'url',
				'label' => 'External URL',
				'description' => 'A URL for users to be redirected to when opening the notification',
				'hideInLists' => true,
				'canSort' => false,
			],
			'libraries' => [
				'property' => 'libraries',
				'type' => 'multiSelect',
				'listStyle' => 'checkboxSimple',
				'label' => 'Libraries',
				'description' => 'Define libraries that see this notification',
				'values' => $libraryList,
				'hideInLists' => true,
			],
			'locations' => [
				'property' => 'locations',
				'type' => 'multiSelect',
				'listStyle' => 'checkboxSimple',
				'label' => 'Locations',
				'description' => 'Define locations that use this notification',
				'values' => $locationList,
				'hideInLists' => true,
			],
			'ptypes' => [
				'property' => 'ptypes',
				'type' => 'multiSelect',
				'listStyle' => 'checkboxSimple',
				'label' => 'Patron Types',
				'description' => 'Define what patron types should receive this notification',
				'values' => $ptypeList,
				'hideInLists' => true,
			],
			'sent' => [
				'property' => 'sent',
				'type' => 'checkbox',
				'label' => 'Notification sent',
				'description' => 'Whether or not the system has processed and sent the notification',
				'note' => 'Need to resend? Uncheck to trigger a new notification',
			],
		];
	}

	public function getNumericColumnNames(): array {
		return [
			'sendOn',
			'expiresOn',
		];
	}

	public function __get($name) {
		if ($name == "libraries") {
			return $this->getLibraries();
		} elseif ($name == "locations") {
			return $this->getLocations();
		} elseif ($name == "ptypes") {
			return $this->getPatronTypes();
		} else {
			return parent::__get($name);
		}
	}

	public function __set($name, $value) {
		if ($name == "libraries") {
			$this->_libraries = $value;
		} elseif ($name == "locations") {
			$this->_locations = $value;
		} elseif ($name == "ptypes") {
			$this->_ptypes = $value;
		} else {
			parent::__set($name, $value);
		}
	}

	public function update($context = '') {
		$ret = parent::update();
		if ($ret !== FALSE) {
			$this->saveLibraries();
			$this->saveLocations();
			$this->savePatronTypes();
		}
		return $ret;
	}

	public function insert($context = '') {
		$ret = parent::insert();
		if ($ret !== FALSE) {
			$this->saveLibraries();
			$this->saveLocations();
			$this->savePatronTypes();
		}
		return $ret;
	}

	public function delete($useWhere = false, $hardDelete = false) : int {
		$ret = parent::delete($useWhere, $hardDelete);
		if ($ret && !empty($this->id)) {
			$this->clearLibraries();
			$this->clearLocations();
			$this->clearPatronTypes();
		}
		return $ret;
	}

	public function getLocations(): ?array {
		if (!isset($this->_locations) && $this->id) {
			$this->_locations = [];
			$locationLink = new LiDANotificationLocation();
			$locationLink->lidaNotificationId = $this->id;
			$locationLink->find();
			while ($locationLink->fetch()) {
				$this->_locations[$locationLink->locationId] = $locationLink->locationId;
			}
		}
		return $this->_locations;
	}

	public function getLibraries(): ?array {
		if (!isset($this->_libraries) && $this->id) {
			$this->_libraries = [];
			$libraryLink = new LiDANotificationLibrary();
			$libraryLink->lidaNotificationId = $this->id;
			$libraryLink->find();
			while ($libraryLink->fetch()) {
				$this->_libraries[$libraryLink->libraryId] = $libraryLink->libraryId;
			}
		}
		return $this->_libraries;
	}

	public function getPatronTypes(): ?array {
		if (!isset($this->_ptypes) && $this->id) {
			$this->_ptypes = [];
			$patronLink = new LiDANotificationPType();
			$patronLink->lidaNotificationId = $this->id;
			$patronLink->find();
			while ($patronLink->fetch()) {
				$this->_ptypes[$patronLink->patronTypeId] = $patronLink->patronTypeId;
			}
		}
		return $this->_ptypes;
	}

	public function saveLibraries() {
		if (isset ($this->_libraries) && is_array($this->_libraries)) {
			$this->clearLibraries();

			foreach ($this->_libraries as $libraryId) {
				$obj = new LiDANotificationLibrary();
				$obj->lidaNotificationId = $this->id;
				$obj->libraryId = $libraryId;
				$obj->insert();
			}
			unset($this->_libraries);
		}
	}

	public function saveLocations() {
		if (isset ($this->_locations) && is_array($this->_locations)) {
			$this->clearLocations();

			foreach ($this->_locations as $locationId) {
				$obj = new LiDANotificationLocation();
				$obj->lidaNotificationId = $this->id;
				$obj->locationId = $locationId;
				$obj->insert();
			}
			unset($this->_locations);
		}
	}

	public function savePatronTypes() {
		if (isset ($this->_ptypes) && is_array($this->_ptypes)) {
			$this->clearPatronTypes();

			foreach ($this->_ptypes as $ptypeId) {
				$obj = new LiDANotificationPType();
				$obj->lidaNotificationId = $this->id;
				$obj->patronTypeId = $ptypeId;
				$obj->insert();
			}
			unset($this->_ptypes);
		}
	}

	private function clearLibraries() {
		$lib = new LiDANotificationLibrary();
		$lib->lidaNotificationId = $this->id;
		return $lib->delete(true);
	}

	private function clearLocations() {
		$loc = new LiDANotificationLocation();
		$loc->lidaNotificationId = $this->id;
		return $loc->delete(true);
	}

	private function clearPatronTypes() {
		$pType = new LiDANotificationPType();
		$pType->lidaNotificationId = $this->id;
		return $pType->delete(true);
	}

	public function okToExport(array $selectedFilters): bool {
		return parent::okToExport($selectedFilters);
	}

	public function getEligibleUsers() {
		$users = [];
		$tokens = [];

		$libraryForNotifications = new LiDANotificationLibrary();
		$libraryForNotifications->lidaNotificationId = $this->id;
		$libraries = $libraryForNotifications->fetchAll('libraryId');

		$locationForNotifications = new LiDANotificationLocation();
		$locationForNotifications->lidaNotificationId = $this->id;
		$locations = $locationForNotifications->fetchAll('locationId');

		$ptypesForNotifications = new LiDANotificationPType();
		$ptypesForNotifications->lidaNotificationId = $this->id;
		$ptypes = $ptypesForNotifications->fetchAll('patronTypeId');

		$eligiblePTypes = [];
		foreach($ptypes as $ptype) {
			$eligiblePType = new PType();
			$eligiblePType->id = $ptype;
			if($eligiblePType->find(true)) {
				$eligiblePTypes[] = $eligiblePType->pType;
			}
		}

		$allTokens = new UserNotificationToken();
		$allTokens->notifyCustom = 1;
		$userTokens = $allTokens->fetchAll('userId');

		foreach($userTokens as $userToken) {
			$eligibleUser = new User();
			$eligibleUser->id = $userToken;
			if($eligibleUser->find(true)) {
				$homeLocation = $eligibleUser->getHomeLocation();
				$homeLibrary = $eligibleUser->getHomeLibrary();
				if(in_array($eligibleUser->patronType, $eligiblePTypes)) {
					if (in_array($homeLocation->locationId, $locations) && in_array($homeLibrary->libraryId, $libraries)) {
						$users[] = $userToken;
					}
				}
			}
		}

		foreach($users as $user) {
			$eligibleUser = new User();
			$eligibleUser->id = $user;
				if($eligibleUser->find(true)) {
					$token = new UserNotificationToken();
					$token->userId = $user;
					$token->notifyCustom = 1;
					$allUserTokens = $token->fetchAll('pushToken');
					foreach ($allUserTokens as $userToken) {
						$tmpToken['uid'] = $user;
						$tmpToken['token'] = $userToken;
						$tokens[] = $tmpToken;
					}
					$token->__destruct();
					$token = null;
				}
			}

		return $tokens;
	}
}