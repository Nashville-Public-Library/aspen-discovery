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
		'update_record_to_include_defaults' => [
			'title' => 'Update RecordToInclude Column Defaults to Match PHP Defaults',
			'description' => 'Update database column defaults for includeHoldableOnly, includeItemsOnOrder, and includeEContent to match the PHP class defaults.',
			'continueOnError' => false,
			'sql' => [
				'ALTER TABLE library_records_to_include CHANGE COLUMN includeHoldableOnly includeHoldableOnly tinyint(1) NOT NULL DEFAULT 0',
				'ALTER TABLE library_records_to_include CHANGE COLUMN includeItemsOnOrder includeItemsOnOrder tinyint(1) NOT NULL DEFAULT 1',
				'ALTER TABLE library_records_to_include CHANGE COLUMN includeEContent includeEContent tinyint(1) NOT NULL DEFAULT 1',
				'ALTER TABLE location_records_to_include CHANGE COLUMN includeHoldableOnly includeHoldableOnly tinyint(1) NOT NULL DEFAULT 0',
				'ALTER TABLE location_records_to_include CHANGE COLUMN includeItemsOnOrder includeItemsOnOrder tinyint(1) NOT NULL DEFAULT 1',
				'ALTER TABLE location_records_to_include CHANGE COLUMN includeEContent includeEContent tinyint(1) NOT NULL DEFAULT 1',
			]
		], //update_record_to_include_defaults

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
