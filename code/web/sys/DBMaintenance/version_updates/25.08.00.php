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
		'sierra_self_reg_enhancement_settings' => [
			'title' => 'Add new settings for Sierra Self Registration',
			'description' => 'Add new settings to Sierra Self Registration',
			'continueOnError' => false,
			'sql' => [
				'ALTER TABLE self_registration_form_sierra ADD COLUMN selfRegNoDuplicateCheck TINYINT(1) DEFAULT 0;',
				'ALTER TABLE self_registration_form_sierra ADD COLUMN selfRegUseAgency TINYINT(1) DEFAULT 0;',
				'ALTER TABLE self_registration_form_sierra ADD COLUMN selfRegUsePatronIdBarcode TINYINT(1) DEFAULT 0;',
				'ALTER TABLE self_registration_form_sierra ADD COLUMN selfRegNoticePrefOptions VARCHAR(255) DEFAULT "";',
				'ALTER TABLE self_registration_tos ADD COLUMN showTOSFirst TINYINT(1) DEFAULT 0;',
				'ALTER TABLE library ADD COLUMN logSelfRegistrations TINYINT(1) DEFAULT 0;',
				"INSERT INTO permissions (sectionName, name, requiredModule, weight, description) VALUES
					('Cataloging & eContent', 'Manage Self Registration Municipalities', '', 23, 'Allows the user to alter self registration form municipality settings for all libraries');",
				"INSERT INTO permissions (sectionName, name, requiredModule, weight, description) VALUES
					('Cataloging & eContent', 'Review Self Registrations for All Libraries', '', 25, 'Allows the user to review and approve self registrations for all libraries');",
				"INSERT INTO permissions (sectionName, name, requiredModule, weight, description) VALUES
					('Cataloging & eContent', 'Review Self Registrations for Home Library Only', '', 25, 'Allows the user to review and approve self registrations for their home library');",
				"INSERT INTO `permission_groups` (`groupKey`,`sectionName`,`label`,`description`) VALUES
					('adminReviewRegistrations','Cataloging & eContent','Review Self Registrations','Specify whether the role can review all registrations or only those for its home library.');",
				"INSERT INTO `permission_group_permissions` (`groupId`,`permissionId`) SELECT pg.id, p.id FROM `permission_groups` pg JOIN `permissions` p ON p.name IN ('Review Self Registrations for Home Library Only','Review Self Registrations for All Libraries') WHERE pg.groupKey = 'adminReviewRegistrations';",
				"CREATE TABLE IF NOT EXISTS self_reg_municipality_values_sierra (
					`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					`selfRegistrationFormId` int(11) NOT NULL,
					`municipality` varchar(255) default '' NOT NULL,
					`municipalityType` varchar(10),
					`selfRegAllowed` tinyint(1) NOT NULL DEFAULT '1',
					`sierraPType` int(11) DEFAULT NULL,
					`sierraPTypeApproved` int(11) DEFAULT NULL,
					`sierraPCode1` varchar(25) DEFAULT NULL,
					`sierraPCode2` varchar(25) DEFAULT NULL,
					`sierraPCode3` int DEFAULT NULL,
					`sierraPCode4` int DEFAULT NULL,					
					`expirationLength` tinyint,
					`expirationPeriod` char DEFAULT 'd'
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
				"CREATE TABLE IF NOT EXISTS self_registration_sierra (
					`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					`dateRegistered` datetime NOT NULL DEFAULT current_timestamp,
					`barcode` varchar(255) NOT NULL,
					`sierraPType` int(11) DEFAULT NULL,
					`sierraPTypeApproved` int(11) DEFAULT NULL,
					`sierraPCode1` varchar(25) DEFAULT NULL,
					`sierraPCode2` varchar(25) DEFAULT NULL,
					`sierraPCode3` int DEFAULT NULL,
					`sierraPCode4` int DEFAULT NULL,
					`libraryId` int(11) DEFAULT NULL,			
					`locationId` int(11) DEFAULT NULL,
					`approved` tinyint(1) NOT NULL DEFAULT '0',
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
			]
		], //sierra_self_reg_enhancements

		//kirstien - Grove

		//kodi - Grove

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
