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
		'lida_loading_messages' => [
			'title' => 'LiDA Loading messages',
			'description' => 'Update Branded App settings to include the type of loading messages to show as well as configuration of startup meessages',
			'sql' => [
				'ALTER TABLE aspen_lida_branded_settings ADD COLUMN loadingMessageType TINYINT DEFAULT 0',
				'CREATE TABLE lida_loading_messages (
					id INT PRIMARY KEY AUTO_INCREMENT,
					brandedAppSettingId INT(11) NOT NULL, 
					message VARCHAR(255) NOT NULL
				)'
			]
		], //lida_loading_messages

		//katherine - Grove
		'add_deleted_field_to_series_member' => [
			'title' => 'Add a deleted field to Series Member table',
			'description' => 'Add a deleted field so that Series Members can be deleted',
			'sql' => [
				"ALTER TABLE series_member ADD COLUMN deleted TINYINT(1) DEFAULT 0",
			]
		], //add_deleted_field_to_series_member
		'remove_duplicate_series_titles' => [
			'title' => 'Remove duplicate series titles',
			'description' => 'Clean up series that have more than one copy of the same title unless it was manually added or has already been excluded',
			'sql' => [
				"DELETE t1 FROM series_member t1 INNER JOIN series_member t2
				WHERE t1.groupedWorkPermanentId = t2.groupedWorkPermanentId	AND t1.seriesId = t2.seriesId
				AND t1.userAdded = 0 AND t1.excluded = 0
				AND t2.userAdded = 0 AND t2.excluded = 0
				AND t1.id > t2.id;",
			]
		],

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
		'library_add_palace_project_library_id' => [
			'title' => 'library_add_palace_project_library_id',
			'description' => 'Add a field to store the palace project library id for the library',
			'continueOnError' => false,
			'sql' => [
				"ALTER TABLE library add column palaceProjectLibraryId VARCHAR(50) DEFAULT NULL",
			]
		], //library_add_palace_project_library_id


		// Leo Stoyanov - BWS
		'add_placard_image_max_height_to_themes' => [
			'title' => 'Add Placard Image Max Height to Themes',
			'description' => 'Adds a placardImageMaxHeight column to the themes table to control placard image height.',
			'sql' => [
				"ALTER TABLE themes ADD COLUMN IF NOT EXISTS `placardImageMaxHeight` INT DEFAULT 0",
			]
		], //add_placard_image_max_height_to_themes
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
		'show_patron_type_on_library_card' => [
			'title' => 'Add Show Patron Type Option',
			'description' => 'Adds a setting to display patron type under the barcode on the My Library Card page.',
			'sql' => [
				"ALTER TABLE library ADD COLUMN IF NOT EXISTS showPatronTypeOnCard TINYINT(1) DEFAULT 0",
			]
		],//show_patron_type_on_library_card

		// Laura Escamilla - ByWater Solutions

               'blueskyLink' => [
                      'title' => 'Bluesky Link', 
                      'description' => 'The URL to Bluesky (leave blank if the library does not have a Bluesky account)',
                      'continueOnError' => true,
                      'sql' => [
                               "ALTER TABLE library ADD COLUMN blueskyLink VARCHAR(255)",
                               ], 
                ], // blueskyLink

               'threadsLink' => [
                      'title' => 'Threads Link',
                      'description' => 'The URL to Threads (leave blank if the library does not have a Threads account)',
                      'continueOnError' => true,
                      'sql' => [
                               "ALTER TABLE library ADD COLUMN threadsLink VARCHAR(255)",
                               ],
                ], // threadsLink

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
		'add_ability_to_highlight_campaigns_in_account_area' => [
			'title' => 'Add Ability to Highlight Campaigns In Account Area',
			'description' => 'Allow libraries to choose whether to display a block highlighting campaigns on the account page',
			'sql' => [
				"ALTER TABLE library ADD COLUMN highlightCommunityEngagement TINYINT(1) DEFAULT 0",
			],
		], //add_ability_to_highlight_campaigns_in_account_area
		'add_weight_to_campaign_milestones' => [
			'title' => 'Add Weight To Campaign Milestones',
			'description' => 'Add a weight column to campaign milestones to allow ordering',
			'sql' => [
				"ALTER TABLE ce_campaign_milestones ADD COLUMN weight int(11) NOT NULL DEFAULT 0",
			],
		], //add_weight_to_campaign_milestones

		//chloe - Open Fifth
		'add_audienceId_to_grouped_work_records' => [
			'title' => 'Add AudienceId To Grouped Work Records',
			'description' => 'So that audiences can be displayed on grouped work records, add an audienceId column to grouped work records.',
			'continueOnError' => false,
			'sql' => [
				"ALTER TABLE grouped_work_records ADD COLUMN audienceId INT(11) DEFAULT -1",
			]
		], //add_audienceId_to_grouped_work_records
		'create_indexed_audience' => [
			'title' => 'Create Indexed Audience',
			'description' => 'Create the indexed_audience table',
			'continueOnError' => false,
			'sql' => [
			'CREATE TABLE indexed_audience (
				id int(11) NOT NULL AUTO_INCREMENT,
				audience varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
				PRIMARY KEY (id),
				KEY audience (audience(500))
			)'
			]
		], //create_indexed_audience
		'permanentUrl_allows_longer_strings' => [
			'title' => 'PermanentUrl Allows For Longer Strings',
			'description' => 'Allow for longer permanent URLs so that Open Archive records can be indexed without clashing with the length constraint',
			'continueOnError' => false,
			'sql' => [
				"ALTER TABLE open_archives_record MODIFY COLUMN permanentUrl VARCHAR(2048) NOT NULL",
			]
		], // permanentUrl_allows_longer_strings

		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions
		'forceDebugLog' => [
			'title' => 'Enable Forced Logging of Debugging Information for Paypal, PayPalPayflow, Propay, InvoiceCloud, Square, Stripe, and ACISpeedPay Payments',
			'description' => 'Enable to show debugging information about Paypal payments',
			'sql' => [
				'ALTER TABLE paypal_settings ADD COLUMN forceDebugLog TINYINT(1) DEFAULT 0',
				'ALTER TABLE square_settings ADD COLUMN forceDebugLog TINYINT(1) DEFAULT 0',
				'ALTER TABLE stripe_settings ADD COLUMN forceDebugLog TINYINT(1) DEFAULT 0',
				'ALTER TABLE propay_settings ADD COLUMN forceDebugLog TINYINT(1) DEFAULT 0',
				'ALTER TABLE ncr_payments_settings ADD COLUMN forceDebugLog TINYINT(1) DEFAULT 0',
				'ALTER TABLE paypal_payflow_settings ADD COLUMN forceDebugLog TINYINT(1) DEFAULT 0',
				'ALTER TABLE aci_speedpay_settings ADD COLUMN forceDebugLog TINYINT(1) DEFAULT 0',
				'ALTER TABLE invoice_cloud_settings ADD COLUMN forceDebugLog TINYINT(1) DEFAULT 0',
			]
		], //enable_payments_debugging

		//other

	];
}
