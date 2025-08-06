<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../bootstrap_aspen.php';
require_once ROOT_DIR . '/sys/Talpa/TalpaSettings.php';
require_once ROOT_DIR . '/sys/SearchObject/TalpaSearcher.php';
require_once ROOT_DIR . '/sys/Grouping/GroupedWork.php';
require_once ROOT_DIR . '/RecordDrivers/GroupedWorkDriver.php';
require_once ROOT_DIR . '/sys/Talpa/TalpaData.php';
require_once ROOT_DIR . '/sys/ISBN.php';

$startTime = time();
$talpaWorkAPI ='https://www.librarything.com/api_aspen_works_v2.php';

//Set up Globals
global $configArray;
global $serverName;
global $interface;
global $aspen_db;
global $logger;

global $enabledModules;
if (!array_key_exists('Talpa Search', $enabledModules)) {
	$logger->log("Talpa module not enabled, quitting", Logger::LOG_NOTICE);
	return;
}

//Since this is run generically for an interface and is not library-specific, it needs to be run for each setting
$talpaSettings = new TalpaSettings();
if (!$talpaSettings->find(true)) {
	$logger->log("No Talpa settings found, exiting", Logger::LOG_ERROR);
	exit(1);
}

$token = $talpaSettings->talpaApiToken;
$logger->log("Running Talpa groupedWorks cron for settings " . $talpaSettings->id, Logger::LOG_NOTICE);

$noIsbns = 0;

$noIsbnA = array();

//Get all Grouped works:
$results = $aspen_db->query('SELECT COUNT(1) AS total
									FROM grouped_work gw
									LEFT JOIN talpa_ltwork_to_groupedwork ltg
										ON gw.permanent_id = ltg.groupedRecordPermanentId
									WHERE LENGTH(gw.permanent_id) > 36 AND ltg.groupedRecordPermanentId IS NULL AND (checked = 0 OR checked IS NULL)
							');


if ($results) {
	while ($result = $results->fetch()) {
		$logger->log('found '. $result['total'] . ' permanent IDs to send to Talpa for processing', Logger::LOG_NOTICE);
	}
}

$results = $aspen_db->query('SELECT gw.permanent_id, ltg.id
									FROM grouped_work gw
									LEFT JOIN talpa_ltwork_to_groupedwork ltg
										ON gw.permanent_id = ltg.groupedRecordPermanentId
									WHERE LENGTH(gw.permanent_id) > 36 AND ltg.groupedRecordPermanentId IS NULL AND (checked = 0 OR checked IS NULL)
							');

	$permanent_ids = array();
	$ids = array();
	$retA = array();
	$BATCH_SIZE = 25;
	$batchN = 0;
	$seenN = 0;
	$updatedN = 0;
	$insertedN = 0;

if ($results) {
	while ($result = $results->fetch()) {
		$seenN++;
		$permanent_ids[] = $result['permanent_id'];
		if (!empty($result['id'])) {
			$ids[] = $result['id'];
		}

		if( count($permanent_ids) > $BATCH_SIZE ) {
			$logger->log("getting works for batch ". $batchN. ' of size:'. $BATCH_SIZE, Logger::LOG_DEBUG);

				// mark that we have checked these works
				if (!empty($ids)) {
					$sql = 'UPDATE talpa_ltwork_to_groupedwork SET checked=1 WHERE id IN (' . implode(',', $ids) . ')';
					$results_update = $aspen_db->query($sql);
				}

			foreach ($permanent_ids as $permanent_id) {
				$groupedWork = new GroupedWork();
				$groupedWork->permanent_id = $permanent_id;
				if ($groupedWork->find(true)) {
					$groupedWorkDriver = new GroupedWorkDriver($groupedWork->permanent_id);

					//All Fields
					$fields = $groupedWorkDriver->getFields();


					$isbnA = array();
					//ISBN Data
					$primaryISBN = $groupedWorkDriver->getPrimaryISBN();

					if(!$primaryISBN  && isset($fields['primary_isbn'] )) {
						$primaryISBN = $fields['primary_isbn'];
					}
					if( $primaryISBN ) {
						$primaryIsbnObj = new ISBN($primaryISBN);
						if($primaryIsbnObj->isValid()) {
							$isbnA[]= $primaryISBN;
						}
					}


					$allIsbns = $groupedWorkDriver->getISBNs();

					if(!$allIsbns  && isset($fields['isbn'])) {
						$allIsbns = $fields['isbn'];
					}
					if ($allIsbns) {
						foreach ($allIsbns as $rawIsbn) {
							$isbn = '';
							$isbnObj = new ISBN($rawIsbn);
							if ($isbnObj->isValid() && !in_array($rawIsbn, $isbnA)) {
								$isbnA[] = $rawIsbn;
							} elseif (strlen($rawIsbn) == 9) {//When items are indexed into SOLR, the checksum X is removed.
								$_isbn = $rawIsbn . $isbnObj->getISBN10CheckDigit($rawIsbn);
								$convertedIsbn = new ISBN($_isbn);
								if ($convertedIsbn->isValid() && !in_array($rawIsbn, $isbnA)) {
									$isbnA[] = $_isbn;
								}
							} elseif (strlen($rawIsbn) == 11) { //Addressing a bug where an 11th digit is added to valid ISBNs
								$_isbn = substr($rawIsbn, 0, 10);
								$convertedIsbn = new ISBN($_isbn);
								if ($convertedIsbn->isValid() && !in_array($rawIsbn, $isbnA)) {
									$isbnA[] = $_isbn;
								}
							}
						}
					}


					//Check if we have either ISBNs or UPCs to process this record
					$upcA = $groupedWorkDriver->getUpcs();

					$hasIsbns = !empty($isbnA);
					$hasUpcs = !empty($upcA);

					if(!$hasIsbns && !$hasUpcs) {
						// We can't use it - no identifiers
						$noIsbnA[]= $permanent_id;
						$noIsbns++;
						continue;
					}

					if($hasIsbns) {
						$retA[$permanent_id]['isbnA'] = $isbnA;
					}
					if ($hasUpcs) {
						$retA[$permanent_id]['upcA'][] =$upcA;
					}

					//Title and Author
					$primaryAuthor = $groupedWorkDriver->getPrimaryAuthor();
					if(!$primaryAuthor) {
						$primaryAuthor = isset($fields['author2Str'])? $fields['author2Str']:'';
					}
					$retA[$permanent_id]['primary_author'] = $primaryAuthor;

					//secondary author
					$auth2A = array();
					if(isset($fields['auth_author2'])) {
						$auth2A = $fields['auth_author2'];
					}
					$retA[$permanent_id]['secondary_author_or_contributorA'] = $auth2A;
					$title = $groupedWorkDriver->getTitle();
					if(!$title){
						$title =isset($fields['title_display']) ? $fields['title_display'] : '';
					}
					if(!$title){
						$title =isset($fields['base_title']) ? $fields['base_title'] : '';
					}
					if(!$title){
						$title = isset($fields['title_short']) ? $fields['title_short'] : '';
					}
					if($title){
						$retA[$permanent_id]['base_title'] = $title;
					}

					$retA[$permanent_id]['full_titleA'] = isset($fields['title_full']) ? $fields['title_full'] : array();

					//Contributors
					$retA[$permanent_id]['contributorsA'][] = $groupedWorkDriver->getContributors();

					//ISSNS
					$retA[$permanent_id]['issnA'][] = $groupedWorkDriver->getISSNs();

					$groupedWorkDriver = null;

				} else {
					$logger->log('failed to fetch info for grouped work '.$permanent_id, Logger::LOG_ERROR);
				}
				$groupedWork = null;
			}
			$batchN++;

			//batch up the requests to librarything
			$chunks = array_chunk($retA, 50, true);
			foreach ($chunks as $chunk) {
				$data = array(
					'works' => $chunk,
					'token' => $token,
					'type' => 'daily',
					'aspen_version' => getAspenVersion(),
					'api_version' => 2,
				);

				$logger->log('Sending '.count($chunk).' records', Logger::LOG_DEBUG);

				$curlConnection = curl_init($talpaWorkAPI);
				curl_setopt($curlConnection, CURLOPT_CONNECTTIMEOUT, 15);
				curl_setopt($curlConnection, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curlConnection, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($curlConnection, CURLOPT_TIMEOUT, 60);
				curl_setopt($curlConnection, CURLOPT_RETURNTRANSFER, true);

				// Set cURL options to use POST and send data in the request body
				curl_setopt($curlConnection, CURLOPT_POST, true);
				curl_setopt($curlConnection, CURLOPT_POSTFIELDS, http_build_query($data));

				$curl_result = curl_exec($curlConnection);
				if ($curl_result === false) {
					throw new Exception("Error in HTTP Request: " . curl_error($curlConnection));
				}

				$resA = json_decode($curl_result, true);
				curl_close($curlConnection);
				$logger->log($resA, Logger::LOG_DEBUG, true);
				if($resA['status']=='ok') {
					foreach ($chunk as $permanent_id => $data) {
						$talpaData = new TalpaData();
						$talpaData->groupedRecordPermanentId = $permanent_id;
						if ($talpaData->find(true)) {
							$talpaData->checked = 1;
							$talpaData->update();
							$updatedN++;
						} else {
							$talpaData->groupedRecordPermanentId = $permanent_id;
							$talpaData->checked = 1;
							$talpaData->insert();
							$insertedN++;
						}
						$talpaData->__destruct();
						$talpaData = null;
						$logger->log('Successfully sent ' . count($chunk) . ' records to Talpa Search for processing.', Logger::LOG_DEBUG);
					}
				}else {
						$logger->log("Talpa Search encountered an error while attempting to parse your records.", Logger::LOG_DEBUG);
					}
			} // foreach chunksA

			// reset aggregator arrays
			$retA = array();
			$permanent_ids = array();
			$ids = array();
		} // if we have enough in batch
	} // each row needing LT work
}
$results->closeCursor();
$endTime = time();

$logger->log("seenN: ".$seenN , Logger::LOG_NOTICE);
$logger->log("total time: ".($endTime - $startTime), Logger::LOG_NOTICE);
