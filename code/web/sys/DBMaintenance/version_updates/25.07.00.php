<?php

function getUpdates25_07_00(): array {
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
		'add_series_member_priority_score' => [
			'title' => 'Add a priority score to series member table',
			'description' => 'Add a priority score to series members to sort series prioritizing MARC field 800 over 830',
			'sql' => [
				"ALTER TABLE series_member ADD COLUMN priorityScore TINYINT NOT NULL DEFAULT 1;",
			]
		], //add_series_member_priority_score

		//kirstien - Grove

		//kodi - Grove

		//Mark - Grove

		// Myranda - Grove

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
