<?php /** @noinspection PhpMissingFieldTypeInspection */


class AspenSiteCache extends DataObject {
	public $__table = 'greenhouse_cache';
	public $id;
	public $siteId;
	public $name;
	public $locationId;
	public $libraryId;
	public $solrScope;
	public $latitude;
	public $longitude;
	public $unit;
	public $baseUrl;
	public $lastUpdated;
	public $releaseChannel;
	public $logo;
	public $favicon;
	public $primaryBackgroundColor;
	public $primaryForegroundColor;
	public $secondaryBackgroundColor;
	public $secondaryForegroundColor;
	public $tertiaryBackgroundColor;
	public $tertiaryForegroundColor;
}