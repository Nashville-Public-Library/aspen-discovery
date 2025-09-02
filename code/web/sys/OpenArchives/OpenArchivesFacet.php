<?php /** @noinspection PhpMissingFieldTypeInspection */

class OpenArchivesFacet extends FacetSetting {
	public $__table = 'open_archives_facets';
	public $facetGroupId;

	public function getNumericColumnNames(): array {
		$numericColumns = parent::getNumericColumnNames();
		$numericColumns[] = 'facetGroupId';
		return $numericColumns;
	}


	public static function getAvailableFacets() : array {
		$availableFacets = [
			"collection_name" => "Collection",
			"creator_facet" => "Creator",
			"contributor_facet" => "Contributor",
			"type" => "Type",
			"subject_facet" => "Subject",
			"publisher_facet" => "Publisher",
			"source" => "Source",
			"geographic_facet" => "Location",
		];

		asort($availableFacets);
		return $availableFacets;
	}

	static $_objectStructure = [];
	static function getObjectStructure(string $context = ''): array {
		if (isset(self::$_objectStructure[$context]) && self::$_objectStructure[$context] !== null) {
			return self::$_objectStructure[$context];
		}
		$structure = [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id',
			],
			'weight' => [
				'property' => 'weight',
				'type' => 'integer',
				'label' => 'Weight',
				'description' => 'The sort order',
				'default' => 0,
			],
			'facetName' => [
				'property' => 'facetName',
				'type' => 'enum',
				'label' => 'Facet',
				'values' => self::getAvailableFacets(),
				'description' => 'The facet to include',
			],
			'displayName' => [
				'property' => 'displayName',
				'type' => 'text',
				'label' => 'Display Name',
				'description' => 'The full name of the facet for display to the user',
			],
			'displayNamePlural' => [
				'property' => 'displayNamePlural',
				'type' => 'text',
				'label' => 'Plural Display Name',
				'description' => 'The full name pluralized of the facet for display to the user',
			],
			'multiSelect' => [
				'property' => 'multiSelect',
				'type' => 'checkbox',
				'label' => 'Multi Select?',
				'description' => 'Whether or not to allow patrons to select multiple values',
				'default' => '0',
			],
			'canLock' => [
				'property' => 'canLock',
				'type' => 'checkbox',
				'label' => 'Can Lock?',
				'description' => 'Whether or not to allow patrons can lock the facet',
				'default' => '0',
			],
			'collapseByDefault' => [
				'property' => 'collapseByDefault',
				'type' => 'checkbox',
				'label' => 'Collapse by Default',
				'description' => 'Whether or not the facet should be an collapsed by default.',
				'default' => 1,
			],
			'useMoreFacetPopup' => [
				'property' => 'useMoreFacetPopup',
				'type' => 'checkbox',
				'label' => 'Use More Facet Popup',
				'description' => 'Whether or not more facet options are shown in a popup box.',
				'default' => 1,
			],
			'translate' => [
				'property' => 'translate',
				'type' => 'checkbox',
				'label' => 'Translate?',
				'description' => 'Whether or not values are translated when displayed',
				'default' => '0',
			],
			'sortMode' => [
				'property' => 'sortMode',
				'type' => 'enum',
				'label' => 'Sort',
				'values' => [
					'alphabetically' => 'Alphabetically',
					'num_results' => 'By number of results',
				],
				'description' => 'How the facet values should be sorted.',
				'default' => 'num_results',
			],
			'numEntriesToShowByDefault' => [
				'property' => 'numEntriesToShowByDefault',
				'type' => 'integer',
				'label' => 'Num Entries',
				'description' => 'The number of values to show by default.',
				'default' => '5',
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}
}