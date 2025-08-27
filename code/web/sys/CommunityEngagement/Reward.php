<?php /** @noinspection PhpMissingFieldTypeInspection */

class Reward extends DataObject {
	public $__table = 'ce_reward';
	public $id;
	public $name;
	public $displayName;
	public $description;
	public $rewardType;
	public $badgeImage;
	public $awardAutomatically;

	static $_objectStructure = [];
	static function getObjectStructure(string $context = ''): array {
		if (isset(self::$_objectStructure[$context]) && self::$_objectStructure[$context] !== null) {
			return self::$_objectStructure[$context];
		}
		global $serverName;
		$rewardType = self::getRewardType();
		$structure = [
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
				'maxLength' => 50,
				'description' => 'A name for the campaign',
				'required' => true,
			],
			'description' => [
				'property' => 'description',
				'type' => 'translatableTextBlock',
				'label' => 'Description',
				'maxLength' => 255,
				'description' => 'A description of the campaign',
				'defaultTextFile' => 'Reward_description.MD',
				'hideInLists'=> true,
			],
			'rewardType' => [
				'property' => 'rewardType',
				'type' =>'enum',
				'label' => 'Reward Type',
				'description' => 'The type of reward',
				'values' => $rewardType,
				'onchange' => 'AspenDiscovery.CommunityEngagement.updateRewardFields()',
			],
			'displayName' => [
				'property' => 'displayName',
				'type' => 'checkbox',
				'label' => 'Display Name',
				'description' => 'Whether or not to display the reward name to patrons',
				'default' => true,
			],
			'awardAutomatically' => [
				'property' => 'awardAutomatically',
				'type' => 'checkbox',
				'label' => 'Award Automatically',
				'description' => 'Whether or not to give this award automatically upon campaign or milestone completion',
				'default' => false,
			],
			'badgeImage' => [
				'property' => 'badgeImage',
				'type' => 'image',
				'label' => 'Image for Digital Badge',
				'description' => 'The image to use for the digital badge',
				'path' => '/data/aspen-discovery/' . $serverName . '/uploads/reward_image/full',
				'displayUrl' => '/CommunityEngagement/ViewImage?size=full&id=',
				'required' => false,
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}

	public function getDisplayUrl(): string {
		$size = 'full';
		if (empty($this->id)) {
			return  ' ';
		}
		return '/CommunityEngagement/ViewImage?size=' .$size . '&id=' . $this->id;
	}

	public function getShareUrl(): string {
		global $serverName;
		$size = 'full';
		return 'http://' . $serverName . '/CommunityEngagement/ViewImage?size=' . $size . '&id=' . $this->id;
	}

	public function uploadImage() {
		if (!empty($this->badgeImage)) {
			global $serverName;
			$imageFile = '/data/aspen-discovery/' . $serverName . '/uploads/reward_image/full/' . $this->badgeImage;
		}
	}

	public function insert(string $context = '') : int|bool {
			$this->uploadImage();
			$this->saveTextBlockTranslations('description');
		
		return parent::insert();
	}

	public function update(string $context = '') : bool|int {
			$this->uploadImage();
			$this->saveTextBlockTranslations('description');
		
		return parent::update();
	}

	public static function getRewardType () {
		return [
			0 => 'Physical',
			1 => 'Digital',
		];
	}

	/**
	 * @return array
	 */
	public static function getRewardList(): array {
		$reward = new Reward();
		$rewardList = [];

		if ($reward->find()) {
			while ($reward->fetch()) {
				$rewardList[$reward->id] = $reward->name;
			}
		}
		return $rewardList;
	}


}