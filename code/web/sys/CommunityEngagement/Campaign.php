<?php
require_once ROOT_DIR . '/sys/CommunityEngagement/Milestone.php';
require_once ROOT_DIR . '/sys/CommunityEngagement/CampaignMilestone.php';
require_once ROOT_DIR . '/sys/CommunityEngagement/UserCampaign.php';
require_once ROOT_DIR . '/sys/CommunityEngagement/Reward.php';
require_once ROOT_DIR . '/sys/CommunityEngagement/CampaignPatronTypeAccess.php';
require_once ROOT_DIR . '/sys/CommunityEngagement/CampaignLibraryAccess.php';
require_once ROOT_DIR . '/sys/Account/User.php';


class Campaign extends DataObject {
    public $__table = 'ce_campaign';
    public $id;
    public $name;
    public $description;
    public $milestones;
    public $startDate;
    public $endDate;
    public $enrollmentCounter;
    public $unenrollmentCounter;
    public $currentEnrollments;
    public $campaignReward;
    public $userAgeRange;

    /** @var AvailableMilestones[] */
    private $_availableMilestones;

	protected $_allowPatronTypeAccess;
	protected $_allowLibraryAccess;

    public static function getObjectStructure($context = ''): array {
        $milestoneList = Milestone::getMilestoneList();
        $milestoneStructure = CampaignMilestone::getObjectStructure($context);
        unset($milestoneStructure['campaignId']);

        $libraryList = Library::getLibraryList(false);
		$patronTypeList = PType::getPatronTypeList();
        $rewardList = Reward::getRewardList();
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
				'maxLength' => 50,
				'description' => 'A name for the campaign',
				'required' => true,
			],
            'description' => [
				'property' => 'description',
				'type' => 'text',
				'label' => 'Description',
				'maxLength' => 255,
				'description' => 'A description of the campaign',
			],
            'availableMilestones' => [
                'property' => 'availableMilestones',
                'type' => 'oneToMany',
                'label' => 'Milestones',
                'renderAsHeading' => true,
                'description' => 'The Milestones to be linked to this campaign',
                'keyThis' => 'campaignId',
                'keyOther' => 'campaignId',
                'subObjectType' => 'CampaignMilestone',
                'structure' => $milestoneStructure,
                'sortable' => true,
                'storeDb' => true,
                'allowEdit' => false,
                'canEdit' => false,
                'canAddNew' => true,
                'canDelete' => true,
            ],
            'startDate' => [
                'property' => 'startDate',
                'type' => 'date',
                'label' => 'Campaign Start Date',
                'description' => 'The date the campaign starts',
            ],
            'endDate' => [
                'property' => 'endDate',
                'type' => 'date',
                'label' => 'Campaign End Date',
                'description' => 'The date the campaign ends',
            ],
            'campaignReward' => [
                'property' => 'campaignReward',
                'type' => 'enum',
                'label' => 'Reward for Completing Campaign',
                'values' => $rewardList, 
                'description' => 'The reward given for completing the campaign.'
            ],
            'allowPatronTypeAccess' => [
				'property' => 'allowPatronTypeAccess',
				'type' => 'multiSelect',
				'listStyle' => 'checkboxSimple',
				'label' => 'Patron Type Access',
				'description' => 'Define what patron types should have access to this campaign',
				'values' => $patronTypeList,
				'hideInLists' => false,
			],
			'allowLibraryAccess' => [
				'property' => 'allowLibraryAccess',
				'type' => 'multiSelect',
				'listStyle' => 'checkboxSimple',
				'label' => 'Library Access',
				'description' => 'Define what libraries should have access to this campaign',
				'values' => $libraryList,
				'hideInLists' => false,
			],
            'userAgeRange' => [
                'property' => 'userAgeRange',
                'type' => 'text',
                'label' => 'User Age Range',
                'description' => 'Define the age range for this campaign e.g. "14-18", "14+", "All Ages")',
                'default' => 'All Ages',
                'maxLength' => 255,
                'hideInLists' => false,
            ],
        ];
    }

	public function getPatronTypeAccess() {
		if (!isset($this->_allowPatronTypeAccess) && $this->id) {
			$this->_allowPatronTypeAccess = [];
			$patronTypeLink = new CampaignPatronTypeAccess();
			$patronTypeLink->campaignId = $this->id;
			$patronTypeLink->find();
			while ($patronTypeLink->fetch()) {
				$this->_allowPatronTypeAccess[$patronTypeLink->patronTypeId] = $patronTypeLink->patronTypeId;
			}
		}
		return $this->_allowPatronTypeAccess;
	}

	public function getLibraryAccess(): ?array {
		if (!isset($this->_allowLibraryAccess) && $this->id) {
			$this->_allowLibraryAccess = [];
			$libraryLink = new CampaignLibraryAccess();
			$libraryLink->campaignId = $this->id;
			$libraryLink->find();
			while ($libraryLink->fetch()) {
				$this->_allowLibraryAccess[$libraryLink->libraryId] = $libraryLink->libraryId;
			}
		}
		return $this->_allowLibraryAccess;
	}

	public function savePatronTypeAccess() {
		if (isset($this->_allowPatronTypeAccess) && is_array($this->_allowPatronTypeAccess)) {
			$this->clearPatronTypeAccess();

			foreach ($this->_allowPatronTypeAccess as $patronTypeId) {
				$link = new CampaignPatronTypeAccess();
				$link->campaignId = $this->id;
				$link->patronTypeId = $patronTypeId;
				$link->insert();
			}
			unset($this->_allowPatronTypeAccess);
		}
	}

	public function saveLibraryAccess() {
		if (isset($this->_allowLibraryAccess) && is_array($this->_allowLibraryAccess)) {
			$this->clearLibraryAccess();

			foreach ($this->_allowLibraryAccess as $libraryId) {
				$libraryLink = new CampaignLibraryAccess();
				$libraryLink->campaignId = $this->id;
				$libraryLink->libraryId = $libraryId;
				$libraryLink->insert();
			}
			unset($this->_allowLibraryAccess);
		}
	}

	private function clearPatronTypeAccess() {
		//Delete links to the patron types
		$link = new CampaignPatronTypeAccess();
		$link->campaignId = $this->id;
		return $link->delete(true);
	}

	private function clearLibraryAccess() {
		//Delete links to the libraries
		$libraryLink = new CampaignLibraryAccess();
		$libraryLink->campaignId = $this->id;
		return $libraryLink->delete(true);
	}

    public function getUsers() {
        if (is_null($this->_users)) {
            $this->_users = [];

            require_once ROOT_DIR . '/sys/Account/User.php';

            if ($this->id) {
                $escapedId = $this->escape($this->id);
                $user = new User();
                $user->query("SELECT user.* FROM user INNER JOIN ce_user_campaign ON  user.id = ce_user_campaign.userId WHERE ce_user_campaign.campaignId = $escapedId ORDER BY user.username");

                while($user->fetch()) {
                    $this->_users[$user->id] = clone $user;
                }
            }
        }
        return $this->_users;
    }

    public function getUsersForCampaign() {
        require_once ROOT_DIR . '/sys/CommunityEngagement/UserCampaign.php';
        $users = [];

        if ($this->id) {
            $userCampaign = new UserCampaign();

            $userCampaign->campaignId = $this->id;

            if ($userCampaign->find()) {
                while ($userCampaign->fetch()) {
                    $user = new User();
                    $user->id = $userCampaign->userId;
                    if ($user->find(true)) {
                        $users[] = clone $user;
                    }
                }
            }
        }
        return $users;
    }

    public function __get($name) {
		if ($name == 'allowPatronTypeAccess') {
			return $this->getPatronTypeAccess();
	    } else if ($name == 'allowLibraryAccess') {
            return $this->getLibraryAccess();
	    } else if ($name == 'availableMilestones') {
            return $this->getMilestones();
        } else {
            return parent::__get($name);
        }
    }

    public function __set($name, $value) {
		if ($name == 'allowPatronTypeAccess') {
			$this->_allowPatronTypeAccess = $value;
        } else if ($name == 'allowLibraryAccess') {
            $this->_allowLibraryAccess = $value;
        } else if ($name == 'availableMilestones') {
            $this->_availableMilestones = $value;
        } else {
            parent::__set($name, $value);
        }
    }

 
    public function getMilestones(){
        if (!isset($this->_availableMilestones)) {
            $this->_availableMilestones = [];
            if (!empty($this->id)) {
                $campaignMilestone = new CampaignMilestone();
                $campaignMilestone->campaignId = $this->id;
               if ($campaignMilestone->find()) {
                    while ($campaignMilestone->fetch()) {
                        $this->_availableMilestones[$campaignMilestone->id] = clone($campaignMilestone);
                    }
               }
            }
        }
        return $this->_availableMilestones;
    }

    public function getRewardDetails() {
        $reward = new Reward();
        $reward->id = $this->campaignReward;
        if ($reward->find(true)) {
            return [
                'name' => $reward->name,
                'rewardType' => $reward->rewardType,
                'badgeImage' => $reward->getDisplayUrl(),
                'rewardExists' => !empty($reward->badgeImage),
            ];
        }
        return null;
    }

    /**
	 * Override the update functionality to save related objects
	 *
	 * @see DB/DB_DataObject::update()
	 */
	public function update($context = '') {
        $ret = parent::update();
        if ($ret !== FALSE) {
            $this->savePatronTypeAccess();
            $this->saveLibraryAccess();
            $this->saveMilestones();
        }
        return $ret;
    }

    /**
     * Override the insert functionality to save related objects
     * 
     * @see DB/DB_Data_Object::insert()
     */
    public function insert($context = '') {
        $ret = parent::insert();
        if ($ret !== FALSE) {
            $this->savePatronTypeAccess();
            $this->saveLibraryAccess();
            $this->saveMilestones();
        }
        return $ret;
    }

	public function delete($useWhere = false) : int {
		$ret = parent::delete($useWhere);
		if ($ret && !empty($this->id)) {
			$this->clearPatronTypeAccess();
			$this->clearLibraryAccess();
		}
		return $ret;
	}

    public static function getAllCampaigns() : array {
        $campaign = new Campaign();
        $campaignList = [];

        if ($campaign->find()) {
            while ($campaign->fetch()) {
                $campaignList[$campaign->id] = clone $campaign;
            }
        }
        return $campaignList;
    }

    public static function getCampaignById($id) {
        $campaign = new Campaign();
        $campaign->whereAdd("id = '" .$id . "'");
        if ($campaign->find() && $campaign->fetch()) {
          return $campaign;
        }
        return null;
    }

    /**
     * Finds and retrieves campaign records based on user access and login status.
     *
     * If the user is not logged in or is an Aspen admin user, it performs a standard find operation.
     * Otherwise, it filters campaigns based on the user's patron type, ensuring that the user has
     * access to the campaign. This function supports optional fetching of the first match and
     * requires at least one match to return results if specified.
     *
     * @param bool $fetchFirst Optional. Whether to fetch the first matching record. Default is false.
     * @param bool $requireOneMatchToReturn Optional. Whether one match is required to return results. Default is true.
     * @return bool True if a record is found, false otherwise.
     */
    public function find($fetchFirst = false, $requireOneMatchToReturn = true): bool {
        if (!UserAccount::isLoggedIn() || UserAccount::getActiveUserObj()->isAspenAdminUser() || UserAccount::getActiveUserObj()->isUserAdmin())
             return parent::find($fetchFirst, $requireOneMatchToReturn);
        $this->joinAdd(new CampaignPatronTypeAccess(), 'LEFT', 'ce_campaign_patron_type_access', 'id', 'campaignId');
        $this->whereAdd("ce_campaign_patron_type_access.patronTypeId = '" . UserAccount::getActiveUserObj()->getPTypeObj()->id . "' OR ce_campaign_patron_type_access.patronTypeId IS NULL");
        $this->joinAdd(new CampaignLibraryAccess(), 'LEFT', 'ce_campaign_library_access', 'id', 'campaignId');
        $this->whereAdd("ce_campaign_library_access.libraryId = '" . UserAccount::getActiveUserObj()->getHomeLibrary()->libraryId . "' OR NOT EXISTS (SELECT 1 FROM ce_campaign_library_access WHERE ce_campaign_library_access.campaignId = ce_campaign.id)");
        $userAge = (int)UserAccount::getActiveUserObj()->getAge();
        $ageCondition = "(
            userAgeRange IS NULL OR
            userAgeRange = '' OR
            userAgeRange = 'All Ages' OR
            (userAgeRange LIKE 'Under %' AND $userAge < CAST(SUBSTRING_INDEX(userAgeRange, ' ', -1) AS UNSIGNED)) OR
            (userAgeRange LIKE 'Over %' AND $userAge > CAST(SUBSTRING_INDEX(userAgeRange, ' ', -1) AS UNSIGNED)) OR
            (userAgeRange LIKE '%+' AND $userAge >= CAST(LEFT(userAgeRange, LOCATE('+', userAgeRange) -1) AS UNSIGNED)) OR
            (userAgeRange LIKE '%-%' AND $userAge BETWEEN
                CAST(LEFT(userAgeRange, LOCATE('-', userAgeRange) -1) AS UNSIGNED) AND
                CAST(SUBSTRING_INDEX(userAgeRange, '-', -1) AS UNSIGNED)
            )
        )";
        $this->whereAdd($ageCondition);
        return parent::find($fetchFirst, $requireOneMatchToReturn);
	}

    /**
     * Retrieves a list of active campaigns.
     *
     * An active campaign is one that has started and not yet ended.
     *
     * @return array An associative array of active campaigns, where the keys
     *               are the campaign IDs and the values are the campaign names.
     */
    public static function getActiveCampaignsList(): array
    {
        $campaign = new Campaign();
        $campaign->whereAdd("startDate <= '" . date("Y-m-d") . "'");
        $campaign->whereAdd("endDate >='" . date("Y-m-d") . "'");
        $campaignList = [];
        if ($campaign->find()) {
            while ($campaign->fetch()) {
                $campaignList[$campaign->id] = clone $campaign;
            }
        }
        return $campaignList;
    }

    public static function getUpcomingCampaigns():array {
        $campaign = new Campaign();

        //Work out the date one month from today
        $today = date("Y-m-d");
        $nextMonth = date("Y-m-d", strtotime("+1 month"));

        $campaign->whereAdd("startDate > '" . $today . "'");
        $campaign->whereAdd("startDate <= '" . $nextMonth . "'");

        $campaignList = [];
        if ($campaign->find()) {
            while ($campaign->fetch()) {
                $campaignList[$campaign->id] = $campaign;
            }
        }
        return $campaignList;
    }

    public static function getCampaignsEndingThisMonth(): array {
        $campaign = new Campaign();

        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t'); //Last day of current month

        $campaign->whereAdd("endDate >= '$startOfMonth'");
        $campaign->whereAdd("endDate <= '$endOfMonth'");

        $campaignList = [];
        if ($campaign->find()) {
            while ($campaign->fetch()) {
                $campaignList[$campaign->id] = clone $campaign;
            }
        }
        return $campaignList;
    }

    public static function getMilestoneRewards(int $campaignId, int $userId): array {
        $milestoneRewards = [];
        $milestones = CampaignMilestone::getMilestoneByCampaign($campaignId);

        foreach ($milestones as $milestone) {
            $reward = new Reward();
            $reward->id = $milestone->rewardId;
            $rewardName = null;
            if ($reward->find(true)) {
                $rewardName = $reward->name;
            }

            $rewardGiven = 0;

            $userCampaign = new UserCampaign();
            $userCampaign->userId = $userId;
            $userCampaign->campaignId = $campaignId;

            if ($userCampaign->find(true)) {
                $milestoneProgress = CampaignMilestoneUsersProgress::getProgressByMilestoneId($milestone->id, $campaignId, $userId);
                $rewardGiven = (int)$milestoneProgress->rewardGiven;
            }

            $milestoneRewards[$milestone->id] = [
                'name'=> $milestone->name,
                'rewardName' => $rewardName,
                'rewardGiven' => $rewardGiven
            ];
        }
        return $milestoneRewards;
    }

    public static function getAllUsersInCampaigns(): array {
        require_once ROOT_DIR . '/sys/Account/User.php';
    
        $userCampaign = new UserCampaign();
        $userCampaign->selectAdd('userId');
        $userCampaign->groupBy('userId'); 
        $userCampaign->find();
    
        $userIds = [];
        while ($userCampaign->fetch()) {
            $userIds[] = $userCampaign->userId;
        }
    
        // Fetch user details for the unique user IDs
        $users = [];
        if (!empty($userIds)) {
            $userIdList = implode(',', array_map('intval', $userIds));
            $user = new User();
            $user->whereAdd("id IN ($userIdList)");
            if ($user->find()) {
                while ($user->fetch()) {
                    $users[$user->id] = clone $user; // Or store as needed
                }
            }
        }
        return $users;
    }

    public static function getUserInfo($userId) {
        $user = new User();
        $user->whereAdd("id = $userId");
        if ($user->find() && $user->fetch()){
            return $user;
        }
        return null;
    }
    

    public function getPastCampaigns(int $userId): array {
        $campaign = new Campaign();
        $currentDate = date('Y-m-d H:i:s');
    
        $campaign->whereAdd("endDate < '$currentDate'");
        $pastCampaignList = [];
    
        if ($campaign->find()) {
            while ($campaign->fetch()) {
                $pastCampaignList[$campaign->id] = clone $campaign;
    
                // Fetch campaign reward
                $campaignReward = new Reward();
                $campaignReward->id = $campaign->campaignReward;
                if ($campaignReward->find(true)) {
                    $pastCampaignList[$campaign->id]->rewardId = $campaignReward->id;
                    $pastCampaignList[$campaign->id]->rewardName = $campaignReward->name;
                    $pastCampaignList[$campaign->id]->rewardType = $campaignReward->rewardType;
                    $pastCampaignList[$campaign->id]->rewardImage = $campaignReward->getDisplayUrl();
                    if (!empty($campaignReward->badgeImage)) {
                        $pastCampaignList[$campaign->id]->rewardExists = true;
                    }
                }
    
                // Fetch campaign milestones and their rewards using mapping
                $milestones = CampaignMilestone::getMilestoneByCampaign($campaign->id);
                $pastCampaignList[$campaign->id]->milestones = $milestones;
    
                // Check if user is enrolled
                $pastCampaignList[$campaign->id]->enrolled = $campaign->isUserEnrolled($userId);
    
                // If user is enrolled, fetch their progress for each milestone
                if ($pastCampaignList[$campaign->id]->enrolled) {
                    $userCampaign = new UserCampaign();
                    $userCampaign->userId = $userId;
                    $userCampaign->campaignId = $campaign->id;
    
                    if ($userCampaign->find(true)) {
                        $milestoneCompletionStatus = $userCampaign->checkMilestoneCompletionStatus();
                        $pastCampaignList[$campaign->id]->campaignRewardGiven = (int)$userCampaign->rewardGiven;

    
                        // Update milestone details based on user progress
                        foreach ($pastCampaignList[$campaign->id]->milestones as $milestone) {
                            $milestoneProgress = CampaignMilestone::getMilestoneProgress($campaign->id, $userId, $milestone->id);
                            $milestone->userProgress = CampaignMilestoneUsersProgress::getProgressByMilestoneId($milestone->id, $campaign->id, $userId);
                            $milestone->isComplete = $milestoneCompletionStatus[$milestone->id] ?? false;
                            $milestone->rewardGiven = CampaignMilestoneUsersProgress::getRewardGivenForMilestone($milestone->id, $userId);
                            $milestone->progress = $milestoneProgress['progress'];
                        }
                    }
                }
            }
        }
        return $pastCampaignList;
    }
    


   
    

    public static function getUserEnrolledCampaigns($userId): array {
        $campaign = new Campaign();

        $campaign->joinAdd(new UserCampaign(), 'INNER', 'ce_user_campaign', 'id', 'campaignId');

        //Filter by the userId
        $campaign->whereAdd("ce_user_campaign.userId = " . $userId);

        $campaignList = [];
        if ($campaign->find()) {
            while ($campaign->fetch()) {
                $campaignList[] = clone $campaign;
            }
        }
        return $campaignList;
    }

    public function getCompletedUsersCount() {
        $userCampaign = new UserCampaign();
        $completedUsers = [];

        $userCampaign->whereAdd("campaignId = {$this->id}");
        $userCampaign->whereAdd("completed = 1");
        $userCampaign->find();
    
        while($userCampaign->fetch()) {
            $completedUsers[$userCampaign->userId] = true;
        }
        return count($completedUsers);
    }

    public function isUserEnrolled($userId) {
        $userCampaign = new UserCampaign();
        $userCampaign->userId = $userId;
        $userCampaign->campaignId = $this->id;

        return $userCampaign->find(true);
    }

    public function saveMilestones() {
        if (isset($this->_availableMilestones) && is_array($this->_availableMilestones)) {
            $this->saveOneToManyOptions($this->_availableMilestones, 'campaignId');
            unset($this->_availableMilestones);
        }
    }
}