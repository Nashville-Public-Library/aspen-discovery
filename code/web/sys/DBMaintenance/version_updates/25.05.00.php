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
			'title' => 'system_variables_add_lida_github_repository',
			'description' => 'Add a field to store the github repository for LiDA within System Variables to load release notes from',
			'continueOnError' => false,
			'sql' => [
				"ALTER TABLE system_variables add column lidaGitHubRepository VARCHAR(255) DEFAULT 'https://github.com/Aspen-Discovery/aspen-lida'",
			]
		], //system_variables_add_lida_github_repository

		//katherine - Grove

		//kirstien - Grove

		//kodi - Grove

		//Yanjun Li - ByWater
		'hoopla_settings_updates' => [
			'title' => 'Migrate Hoopla Flex Settings',
			'description' => 'Seperate Hoopla Flex and Hoopla Instant settings',
			'continueOnError' => false,
			'sql' => [
				'ALTER TABLE hoopla_settings ADD COLUMN hooplaInstantEnabled TINYINT(1) DEFAULT 1',
				'ALTER TABLE hoopla_settings CHANGE COLUMN runFullUpdate runFullUpdateInstant TINYINT(1) DEFAULT 0',
				'ALTER TABLE hoopla_settings CHANGE COLUMN lastUpdateOfChangedRecords lastUpdateOfChangedRecordsInstant INT(11) DEFAULT 0',
				'ALTER TABLE hoopla_settings CHANGE COLUMN lastUpdateOfAllRecords lastUpdateOfAllRecordsInstant INT(11) DEFAULT 0',
				'ALTER TABLE hoopla_settings ADD COLUMN hooplaFlexEnabled TINYINT(1) DEFAULT 0',
				'ALTER TABLE hoopla_settings ADD COLUMN runFullUpdateFlex TINYINT(1) DEFAULT 0',
				'ALTER TABLE hoopla_settings ADD COLUMN lastUpdateOfChangedRecordsFlex INT(11) DEFAULT 0',
				'ALTER TABLE hoopla_settings ADD COLUMN lastUpdateOfAllRecordsFlex INT(11) DEFAULT 0',
				]
		],//hoopla_settings_updates
		'hoopla_flex_availability' => [
			'title' => 'Add Hoopla Flex Availability Table',
			'description' => 'Get availability for Hoopla Flex titles',
			'continueOnError' => false,
			'sql' => [
				'CREATE TABLE hoopla_flex_availability (
					id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
					hooplaId BIGINT NOT NULL,
					holdsQueueSize INT NOT NULL,
					availableCopies INT NOT NULL,
					totalCopies INT NOT NULL,
					status VARCHAR(10) NOT NULL,
					UNIQUE KEY `hooplaId` (hooplaId)
				)'
			]
		],//hoopla_flex_availability
		'hoopla_export_table_add_type_column' => [
			'title' => 'Add HooplaType Column to Hoopla Export Table',
			'description' => 'Add HooplaType column to hoopla_export table and update existing records to "Instant"',
			'continueOnError' => false,
			'sql' => [
				'ALTER TABLE hoopla_export ADD COLUMN hooplaType VARCHAR(10) DEFAULT NULL',
				'UPDATE hoopla_export SET hooplaType = "Instant" WHERE hooplaType IS NULL',
			]
		],//hoopla_export_table_add_type_column
		'hoopla_export_log_table_add_availability_column' => [
			'title' => 'Add numAvailabilityChanges to Hoopla Export Log',
			'description' => 'Add numAvailabilityChanges column to hoopla_export_log table',
			'continueOnError' => false,
			'sql' => [
				'ALTER TABLE hoopla_export_log ADD COLUMN numAvailabilityChanges INT DEFAULT 0'
			]
		],//hoopla_export_log_table_add_availability_column
		'hoopla_scopes_updates' => [
			'title' => 'Hoopla Scopes Updates',
			'description' => 'Add includeInstant and includeFlex columns to hoopla_scopes table',
			'continueOnError' => false,
			'sql' => [
				'ALTER TABLE hoopla_scopes ADD COLUMN includeInstant TINYINT(1) DEFAULT 1',
				'ALTER TABLE hoopla_scopes ADD COLUMN includeFlex TINYINT(1) DEFAULT 0'
			]
		],//hoopla_scopes_updates
		'hoopla_hold_queue_size_confirmation' => [
			'title' => 'Add hooplaHoldQueueSizeConfirmation column to user table',
			'description' => 'Add hooplaHoldQueueSizeConfirmation column to user table',
			'continueOnError' => false,
			'sql' => [
				'ALTER TABLE user ADD COLUMN hooplaHoldQueueSizeConfirmation TINYINT(3) DEFAULT 1'
			]
		],//hoopla_hold_queue_size_confirmation
		'hoopla_export_token_store' => [
			'title' => 'Hoopla Export Token Store',
			'description' => 'Add token store to hoopla settings table',
			'sql' => [
				"ALTER TABLE hoopla_settings ADD COLUMN accessToken VARCHAR(255) DEFAULT NULL",
				"ALTER TABLE hoopla_settings ADD COLUMN tokenExpirationTime INT(11) DEFAULT NULL",
			],
		], //hoopla_export_token_store
		'hoopla_record_usage_add_times_held' => [
			'title' => 'Add timesHeld column to hoopla_record_usage table',
			'description' => 'Add a field to store the number of times a hoopla record has been held for Hoopla Dashboard',
			'continueOnError' => false,
			'sql' => [
				"ALTER TABLE hoopla_record_usage add column timesHeld INT DEFAULT 0",
			]
		], //hoopla_record_usage_add_times_held
		'hoopla_index_by_day_remove' => [
			'title' => 'Remove indexByDay column from hoopla_settings table',
			'description' => 'Remove indexByDay column from hoopla_settings table',
			'continueOnError' => false,
			'sql' => [
				"ALTER TABLE hoopla_settings DROP COLUMN indexByDay",
			]
		], //hoopla_index_by_day_remove


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