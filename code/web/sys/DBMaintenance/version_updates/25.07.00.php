<?php

function getUpdates25_07_00(): array {
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

		//Mark - Grove

		// Myranda - Grove

		//Yanjun Li - ByWater

		// Leo Stoyanov - BWS

		// Laura Escamilla - ByWater Solutions

		//alexander - Open Fifth

		//chloe - Open Fifth

		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions

		//other

		//Talpa Search
		'talpa_settings_defaults_update_07_25' => [
			'title' => 'Update to Talpa Default "Other Results" Explainer Text',
			'description' => 'Updates the default value of talpaOtherResultsExplainerText to clarify results are not owned by the user’s library.',
			'sql' => [
				"ALTER TABLE talpa_settings MODIFY COLUMN talpaOtherResultsExplainerText VARCHAR(180) DEFAULT 'Talpa Search found these other results not owned by your library.'"
			]
		]

	];
}
