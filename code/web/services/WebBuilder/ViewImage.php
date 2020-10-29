<?php

require_once ROOT_DIR . '/sys/File/ImageUpload.php';
class WebBuilder_ViewImage extends Action{
	private $uploadedImage;
	function launch()
	{
		global $interface;

		$id = strip_tags($_REQUEST['id']);
		$interface->assign('id', $id);

		require_once ROOT_DIR . '/sys/File/ImageUpload.php';
		$this->uploadedImage = new ImageUpload();
		$this->uploadedImage->id = $id;
		if (!$this->uploadedImage->find(true)){
			$this->display('../Record/invalidPage.tpl', 'Invalid Image');
			die();
		}

		global $serverName;
		$dataPath = '/data/aspen-discovery/' . $serverName . '/uploads/web_builder_image/';
		if (isset($_REQUEST['size'])){
			$size = $_REQUEST['size'];
		}else{
			$size = 'full';
		}
		$dataPath .= $size . '/';
		$fullPath = $dataPath . $this->uploadedImage->fullSizePath;
		if (file_exists($fullPath)) {
			set_time_limit(300);
			$chunkSize = 2 * (1024 * 1024);

			$size = intval(sprintf("%u", filesize($fullPath)));

			header('Content-Type: image/png');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: ' . $size);

			if ($size > $chunkSize) {
				$handle = fopen($fullPath, 'rb');

				while (!feof($handle)) {
					set_time_limit(300);
					print(@fread($handle, $chunkSize));

					ob_flush();
					flush();
				}

				fclose($handle);
			} else {
				readfile($fullPath);
			}

			die();
		} else {
			AspenError::raiseError(new AspenError("Image $id does not exist"));
		}

	}

	function getBreadcrumbs()
	{
		$breadcrumbs = [];
		$breadcrumbs[] = new Breadcrumb('/', 'Home');
		$breadcrumbs[] = new Breadcrumb('', $this->uploadedImage->title, true);
		if (UserAccount::userHasPermission('Administer All Web Content')){
			$breadcrumbs[] = new Breadcrumb('/WebBuilder/Images?id=' . $this->uploadedImage->id . '&objectAction=edit', 'Edit', true);
		}
		return $breadcrumbs;
	}
}