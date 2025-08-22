<?php /** @noinspection PhpMissingFieldTypeInspection */

class OverDriveAPIProductFormats extends DataObject {
	public $__table = 'overdrive_api_product_formats';   // table name

	public $id;
	public $productId;
	public $textId;
	/** @noinspection PhpUnused */
	public $numericId;
	public $name;
	public $fileName;
	/** @noinspection PhpUnused */
	public $fileSize;
	/** @noinspection PhpUnused */
	public $partCount;
	public $sampleSource_1;
	public $sampleUrl_1;
	public $sampleSource_2;
	public $sampleUrl_2;

} 