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
		'object_restoration_permission' => [
			'title' => 'Add Object Restoration Permission',
			'description' => 'Add new permission to allow administrators to restore soft-deleted objects.',
			'continueOnError' => true,
			'sql' => [
				"INSERT INTO permissions (sectionName, name, requiredModule, weight, description) VALUES ('System Administration', 'Administer Object Restoration', '', 13, 'Allows the user to view and restore soft-deleted objects (e.g., User Lists) within Aspen.')",
			],
		],// object_restoration_permission
		'add_soft_delete_columns' => [
			'title' => 'Add Soft-Delete Columns to Supported Tables',
			'description' => 'Ensure tables for soft-deletable objects have deleted and dateDeleted columns.',
			'continueOnError' => true,
			'sql' => [
				"ALTER TABLE user_list ADD COLUMN IF NOT EXISTS deleted TINYINT(1) DEFAULT 0",
				"ALTER TABLE user_list ADD COLUMN IF NOT EXISTS dateDeleted INT(11) DEFAULT 0",
				"ALTER TABLE user_list ADD COLUMN IF NOT EXISTS deletedBy INT(11) DEFAULT NULL",
				"ALTER TABLE user_list_entry ADD COLUMN IF NOT EXISTS deleted TINYINT(1) DEFAULT 0",
				"ALTER TABLE user_list_entry ADD COLUMN IF NOT EXISTS dateDeleted INT(11) DEFAULT 0",
				"ALTER TABLE user_list_entry ADD COLUMN IF NOT EXISTS deletedBy INT(11) DEFAULT NULL",
				"ALTER TABLE web_builder_basic_page ADD COLUMN IF NOT EXISTS deleted TINYINT(1) DEFAULT 0",
				"ALTER TABLE web_builder_basic_page ADD COLUMN IF NOT EXISTS dateDeleted INT(11) DEFAULT 0",
				"ALTER TABLE web_builder_basic_page ADD COLUMN IF NOT EXISTS deletedBy INT(11) DEFAULT NULL",
				"ALTER TABLE web_builder_portal_page ADD COLUMN IF NOT EXISTS deleted TINYINT(1) DEFAULT 0",
				"ALTER TABLE web_builder_portal_page ADD COLUMN IF NOT EXISTS dateDeleted INT(11) DEFAULT 0",
				"ALTER TABLE web_builder_portal_page ADD COLUMN IF NOT EXISTS deletedBy INT(11) DEFAULT NULL",
				"ALTER TABLE web_builder_custom_form ADD COLUMN IF NOT EXISTS deleted TINYINT(1) DEFAULT 0",
				"ALTER TABLE web_builder_custom_form ADD COLUMN IF NOT EXISTS dateDeleted INT(11) DEFAULT 0",
				"ALTER TABLE web_builder_custom_form ADD COLUMN IF NOT EXISTS deletedBy INT(11) DEFAULT NULL",
				"ALTER TABLE web_builder_custom_web_resource_page ADD COLUMN IF NOT EXISTS deleted TINYINT(1) DEFAULT 0",
				"ALTER TABLE web_builder_custom_web_resource_page ADD COLUMN IF NOT EXISTS dateDeleted INT(11) DEFAULT 0",
				"ALTER TABLE web_builder_custom_web_resource_page ADD COLUMN IF NOT EXISTS deletedBy INT(11) DEFAULT NULL",
				"ALTER TABLE web_builder_resource ADD COLUMN IF NOT EXISTS deleted TINYINT(1) DEFAULT 0",
				"ALTER TABLE web_builder_resource ADD COLUMN IF NOT EXISTS dateDeleted INT(11) DEFAULT 0",
				"ALTER TABLE web_builder_resource ADD COLUMN IF NOT EXISTS deletedBy INT(11) DEFAULT NULL",
				"ALTER TABLE image_uploads ADD COLUMN IF NOT EXISTS deleted TINYINT(1) DEFAULT 0",
				"ALTER TABLE image_uploads ADD COLUMN IF NOT EXISTS dateDeleted INT(11) DEFAULT 0",
				"ALTER TABLE image_uploads ADD COLUMN IF NOT EXISTS deletedBy INT(11) DEFAULT NULL",
				"ALTER TABLE file_uploads ADD COLUMN IF NOT EXISTS deleted TINYINT(1) DEFAULT 0",
				"ALTER TABLE file_uploads ADD COLUMN IF NOT EXISTS dateDeleted INT(11) DEFAULT 0",
				"ALTER TABLE file_uploads ADD COLUMN IF NOT EXISTS deletedBy INT(11) DEFAULT NULL",
				"ALTER TABLE placards ADD COLUMN IF NOT EXISTS deleted TINYINT(1) DEFAULT 0",
				"ALTER TABLE placards ADD COLUMN IF NOT EXISTS dateDeleted INT(11) DEFAULT 0",
				"ALTER TABLE placards ADD COLUMN IF NOT EXISTS deletedBy INT(11) DEFAULT NULL",
			],
		],// add_soft_delete_columns

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
