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
		'remove_rbdigital_tables' => [
			'title' => 'Remove RBdigital tables',
			'description' => 'Remove Unused RBdigital Tables',
			'continueOnError' => false,
			'sql' => [
				'DROP TABLE rbdigital_magazine_issue',
				'DROP TABLE rbdigital_magazine',
				'DROP TABLE rbdigital_magazine_usage',
				'DROP TABLE rbdigital_title',
				'DROP TABLE rbdigital_record_usage',
				'DROP TABLE user_rbdigital_usage',
				"DELETE FROM modules where name = 'RBdigital'",
				"DELETE FROM role_permissions where permissionId = (SELECT id from permissions where name = 'Administer RBdigital')",
				"DELETE FROM permissions where name = 'Administer RBdigital'",
			]
		], //remove_rbdigital_tables

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
