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

		//alexander - PTFS-Europe
		'allow_filtering_of_linked_users_in_holds' => [
			'title' => 'Allow Filtering of Linked Users in Holds',
			'description' => 'Allow libraries the option of allowing users to filter their holds by linked user',
			'sql' => [
				'ALTER TABLE library ADD COLUMN allowFilteringOfLinkedAccountsInHolds TINYINT(1) DEFAULT 0',
			]
		],
		'allow_selecting_holds_to_display' => [
			'title' => 'Allow Selecting Holds to Display',
			'description' => 'Allow libraries the option of allowing users to display only selected holds',
			'sql' => [
				'ALTER TABLE library ADD COLUMN allowSelectingHoldsToDisplay TINYINT(1) DEFAULT 0',
			]
		],

		//chloe - PTFS-Europe

		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions

		//other

	];
}