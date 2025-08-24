<?php /** @noinspection PhpMissingFieldTypeInspection */

class TaskTicketLink extends DataObject {
	public $__table = 'development_task_ticket_link';
	public $id;
	public $ticketId;
	public $taskId;

	static $_objectStructure = [];
	static function getObjectStructure(string $context = ''): array {
		if (isset(self::$_objectStructure[$context]) && self::$_objectStructure[$context] !== null) {
			return self::$_objectStructure[$context];
		}
		$taskList = [];
		require_once ROOT_DIR . '/sys/Development/DevelopmentTask.php';
		$task = new DevelopmentTask();
		$task->find();
		while ($task->fetch()) {
			$taskList[$task->id] = $task->name;
		}

		$ticketList = [];
		require_once ROOT_DIR . '/sys/Support/Ticket.php';
		$ticket = new Ticket();
		$ticket->whereAdd('status <> "Closed"');
		$ticket->whereAdd("queue IN ('Development', 'Bugs')");

		$ticket->orderBy('ticketId + 0 DESC');
		$ticket->find();
		while ($ticket->fetch()) {
			$ticketList[$ticket->id] = $ticket->ticketId . ': ' . $ticket->title;
		}

		$structure = [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id',
			],
			'ticketId' => [
				'property' => 'ticketId',
				'type' => 'enum',
				'values' => $ticketList,
				'label' => 'Ticket',
				'description' => 'The ticket related to the task',
				'required' => true,
			],
			'taskId' => [
				'property' => 'taskId',
				'type' => 'enum',
				'values' => $taskList,
				'label' => 'Task',
				'description' => 'The task related to the ticket',
				'required' => true,
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}

	public function getEditLink(string $context): string {
		if ($context == 'relatedTickets') {
			return '/Greenhouse/Tickets?objectAction=edit&id=' . $this->ticketId;
		} else {
			return '/Development/Tasks?objectAction=edit&id=' . $this->taskId;
		}
	}
}