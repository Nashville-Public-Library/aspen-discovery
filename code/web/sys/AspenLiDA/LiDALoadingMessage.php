<?php

class LiDALoadingMessage extends DataObject {
	public $__table = 'lida_loading_messages';
	public $id;
	public $brandedAppSettingId;
	public $message;

	/** @noinspection PhpUnusedParameterInspection */
	static function getObjectStructure($context = ''): array {
		return [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id',
			],
			'message' => [
				'property' => 'message',
				'type' => 'text',
				'label' => 'Message',
				'description' => 'The message to be displayed',
				'required' => true,
				'maxLength' => 255
			]
		];
	}
}