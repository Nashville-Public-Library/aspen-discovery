<?php /** @noinspection PhpMissingFieldTypeInspection */

require_once ROOT_DIR . '/sys/DB/DataObject.php';

class Series extends DataObject {
	public $__table = 'series';
	public $id;
	public $displayName;
	public $_authors; // Get all authors from series members
	public $description;
	public $cover;
	public $audience;
	public $isIndexed;

	public $_seriesMembers; // grouped works and placeholders
}