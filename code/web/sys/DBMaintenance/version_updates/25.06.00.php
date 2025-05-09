<?php

function getUpdates25_06_00(): array {
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
		'side_loads_library_permissions' => [
			'title' => 'Side Load Home Library Permissions',
			'description' => 'Add permissions for administering side loads and side load scopes based on home library.',
			'sql' => [
				"INSERT INTO permissions (sectionName, name, requiredModule, weight, description) VALUES
					('Cataloging & eContent', 'Administer Side Loads for Home Library', 'Side Loads', 171, 'Allows the user to administer side loads for their home library only.'),
					('Cataloging & eContent', 'Administer Side Load Scopes for Home Library', 'Side Loads', 172, 'Allows the user to administer side load scopes for their home library only.')",
			],
		], //side_loads_library_permissions
		'side_loads_owning_and_sharing' => [
			'title' => 'Side Load Owning and Sharing Library',
			'description' => 'Add owning and sharing library to side loads table.',
			'sql' => [
				"ALTER TABLE sideloads ADD COLUMN owningLibrary INT(11) NOT NULL DEFAULT -1",
				"ALTER TABLE sideloads ADD COLUMN sharing INT(11) NOT NULL DEFAULT 1",
			],
		], //side_loads_owning_and_sharing

		//Yanjun Li - ByWater

		// Leo Stoyanov - BWS

		// Laura Escamilla - ByWater Solutions

		//alexander - Open Fifth

		//chloe - Open Fifth

		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions

		//other

	];
}
