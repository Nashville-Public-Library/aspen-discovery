<?php
requireSystemLibrariesAspen();

$timer->logTime("Finished load library and location");
loadSearchInformation();

spl_autoload_register('aspen_autoloader', true, false);

function requireSystemLibrariesAspen() {
	// Require System Libraries
	require_once ROOT_DIR . '/sys/SearchObject/SearchObjectFactory.php';
	require_once ROOT_DIR . '/RecordDrivers/RecordDriverFactory.php';
}

function loadSearchInformation() {
	//Determine the Search Source, need to do this always.
	global $searchSource;
	global $library;
	global $configArray;

	$module = (isset($_GET['module'])) ? $_GET['module'] : null;
	$module = preg_replace('/[^\w]/', '', $module);

	$searchSource = 'global';
	if (!empty($_GET['searchSource'])) {
		if (is_array($_GET['searchSource'])) {
			$_GET['searchSource'] = reset($_GET['searchSource']);
		}
		$searchSource = $_GET['searchSource'];

		require_once(ROOT_DIR . '/Drivers/marmot_inc/SearchSources.php');
		$searchSources = new SearchSources();
		$validSearchSources = $searchSources->getSearchSources();
		//Validate that we got a good search source
		if (!array_key_exists($searchSource, $validSearchSources)) {
			$searchSource = 'local';
		}

		$_REQUEST['searchSource'] = $searchSource; //Update request since other check for it here
		$_SESSION['searchSource'] = $searchSource; //Update the session so we can remember what the user was doing last.
	} else {
		if (!empty($_SESSION['searchSource'])) { //Didn't get a source, use what the user was doing last
			$searchSource = $_SESSION['searchSource'];
			$_REQUEST['searchSource'] = $searchSource;
		} else {
			//Use a default search source
			if ($module == 'Person') {
				$searchSource = 'genealogy';
			} elseif ($module == 'OpenArchives') {
				$searchSource = 'open_archives';
			} elseif ($module == 'List') {
				$searchSource = 'lists';
			} elseif ($module == 'EBSCO') {
				$searchSource = 'ebsco_eds';
			} else {
				require_once(ROOT_DIR . '/Drivers/marmot_inc/SearchSources.php');
				$searchSources = new SearchSources();
				global $locationSingleton;
				$location = $locationSingleton->getActiveLocation();
				[
					$enableCombinedResults,
					$showCombinedResultsFirst,
				] = $searchSources::getCombinedSearchSetupParameters($location, $library);
				if ($enableCombinedResults && $showCombinedResultsFirst) {
					$searchSource = 'combined';
				} else {
					$searchSource = 'local';
				}
			}
			$_REQUEST['searchSource'] = $searchSource;
		}
	}

	/** @var Library $searchLibrary */
	$searchLibrary = Library::getSearchLibrary($searchSource);
	$searchLocation = Location::getSearchLocation($searchSource);

	if ($searchSource == 'marmot' || $searchSource == 'global') {
		$searchSource = $searchLibrary->subdomain;
	}

	//Based on the search source, determine the search scope and set a global variable
	global $solrScope;
	global $scopeType;
	global $isGlobalScope;
	$solrScope = false;
	$scopeType = '';
	$isGlobalScope = false;

	if ($searchLibrary) {
		$solrScope = $searchLibrary->subdomain;
		$scopeType = 'Library';
		if ($searchLibrary->isConsortialCatalog) {
			$isGlobalScope = true;
		}
	}
	if ($searchLocation && $searchLibrary->getNumSearchLocationsForLibrary() > 1) {
		if ($searchLibrary && strtolower($searchLocation->code) == $solrScope) {
			$solrScope .= 'loc';
		} else {
			$solrScope = strtolower($searchLocation->code);
		}
		if (!empty($searchLocation->subLocation)) {
			$solrScope = strtolower($searchLocation->subLocation);
		}
		$scopeType = 'Location';
	}

	$solrScope = trim($solrScope);
	$solrScope = preg_replace('/[^a-zA-Z0-9_]/', '', $solrScope);
	if (strlen($solrScope) == 0) {
		$solrScope = false;
		$scopeType = 'Unscoped';
	}

	$searchLibrary = Library::getSearchLibrary($searchSource);
	$searchLocation = Location::getSearchLocation($searchSource);

	global $millenniumScope;
	if ($library) {
		if ($searchLibrary) {
			$millenniumScope = $searchLibrary->scope;
		} elseif (isset($searchLocation)) {
			Millennium::$scopingLocationCode = $searchLocation->code;
		} else {
			$millenniumScope = isset($configArray['OPAC']['defaultScope']) ? $configArray['OPAC']['defaultScope'] : '93';
		}
	} else {
		$millenniumScope = isset($configArray['OPAC']['defaultScope']) ? $configArray['OPAC']['defaultScope'] : '93';
	}

	//Load indexing profiles
	require_once ROOT_DIR . '/sys/Indexing/IndexingProfile.php';
	global $indexingProfiles;
	$indexingProfiles = [];
	$indexingProfile = new IndexingProfile();
	$indexingProfile->orderBy('name');
	$indexingProfile->find();
	while ($indexingProfile->fetch()) {
		$indexingProfiles[$indexingProfile->name] = clone($indexingProfile);
	}
	require_once ROOT_DIR . '/sys/Indexing/SideLoad.php';
	/** @var $indexingProfiles IndexingProfile[] */ global $sideLoadSettings;
	$sideLoadSettings = [];
	try {
		$sideLoadSetting = new SideLoad();
		$sideLoadSetting->orderBy('name');
		$sideLoadSetting->find();
		while ($sideLoadSetting->fetch()) {
			$sideLoadSettings[strtolower($sideLoadSetting->name)] = clone($sideLoadSetting);
		}
	} catch (PDOException $e) {
		//Ignore, the tables have not been created yet.
	}
}

// Set up autoloader (needed for YAML)
function aspen_autoloader($class) {
	if (substr($class, 0, 4) == 'CAS_') {
		if (CAS_autoload($class)) {
			return;
		}
	}
	// Don't get involved if we're being called for a SimpleSAML method
	if (substr($class, 0, 10) == 'SimpleSAML' || substr($class, 0, 6) == 'sspmod') {
		return;
	}
	if (strpos($class, '.php') > 0) {
		$class = substr($class, 0, strpos($class, '.php'));
	}
	$nameSpaceClass = str_replace('_', '/', $class) . '.php';
	try {
		if (strpos($class, 'Smarty_') === 0) {
			Smarty_Autoloader::autoload($class);
			return;
		} elseif (strpos($class, 'PHPUnit') === 0) {
			return;
		} elseif (file_exists('sys/' . $class . '.php')) {
			$className = ROOT_DIR . '/sys/' . $class . '.php';
			require_once $className;
		} elseif (file_exists('sys/Account/' . $class . '.php')) {
			$className = ROOT_DIR . '/sys/Account/' . $class . '.php';
			require_once $className;
		} elseif (file_exists('Drivers/' . $class . '.php')) {
			$className = ROOT_DIR . '/Drivers/' . $class . '.php';
			require_once $className;
		} elseif (file_exists('services/MyAccount/lib/' . $class . '.php')) {
			$className = ROOT_DIR . '/services/MyAccount/lib/' . $class . '.php';
			require_once $className;
		} else {
			require_once $nameSpaceClass;
		}
	} catch (Exception $e) {
		AspenError::raiseError("Error loading class $class");
	}
}