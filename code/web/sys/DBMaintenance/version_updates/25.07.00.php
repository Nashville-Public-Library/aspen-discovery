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
		'account_profile_enable_fetching_ils_messages' => [
			'title' => 'Add Enable Fetching ILS Messages to Account Profile',
			'description' => 'Add Enable Fetching ILS Messages to Account Profile',
			'continueOnError' => false,
			'sql' => [
				'ALTER TABLE account_profiles ADD COLUMN enableFetchingIlsMessages TINYINT(1) DEFAULT 0'
			]
		], //account_profile_enable_fetching_ils_messages
		'branded_app_notification_access_token' => [
			'title' => 'Add Notification Access Token To Branded App Settings',
			'description' => 'Add Notification Access Token To Branded App Settings',
			'continueOnError' => false,
			'sql' => [
				'ALTER TABLE aspen_lida_branded_settings ADD COLUMN notificationAccessToken varchar(256) DEFAULT NULL',
			]
		], //branded_app_notification_access_token
		'ils_notification_setting_account_profile' => [
			'title' => 'Link ILS Notification Setting to Account Profile',
			'description' => 'Link ILS Notification Setting to Account Profile',
			'continueOnError' => false,
			'sql' => [
				'ALTER TABLE ils_notification_setting ADD COLUMN accountProfileId INT(11) DEFAULT -1',
				"UPDATE ils_notification_setting SET accountProfileId = (SELECT id from account_profiles where name <> 'admin' and name <> 'admin_sso' LIMIT 1)",
			]
		], //ils_notification_setting_account_profile
		'remove_vendor_specific_defaults' => [
			'title' => 'Remove Vendor Specific Defaults',
			'description' => 'Remove Vendor Specific Default Values',
			'sql' => [
				"ALTER TABLE system_variables CHANGE COLUMN supportingCompany supportingCompany varchar(72) DEFAULT ''",
			]
		], //remove_vendor_specific_defaults
		'remember_page_defaults_for_user' => [
			'title' => 'Remember Page Size and Sort For User',
			'description' => 'Remember Page Size and Sort for User',
			'sql' => [
				'CREATE TABLE user_page_defaults (
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
					userId INT(11),
					module VARCHAR(100),
					action VARCHAR(100),
					objectId INT(11),
					pageSize INT(11),
					pageSort VARCHAR(25),
					UNIQUE INDEX (userId, module, action, objectId)
				)'
			]
		], //remember_page_defaults_for_user
		'increase_allowable_sort_length' => [
			'title' => 'Increase Allowable Sort Length for User page defaults',
			'description' => 'Increase Allowable Sort Length for User page defaults',
			'sql' => [
				'ALTER TABLE user_page_defaults CHANGE COLUMN pageSort pageSort VARCHAR(50)'
			]
		], //increase_allowable_sort_length

		//katherine - Grove
		'add_series_member_priority_score' => [
			'title' => 'Add a priority score to series member table',
			'description' => 'Add a priority score to series members to sort series prioritizing MARC field 800 over 830',
			'sql' => [
				"ALTER TABLE series_member ADD COLUMN priorityScore TINYINT NOT NULL DEFAULT 1;",
			]
		], //add_series_member_priority_score

		//kirstien - Grove

		//kodi - Grove
		'image_pdf_owning_sharing' => [
			'title' => 'Owning and Sharing for Images and PDFs',
			'description' => 'Add owning and sharing columns to file_uploads and image_uploads.',
			'sql' => [
				"ALTER TABLE file_uploads ADD COLUMN owningLibrary INT(11) NOT NULL DEFAULT -1",
				"ALTER TABLE file_uploads ADD COLUMN sharing INT(11) NOT NULL DEFAULT 2",
				"ALTER TABLE file_uploads ADD COLUMN sharedWithLibrary INT(11) NOT NULL DEFAULT -1",
				"ALTER TABLE image_uploads ADD COLUMN owningLibrary INT(11) NOT NULL DEFAULT -1",
				"ALTER TABLE image_uploads ADD COLUMN sharing INT(11) NOT NULL DEFAULT 2",
				"ALTER TABLE image_uploads ADD COLUMN sharedWithLibrary INT(11) NOT NULL DEFAULT -1",
			],
		], //image_pdf_owning_sharing
		'web_content_permissions' => [
			'title' => 'Web Content Permissions',
			'description' => 'Add restricted (home library only) permissions for web content.',
			'sql' => [
				"INSERT INTO permissions (sectionName, name, requiredModule, weight, description) VALUES
					('Web Builder', 'Administer Web Content for Home Library', 'Web Builder', 61, 'Allows the user to manage images and pdfs for their home library only.')",
			],
		], //custom_web_resource_pages_roles
		'rename_web_builder_image_uploads' => [
			'title' => 'Rename Web Builder Image Uploads',
			'description' => 'Rename Web Builder image uploads so they do not conflict with other image uploads',
			'sql' => [
				'renameUploadedWBImages'
			]
		], //rename_web_builder_image_uploads
		'rename_web_builder_pdf_uploads' => [
			'title' => 'Rename Web Builder PDF Uploads',
			'description' => 'Rename Web Builder PDF uploads so they do not conflict with other PDF uploads',
			'sql' => [
				'renameUploadedWBFiles'
			]
		], //rename_web_builder_pdf_uploads
		'rename_theme_image_uploads' => [
			'title' => 'Rename Theme Image Uploads',
			'description' => 'Rename images uploaded for themes so they do not conflict with other image uploads',
			'sql' => [
				'renameUploadedThemeImages'
			]
		], //rename_theme_image_uploads
		'rename_web_resource_placard_images' => [
			'title' => 'Rename Web Resource and Placard Images',
			'description' => 'Rename Web Resource and Placard image uploads so they do not conflict with other image uploads',
			'sql' => [
				'renameUploadedWebResourceandPlacardImages'
			]
		], //rename_web_resource_placard_images

		// Myranda - Grove

		//Yanjun Li - ByWater
		'add_comprise_donation_settings' => [
			'title' => 'Add Comprise Donation Settings',
			'description' => 'Add customer name and id for donation in Comprise Settings',
			'sql' => [
				"ALTER TABLE comprise_settings ADD COLUMN customerNameForDonation VARCHAR(50) DEFAULT NULL",
				"ALTER TABLE comprise_settings ADD COLUMN customerIdForDonation INT(11) DEFAULT NULL",
			]
		], //add_comprise_donation_settings
		'remove_starRating_from_overdrive_api_product_metadata' => [
			'title' => 'Remove Star Rating from overdrive_api_product_metadata',
			'description' => 'Remove starRating from overdrive_api_product_metadata table.',
			'sql' => [
				"ALTER TABLE overdrive_api_product_metadata DROP COLUMN starRating",
			]
		], //remove_starRating_from_overdrive_api_product_metadata

		// Leo Stoyanov - BWS

		// Laura Escamilla - ByWater Solutions

		//alexander - Open Fifth
		'add_grapes_templates_to_db' => [
			'title' => 'Add Grapes Temaplates To DB',
			'description' => 'Add Grapes templates to db',
			'sql' => [
				'addTemplateFromJson'
			]
		], //add_grapes_templates_to_db

		//chloe - Open Fifth
		'move_heycentric_permission' => [
			 'title' => 'Move HeyCentric Permission',
			 'description' => 'Move the Administrer HeyCentric Settings permission into the existing eCommerce section',
			 'continueOnError' => false,
			 'sql' => [
				"UPDATE permissions SET name='Administer HeyCentric', sectionName='eCommerce', description='Allows the user to administer the integration with HeyCentric <em>This has potential security and cost implications.</em>' WHERE name='Administer HeyCentric Settings' AND sectionName='ecommerce'",
			 ],
			 
		 ], // move_heycentric_permission


		//Jacob - Open Fifth
		'sso_do_not_create_user_in_ils' => [
			'title' => 'Do not create SSO user in ils',
			'description' => 'Ability to stop SSO from creating users in the ils',
			'continueOnError' => true,
			'sql' => [
				'ALTER TABLE sso_setting ADD COLUMN createUserInIls int(11) DEFAULT 1',
			]
		],
		//sso_do_not_create_user_in_ils

		//Jacob - Open Fifth
		'disable_user_agent_logging' => [
			'title' => 'Disable User Agent Logging',
			'description' => 'Add system variable to control user agent logging',
			'sql' => [
				"ALTER TABLE system_variables ADD COLUMN disable_user_agent_logging tinyint(1) DEFAULT 0",
			]
		], //disable_user_agent_logging

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

 function addTemplateFromJson(&$update) {
	require_once ROOT_DIR . '/sys/WebBuilder/GrapesTemplate.php';

	$jsonFile = './web_builder/templates.json';
	if(file_exists($jsonFile)){
		$jsonData = file_get_contents($jsonFile);
		$jsonDecoded = json_decode($jsonData, true);
		$templates = $jsonDecoded['templates'];

		foreach($templates as $preMadeTemplate) {
			$template = new GrapesTemplate();
			$template->addTemplate($preMadeTemplate['templateName'], $preMadeTemplate['templateContent'], $preMadeTemplate['htmlData'] ?? '', $preMadeTemplate['cssData'] ?? '');
		}
		$update['success'] = true;
	}
}

function renameUploadedWBImages(&$update) : void {
	global $configArray;
	global $aspen_db;

	require_once ROOT_DIR . '/sys/File/ImageUpload.php';
	$imageUploadFilesUpdatedFull = 0;
	$imageUploadFilesUpdatedSmall = 0;
	$imageUploadFilesUpdatedMedium = 0;
	$imageUploadFilesUpdatedLarge = 0;
	$imageUploadFilesUpdatedXLarge = 0;
	$imageSizes = ['full', 'small', 'medium', 'large', 'xLarge'];

	$uploadedImage = new ImageUpload();
	$uploadedImage->find();
	while ($uploadedImage->fetch()){
		$imageId = $uploadedImage->id;
		$uploadedImagePattern = 'web_builder_image_' . $imageId;
		foreach($imageSizes as $imageSize){
			$sizePath = $imageSize."SizePath";
			if (substr($uploadedImage->$sizePath, 0, -4) != $uploadedImagePattern){
				if ($imageSize == 'xLarge') {
					$originalPath = substr($configArray['Site']['coverPath'], 0, -6)."uploads/web_builder_image/x-large/".$uploadedImage->fullSizePath;
				} else {
					$originalPath = substr($configArray['Site']['coverPath'], 0, -6)."uploads/web_builder_image/$imageSize/".$uploadedImage->fullSizePath;
				}
				$fileType = substr($uploadedImage->$sizePath, -3);
				$fileType = match($fileType){
					'gif' => ".gif",
					'png' => ".png",
					'svg' => ".svg",
					default => ".jpg",
				};
				if ($imageSize == 'xLarge') {
					$newPath = substr($configArray['Site']['coverPath'], 0, -6)."uploads/web_builder_image/x-large/web_builder_image_".$imageId.$fileType;
				} else {
					$newPath = substr($configArray['Site']['coverPath'], 0, -6)."uploads/web_builder_image/$imageSize/web_builder_image_".$imageId.$fileType;
				}
				$newImageName = "web_builder_image_".$imageId.$fileType;

				if (file_exists($originalPath) && !file_exists($newPath)) {
					rename($originalPath, $newPath);
				}

				$aspen_db->query("UPDATE image_uploads set $sizePath = '$newImageName' WHERE id=$imageId");
				switch ($imageSize) {
					case 'full':
						$imageUploadFilesUpdatedFull++;
						break;
					case 'small':
						$imageUploadFilesUpdatedSmall++;
						break;
					case 'medium':
						$imageUploadFilesUpdatedMedium++;
						break;
					case 'large':
						$imageUploadFilesUpdatedLarge++;
						break;
					case 'xLarge':
						$imageUploadFilesUpdatedXLarge++;
						break;
				}
			}
		}
	}
	$update['status'] = "Renamed $imageUploadFilesUpdatedFull full, $imageUploadFilesUpdatedSmall small, $imageUploadFilesUpdatedMedium medium, $imageUploadFilesUpdatedLarge large, and $imageUploadFilesUpdatedXLarge x-large image uploads so they will not conflict with other image uploads.";
	$update['success'] = true;
}

function renameUploadedWBFiles(&$update) : void {
	global $configArray;
	global $aspen_db;
	require_once ROOT_DIR . '/sys/File/FileUpload.php';
	$fileUploadsUpdated = 0;
	$fileThumbnailsUpdated = 0;

	$uploadedFile = new FileUpload();
	$uploadedFile->find();
	while ($uploadedFile->fetch()){
		$fileId = $uploadedFile->id;
		$fullPathLength = strlen(substr($configArray['Site']['coverPath'], 0, -6)."uploads/web_builder_pdf/");
		$fileUploaded = substr($uploadedFile->fullPath, $fullPathLength);
		$uploadedFilePattern = 'web_builder_pdf_'.$fileId.".pdf";
		if ($fileUploaded != $uploadedFilePattern) {
			$originalPath = $uploadedFile->fullPath;
			$newPath = substr($configArray['Site']['coverPath'], 0, -6)."uploads/web_builder_image/full/web_builder_pdf_".$fileId.".pdf";
			if (file_exists($originalPath) && !file_exists($newPath)) {
				rename($originalPath, $newPath);
			}

			$aspen_db->query("UPDATE file_uploads set fullPath = '$newPath' WHERE id=$fileId");
			$fileUploadsUpdated++;
		}
		//check for thumbnail as well
		if (!empty($uploadedFile->thumbFullPath)){
			$thumbFullPathLength = strlen("/web/aspen-discovery/code/web/files/thumbnail/");
			$thumbUploaded = substr($uploadedFile->thumbFullPath, $thumbFullPathLength);
			$fileType = substr($uploadedFile->thumbFullPath, -3);
			$fileType = match($fileType){
				'gif' => ".gif",
				'png' => ".png",
				'svg' => ".svg",
				default => ".jpg",
			};
			$uploadedThumbPattern = 'web_builder_pdf_'.$fileId.$fileType;
			if ($thumbUploaded != $uploadedThumbPattern) {
				$originalThumbPath = $uploadedFile->thumbFullPath;
				$newThumbPath = "/web/aspen-discovery/code/web/files/thumbnail/".$uploadedThumbPattern;

				if (file_exists($originalThumbPath) && !file_exists($newThumbPath)) {
					rename($originalThumbPath, $newThumbPath);
				}

				$aspen_db->query("UPDATE file_uploads set thumbFullPath = '$newThumbPath' WHERE id=$fileId");
				$fileThumbnailsUpdated++;
			}

		}
	}
	$update['status'] = "Renamed $fileUploadsUpdated file uploads and $fileThumbnailsUpdated thumbnails so they will not conflict with other file uploads.";
	$update['success'] = true;
}
function renameUploadedThemeImages(&$update) : void {
	global $aspen_db;
	require_once ROOT_DIR . '/sys/Theming/Theme.php';
	$themeImagesUpdated = 0;

	$theme = new Theme();
	$theme->find();
	while ($theme->fetch()){
		$themeId = $theme->id;
		$themeImagesToCheck = [
			['dbName' => 'logoName', 'prefix' => 'discovery_logo_'.$themeId],
			['dbName' => 'favicon', 'prefix' => 'favicon_'.$themeId],
			['dbName' => 'defaultCover', 'prefix' => 'default_cover_'.$themeId],
			['dbName' => 'headerBackgroundImage', 'prefix' => 'header_background_image_'.$themeId],
			['dbName' => 'footerLogo', 'prefix' => 'footer_logo_'.$themeId],
			['dbName' => 'logoApp', 'prefix' => 'logo_app_'.$themeId],
			['dbName' => 'headerLogoApp', 'prefix' => 'header_logo_app_'.$themeId],
			['dbName' => 'booksImage', 'prefix' => 'books_image_'.$themeId],
			['dbName' => 'booksImageSelected', 'prefix' => 'books_image_selected_'.$themeId],
			['dbName' => 'eBooksImage', 'prefix' => 'ebooks_image_'.$themeId],
			['dbName' => 'eBooksImageSelected', 'prefix' => 'ebooks_image_selected_'.$themeId],
			['dbName' => 'audioBooksImage', 'prefix' => 'audioBooks_image_'.$themeId],
			['dbName' => 'audioBooksImageSelected', 'prefix' => 'audioBooks_image_selected_'.$themeId],
			['dbName' => 'musicImage', 'prefix' => 'music_image_'.$themeId],
			['dbName' => 'musicImageSelected', 'prefix' => 'music_image_selected_'.$themeId],
			['dbName' => 'moviesImage', 'prefix' => 'movies_image_'.$themeId],
			['dbName' => 'moviesImageSelected', 'prefix' => 'movies_image_selected_'.$themeId],
			['dbName' => 'catalogImage', 'prefix' => 'catalog_image_'.$themeId],
			['dbName' => 'genealogyImage', 'prefix' => 'genealogy_image_'.$themeId],
			['dbName' => 'articlesDBImage', 'prefix' => 'articles_db_image_'.$themeId],
			['dbName' => 'eventsImage', 'prefix' => 'events_image_'.$themeId],
			['dbName' => 'listsImage', 'prefix' => 'lists_image_'.$themeId],
			['dbName' => 'seriesImage', 'prefix' => 'series_image_'.$themeId],
			['dbName' => 'libraryWebsiteImage', 'prefix' => 'library_website_image_'.$themeId],
			['dbName' => 'historyArchivesImage', 'prefix' => 'history_archives_image_'.$themeId],
		];

		foreach ($themeImagesToCheck as $image) {
			$dbName = $image['dbName'];
			if (!empty($theme->$dbName) && substr($theme->$dbName, 0, -4) != $image['prefix']) {
				$originalPath = "/web/aspen-discovery/code/web/files/original/".$theme->$dbName;
				$originalThumbnailPath = "/web/aspen-discovery/code/web/files/thumbnail/".$theme->$dbName;
				$fileType = substr($theme->$dbName, -3);
				$fileType = match($fileType){
					'gif' => ".gif",
					'png' => ".png",
					'svg' => ".svg",
					default => ".jpg",
				};
				$newPathOriginal = "/web/aspen-discovery/code/web/files/original/".$image['prefix'].$fileType;
				$newPathThumbnail = "/web/aspen-discovery/code/web/files/thumbnail/".$image['prefix'].$fileType;
				$newFileName = $image['prefix'].$fileType;
				if (file_exists($originalPath) && !file_exists($newPathOriginal)) {
					rename($originalPath, $newPathOriginal);
				}
				if (file_exists($originalThumbnailPath) && !file_exists($newPathThumbnail)) {
					rename($originalThumbnailPath, $newPathThumbnail);
				}

				$aspen_db->query("UPDATE themes set $dbName = '$newFileName' WHERE id=$themeId");
				$themeImagesUpdated++;
				}
		}
	}
	$update['status'] = "Renamed $themeImagesUpdated images in themes so they will not conflict with other image uploads.";
	$update['success'] = true;
}
function renameUploadedWebResourceandPlacardImages(&$update) : void {
	global $aspen_db;

	//Web Resources
	require_once ROOT_DIR . '/sys/WebBuilder/WebResource.php';
	$webResourceImagesUpdated = 0;
	$linkedPlacardsUpdated = 0;

	$webResource = new WebResource();
	$webResource->find();
	while ($webResource->fetch()){
		$resourceId = $webResource->id;
		$uploadedFilePattern = "web_resource_image_".$resourceId;

		if (!empty($webResource->logo) && substr($webResource->logo, 0, -4) != $uploadedFilePattern) {
			$originalPath =  "/web/aspen-discovery/code/web/files/original/".$webResource->logo;
			$originalThumbnailPath = "/web/aspen-discovery/code/web/files/thumbnail/".$webResource->logo;
			$fileType = substr($webResource->logo, -3);
			$fileType = match($fileType){
				'gif' => ".gif",
				'png' => ".png",
				'svg' => ".svg",
				default => ".jpg",
			};
			$newFileName = "web_resource_image_".$resourceId.$fileType;
			$newPathOriginal = "/web/aspen-discovery/code/web/files/original/".$newFileName;
			$newPathThumbnail = "/web/aspen-discovery/code/web/files/thumbnail/".$newFileName;

			if (file_exists($originalPath) && !file_exists($newPathOriginal)) {
				rename($originalPath, $newPathOriginal);
			}
			if (file_exists($originalThumbnailPath) && !file_exists($newPathThumbnail)) {
				rename($originalThumbnailPath, $newPathThumbnail);
			}

			require_once ROOT_DIR . '/sys/LocalEnrichment/Placard.php';
			$linkedPlacard = new Placard();
			$linkedPlacard->sourceId = $resourceId;
			$linkedPlacard->sourceType = 'web_resource';
			if ($linkedPlacard->find(true)){
				//check if linked placard is customized, if yes only update the image if it's using the same one as the web resource
				if (($linkedPlacard->isCustomized && $linkedPlacard->image = $webResource->logo) || !$linkedPlacard->isCustomized) {
					$aspen_db->query("UPDATE placards set image = '$newFileName' WHERE id=$linkedPlacard->id");
					$linkedPlacardsUpdated++;
				}
			}

			$aspen_db->query("UPDATE web_builder_resource set logo = '$newFileName' WHERE id=$resourceId");
			$webResourceImagesUpdated++;

		}
	}

	//Placards
	require_once ROOT_DIR . '/sys/LocalEnrichment/Placard.php';
	$placardImagesUpdated = 0;

	$placard = new Placard();
	$placard->find();
	while ($placard->fetch()){
		$placardId = $placard->id;
		$uploadedFilePattern = "placard_image_".$placardId;

		if (!empty($placard->image) && substr($placard->image, 0, -4) != $uploadedFilePattern && !str_starts_with($placard->image, 'web_resource_image')) { //don't update if it's a linked web resource image
			$fileType = substr($placard->image, -3);
			$fileType = match($fileType){
				'gif' => ".gif",
				'png' => ".png",
				'svg' => ".svg",
				default => ".jpg",
			};
			$newFileName = "placard_image_".$placardId.$fileType;
			$originalPath =  "/web/aspen-discovery/code/web/files/original/".$placard->image;
			$originalThumbnailPath = "/web/aspen-discovery/code/web/files/thumbnail/".$placard->image;
			$newPathOriginal = "/web/aspen-discovery/code/web/files/original/".$newFileName;
			$newPathThumbnail = "/web/aspen-discovery/code/web/files/thumbnail/".$newFileName;
			if (file_exists($originalPath) && !file_exists($newPathOriginal)) {
				rename($originalPath, $newPathOriginal);
			}
			if (file_exists($originalThumbnailPath) && !file_exists($newPathThumbnail)) {
				rename($originalThumbnailPath, $newPathThumbnail);
			}

			$aspen_db->query("UPDATE placards set image = '$newFileName' WHERE id=$placardId");
			$placardImagesUpdated++;

		}
	}
	$update['status'] = "Renamed $webResourceImagesUpdated Web Resource image uploads and $linkedPlacardsUpdated linked placards so they will not conflict with other file uploads. ";
	$update['status'] .= "<br>Renamed $placardImagesUpdated Placard image uploads so they will not conflict with other file uploads.";
	$update['success'] = true;
}