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

		//kirstien - Grove

		// Leo Stoyanov - BWS
		'useOriginalCoverUrls' => [
			'title' => 'Add Option to Use Original Cover URLs',
			'description' => 'Add an option to allow the use of original cover URLs rather than cached images in the file system.',
			'continueOnError' => false,
			'sql' => [
				'ALTER TABLE system_variables ADD COLUMN useOriginalCoverUrls TINYINT(1) DEFAULT 0'
			]
		],

		//alexander - PTFS-Europe

		//chloe - PTFS-Europe

		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions

		//other

	];
}