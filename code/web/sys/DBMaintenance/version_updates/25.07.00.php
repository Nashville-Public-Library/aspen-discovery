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
		'snappay_settings_add_reconciliation' => [
			'title' => 'SnapPay Settings Add Automated Reconciliation',
			'description' => 'Add Automated Reconciliation to SnapPay Settings',
			'sql' => [
				"ALTER TABLE snappay_settings ADD COLUMN enableAutomatedReconciliation TINYINT(1) DEFAULT 0",
				"ALTER TABLE snappay_settings ADD COLUMN automatedReconciliationFrequency INT DEFAULT 60",
				"ALTER TABLE snappay_settings ADD COLUMN automatedReconciliationFilters VARCHAR(255) DEFAULT '{\"status\":\"success\"}'",
			],
		],

		'snappay_settings_add_last_reconciliation_time' => [
			'title' => 'SnapPay Settings Add Last Reconciliation Time',
			'description' => 'Add field to track when reconciliation was last run',
			'sql' => [
				"ALTER TABLE snappay_settings ADD COLUMN lastReconciliationTime INT DEFAULT 0",
			],
		],

		'snappay_settings_add_api_basic_auth_password' => [
			'title' => 'SnapPay Settings Add SnapPay API Basic Auth Password',
			'description' => 'Add file for SnapPay API Basic Auth Password',
			'sql' => [
				"ALTER TABLE snappay_settings ADD COLUMN apiBasicAuthPassword VARCHAR(50) DEFAULT NULL",
			],
		],

		//Lucas Montoya - Theke Solutions

		//other

	];
}
