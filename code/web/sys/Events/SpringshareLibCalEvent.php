<?php /** @noinspection PhpMissingFieldTypeInspection */

class SpringshareLibCalEvent extends DataObject {
	public $__table = 'springshare_libcal_events';
	public $id;
	/** @noinspection PhpUnused */
	public $settingsId;
	public $externalId;
	public $title;
	/** @noinspection PhpUnused */
	public $rawChecksum;
	public $rawResponse;
	public $deleted;

	private $_rawDataDecoded = null;

	function getDecodedData() {
		if ($this->_rawDataDecoded == null) {
			$this->_rawDataDecoded = json_decode($this->rawResponse);
		}
		return $this->_rawDataDecoded;
	}
}