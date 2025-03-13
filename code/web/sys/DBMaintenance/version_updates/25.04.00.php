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

		//katherine - Grove

		//kirstien - Grove

		//kodi - Grove

		//Yanjun Li - ByWater

		// Leo Stoyanov - BWS
		'remove_palace_project_regroup_flag' => [
			'title' => 'Remove Unused Palace Project Regroup Option',
			'description' => 'Remove regroupAllRecords column from palace_project_settings table as it is never used.',
			'continueOnError' => false,
			'sql' => [
				'ALTER TABLE palace_project_settings DROP COLUMN IF EXISTS regroupAllRecords'
			]
		], //remove_palace_project_regroup_flag

		//alexander - PTFS-Europe

		//chloe - PTFS-Europe

		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions

		//other

	];
}