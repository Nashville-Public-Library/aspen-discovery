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
		'reading_history_columns_and_index' => [
			'title' => 'Add Force Reading History Load Flag, Reading History Import Start Datetime, & Index',
			'description' => 'Add a flag to force immediate loading of reading history for users, a reading history import start datetime, and an index of initial reading history loaded and the previous two new columns.',
			'continueOnError' => false,
			'sql' => [
				"ALTER TABLE user ADD COLUMN IF NOT EXISTS forceReadingHistoryLoad TINYINT(1) DEFAULT 0",
				"ALTER TABLE user ADD COLUMN IF NOT EXISTS readingHistoryImportStartedAt DATETIME DEFAULT NULL",
				"DROP INDEX IF EXISTS idx_reading_history_import_status ON user",
				"CREATE INDEX idx_reading_history_import_status ON user (initialReadingHistoryLoaded, forceReadingHistoryLoad, readingHistoryImportStartedAt)"
			]
		], //reading_history_columns_and_index

		// Laura Escamilla - ByWater Solutions

		//alexander - Open Fifth

		//chloe - Open Fifth

		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions

		//other

	];
}
