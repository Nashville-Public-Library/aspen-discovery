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

		//katherine - Grove

		//kirstien - Grove

		//kodi - Grove

		//Yanjun Li - ByWater

		// Leo Stoyanov - BWS
		'show_patron_type_on_library_card' => [
			'title' => 'Add Show Patron Type Option',
			'description' => 'Adds a setting to display patron type under the barcode on the My Library Card page.',
			'sql' => [
				"ALTER TABLE library ADD COLUMN IF NOT EXISTS showPatronTypeOnCard TINYINT(1) DEFAULT 0",
			]
		],//show_patron_type_on_library_card

		//alexander - PTFS-Europe

		//chloe - PTFS-Europe

		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions

		//other

	];
}