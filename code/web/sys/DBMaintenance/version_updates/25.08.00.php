<?php

function getUpdates25_08_00(): array {
	$curTime = time();
	return [
		/*'name' => [
			 'title' => '',
			 'description' => '',
			 'continueOnError' => false,
			 'sql' => [
				 ''
			 ]
		 ], //name*/

		//mark - Grove

		//katherine - Grove

		//kirstien - Grove

		//kodi - Grove

		// Myranda - Grove

		//Yanjun Li - ByWater

		// Leo Stoyanov - BWS
		'update_browse_category_sort_options' => [
			'title' => 'Update Browse Category Sort Options for Lists Search',
			'description' => 'Add new date sorting options for browse categories when using Lists as search source.',
			'sql' => [
				"ALTER TABLE browse_category MODIFY COLUMN defaultSort ENUM('relevance','popularity','newest_to_oldest','author','title','user_rating','holds','publication_year_desc','publication_year_asc','event_date','oldest_to_newest','newest_updated_to_oldest','oldest_updated_to_newest') DEFAULT 'relevance'"
			]
		],
		'update_collection_spotlight_sort_options' => [
			'title' => 'Update Collection Spotlight Sort Options for Lists Search',
			'description' => 'Add new date sorting options for collection spotlights when using Lists as search source.',
			'sql' => [
				"ALTER TABLE collection_spotlight_lists MODIFY COLUMN defaultSort ENUM('relevance','popularity','newest_to_oldest','author','title','user_rating','holds','publication_year_desc','publication_year_asc','event_date','oldest_to_newest','newest_updated_to_oldest','oldest_updated_to_newest') DEFAULT 'relevance'"
			]
		]

		// Laura Escamilla - ByWater Solutions

		//alexander - Open Fifth

		//chloe - Open Fifth


		//Jacob - Open Fifth

		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions

		//other

		//Talpa Search
		
	];
}
