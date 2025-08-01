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
		'library_local_ill_email' => [
			'title' => 'Library - Local ILL Email',
			'description' => 'Add Local ILL Email to Library Settings',
			'continueOnError' => false,
			'sql' => [
				"ALTER TABLE library ADD COLUMN localIllEmail varchar(255) default ''"
			]
		], //library_local_ill_email
		'materials_request_add_source' => [
			'title' => 'Materials Request - Add Source',
			'description' => 'Add Source to Materials Request to differentiate between Local ILL and standard requests',
			'sql' => [
				"ALTER TABLE materials_request ADD COLUMN source TINYINT DEFAULT 1"
			]
		], //materials_request_add_source

		//katherine - Grove

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
