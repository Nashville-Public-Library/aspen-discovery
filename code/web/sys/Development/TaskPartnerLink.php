<?php /** @noinspection PhpMissingFieldTypeInspection */

class TaskPartnerLink extends DataObject {
	public $__table = 'development_task_partner_link';
	public $id;
	public $partnerId;
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

		$partnerList = [];
		require_once ROOT_DIR . '/sys/Greenhouse/AspenSite.php';
		$partner = new AspenSite();
		$partner->siteType = 0;
		$partner->orderBy('name asc');
		$partner->find();
		while ($partner->fetch()) {
			$partnerList[$partner->id] = $partner->name;
		}

		$structure = [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id',
			],
			'partnerId' => [
				'property' => 'partnerId',
				'type' => 'enum',
				'values' => $partnerList,
				'label' => 'Partner',
				'description' => 'The partner who requested the task',
				'required' => true,
			],
			'taskId' => [
				'property' => 'taskId',
				'type' => 'enum',
				'values' => $taskList,
				'label' => 'Task',
				'description' => 'The task related to the partner',
				'required' => true,
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}
}