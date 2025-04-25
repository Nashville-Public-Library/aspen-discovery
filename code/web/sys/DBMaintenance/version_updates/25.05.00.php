<?php

function getUpdates25_05_00(): array {
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
		'system_variables_add_lida_github_repository' => [
			'title' => 'Add LIDA GitHub Repository to System Variables',
			'description' => 'Add a field to store the github repository for LiDA within System Variables to load release notes from',
			'continueOnError' => false,
			'sql' => [
				"ALTER TABLE system_variables add column lidaGitHubRepository VARCHAR(255) DEFAULT 'https://github.com/Aspen-Discovery/aspen-lida'",
			]
		], //system_variables_add_lida_github_repository
		'axis360_indexing_concurrency' => [
			'title' => 'Setup indexing concurrency for Boundless',
			'description' => 'Define numBoundlessSettingsToProcessInParallel in System variables',
			'sql' => [
				"ALTER TABLE system_variables add column numBoundlessSettingsToProcessInParallel int DEFAULT 1",
			]
		], //axis360_indexing_concurrency
		'axis360_setting_name' => [
			'title' => 'Add Boundless setting name',
			'description' => 'Add a name for Boundless settings',
			'sql' => [
				"ALTER TABLE axis360_settings ADD COLUMN name VARCHAR(100) DEFAULT ''",
				"UPDATE axis360_settings set name = concat('Setting ', id)",
			]
		], //axis360_setting_name

		//katherine - Grove
		'add_deleted_field_to_series_member' => [
			'title' => 'Add a deleted field to Series Member table',
			'description' => 'Add a deleted field so that Series Members can be deleted',
			'sql' => [
				"ALTER TABLE series_member ADD COLUMN deleted TINYINT(1) DEFAULT 0",
			]
		], //add_deleted_field_to_series_member

		//kirstien - Grove

		//kodi - Grove

		//Yanjun Li - ByWater

		// Leo Stoyanov - BWS
		'custom_form_field_enums_to_text' => [
			'title' => 'Increase Custom Form Field EnumValues Size',
			'description' => 'Changes the enumValues column in web_builder_custom_form_field from VARCHAR(255) to TEXT to allow for longer select lists.',
			'sql' => [
				"ALTER TABLE web_builder_custom_form_field MODIFY COLUMN enumValues TEXT DEFAULT NULL",
			]
		], //custom_form_field_enums_to_text
		'ip_lookup_ipv6_support' => [
			'title' => 'Add Support for IPv6 Addresses',
			'description' => 'Add support for IPv6 addresses in ip_lookup table.',
			'continueOnError' => true,
			'sql' => [
				"ALTER TABLE ip_lookup MODIFY startIpVal VARCHAR(255) NULL COMMENT 'Numeric value for IPv4 or encoded string for IPv6'",
				"ALTER TABLE ip_lookup MODIFY endIpVal VARCHAR(255) NULL COMMENT 'Numeric value for IPv4 or encoded string for IPv6'"
			],
		], //ip_lookup_ipv6_support

		//alexander - Open Fifth
		'allow_filtering_of_linked_users_in_checkouts' => [
			'title' => 'Allow Filtering of Linked Users in Checkouts',
			'description' => 'Allow libraries the option of allowing users to filter their checkouts by linked user',
			'sql' => [
				'ALTER TABLE library ADD COLUMN allowFilteringOfLinkedAccountsInCheckouts TINYINT(1) DEFAULT 0',
			],
		], //allow_filtering_of_linked_users_in_checkouts
		'allow_selecting_checkouts_to_export' => [
			'title' => 'Allow Selecting Checkouts to Export',
			'description' => 'Allow libraries the option of allowing users to export only selected checkouts',
			'sql' => [
				'ALTER TABLE library ADD COLUMN allowSelectingCheckoutsToExport TINYINT(1) DEFAULT 0'
			],
		], //allow_selecting_checkouts_to_export

		//chloe - PTFS-Europe

		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions

		//other

	];
}