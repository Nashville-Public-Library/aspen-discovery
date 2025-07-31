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

		// Laura Escamilla - ByWater Solutions

		//alexander - Open Fifth

		//chloe - Open Fifth


		//Jacob - Open Fifth

		//James Staub - Nashville Public Library
		'librarian_facebook_report_permissions' => [
			'title' => 'View Librarian Facebook report permissions',
			'description' => 'Create permissions for Librarian Facebook report',
			'continueOnError' => true,
			'sql' => [
				"INSERT INTO permissions (sectionName, name, requiredModule, weight, description) VALUES 
					('Circulation Reports', 'View Librarian Facebook', '', 70, 'Allows the user to view the Librarian Facebook.')
				",
			]
		], //librarian_facebook_report_permissions

		//Lucas Montoya - Theke Solutions

		//other

		//Talpa Search
		
	];
}
