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

		//Yanjun Li - ByWater
		'prefer_ils_description' => [
			'title' => 'Prefer ILS Description',
			'description' => 'Add a new setting to prefer ILS Description over eContent Description',
			'sql' => [
				"ALTER TABLE grouped_work_display_settings ADD COLUMN preferIlsDescription TINYINT(1) DEFAULT 0",
			]
		], //prefer_ils_description

		//mark - Grove

		//katherine - Grove

		//kirstien - Grove

		// Leo Stoyanov - BWS

		//alexander - PTFS-Europe

		//chloe - PTFS-Europe

		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions

		//other

	];
}