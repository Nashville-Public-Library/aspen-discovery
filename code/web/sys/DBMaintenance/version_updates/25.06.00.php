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

		//Yanjun Li - ByWater

		// Leo Stoyanov - BWS
		'add_num_regrouped_to_cloudlibrary_extract_logs' => [
			'title' => 'Add numRegrouped Column to CloudLibrary Extract Logs',
			'description' => 'Adds a numRegrouped column to the cloud_library_export_log table to track the number of works regrouped during an extract.',
			'sql' => [
				"ALTER TABLE cloud_library_export_log ADD COLUMN IF NOT EXISTS numRegrouped int(11) DEFAULT 0 AFTER settingId",
			]
		], //add_num_regrouped_to_cloudlibrary_extract_logs

		// Laura Escamilla - ByWater Solutions

		//alexander - Open Fifth

		//chloe - Open Fifth

		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions

		//other

	];
}
