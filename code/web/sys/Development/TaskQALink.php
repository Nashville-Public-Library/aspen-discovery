<?php /** @noinspection PhpMissingFieldTypeInspection */

class TaskQALink extends DataObject {
	public $__table = 'development_task_qa_link';
	public $id;
	public $userId;
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

		$userList = [];
		$user = new User();
		$user->source = 'development';
		$user->find();
		while ($user->fetch()) {
			$userList[$user->id] = $user->displayName;
		}

		$structure = [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id',
			],
			'userId' => [
				'property' => 'userId',
				'type' => 'enum',
				'values' => $userList,
				'label' => 'Q/A Analyst',
				'description' => 'A Q/A Analyst working on this task',
				'required' => true,
			],
			'taskId' => [
				'property' => 'taskId',
				'type' => 'enum',
				'values' => $taskList,
				'label' => 'Task',
				'description' => 'The task being tested',
				'required' => true,
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}
}