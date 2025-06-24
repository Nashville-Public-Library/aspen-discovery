<?php

require_once ROOT_DIR . '/services/Admin/Admin.php';
require_once ROOT_DIR . '/sys/Support/Ticket.php';
require_once ROOT_DIR . '/sys/Greenhouse/AspenSite.php';

class Greenhouse_PartnerTicketDashboard extends Admin_Admin{
	function launch() {
		global $interface;

		require_once ROOT_DIR . '/sys/Support/RequestTrackerConnection.php';
		$supportConnections = new RequestTrackerConnection();
		if ($supportConnections->find(true)) {
			$interface->assign('rtBaseUrl', $supportConnections->baseUrl);
		}

		$aspenSite = new AspenSite();
		$aspenSite->siteType = 0;
		$aspenSite->orderBy('name');
		$allSites = [];
		$aspenSite->find();
		$selectedSite = '';
		while ($aspenSite->fetch()) {
			$allSites[$aspenSite->id] = $aspenSite->name;
			if ($selectedSite == '') {
				$selectedSite = $aspenSite->id;
			}
		}
		$interface->assign('allSites', $allSites);

		if (!empty($_REQUEST['site'])) {
			$selectedSite = $_REQUEST['site'];
		}
		$interface->assign('selectedSite', $selectedSite);

		//Closed tickets
		$ticket = new Ticket();
		$ticket->status = 'Closed';
		$ticket->requestingPartner = $selectedSite;
		$ticket->orderBy('dateClosed DESC');
		$interface->assign('totalTicketsClosed', $ticket->count());
		$ticket->find();
		$closedTickets = [];
		while ($ticket->fetch()) {
			$closedTickets[] = clone $ticket;
		}
		$interface->assign('closedTickets', $closedTickets);

		//Closed Priority Tickets
		$ticket = new Ticket();
		$ticket->status = 'Closed';
		$ticket->requestingPartner = $selectedSite;
		$ticket->whereAdd('partnerPriority > 0');
		$ticket->orderBy('dateClosed DESC');
		$interface->assign('totalPriorityTicketsClosed', $ticket->count());
		$ticket->find();
		$lastPriorityClosed = '';
		$lastPriority1Closed = '';
		$lastPriority2Closed = '';
		$lastPriority3Closed = '';
		$closedPriorityTickets = [];
		while ($ticket->fetch()) {
			if (empty($lastPriorityClosed)) {
				$lastPriorityClosed = clone $ticket;
			}
			if ($ticket->partnerPriority == 1 && empty($lastPriority1Closed)) {
				$lastPriority1Closed = clone $ticket;
			}
			if ($ticket->partnerPriority == 2 && empty($lastPriority2Closed)) {
				$lastPriority2Closed = clone $ticket;
			}
			if ($ticket->partnerPriority == 3 && empty($lastPriority3Closed)) {
				$lastPriority3Closed = clone $ticket;
			}
			$closedPriorityTickets[] = clone $ticket;
		}
		$interface->assign('lastPriorityClosed', $lastPriorityClosed);
		$interface->assign('lastPriority1Closed', $lastPriority1Closed);
		$interface->assign('lastPriority2Closed', $lastPriority2Closed);
		$interface->assign('lastPriority3Closed', $lastPriority3Closed);
		$interface->assign('closedPriorityTickets', $closedPriorityTickets);

		//Priorities for the library.
		$ticket = new Ticket();
		$ticket->whereAdd("status <> 'Closed'");
		$ticket->requestingPartner = $selectedSite;
		$ticket->whereAdd('partnerPriority > 0');
		$ticket->orderBy('dateCreated DESC');
		$ticket->find();
		$priority1Ticket = null;
		$priority2Ticket = null;
		$priority3Ticket = null;
		while ($ticket->fetch()) {
			if ($ticket->partnerPriority == 1) {
				$priority1Ticket = clone $ticket;
			} elseif ($ticket->partnerPriority == 2) {
				$priority2Ticket = clone $ticket;
			} elseif ($ticket->partnerPriority == 3) {
				$priority3Ticket = clone $ticket;
			}
		}
		$interface->assign('priority1Ticket', $priority1Ticket);
		$interface->assign('priority2Ticket', $priority2Ticket);
		$interface->assign('priority3Ticket', $priority3Ticket);

		//Open Support Tickets
		$ticket = new Ticket();
		$ticket->whereAdd("status <> 'Closed'");
		$ticket->requestingPartner = $selectedSite;
		$ticket->queue = 'Support';
		$ticket->orderBy('dateCreated DESC');
		$ticket->find();
		$openSupportTickets = [];
		while ($ticket->fetch()) {
			$openSupportTickets[] = clone $ticket;
		}
		$interface->assign('openSupportTickets', $openSupportTickets);

		//Open Bugs
		$ticket = new Ticket();
		$ticket->whereAdd("status <> 'Closed'");
		$ticket->requestingPartner = $selectedSite;
		$ticket->queue = 'Bugs';
		$ticket->orderBy('dateCreated DESC');
		$ticket->find();
		$openBugs = [];
		while ($ticket->fetch()) {
			$openBugs[] = clone $ticket;
		}
		$interface->assign('openBugs', $openBugs);

		//Open Developments
		$ticket = new Ticket();
		$ticket->whereAdd("status <> 'Closed'");
		$ticket->requestingPartner = $selectedSite;
		$ticket->queue = 'Development';
		$ticket->orderBy('dateCreated DESC');
		$ticket->find();
		$openDevelopments = [];
		while ($ticket->fetch()) {
			$openDevelopments[] = clone $ticket;
		}
		$interface->assign('openDevelopments', $openDevelopments);

		//Open Implementation Tickets
		$ticket = new Ticket();
		$ticket->whereAdd("status <> 'Closed'");
		$ticket->requestingPartner = $selectedSite;
		$ticket->queue = 'Implementation';
		$ticket->orderBy('dateCreated DESC');
		$ticket->find();
		$openImplementation = [];
		while ($ticket->fetch()) {
			$openImplementation[] = clone $ticket;
		}
		$interface->assign('openImplementationTickets', $openImplementation);

		//Load ticket trend
		$dataSeries = [];
		$columnLabels = [];
		require_once ROOT_DIR . '/sys/Utils/GraphingUtils.php';

		$dataSeries['Open Tickets'] = GraphingUtils::getDataSeriesArray(count($dataSeries));

		require_once ROOT_DIR . '/sys/Support/TicketTrendByPartner.php';
		$ticketTrend = new TicketTrendByPartner();
		$ticketTrend->orderBy('year, month, day');
		$ticketTrend->requestingPartner = $selectedSite;
		$ticketTrend->find();
		$lastPeriod = null;
		$foundOpenTickets = false;
		while ($ticketTrend->fetch()) {
			$curPeriod = "{$ticketTrend->month}-{$ticketTrend->day}-{$ticketTrend->year}";
			if ($lastPeriod == null) {
				$lastPeriod = $curPeriod;
			}
			if ($foundOpenTickets || $ticketTrend->count > 0) {
				if (!$foundOpenTickets && $curPeriod != $lastPeriod) {
					$columnLabels[] = $lastPeriod;
					$dataSeries['Open Tickets']['data'][$lastPeriod] = 0;
				}
				$foundOpenTickets = true;
				if (!in_array($curPeriod, $columnLabels)) {
					$columnLabels[] = $curPeriod;
				}
				$dataSeries['Open Tickets']['data'][$curPeriod] = $ticketTrend->count;
			}
		}

		$interface->assign('columnLabels', $columnLabels);
		$interface->assign('dataSeries', $dataSeries);
		$interface->assign('translateDataSeries', true);
		$interface->assign('translateColumnLabels', false);

		$this->display('partnerTicketDashboard.tpl', 'Partner Ticket Dashboard', 'Development/development-sidebar.tpl');
	}

	function getBreadcrumbs(): array {
		$breadcrumbs = [];
		$breadcrumbs[] = new Breadcrumb('/Greenhouse/Home', 'Greenhouse Home');
		$breadcrumbs[] = new Breadcrumb('/Greenhouse/PartnerTicketDashboard', 'Partner Ticket Dashboard');
		return $breadcrumbs;
	}

	function canView() {
		if (UserAccount::isLoggedIn()) {
			if (UserAccount::getActiveUserObj()->isAspenAdminUser()) {
				return true;
			}
		}
		return false;
	}

	function getActiveAdminSection(): string {
		return 'greenhouse';
	}

	public function display($mainContentTemplate, $pageTitle, $sidebarTemplate = 'Development/development-sidebar.tpl', $translateTitle = true) {
		parent::display($mainContentTemplate, $pageTitle, $sidebarTemplate, $translateTitle);
	}
}