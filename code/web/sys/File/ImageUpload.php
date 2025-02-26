<?php


class ImageUpload extends DataObject {
	public $__table = 'image_uploads';
	public $id;
	public $title;
	public $fullSizePath; //Stores the original file maximum width of 1068px
	public $generateXLargeSize;
	public $xLargeSizePath; //Stores the thumbnail with a maximum size of 350px
	public $generateLargeSize;
	public $largeSizePath; //Stores the thumbnail with a maximum size of 350px
	public $generateMediumSize;
	public $mediumSizePath; //Stores the thumbnail with a maximum size of 350px
	public $generateSmallSize;
	public $smallSizePath; //Stores the thumbnail with a maximum size of 200x200px
	public $type;

	static $xLargeSize = 1100;
	static $largeSize = 600;
	static $mediumSize = 400;
	static $smallSize = 200;

	public function getUniquenessFields(): array {
		return ['id'];
	}

	static function getObjectStructure($context = ''): array {
		global $serverName;
		return [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id within the database',
			],
			'title' => [
				'property' => 'title',
				'type' => 'text',
				'label' => 'Title',
				'description' => 'The title of the image',
				'size' => '40',
				'maxLength' => 255,
			],
			'type' => [
				'property' => 'type',
				'type' => 'text',
				'label' => 'Type',
				'description' => 'The type of image being uploaded',
				'maxLength' => 50,
			],
			'fullSizePath' => [
				'property' => 'fullSizePath',
				'type' => 'image',
				'label' => 'Full Size Image',
				'description' => 'The full size image (max width 1068px)',
				'maxWidth' => 1068,
				'maxHeight' => 1068,
				'path' => '/data/aspen-discovery/' . $serverName . '/uploads/web_builder_image/full',
				'displayUrl' => '/WebBuilder/ViewImage?size=full&id=',
				'hideInLists' => true,
				'required' => true,
				'note' => translate(['text' => 'Allowed formats: JPG, JPEG, PNG, SVG', 'isAdminFacing' => true]),
				'validTypes' => ['image/jpeg', 'image/png', 'image/svg+xml']
			],
			'generateXLargeSize' => [
				'property' => 'generateXLargeSize',
				'type' => 'checkbox',
				'label' => 'Generate x-large size image',
				'default' => 1,
				'hideInLists' => true,
			],
			'xLargeSizePath' => [
				'property' => 'xLargeSizePath',
				'type' => 'image',
				'label' => 'X-Large Size Image',
				'description' => 'The x-large size image (max width 1100 px)',
				'maxWidth' => ImageUpload::$xLargeSize,
				'maxHeight' => ImageUpload::$xLargeSize,
				'path' => '/data/aspen-discovery/' . $serverName . '/uploads/web_builder_image/x-large',
				'displayUrl' => '/WebBuilder/ViewImage?size=x-large&id=',
				'hideInLists' => true,
				'note' => translate(['text' => 'Allowed formats: JPG, JPEG, PNG, SVG', 'isAdminFacing' => true]),
				'validTypes' => ['image/jpeg', 'image/png', 'image/svg+xml']
			],
			'generateLargeSize' => [
				'property' => 'generateLargeSize',
				'type' => 'checkbox',
				'label' => 'Generate large size image',
				'default' => 1,
				'hideInLists' => true,
			],
			'largeSizePath' => [
				'property' => 'largeSizePath',
				'type' => 'image',
				'label' => 'Large Size Image',
				'description' => 'The medium size image (max width 600px)',
				'maxWidth' => ImageUpload::$largeSize,
				'maxHeight' => ImageUpload::$largeSize,
				'path' => '/data/aspen-discovery/' . $serverName . '/uploads/web_builder_image/large',
				'displayUrl' => '/WebBuilder/ViewImage?size=large&id=',
				'hideInLists' => true,
				'note' => translate(['text' => 'Allowed formats: JPG, JPEG, PNG, SVG', 'isAdminFacing' => true]),
				'validTypes' => ['image/jpeg', 'image/png', 'image/svg+xml']
			],
			'generateMediumSize' => [
				'property' => 'generateMediumSize',
				'type' => 'checkbox',
				'label' => 'Generate medium size image',
				'default' => 1,
				'hideInLists' => true,
			],
			'mediumSizePath' => [
				'property' => 'mediumSizePath',
				'type' => 'image',
				'label' => 'Medium Size Image',
				'description' => 'The medium size image (max width 400px)',
				'maxWidth' => ImageUpload::$mediumSize,
				'maxHeight' => ImageUpload::$mediumSize,
				'path' => '/data/aspen-discovery/' . $serverName . '/uploads/web_builder_image/medium',
				'displayUrl' => '/WebBuilder/ViewImage?size=medium&id=',
				'hideInLists' => true,
				'note' => translate(['text' => 'Allowed formats: JPG, JPEG, PNG, SVG', 'isAdminFacing' => true]),
				'validTypes' => ['image/jpeg', 'image/png', 'image/svg+xml']
			],
			'generateSmallSize' => [
				'property' => 'generateSmallSize',
				'type' => 'checkbox',
				'label' => 'Generate small size image',
				'default' => 1,
				'hideInLists' => true,
			],
			'smallSizePath' => [
				'property' => 'smallSizePath',
				'type' => 'image',
				'label' => 'Small Size Image',
				'description' => 'The small size image (max width 200px)',
				'maxWidth' => ImageUpload::$smallSize,
				'maxHeight' => ImageUpload::$smallSize,
				'path' => '/data/aspen-discovery/' . $serverName . '/uploads/web_builder_image/small',
				'displayUrl' => '/WebBuilder/ViewImage?size=small&id=',
				'note' => translate(['text' => 'Allowed formats: JPG, JPEG, PNG, SVG', 'isAdminFacing' => true]),
				'validTypes' => ['image/jpeg', 'image/png', 'image/svg+xml']
			],
		];
	}

	function getDisplayUrl($property) {
		if (empty($this->id)) {
			return '';
		}
		if ($property == 'xLargeSizePath') {
			$size = 'x-large';
		} elseif ($property == 'largeSizePath') {
			$size = 'large';
		} elseif ($property == 'mediumSizePath') {
			$size = 'medium';
		} elseif ($property == 'smallSizePath') {
			$size = 'small';
		} else {
			$size = 'full';
		}
		return '/WebBuilder/ViewImage?size=' . $size . '&id=' . $this->id;
	}

	function insert($context = '') {
		$this->generateDerivatives();
		return parent::insert();
	}

	function update($context = '') {
		$this->generateDerivatives();
		return parent::update();
	}

	private function generateDerivatives() {
		if (!empty($this->fullSizePath)) {
			global $serverName;
			require_once ROOT_DIR . '/sys/Covers/CoverImageUtils.php';
			$fullSizeFile = '/data/aspen-discovery/' . $serverName . '/uploads/web_builder_image/full/' . $this->fullSizePath;
			if ($this->generateXLargeSize && empty($this->xLargeSizePath)) {
				$xLargeFile = '/data/aspen-discovery/' . $serverName . '/uploads/web_builder_image/x-large/';
				if (!file_exists($xLargeFile)) {
					mkdir($xLargeFile, 0755, true);
				}
				$xLargeFile .= $this->fullSizePath;
				if (resizeImage($fullSizeFile, $xLargeFile, ImageUpload::$xLargeSize, ImageUpload::$xLargeSize)) {
					$this->xLargeSizePath = $this->fullSizePath;
				}
			}
			if ($this->generateLargeSize && empty($this->largeSizePath)) {
				$largeFile = '/data/aspen-discovery/' . $serverName . '/uploads/web_builder_image/large/';
				if (!file_exists($largeFile)) {
					mkdir($largeFile, 0755, true);
				}
				$largeFile .= $this->fullSizePath;
				if (resizeImage($fullSizeFile, $largeFile, ImageUpload::$largeSize, ImageUpload::$largeSize)) {
					$this->largeSizePath = $this->fullSizePath;
				}
			}
			if ($this->generateMediumSize && empty($this->mediumSizePath)) {
				$mediumFile = '/data/aspen-discovery/' . $serverName . '/uploads/web_builder_image/medium/';
				if (!file_exists($mediumFile)) {
					mkdir($mediumFile, 0755, true);
				}
				$mediumFile .= $this->fullSizePath;
				if (resizeImage($fullSizeFile, $mediumFile, ImageUpload::$mediumSize, ImageUpload::$mediumSize)) {
					$this->mediumSizePath = $this->fullSizePath;
				}
			}
			if ($this->generateSmallSize && empty($this->smallSizePath)) {
				$smallFile = '/data/aspen-discovery/' . $serverName . '/uploads/web_builder_image/small/';
				if (!file_exists($smallFile)) {
					mkdir($smallFile, 0755, true);
				}
				$smallFile .= $this->fullSizePath;
				if (resizeImage($fullSizeFile, $smallFile, ImageUpload::$smallSize, ImageUpload::$smallSize)) {
					$this->smallSizePath = $this->fullSizePath;
				}
			}
		}
	}

	public function okToExport(array $selectedFilters): bool {
		return true;
	}
}