<?php
require_once ROOT_DIR . '/services/Admin/ObjectEditor.php';
require_once ROOT_DIR . '/sys/File/FileUpload.php';

class WebBuilder_PDFs extends ObjectEditor {
	function getObjectType(): string {
		return 'FileUpload';
	}

	function getToolName(): string {
		return 'PDFs';
	}

	function getModule(): string {
		return 'WebBuilder';
	}

	function getPageTitle(): string {
		return 'Uploaded PDFs';
	}

	function getAllObjects($page, $recordsPerPage): array {
		$user = UserAccount::getLoggedInUser();
		$object = new FileUpload();
		$object->type = 'web_builder_pdf';
		$object->orderBy($this->getSort());
		$this->applyFilters($object);
		$object->limit(($page - 1) * $recordsPerPage, $recordsPerPage);
		if (!UserAccount::userHasPermission('Administer All Web Content') && (UserAccount::userHasPermission('Administer Web Content for Home Library'))) {
			$libraryList = Library::getLibraryList(true);
			$object->whereAddIn("owningLibrary", array_keys($libraryList), false, "OR");
			$object->whereAdd("owningLibrary = -1", "OR");
			$object->whereAdd("sharing = 2 OR sharing = 3", "OR");
			$object->whereAdd("sharing = 1 AND sharedWithLibrary = " . $user->getHomeLibrary()->libraryId, "OR");
		}
		$object->find();
		$objectList = [];
		while ($object->fetch()) {
			$objectList[$object->id] = clone $object;
		}
		return $objectList;
	}

	function getDefaultSort(): string {
		return 'title asc';
	}

	function updateFromUI($object, $structure, $fieldLocks) {
		$object->type = 'web_builder_pdf';
		return parent::updateFromUI($object, $structure, $fieldLocks);
	}

	function getObjectStructure($context = ''): array {
		$objectStructure = FileUpload::getObjectStructure($context);
		unset($objectStructure['type']);
		$fileProperty = $objectStructure['fullPath'];
		global $serverName;
		$dataPath = '/data/aspen-discovery/' . $serverName . '/uploads/web_builder_pdf/';
		$fileProperty['path'] = $dataPath;
		$fileProperty['validTypes'] = ['application/pdf'];
		$objectStructure['fullPath'] = $fileProperty;
		return $objectStructure;
	}

	function getPrimaryKeyColumn(): string {
		return 'id';
	}

	function getIdKeyColumn(): string {
		return 'id';
	}

	/**
	 * @param FileUpload $existingObject
	 * @return array
	 */
	function getAdditionalObjectActions($existingObject): array {
		$objectActions = [];
		if (!empty($existingObject) && !empty($existingObject->id)) {
			$objectActions[] = [
				'text' => 'View PDF',
				'url' => '/Files/' . $existingObject->id . '/ViewPDF',
			];
			$objectActions[] = [
				'text' => 'Download PDF',
				'url' => '/WebBuilder/DownloadPDF?id=' . $existingObject->id,
			];
			$objectActions[] = [
				'text' => 'View Thumbnail',
				'url' => '/WebBuilder/ViewThumbnail?id=' . $existingObject->id,
			];
		}
		return $objectActions;
	}

	function getInstructions(): string {
		return 'https://help.aspendiscovery.org/help/webbuilder/imagespdfs';
	}

	function getBreadcrumbs(): array {
		$breadcrumbs = [];
		$breadcrumbs[] = new Breadcrumb('/Admin/Home', 'Administration Home');
		$breadcrumbs[] = new Breadcrumb('/Admin/Home#web_builder', 'Web Builder');
		$breadcrumbs[] = new Breadcrumb('/WebBuilder/PDFs', 'PDFs');
		return $breadcrumbs;
	}

	function canView(): bool {
		return UserAccount::userHasPermission(['Administer All Web Content', 'Administer Web Content for Home Library']);
	}

	function getActiveAdminSection(): string {
		return 'web_builder';
	}

	function getInitializationJs(): string {
		return 'AspenDiscovery.Admin.toggleLibrarySharingOptions();';
	}
}