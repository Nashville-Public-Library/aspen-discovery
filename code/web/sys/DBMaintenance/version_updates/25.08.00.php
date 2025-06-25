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

		//Pedro - Open Fifth
		'add_timestamp_to_ce_campaign_milestone_progress_entries' => [
			'title' => 'Update ce_campaign_milestone_progress_entries',
			'description' => 'Add timestamp to ce_campaign_milestone_progress_entries',
			'sql' => [
				"ALTER TABLE ce_campaign_milestone_progress_entries ADD COLUMN `timestamp` datetime NOT NULL DEFAULT (CURRENT_TIME)",
			],
		], //add_timestamp_to_ce_campaign_milestone_progress_entries


		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions

		//other

		//Talpa Search
		
	];
}
