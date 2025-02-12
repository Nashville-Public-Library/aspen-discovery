<?php

function getUpdates25_03_00(): array {
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

		'add_series_settings' => [
			'title' => 'Add Series Search settings to Library Systems',
			'description' => 'Add Series Search settings to Library Systems',
			'continueOnError' => true,
			'sql' => [
				"ALTER TABLE library ADD COLUMN useSeriesSearchIndex TINYINT(1) DEFAULT 0"
			]
		], //add_series_settings
		'add_series_search_tables' => [
			'title' => 'Add Series Search tables',
			'description' => 'Add Series Search tables',
			'continueOnError' => true,
			'sql' => [
				"CREATE TABLE series (
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
					displayName VARCHAR(500),
					description TEXT,
					cover VARCHAR(50),
					audience VARCHAR(25),
					isIndexed TINYINT(1) DEFAULT 0
				) ENGINE INNODB CHARACTER SET utf8 COLLATE utf8_general_ci",
				"CREATE TABLE series_member (
    				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    				isPlaceholder TINYINT(1) DEFAULT 0,
					groupedWorkId CHAR(40),
					displayName VARCHAR(500),
					author VARCHAR(200),
					description TEXT,
					cover VARCHAR(50),
					volume VARCHAR(50),
					pubDate Date,
					weight INT NOT NULL DEFAULT 0
				) ENGINE INNODB CHARACTER SET utf8 COLLATE utf8_general_ci",
			]
		], //add_series_tables

		//kirstien - Grove

		// Leo Stoyanov - BWS

		//alexander - PTFS-Europe

		//chloe - PTFS-Europe

		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions

		//other

	];
}