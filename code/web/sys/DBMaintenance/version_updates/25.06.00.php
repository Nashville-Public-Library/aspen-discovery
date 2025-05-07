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
		'add_lida_barcode_entry_keyboard_type_setting' => [
			'title' => 'Add a barcode entry keyboard type field to LiDA self check settings',
			'description' => 'Add keyboard type options to use numeric keyboard, alphanumeric keyboard, or not to allow keyboard entry',
			'sql' => [
				"ALTER TABLE aspen_lida_self_check_settings ADD COLUMN barcodeEntryKeyboardType TINYINT(1) NOT NULL DEFAULT 1;",
			]
		], //add_lida_barcode_entry_keyboard_type_setting

		//kirstien - Grove

		//kodi - Grove

		//Yanjun Li - ByWater

		// Leo Stoyanov - BWS

		// Laura Escamilla - ByWater Solutions

		//alexander - Open Fifth

		//chloe - Open Fifth

		//James Staub - Nashville Public Library

		//Lucas Montoya - Theke Solutions

		//other

	];
}
