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
		'add_max_hold_cancellation_date_field' => [
			'title' => 'Add Max Hold Cancellation Date Field',
			'description' => 'Add the Max Hold Cancellation Date field for when hold cancellations are enabled.',
			'continueOnError' => true,
			'sql' => [
				'ALTER TABLE library ADD COLUMN IF NOT EXISTS maxHoldCancellationDate int(11) DEFAULT -1 AFTER defaultNotNeededAfterDays;'
			]
		], //add_max_hold_cancellation_date_field

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
