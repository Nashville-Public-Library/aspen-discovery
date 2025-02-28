<?php
require_once ROOT_DIR . '/sys/Events/EventsFacetGroup.php';

class CalendarDisplaySetting extends DataObject {
	public $__table = 'calendar_display_settings';
	public $id;
	public $name;
	public $cover;
	public $altText;

	public static function getObjectStructure($context = ''): array {
		global $configArray;
		$coverPath = $configArray['Site']['coverPath'];

		$structure = [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id',
			],
			'name' => [
				'property' => 'name',
				'type' => 'text',
				'label' => 'Name',
				'description' => 'A name for the settings',
			],
			'cover' => [
				'property' => 'cover',
				'type' => 'image',
				'label' => 'Header Image',
				'thumbWidth' => 750,
				'maxWidth' => 1170,
				'maxHeight' => 250,
				'description' => 'Calendar header image (1140 x 100px max)',
				'hideInLists' => true,
			],
			'altText' => [
				'property' => 'altText',
				'type' => 'text',
				'label' => 'Header image description',
				'description' => 'A header image description to use for alt-text',
			]
		];
		return $structure;
	}

}