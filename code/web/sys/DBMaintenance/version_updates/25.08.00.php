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
		'sierra_self_reg_enhancement_settings' => [
			'title' => 'Add new settings for Sierra Self Registration',
			'description' => 'Add new settings to Sierra Self Registration',
			'continueOnError' => false,
			'sql' => [
				'ALTER TABLE self_registration_form_sierra ADD COLUMN selfRegNoDuplicateCheck TINYINT(1) DEFAULT 0',
				'ALTER TABLE self_registration_form_sierra ADD COLUMN selfRegUseAgency TINYINT(1) DEFAULT 0',
				"CREATE TABLE IF NOT EXISTS sierra_self_reg_municipality_values (
					`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					`selfRegistrationFormId` int(11) NOT NULL,
					`municipality` varchar(255) default '' NOT NULL,
					`municipalityType` varchar(10),
					`selfRegAllowed` tinyint(1) NOT NULL DEFAULT '1',
					`sierraPType` int(11) DEFAULT NULL,
					`sierraPCode1` varchar(25) DEFAULT NULL,
					`sierraPCode2` varchar(25) DEFAULT NULL,
					`sierraPCode3` int DEFAULT NULL,
					`sierraPCode4` int DEFAULT NULL,					
					`expirationLength` tinyint,
					`expirationPeriod` varchar(10) DEFAULT 'day'
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
			]
		], //sierra_self_reg_enhancements

		//kirstien - Grove

		//kodi - Grove

		// Myranda - Grove

		//Yanjun Li - ByWater

		// Leo Stoyanov - BWS

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
