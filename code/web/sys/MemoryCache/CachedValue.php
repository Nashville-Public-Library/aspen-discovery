<?php /** @noinspection PhpMissingFieldTypeInspection */

class CachedValue extends DataObject {
	public $__table = 'cached_values';
	public $__primaryKey = 'cacheKey';
	public $cacheKey;
	/** @noinspection PhpUnused */
	public $valueType;
	public $value;
	public $expirationTime;
}