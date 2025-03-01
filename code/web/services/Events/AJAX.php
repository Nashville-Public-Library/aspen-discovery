<?php

require_once ROOT_DIR . '/JSON_Action.php';

class Events_AJAX extends JSON_Action {

	/** @noinspection PhpUnused */
	public function getEventTypesAndSubLocationsForLocation() {
		require_once ROOT_DIR . '/sys/Events/EventType.php';
		$result = [
			'success' => false,
			'title' => translate([
				'text' => "Error",
				'isAdminFacing' => true,
			]),
			'message' =>  translate([
				'text' => 'Unknown location',
				'isAdminFacing' => true,
			])
		];
		if (!empty($_REQUEST['locationId'])) {
			$eventTypeIds = EventType::getEventTypeIdsForLocation($_REQUEST['locationId']);
			$sublocations = Location::getEventSublocations($_REQUEST['locationId']);
			if (!empty($eventTypeIds)) {
				$result = [
					'success' => true,
					'eventTypeIds' => json_encode($eventTypeIds),
					'sublocations' => json_encode($sublocations),
				];
			} else {
				$result = [
					'success' => true,
					'eventTypeIds' => '',
					'title' => translate([
						'text' => "No available event types",
						'isAdminFacing' => true,
					]),
					'message' => translate([
						'text' => 'No event types are available for this location.',
						'isAdminFacing' => true,
					])
				];
			}
		}
		return $result;
	}

	/** @noinspection PhpUnused */
	public function getEventTypeFields() {
		require_once ROOT_DIR . '/sys/Events/EventType.php';
		$result = [
			'success' => false,
			'title' => translate([
				'text' => "Error",
				'isAdminFacing' => true,
			]),
			'message' =>  translate([
				'text' => 'Unknown event type.',
				'isAdminFacing' => true,
			])
		];
		if (!empty($_REQUEST['eventTypeId'])) {
			$eventType = new EventType();
			$eventType->id = $_REQUEST['eventTypeId'];
			if ($eventType->find(true)) {
				$fieldStructure = $eventType->getFieldSetFields();
				global $interface;
				$fieldHTML = [];
				foreach ($fieldStructure as $property) {
					$interface->assign('property', $property);
					$fieldHTML[] = $interface->fetch('DataObjectUtil/property.tpl');
				}
				$locations = $eventType->getLocations();
				$result = [
					'success' => true,
					'eventType' => $eventType->jsonSerialize(),
					'typeFields' => $fieldHTML,
					'locationIds' => json_encode(array_keys($locations)),
				];
			}
		}
		return $result;
	}

	public function exportUsageData() {
		require_once ROOT_DIR . '/services/Events/EventGraphs.php';
		$aspenUsageGraph = new Events_EventGraphs();
		$aspenUsageGraph->buildCSV();
	}

	public function iCalendarExport() {
		require_once ROOT_DIR . '/sys/Events/Event.php';
		require_once ROOT_DIR . '/sys/Events/EventInstance.php';
		$result = [
			'success' => false,
			'title' => translate([
				'text' => "Error",
				'isAdminFacing' => true,
			]),
			'message' =>  translate([
				'text' => 'Could not export event.',
				'isAdminFacing' => true,
			])
		];
		$eventId = $_REQUEST['eventId'] ?? '';
		$wholeSeries = $_REQUEST['wholeSeries'] ?? '';
		if (!empty($eventId)) {
			global $interface;
			global $configArray;
			$eventIdParts = explode("_", $eventId, 3);
			if (isset($eventIdParts[2])) {
				$eventInstance = new EventInstance();
				$eventInstance->id = $eventIdParts[2];
				$eventInstance->find(true);
				$eventInfo = $eventInstance->getParentEvent();
				$interface->assign('title', $eventInfo->title ?? '');
				$interface->assign('status', $eventInstance->status ? '' : "Cancelled");
				$description = $eventInfo->description ?? '';
				$description = preg_replace("/(<br\s?\/?>)|(<\/p>)/", "\n", $description);
				$description = strip_tags($description);
				$interface->assign('description', $description);
				$interface->assign('location', $eventInstance->getLocation() ?? '');
				$interface->assign('hours', (int)($eventInstance->length / 60));
				$interface->assign('minutes', $eventInstance->length % 60);
				$interface->assign('timezone', $configArray['Site']['timezone']);
				$startTime = $eventInstance->date . "T" . $eventInstance->time;
				$event = new stdClass();
				$event->date = preg_replace('/([-:])/', '', $startTime);
				$event->uid = $eventId;
				$event->sublocation = $eventInstance->getSublocation() ?? '';
				$instances[] = $event;
				if ($wholeSeries) {
					$series = $eventInstance->getSeries(true);
					foreach ($series as $instance) {
						$event = new stdClass();
						$date = $instance->date . "T" . $instance->time;
						$event->date = preg_replace('/([-:])/', '', $date);
						$event->uid = join("_", [$eventIdParts[0], $eventIdParts[0], $instance->id]);
						$event->sublocation = $instance->getSublocation() ?? '';
						$instances[] = $event;
					}
				}
				$interface->assign('instances', $instances);
				$icsFile = $interface->fetch('Events/ics-export.tpl');
				$result = [
					'success' => true,
					'icsFile' => $icsFile,
				];
			}
		}
		return $result;
	}

}