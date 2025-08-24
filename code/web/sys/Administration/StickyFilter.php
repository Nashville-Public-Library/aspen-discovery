<?php /** @noinspection PhpMissingFieldTypeInspection */

class StickyFilter extends DataObject
{
	public $__table = 'admin_sticky_filters';
	public $id;
	public $userId;
	public $filterFor;
	public $filterValue;
}