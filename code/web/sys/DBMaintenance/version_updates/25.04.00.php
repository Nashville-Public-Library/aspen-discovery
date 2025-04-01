<?php

function getUpdates25_04_00(): array {
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
		'restrict_local_ill_by_patron_type' => [
			'title' => 'Restrict Local ILL by Patron Type',
			'description' => 'Add an option to restrict local ILL by Patron Type',
			'continueOnError' => false,
			'sql' => [
				'ALTER TABLE ptype ADD COLUMN allowLocalIll TINYINT DEFAULT  1'
			]
		], //restrict_local_ill_by_patron_type

		//katherine - Grove

		//kirstien - Grove

		//kodi - Grove

		//Yanjun Li - ByWater

		// Leo Stoyanov - BWS
		'fix_nyt_user_home_location' => [
			'title' => 'Fix NYT User Home Location',
			'description' => 'Set nyt_user home location to -1 to ensure NYT lists are visible in consortia when "Lists from library list publishers Only" is selected.',
			'continueOnError' => true,
			'sql' => [
				"UPDATE user SET homeLocationId = -1 WHERE username = 'nyt_user' AND source = 'admin'",
			],
		], //fix_nyt_user_home_location

		//alexander - PTFS-Europe

		//chloe - PTFS-Europe

		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions

		//other

	];
}