<?php
require_once ROOT_DIR . '/JSON_Action.php';

class WebBuilder_AJAX extends JSON_Action
{
	/** @noinspection PhpUnused */
	function getPortalCellValuesForSource() {
		$result = [
			'success' => false,
			'message' => 'Unknown error'
		];

		$sourceType = $_REQUEST['sourceType'];
		switch ($sourceType){
		case 'basic_page':
		case 'basic_page_teaser':
			require_once ROOT_DIR . '/sys/WebBuilder/BasicPage.php';
			$list = [];
			$list['-1'] = 'Select a page';

			$basicPage = new BasicPage();
			$basicPage->orderBy('title');
			$basicPage->find();

			while ($basicPage->fetch()){
				$list[$basicPage->id] = $basicPage->title;
			}

			$result = [
				'success' => true,
				'values' => $list
			];
			break;
		case 'collection_spotlight':
			require_once ROOT_DIR . '/sys/LocalEnrichment/CollectionSpotlight.php';
			$list = [];
			$list['-1'] = 'Select a spotlight';

			$collectionSpotlight = new CollectionSpotlight();
			if (!UserAccount::userHasPermission('Administer All Custom Pages')){
				$homeLibrary = Library::getPatronHomeLibrary();
				$collectionSpotlight->whereAdd('libraryId = ' . $homeLibrary->libraryId . ' OR libraryId = -1');
			}
			$collectionSpotlight->orderBy('name');
			$collectionSpotlight->find();
			while ($collectionSpotlight->fetch()){
				$list[$collectionSpotlight->id] = $collectionSpotlight->name;
			}

			$result = [
				'success' => true,
				'values' => $list
			];
			break;
		case 'custom_form':
			require_once ROOT_DIR . '/sys/WebBuilder/CustomForm.php';
			$list = [];
			$list['-1'] = 'Select a form';

			$customForm = new CustomForm();
			$customForm->orderBy('title');
			$customForm->find();

			while ($customForm->fetch()){
				$list[$customForm->id] = $customForm->title;
			}

			$result = [
				'success' => true,
				'values' => $list
			];
			break;
		case 'image':
			require_once ROOT_DIR . '/sys/File/ImageUpload.php';
			$list = [];
			$list['-1'] = 'Select an image';
			$object = new ImageUpload();
			$object->type = 'web_builder_image';
			$object->orderBy('title');
			$object->find();
			while ($object->fetch()) {
				$list[$object->id] =$object->title;
			}
			$result = [
				'success' => true,
				'values' => $list
			];
			break;
		case 'video':
			require_once ROOT_DIR . '/sys/File/FileUpload.php';
			$list = [];
			$list['-1'] = 'Select a video';
			$object = new FileUpload();
			$object->type = 'web_builder_video';
			$object->orderBy('title');
			$object->find();
			while ($object->fetch()) {
				$list[$object->id] =$object->title;
			}
			$result = [
				'success' => true,
				'values' => $list
			];
			break;
		default:
			$result['message'] = 'Unhandled Source Type ' . $sourceType;
		}

		$portalCellId = $_REQUEST['portalCellId'];
		$portalCell = null;
		$result['selected'] = '-1';
		if (!empty($portalCellId)){
			$portalCell = new PortalCell();
			$portalCell->id = $portalCellId;
			if ($portalCell->find(true)){
				if ($portalCell->sourceType == $sourceType){
					$result['selected'] = $portalCell->sourceId;
				}
			}
		}

		return $result;
	}

	/** @noinspection PhpUnused */
	function uploadImage(){
		$result = [
			'success' => false,
			'message' => 'Unknown error uploading image'
		];
		if (UserAccount::isLoggedIn()){
			if (UserAccount::userHasPermission('Administer All Web Resources')){
				if (! empty($_FILES)) {
					require_once ROOT_DIR . '/sys/File/ImageUpload.php';
					$structure = ImageUpload::getObjectStructure();
					foreach ($_FILES as $file) {
						$image = new ImageUpload();
						$image->type = 'web_builder_image';
						$image->fullSizePath = $file['name'];
						$image->generateMediumSize = true;
						$image->generateSmallSize = true;
						$destFileName = $file['name'];
						$destFolder = $structure['fullSizePath']['path'];
						if (!is_dir($destFolder)){
							if (!mkdir($destFolder, 0755, true)){
								$result['message'] = 'Could not create directory to upload files';
								if (IPAddress::showDebuggingInformation()){
									$result['message'] .= " " . $destFolder;
								}
							}
						}
						$destFullPath = $destFolder . '/' . $destFileName;
						if (file_exists($destFullPath)){
							$image->find(true);
						}

						$image->title = $file['name'];
						$copyResult = copy($file["tmp_name"], $destFullPath);
						if ($copyResult) {
							$image->update();
							$result = [
								'success' => true,
								'title' => $image->title,
								'imageUrl' => $image->getDisplayUrl('full')
							];
							break;
						}else{
							$result['message'] = 'Could not save the image to disk';
						}
					}
				}else{
					$result['message'] = 'No file was selected';
				}
			}else{
				$result['message'] = 'You don\'t have the correct permissions to upload an image';
			}
		}else{
			$result['message'] = 'You must be logged in to upload an image';
		}
		return $result;
	}

	/** @noinspection PhpUnused */
	function getUploadImageForm(){
		global $interface;
		$results = [
			'success' => false,
			'message' => 'Unknown error getting upload form'
		];
		if (UserAccount::isLoggedIn()) {
			if (UserAccount::userHasPermission('Administer All Web Resources')) {
				$editorName = strip_tags($_REQUEST['editorName']);
				$interface->assign('editorName', $editorName);
				$results = array(
					'success' => true,
					'title' => 'Upload an Image',
					'modalBody' => $interface->fetch('WebBuilder/uploadImage.tpl'),
					'modalButtons' => "<button class='tool btn btn-primary' onclick='return AspenDiscovery.WebBuilder.doImageUpload()'>Upload Image</button>"
				);
			}else {
				$result['message'] = 'You don\'t have the correct permissions to upload an image';
			}
		}else{
			$result['message'] = 'You must be logged in to upload an image';
		}

		return $results;
	}

	/** @noinspection PhpUnused */
	function deleteCell() {
		$result = [
			'success' => false,
			'message' => 'Unknown error deleting cell'
		];
		if (UserAccount::isLoggedIn()) {
			if (UserAccount::userHasPermission(['Administer All Custom Pages', 'Administer Library Custom Pages'])) {
				if (isset($_REQUEST['id'])) {
					require_once ROOT_DIR . '/sys/WebBuilder/PortalCell.php';
					$portalCell = new PortalCell();
					$portalCell->id = $_REQUEST['id'];
					if ($portalCell->find(true)){
						//Update the widths of the cells based on the number of cells in the row
						$portalRow = new PortalRow();
						$portalRow->id = $portalCell->portalRowId;
						$portalCell->delete();
						if ($portalRow->find(true)){
							$portalRow->resizeColumnWidths();
						}
						$result['success'] = true;
						$result['message'] = 'The cell was deleted successfully';
						global $interface;
						$interface->assign('portalRow', $portalRow);
						$result['rowId'] = $portalCell->portalRowId;
						$result['newRow'] = $interface->fetch('DataObjectUtil/portalRow.tpl');
					}else{
						$result['message'] = 'Unable to find that cell, it may have been deleted already';
					}
				}else{
					$result['message'] = 'No cell id was provided';
				}
			}else {
				$result['message'] = 'You don\'t have the correct permissions to delete a cell';
			}
		}else{
			$result['message'] = 'You must be logged in to delete a cell';
		}
		return $result;
	}

	/** @noinspection PhpUnused */
	function deleteRow() {
		$result = [
			'success' => false,
			'message' => 'Unknown error deleting row'
		];
		if (UserAccount::isLoggedIn()) {
			if (UserAccount::userHasPermission(['Administer All Custom Pages', 'Administer Library Custom Pages'])) {
				if (isset($_REQUEST['id'])) {
					require_once ROOT_DIR . '/sys/WebBuilder/PortalRow.php';
					$portalRow = new PortalRow();
					$portalRow->id = $_REQUEST['id'];
					if ($portalRow->find(true)){
						$portalRow->delete();
						$result['success'] = true;
						$result['message'] = 'The row was deleted successfully';
					}else{
						$result['message'] = 'Unable to find that row, it may have been deleted already';
					}
				}else{
					$result['message'] = 'No row id was provided';
				}
			}else {
				$result['message'] = 'You don\'t have the correct permissions to delete a row';
			}
		}else{
			$result['message'] = 'You must be logged in to delete a row';
		}
		return $result;
	}

	/** @noinspection PhpUnused */
	function moveRow() {
		$result = [
			'success' => false,
			'message' => 'Unknown error moving row'
		];
		if (UserAccount::isLoggedIn()) {
			if (UserAccount::userHasPermission(['Administer All Custom Pages', 'Administer Library Custom Pages'])) {
				if (isset($_REQUEST['rowId'])) {
					require_once ROOT_DIR . '/sys/WebBuilder/PortalPage.php';
					require_once ROOT_DIR . '/sys/WebBuilder/PortalRow.php';
					$portalRow = new PortalRow();
					$portalRow->id = $_REQUEST['rowId'];
					if ($portalRow->find(true)){
						//Figure out new weights for rows
						$direction = $_REQUEST['direction'];
						$oldWeight = $portalRow->weight;
						if ($direction == 'up'){
							$newWeight = $oldWeight - 1;
						}else{
							$newWeight = $oldWeight + 1;
						}
						$rowToSwap = new PortalRow();
						$rowToSwap->portalPageId = $portalRow->portalPageId;
						$rowToSwap->weight = $newWeight;
						if ($rowToSwap->find(true)) {
							$portalRow->weight = $newWeight;
							$portalRow->update();
							$rowToSwap->weight = $oldWeight;
							$rowToSwap->update();

							$result['success'] = true;
							$result['message'] = 'The row was moved successfully';
							$result['swappedWithId'] = $rowToSwap->id;
						}else{
							if ($direction == 'up'){
								$result['message'] = 'Row is already at the top';
							}else{
								$result['message'] = 'Row is already at the bottom';
							}
						}
					}else{
						$result['message'] = 'Unable to find that row';
					}
				}else{
					$result['message'] = 'No row id was provided';
				}
			}else {
				$result['message'] = 'You don\'t have the correct permissions to move a row';
			}
		}else{
			$result['message'] = 'You must be logged in to move a row';
		}
		return $result;
	}

	/** @noinspection PhpUnused */
	function moveCell() {
		$result = [
			'success' => false,
			'message' => 'Unknown error moving cell'
		];
		if (UserAccount::isLoggedIn()) {
			if (UserAccount::userHasPermission(['Administer All Custom Pages', 'Administer Library Custom Pages'])) {
				if (isset($_REQUEST['cellId'])) {
					require_once ROOT_DIR . '/sys/WebBuilder/PortalRow.php';
					require_once ROOT_DIR . '/sys/WebBuilder/PortalCell.php';
					$portalCell = new PortalCell();
					$portalCell->id = $_REQUEST['cellId'];
					if ($portalCell->find(true)){
						//Figure out new weights for rows
						$direction = $_REQUEST['direction'];
						$oldWeight = $portalCell->weight;
						if ($direction == 'left'){
							$newWeight = $oldWeight - 1;
						}else{
							$newWeight = $oldWeight + 1;
						}
						$cellToSwap = new PortalCell();
						$cellToSwap->portalRowId = $portalCell->portalRowId;
						$cellToSwap->weight = $newWeight;
						if ($cellToSwap->find(true)) {
							$portalCell->weight = $newWeight;
							$portalCell->update();
							$cellToSwap->weight = $oldWeight;
							$cellToSwap->update();

							$result['success'] = true;
							$result['message'] = 'The cell was moved successfully';
							$result['swappedWithId'] = $cellToSwap->id;
						}else{
							if ($direction == 'left'){
								$result['message'] = 'The cell is already the first cell in the row';
							}else{
								$result['message'] = 'The cell is already the last cell in the row';
							}
						}
					}else{
						$result['message'] = 'Unable to find that cell';
					}
				}else{
					$result['message'] = 'No cell id was provided';
				}
			}else {
				$result['message'] = 'You don\'t have the correct permissions to move a cell';
			}
		}else{
			$result['message'] = 'You must be logged in to move a cell';
		}
		return $result;
	}

	/** @noinspection PhpUnused */
	function addRow(){
		$result = [
			'success' => false,
			'message' => 'Unknown error adding row'
		];
		if (UserAccount::isLoggedIn()) {
			if (UserAccount::userHasPermission(['Administer All Custom Pages', 'Administer Library Custom Pages'])) {
				if (isset($_REQUEST['pageId'])) {
					require_once ROOT_DIR . '/sys/WebBuilder/PortalPage.php';
					require_once ROOT_DIR . '/sys/WebBuilder/PortalRow.php';
					$portalPage = new PortalPage();
					$portalPage->id = $_REQUEST['pageId'];
					if ($portalPage->find(true)){
						$portalRow = new PortalRow();
						$portalRow->portalPageId = $portalPage->id;
						$portalRow->weight = count($portalPage->getRows());
						$portalRow->insert();
						global $interface;
						$interface->assign('portalRow', $portalRow);

						$result['success'] = true;
						$result['message'] = 'Added a new row';
						$result['newRow'] = $interface->fetch('DataObjectUtil/portalRow.tpl');
					}else{
						$result['message'] = 'Unable to find that page';
					}
				}else{
					$result['message'] = 'No page id was provided';
				}
			}else {
				$result['message'] = 'You don\'t have the correct permissions to add a row';
			}
		}else{
			$result['message'] = 'You must be logged in to add a row';
		}
		return $result;
	}

	/** @noinspection PhpUnused */
	function addCell(){
		$result = [
			'success' => false,
			'message' => 'Unknown error adding cell'
		];
		if (UserAccount::isLoggedIn()) {
			if (UserAccount::userHasPermission(['Administer All Custom Pages', 'Administer Library Custom Pages'])) {
				if (isset($_REQUEST['rowId'])) {
					require_once ROOT_DIR . '/sys/WebBuilder/PortalRow.php';
					require_once ROOT_DIR . '/sys/WebBuilder/PortalCell.php';
					$portalRow = new PortalRow();
					$portalRow->id = $_REQUEST['rowId'];
					if ($portalRow->find(true)){
						$portalCell = new PortalCell();
						$portalCell->portalRowId = $portalRow->id;
						$portalCell->weight = count($portalRow->getCells());
						$portalCell->widthTiny = 12;
						$portalCell->widthXs = 12;
						$portalCell->widthSm = 12;
						$portalCell->widthMd = 12;
						$portalCell->widthLg = 12;
						$portalCell->insert();

						$portalRow->resizeColumnWidths();

						global $interface;
						$interface->assign('portalCell', $portalCell);
						$interface->assign('portalRow', $portalRow);

						$result['success'] = true;
						$result['message'] = 'Added a new cell';
						$result['newCell'] = $interface->fetch('DataObjectUtil/portalCell.tpl');
						$result['newRow'] = $interface->fetch('DataObjectUtil/portalRow.tpl');
					}else{
						$result['message'] = 'Unable to find that row';
					}
				}else{
					$result['message'] = 'No row id was provided';
				}
			}else {
				$result['message'] = 'You don\'t have the correct permissions to add a cell';
			}
		}else{
			$result['message'] = 'You must be logged in to add a cell';
		}
		return $result;
	}

	function getEditCellForm(){
		$result = [
			'success' => false,
			'message' => 'Unknown error adding cell'
		];
		if (UserAccount::isLoggedIn()) {
			if (UserAccount::userHasPermission(['Administer All Custom Pages', 'Administer Library Custom Pages'])) {
				if (isset($_REQUEST['cellId'])) {
					require_once ROOT_DIR . '/sys/WebBuilder/PortalCell.php';
					$portalCell = new PortalCell();
					$portalCell->id = $_REQUEST['cellId'];
					if ($portalCell->find(true)){
						global $interface;
						$interface->assign('object', $portalCell);
						$interface->assign('structure', PortalCell::getObjectStructure());
						$interface->assign('saveButtonText', 'Update');
						$result['success'] = true;
						$result['message'] = 'Display form';
						$result['title'] = 'Edit Cell';
						$result['modalBody'] = $interface->fetch('DataObjectUtil/objectEditForm.tpl');
						$result['modalButtons'] = "<button class='tool btn btn-primary' onclick='AspenDiscovery.WebBuilder.editCell()'>" . translate('Update Cell') . "</button>";
					}else{
						$result['message'] = 'Unable to find that cell';
					}
				}else{
					$result['message'] = 'No cell id was provided';
				}
			}else {
				$result['message'] = 'You don\'t have the correct permissions to edit a cell';
			}
		}else{
			$result['message'] = 'You must be logged in to edit a cell';
		}
		return $result;

	}
}