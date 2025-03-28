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
		'force_regrouping_all_works_25_04' => [
			'title' => 'Force Regrouping All Works 25.04',
			'description' => 'Force Regrouping All Works',
			'sql' => [
				"UPDATE system_variables set regroupAllRecordsDuringNightlyIndex = 1",
			],
		], //force_regrouping_all_works_25_04

		//katherine - Grove

		//kirstien - Grove

		//kodi - Grove

		//Yanjun Li - ByWater

		// Leo Stoyanov - BWS

		//alexander - PTFS-Europe

		//chloe - PTFS-Europe

		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions

		//other

	];
}