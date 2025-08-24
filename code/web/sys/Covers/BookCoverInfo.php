<?php /** @noinspection PhpMissingFieldTypeInspection */


class BookCoverInfo extends DataObject {
	public $__table = 'bookcover_info';    // table name
	protected $id;
	protected $recordType;
	protected $recordId;
	protected $firstLoaded;
	protected $lastUsed;
	protected $imageSource;
	protected $sourceWidth;
	protected $sourceHeight;
	protected $thumbnailLoaded;
	protected $mediumLoaded;
	protected $largeLoaded;
	protected $uploadedImage;
	protected $disallowThirdPartyCover;
	protected $original_url;
	protected $last_url_validation;

	public function getNumericColumnNames(): array {
		return [
			'id',
			'sourceWidth',
			'sourceHeight',
			'thumbnailLoaded',
			'mediumLoaded',
			'largeLoaded',
			'uploadedImage',
			'disallowThirdPartyCover',
			'last_url_validation',
		];
	}

	private static $_allCoversReloadedThisSession = false;

	/**
	 * Reloads all default covers, will only reload them once per session to improve performance when updating all themes etc.
	 * @return void
	 */
	public function reloadAllDefaultCovers() : void {
		if (!self::$_allCoversReloadedThisSession) {
			$this->query("UPDATE " . $this->__table . " SET thumbnailLoaded = 0, mediumLoaded = 0, largeLoaded = 0 where imageSource = 'default'");
			self::$_allCoversReloadedThisSession = true;
		}
	}

	public function reloadOMDBCovers() : void {
		$this->query("UPDATE " . $this->__table . " SET thumbnailLoaded = 0, mediumLoaded = 0, largeLoaded = 0 where imageSource = 'omdb_title' OR imageSource = 'omdb_title_year'");
	}

	public function getImageSource() : string {
		return $this->imageSource;
	}

	public function getDisallowThirdPartyCover() {
		return $this->disallowThirdPartyCover;
	}

	/**
	 * @return mixed
	 */
	public function getRecordId() : string {
		return $this->recordId;
	}

	/**
	 * @return mixed
	 */
	public function getRecordType() {
		return $this->recordType;
	}

	/**
	 * @param mixed $recordType
	 */
	public function setRecordType($recordType): void {
		$this->__set('recordType', $recordType);
	}

	/**
	 * @param mixed $recordId
	 */
	public function setRecordId($recordId): void {
		$this->__set('recordId', $recordId);
	}

	public function setImageSource($imageSource): void {
		$this->__set('imageSource', $imageSource);
	}

	/**
	 * Get the original URL of the cover image
	 * @return string|null
	 */
	public function getOriginalUrl(): ?string
	{
		return $this->original_url;
	}

	/**
	 * Set the original URL of the cover image
	 * @param string $url
	 */
	public function setOriginalUrl(string $url): void {
		$this->__set('original_url', $url);
	}

	/**
	 * Get the timestamp when the URL was last validated
	 * @return int|null
	 */
	public function getLastUrlValidation(): ?int
	{
		return $this->last_url_validation;
	}

	/**
	 * Set the timestamp when the URL was last validated
	 * @param int $timestamp
	 */
	public function setLastUrlValidation(int $timestamp): void {
		$this->__set('last_url_validation', $timestamp);
	}
	public function setThumbnailLoaded(int $loaded) : void {
		$this->__set('thumbnailLoaded', $loaded);
	}

	public function setMediumLoaded(int $loaded) : void {
		$this->__set('mediumLoaded', $loaded);
	}

	public function setLargeLoaded(int $loaded) : void {
		$this->__set('largeLoaded', $loaded);
	}
}