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
		'library_you_might_also_like' => [
			'title' => 'Library You Might Also Like Setting',
			'description' => 'Add a setting for libraries for the "You Might Also Like" feature to disable or enable with restrictions.',
			'sql' => [
				"ALTER TABLE library ADD COLUMN showYouMightAlsoLike TINYINT(1) DEFAULT 1;",
				"UPDATE library SET showYouMightAlsoLike =0 WHERE showWhileYouWait=0;"
			],
		], //library_you_might_also_like
		// Myranda - Grove

		//Yanjun Li - ByWater

		// Leo Stoyanov - BWS

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
