<?php

function getUpdates25_03_00(): array {
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
		'make_app_icons_os_specific' => [
			'title' => 'Make App Icons OS Specific',
			'description' => 'Update settings to store separate icons per OS',
			'continueOnError' => false,
			'sql' => [
				'ALTER TABLE aspen_lida_branded_settings add COLUMN logoAppIconAndroid varchar(100) DEFAULT NULL'
			]
		], //make_app_icons_os_specific

		//katherine - Grove

		//kirstien - Grove

		//kodi - Grove
		'descriptions_for_categories_audiences' => [
			'title' => 'Audience and Category Descriptions',
			'description' => 'Add descriptions for categories and audiences in Web Builder',
			'continueOnError' => false,
			'sql' => [
				'ALTER TABLE web_builder_audience ADD COLUMN description mediumtext DEFAULT NULL',
				'ALTER TABLE web_builder_category ADD COLUMN description mediumtext DEFAULT NULL'
			]
		]

		// Leo Stoyanov - BWS

		//alexander - PTFS-Europe

		//chloe - PTFS-Europe

		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions

		//other

	];
}