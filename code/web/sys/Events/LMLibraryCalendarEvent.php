<?php /** @noinspection PhpMissingFieldTypeInspection */


class LMLibraryCalendarEvent extends DataObject {
	public $__table = 'lm_library_calendar_events';
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