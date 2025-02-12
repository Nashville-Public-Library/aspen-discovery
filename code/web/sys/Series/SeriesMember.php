<?php /** @noinspection PhpMissingFieldTypeInspection */

require_once ROOT_DIR . '/sys/DB/DataObject.php';

class Series extends DataObject {
	public $__table = 'seriesMember';
	public $id;
	public $groupedWorkId;
	public $isPlaceholder;
	public $displayName;
	public $author;
	public $volume;
	public $pubDate;
	public $cover;
}