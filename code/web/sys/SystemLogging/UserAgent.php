<?php

require_once ROOT_DIR . '/sys/DB/DataObject.php';

class UserAgent extends DataObject {
	public $__table = 'user_agent';
	public $id;
	public $userAgent;
	public $isBot;
	public $blockAccess;

	static function getObjectStructure($context = ''): array {
		return [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id within the database',
			],
			'userAgent' => [
				'property' => 'userAgent',
				'type' => 'text',
				'label' => 'User Agent',
				'description' => 'The User Agent from requests',
			],
			'isBot' => [
				'property' => 'isBot',
				'type' => 'checkbox',
				'label' => 'Is Bot?',
				'description' => 'Is the User Agent representing a bot.',
				'default' => true,
			],
			'blockAccess' => [
				'property' => 'blockAccess',
				'type' => 'checkbox',
				'label' => 'Block Access from this User Agent',
				'description' => 'Traffic from this User Agent will not be allowed to use Aspen.',
				'default' => false,
			],
		];
	}

	public function objectHistoryEnabled(): bool {
		return false;
	}
}