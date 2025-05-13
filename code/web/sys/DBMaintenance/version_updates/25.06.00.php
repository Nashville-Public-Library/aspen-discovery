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
		'delete_orphaned_series_members' => [
			'title' => 'Delete Orphaned Series Members',
			'description' => 'Delete Series Members that are no longer linked to a valid grouped Work',
			'continueOnError' => false,
			'sql' => [
				'DELETE from series_member where id IN (select series_member.id from series_member left join grouped_work on series_member.groupedWorkPermanentId = permanent_id left join grouped_work_records on grouped_work_records.groupedWorkId = grouped_work.id where grouped_work_records.groupedWorkId IS NULL and userAdded = 0);'
			]
		], //delete_orphaned_series_members
		'correct_default_include_only_holdable_for_records_to_include' => [
			'title' => 'Correct Default Include Only Holdable for Records To Include',
			'description' => 'Correct Default Include Only Holdable for Records To Include',
			'continueOnError' => false,
			'sql' => [
				'ALTER TABLE library_records_to_include CHANGE COLUMN includeHoldableOnly includeHoldableOnly tinyint(1) NOT NULL DEFAULT 0',
				'ALTER TABLE location_records_to_include CHANGE COLUMN includeHoldableOnly includeHoldableOnly tinyint(1) NOT NULL DEFAULT 0'
			]
		], //correct_default_include_only_holdable_for_records_to_include

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
