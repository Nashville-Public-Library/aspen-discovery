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

		'track_event_length_in_minutes' => [
			'title' => 'Track Event Length In Minutes',
			'description' => 'Multiply existing event lengths by 60 to get minutes',
			'sql' => [
				'UPDATE event SET eventLength = eventLength * 60;',
				'UPDATE event_instance SET length = length * 60;'
			]
		], //track_event_length_in_minutes
		'event_calendar_display_settings' => [
			'title' => 'Event Calendar Display Settings',
			'description' => 'Add table to store calendar display settings',
			'sql' => [
				"CREATE TABLE calendar_display_settings (
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
					name VARCHAR(50),
					cover VARCHAR(100),
					altText VARCHAR(100)
				) ENGINE INNODB CHARACTER SET utf8 COLLATE utf8_general_ci",
			]
		], //event_calendar_display_settings

		//kirstien - Grove

		// Leo Stoyanov - BWS

		//alexander - PTFS-Europe

		//chloe - PTFS-Europe

		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions

		//other

	];
}