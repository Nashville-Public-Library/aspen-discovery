<?php /** @noinspection PhpMissingFieldTypeInspection */

class OverDriveAPIProductMetaData extends DataObject {
	public $__table = 'overdrive_api_product_metadata';   // table name

	public $id;
	public $productId;
	/** @noinspection PhpUnused */
	public $checksum;
	public $sortTitle;
	public $publisher;
	public $publishDate;
	/** @noinspection PhpUnused */
	public $isPublicDomain;
	/** @noinspection PhpUnused */
	public $isPublicPerformanceAllowed;
	/** @noinspection PhpUnused */
	public $shortDescription;
	public $fullDescription;
	/** @noinspection PhpUnused */
	public $popularity;
	public $rawData;
	public $thumbnail;
	public $cover;

	private $decodedRawData = null;

	public function getDecodedRawData() {
		if ($this->decodedRawData == null) {
			$this->decodedRawData = json_decode($this->rawData);
		}
		return $this->decodedRawData;
	}

	public function getCompressedColumnNames(): array {
		return ['rawData'];
	}
}