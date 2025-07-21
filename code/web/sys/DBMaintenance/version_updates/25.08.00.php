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
		'add_updating_contact_info_from_ils' => [
			'title' => 'Add the Option to "Automatically Update Contact Information from the ILS"',
			'description' => 'Add the Option to "Automatically Update Contact Information from the ILS" ',
			'continueOnError' => false,
			'sql' => [
				"ALTER TABLE location ADD COLUMN allowUpdatingContactInfoFromILS TINYINT(1) DEFAULT 0",
			],
		], // add_updating_contact_info_from_ils

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
