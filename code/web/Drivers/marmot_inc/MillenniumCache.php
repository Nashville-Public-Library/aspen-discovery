<?php

class MillenniumCache extends DataObject {
	public $__table = 'millennium_cache';    // table name
	public $recordId;                    //varchar(20)
	public $scope;                    //int(16)
	public $holdingsInfo;             //mediumText
	public $framesetInfo;             //mediumText
	public $cacheDate;         //timestamp

}