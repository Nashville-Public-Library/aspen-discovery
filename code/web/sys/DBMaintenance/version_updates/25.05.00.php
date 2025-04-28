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
		'system_variables_add_lida_github_repository' => [
			'title' => 'system_variables_add_lida_github_repository',
			'description' => 'Add a field to store the github repository for LiDA within System Variables to load release notes from',
			'continueOnError' => false,
			'sql' => [
				"ALTER TABLE system_variables add column lidaGitHubRepository VARCHAR(255) DEFAULT 'https://github.com/Aspen-Discovery/aspen-lida'",
			]
		], //system_variables_add_lida_github_repository

		//katherine - Grove

		//kirstien - Grove

		//kodi - Grove

		//Yanjun Li - ByWater

		// Leo Stoyanov - BWS

		//alexander - PTFS-Europe

		//chloe - Open Fifth
		'permanentUrl_allows_longer_strings' => [
			'title' => 'PermanentUrl Allows For Longer Strings',
			'description' => 'Allow for longer permanent URLs so that Open Archive records can be indexed without clashing with the length constraint',
			'continueOnError' => false,
			'sql' => [
				"ALTER TABLE open_archives_record MODIFY COLUMN permanentUrl VARCHAR(2048) NOT NULL",
			]
		], // permanentUrl_allows_longer_strings

		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions

		//other

	];
}