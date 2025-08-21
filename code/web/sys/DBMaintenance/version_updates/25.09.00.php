<?php

function getUpdates25_09_00(): array {
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
		'25_09_add_performance_indexes' => [
			'title' => '25.09 Add Performance Indexes',
			'description' => '25.09 Add Performance Indexes',
			'continueOnError' => true,
			'sql' => [
				'ALTER TABLE search ADD INDEX searchLookup(searchUrl(500),session_id,user_id)',
				'ALTER TABLE themes ADD INDEX nameById(id,displayName)',
				'ALTER TABLE library ADD INDEX isDefault(isDefault)',
				'ALTER TABLE library ADD INDEX subdomainUrl(subdomain, baseUrl)'
			]
		], //25_09_add_performance_indexes
		'remove_quick_searches' => [
			'title' => 'Remove Quick Searches',
			'description' => 'Remove Unused Quick Search Tables',
			'continueOnError' => false,
			'sql' => [
				'DROP TABLE aspen_lida_quick_search_setting',
				'DROP TABLE aspen_lida_quick_searches'
			]
		], //remove_quick_searches

		//katherine - Grove

		//kirstien - Grove

		//kodi - Grove

		// Myranda - Grove

		//Yanjun Li - ByWater

		// Leo Stoyanov - BWS

		//alexander - Open Fifth

		//chloe - Open Fifth


		//Jacob - Open Fifth

		//Pedro - Open Fifth


		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions

		//other

		//Talpa Search
		
	];
}
