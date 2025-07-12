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
				'ALTER TABLE self_registration_form_sierra ADD COLUMN selfRegNoDuplicateCheck TINYINT(1) DEFAULT 0'
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
