<?php


class WebBuilderCategory extends DataObject {
	public $__table = 'web_builder_category';
	public $__displayNameColumn = 'name';
	public $id;
	public $name;
	public $description;

	public function getUniquenessFields(): array {
		return ['name'];
	}

	public static function getObjectStructure($context = ''): array {
		return [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id',
			],
			'name' => [
				'property' => 'name',
				'type' => 'text',
				'label' => 'Name',
				'description' => 'A name for the settings',
				'required' => true,
				'maxLength' => 100,
			],
			'description' => [
				'property' => 'description',
				'type' => 'html',
				'label' => 'Description',
				'description' => 'A description for the category',
				'allowableTags' => '<p><em><i><strong><b><a><ul><ol><li><h1><h2><h3><h4><h5><h6><h7><pre><code><hr><table><tbody><tr><th><td><caption><img><br><div><span><sub><sup>',
				'required' => false,
				'hideInLists' => true,
			]
		];
	}

	public static function getCategories() {
		$categories = [];
		$category = new WebBuilderCategory();
		$category->orderBy('name');
		$category->find();
		while ($category->fetch()) {
			$categories[$category->id] = $category->name;
		}
		return $categories;
	}

	public function okToExport(array $selectedFilters): bool {
		return true;
	}
}
