<?php /** @noinspection PhpMissingFieldTypeInspection */
require_once ROOT_DIR . '/sys/Events/EventsFacetGroup.php';

class LibraryEventsSetting extends DataObject {
	public $__table = 'library_events_setting';
	public $id;
	public $settingSource;
	public $settingId;
	/** @noinspection PhpUnused */
	public $eventsFacetSettingsId;
	public $libraryId;

	private $_facetGroup = false;

	/** @return EventsFacet[] */
	public function getFacets() : array {
		try {
			if (!is_null($this->getFacetGroup())) {
				return $this->getFacetGroup()->getFacets();
			} else {
				return [];
			}
		} catch (Exception) {
			return [];
		}
	}

	public function getFacetGroup(): ?EventsFacetGroup {
		try {
			if ($this->_facetGroup === false) {
				$this->_facetGroup = new EventsFacetGroup();
				$this->_facetGroup->id = $this->facetGroupId;
				if (!$this->_facetGroup->find(true)) {
					$this->_facetGroup = null;
				}
			}
			return $this->_facetGroup;
		} catch (Exception) {
			return null;
		}
	}
}