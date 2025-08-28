<?php /** @noinspection PhpMissingFieldTypeInspection */

class GroupedWorkFacet extends FacetSetting {
	public $__table = 'grouped_work_facet';
	public $facetGroupId;

	public function getNumericColumnNames(): array {
		$numericColumns = parent::getNumericColumnNames();
		$numericColumns[] = 'facetGroupId';
		return $numericColumns;
	}


	public static function getAvailableFacets() : array {
		$availableFacets = [
			"owning_library" => "Library System",
			"owning_location" => "Branch",
			"available_at" => "Available At",
			"availability_toggle" => "Search Within",
			"collection_group" => "Collection",
			"rating_facet" => "Rating",
			"publishDate" => "Publication Year",
			"publishDateSort" => "Earliest Publication Year",
			"format" => "Format",
			"format_category" => "Format Category",
			"econtent_source" => "E-Content Collection",
			"subject_facet" => "Subjects",
			"topic_facet" => "Topics",
			"target_audience" => "Audience",
			"mpaa_rating" => "Movie Rating",
			"literary_form" => "Form",
			"authorStr" => "Author",
			"language" => "Language",
			"translation" => "Translations",
			"genre_facet" => "Genre",
			"era" => "Era",
			"geographic_facet" => "Region",
			"personal_name_facet" => "Personal Name",
			"corporate_name_facet" => "Corporate Name",
			"target_audience_full" => "Reading Level",
			"literary_form_full" => "Literary Form",
			"lexile_code" => "Lexile code",
			"lexile_score" => "Lexile measure",
			"itype" => "Item Type",
			"time_since_added" => "Added In The Last",
			"callnumber-first" => "LC Call Number",
			"awards_facet" => "Awards",
			"shelf_location" => "Shelf Location",
			"detailed_location" => "Detailed Location",
			"lc_subject" => "LC Subject",
			"bisac_subject" => "Bisac Subject",
			"accelerated_reader_interest_level" => "AR Interest Level",
			"accelerated_reader_reading_level" => "AR Reading Level",
			"accelerated_reader_point_value" => "AR Point Value",
			"fountas_pinnell" => "Fountas &amp; Pinnell",
			"series_facet" => "Series",
			"publisherStr" => "Publisher",
			"placeOfPublication" => "Place of Publication",
			"custom_facet_1" => "Custom Facet 1",
			"custom_facet_2" => "Custom Facet 2",
			"custom_facet_3" => "Custom Facet 3"
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
			'showAboveResults' => [
				'property' => 'showAboveResults',
				'type' => 'checkbox',
				'label' => 'Show Above Results',
				'description' => 'Whether or not the facets should be shown above the results',
				'default' => 0,
			],
			'showInResults' => [
				'property' => 'showInResults',
				'type' => 'checkbox',
				'label' => 'Show on Results Page',
				'description' => 'Whether or not the facets should be shown in regular search results',
				'default' => 1,
			],
			'showInAdvancedSearch' => [
				'property' => 'showInAdvancedSearch',
				'type' => 'checkbox',
				'label' => 'Show on Advanced Search',
				'description' => 'Whether or not the facet should be an option on the Advanced Search Page',
				'default' => 1,
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
			'showAsDropDown' => [
				'property' => 'showAsDropDown',
				'type' => 'checkbox',
				'label' => 'Drop Down?',
				'description' => 'Whether or not the facets should be shown in a drop down list',
				'default' => '0',
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