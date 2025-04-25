<?php

function getUpdates25_05_00(): array {
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

		//alexander - Open Fifth

		//chloe - Open Fifth
		'add_audienceId_to_grouped_work_records' => [
			'title' => 'Add AudienceId To Grouped Work Records',
			'description' => 'So that audiences can be displayed on grouped work records, add an audienceId column to grouped work records.',
			'continueOnError' => false,
			'sql' => [
				"ALTER TABLE grouped_work_records ADD COLUMN audienceId INT(11) DEFAULT -1",
			]
		], //add_audienceId_to_grouped_work_records
		'create_indexed_audience' => [
			'title' => 'Create Indexed Audience',
			'description' => 'Create the indexed_audience table',
			'continueOnError' => false,
			'sql' => [
			'CREATE TABLE indexed_audience (
				id int(11) NOT NULL AUTO_INCREMENT,
				audience varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
				PRIMARY KEY (id),
				KEY audience (audience(500))
			)'
			]
		], //create_indexed_audience

		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions

		//other

	];
}