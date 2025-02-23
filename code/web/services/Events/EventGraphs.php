<?php

require_once ROOT_DIR . '/services/Admin/Admin.php';
require_once ROOT_DIR . '/sys/Events/EventInstance.php';
require_once ROOT_DIR . '/sys/Events/Event.php';
require_once ROOT_DIR . '/sys/Events/EventField.php';

class Events_EventGraphs extends Admin_Admin {

	function launch() {
		global $interface;

		$stat = "eventHours";

		// Form options
		$timeframe = $_REQUEST['timeframe'] ?? 'days';
		$interface->assign('timeframe', $timeframe);
		$eventType = $_REQUEST['type'] ?? '';
		$interface->assign('eventTypeValue', $eventType);
		$interface->assign('eventTypes', EventType::getEventTypeList());

		// $libraryList = Library::getLibraryList(!UserAccount::userHasPermission('View Event Reports For All Libraries'));
		$locations = Location::getLocationList(!UserAccount::userHasPermission('View Event Reports for All Libraries') || UserAccount::userHasPermission('View Event Reports for Home Library'));
		$location = $_REQUEST['location'] ?? '';
		$interface->assign('locationValue', $location);
		$interface->assign('locations', $locations);

		$sublocation = $_REQUEST['sublocation'] ?? '';
		// Only get sublocation options if there's a location
		if ($location != '') {
			$sublocations = Location::getEventSublocations($location);
			$interface->assign('sublocationValue', $sublocation);
			$interface->assign('sublocations', $sublocations);
		} else {
			$interface->assign('sublocations', '');
		}

		$checkboxFields = EventField::getEventFieldsByTypes([2]);
		$interface->assign('checkboxFields', $checkboxFields);
		$selectFields = EventField::getEventFieldsByTypes([3]);
		$interface->assign('selectFields', $selectFields);
		$fields = array_filter($_REQUEST, function($v, $k) {
			return str_contains($k, 'field_') && $v != NULL && $v !== '';
		}, ARRAY_FILTER_USE_BOTH);
		foreach ($fields as $key => $value) {
			$field[$key] = $_REQUEST[$key];
		}
		$interface->assign('fields', $fields);
		$query = $_REQUEST['query'] ?? '';
		$interface->assign('query', $query);


		$title = 'Aspen Event Hours Graph';
		$interface->assign('section', 'Events');
		// $interface->assign('showCSVExportButton', true);
		$interface->assign('graphTitle', $title);
		// $this->assignGraphSpecificTitle($stat);
		$this->getAndSetInterfaceDataSeries($stat, $timeframe, $eventType, $location, $sublocation, $query, $fields);
		$interface->assign('stat', $stat);
		$interface->assign('propName', 'exportToCSV');
		$title = $interface->getVariable('graphTitle');
		$this->display('event-graph.tpl', $title);
	}

	function getBreadcrumbs(): array {
		$breadcrumbs = [];
		$breadcrumbs[] = new Breadcrumb('/Admin/Home', 'Administration Home');
		$breadcrumbs[] = new Breadcrumb('/Admin/Home#events', 'Events');
		$breadcrumbs[] = new Breadcrumb('/Events/EventGraphs', 'Events Graphs');
		return $breadcrumbs;
	}

	function getActiveAdminSection(): string {
		return 'Events';
	}

	function canView(): bool {
		return UserAccount::userHasPermission([
			"View Event Reports for All Libraries",
			"View Event Reports for Home Library",
		]);
	}

	private function getAndSetInterfaceDataSeries($stat, $timeframe, $eventType, $location, $sublocation = '', $query = '', $fields = []) {
		global $interface;

		$dataSeries = [];
		$columnLabels = [];
		$userHours = new EventInstance();
		$userHours->selectAdd();
		$userHours->whereAdd("event_instance.deleted = 0");
		$userHours->whereAdd("event_instance.status = 1"); // Exclude cancelled events
		if (!empty($query) || !empty($fields)) {
			$eventField = new EventEventField();
			foreach ($fields as $key => $value) {
				$eventField->whereAdd("(eventFieldId = " . substr($key, -1) . " AND value = $value)");
			}
			$eventField->groupBy("eventId");
			$userHours->joinAdd($eventField, 'INNER', 'eventEventField', 'eventId', 'eventId');
		}
		if (!empty($eventType) || !empty($location) || !empty($query)) {
			$event = new Event();
			if (!empty($eventType)) {
				$event->whereAdd("eventTypeId = '$eventType'");
			}
			if (!empty($location)) {
				$event->whereAdd("locationId = '$location'");
				if (!empty($sublocation)) {
					$event->whereAdd("sublocationId = '$sublocation'");
				}
			}
			$userHours->joinAdd($event, 'INNER', 'event', 'eventId', 'id');
		}
		if (!empty($query)) {
			$escapedQuery = $userHours->escape('%' . $query . '%');
			$userHours->whereAdd("(event.title LIKE $escapedQuery OR event.description LIKE $escapedQuery OR eventEventField.value LIKE $escapedQuery)");
		}
		switch ($timeframe) {
			case "weeks":
				$userHours->selectAdd("WEEK(date) AS week, YEAR(date) AS year");
				$userHours->groupBy("week, year");
				break;
			case "months":
				$userHours->selectAdd("MONTH(date) AS month, YEAR(date) AS year");
				$userHours->groupBy("month, year");
				break;
			case "years":
				$userHours->selectAdd("YEAR(date) AS year");
				$userHours->groupBy("year");
				break;
			case "days":
			default: // default to hours per day
				$userHours->selectAdd("date");
				$userHours->groupBy("date");
		}
		$userHours->orderBy('date');

		if ($stat == "eventHours") {
			$dataSeries['Event Hours'] = [
				'borderColor' => 'rgba(255, 99, 132, 1)',
				'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
				'data' => [],
			];
			$userHours->selectAdd('SUM(length) / 60 AS sumHours');
		}

		$userHours->find();

		while ($userHours->fetch()) {
			switch ($timeframe) {
				case "weeks":
					$curPeriod = "{$userHours->week}-{$userHours->year}";
					break;
				case "months":
					$curPeriod = "{$userHours->month}-{$userHours->year}";
					break;
				case "years":
					$curPeriod = "{$userHours->year}";
					break;
				case "days":
				default: // Default to hours per day
					$curPeriod = "{$userHours->date}";
			}

			$columnLabels[] = $curPeriod;

			if ($stat == 'eventHours') {
				/** @noinspection PhpUndefinedFieldInspection */
				$dataSeries['Event Hours']['data'][$curPeriod] = $userHours->sumHours;
			}
		}

		$interface->assign('columnLabels', $columnLabels);
		$interface->assign('dataSeries', $dataSeries);
		$interface->assign('translateDataSeries', true);
		$interface->assign('translateColumnLabels', false);
	}

}