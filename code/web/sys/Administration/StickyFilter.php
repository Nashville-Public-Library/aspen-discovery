<?php

require_once ROOT_DIR . '/sys/DB/DataObject.php';

class StickyFilter extends DataObject
{
	public $__table = 'admin_sticky_filters';
	public $id;
	public $userId;
	public $filterFor;
	public $filterValue;
}