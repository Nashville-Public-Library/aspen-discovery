<?php

require_once ROOT_DIR . '/Action.php';
require_once ROOT_DIR . '/services/Admin/Dashboard.php';
require_once ROOT_DIR . '/sys/CommunityEngagement/CampaignData.php';
require_once ROOT_DIR . '/sys/CommunityEngagement/Campaign.php';
require_once ROOT_DIR . '/sys/CommunityEngagement/UserCampaign.php';
require_once ROOT_DIR . '/sys/CommunityEngagement/UserCompletedMilestone.php';
require_once ROOT_DIR . '/sys/CommunityEngagement/CampaignMilestone.php';



class CommunityEngagement_Dashboard extends Admin_Dashboard {
    function launch() {
        global $interface;

        $campaign = new Campaign();
        $userCampaign = new UserCampaign();

        $campaigns = $campaign->getAllCampaigns();
        $interface->assign('campaigns', $campaigns);

        $campaignsEndingThisMonth = $campaign->getCampaignsEndingThisMonth();
        $interface->assign('campaignsEndingThisMonth', $campaignsEndingThisMonth);
        
        $activeCampaigns = $campaign->getActiveCampaignsList();
        $interface->assign('activeCampaigns', $activeCampaigns);

        $upcomingCampaigns = $campaign->getUpcomingCampaigns();
        $interface->assign('upcomingCampaigns', $upcomingCampaigns);

        $users = $campaign->getAllUsersInCampaigns();
        $interface->assign('users', $users);

        $userCampaigns = [];
        $campaignMilestones = [];
        $userCampaignMilestones = [];

        foreach ($campaigns as $campaign) {
            $milestones = CampaignMilestone::getMilestoneByCampaign($campaign->id);
            $campaignMilestones[$campaign->id] = $milestones;

            $users = $campaign->getUsersForCampaign();
            foreach ($users as $user) {
                $userCampaign = new UserCampaign();
                $userCampaign->userId = $user->id;
                $userCampaign->campaignId = $campaign->id;
            
            if ($userCampaign->find(true)) {
                $isCampaignComplete = $userCampaign->checkCompletionStatus();
                if (!isset($userCampaigns[$campaign->id][$user->id])) {
                    $userCampaigns[$campaign->id][$user->id] = [];
                }

                $userCampaigns[$campaign->id][$user->id]['rewardGiven']= (int)$userCampaign->rewardGiven;

                $userCampaigns[$campaign->id][$user->id]['isCampaignComplete'] = $isCampaignComplete;


                $milestoneCompletionStatus = $userCampaign->checkMilestoneCompletionStatus();
                foreach ($milestones as $milestone) {
                    $milestoneComplete = $milestoneCompletionStatus[$milestone->id] ?? false;
                    $userProgress = CampaignMilestoneUsersProgress::getProgressByMilestoneId($milestone->id, $campaign->id, $user->id);
                    $totalGoals = CampaignMilestone::getMilestoneGoalCountByCampaign($campaign->id, $milestone->id);
                    $milestoneRewardGiven = CampaignMilestoneUsersProgress::getRewardGivenForMilestone($milestone->id, $user->id);
                    $userCampaigns[$campaign->id][$user->id]['milestones'][$milestone->id] = [
                        'milestoneComplete' => $milestoneComplete, 
                        'userProgress' => $userProgress,
                        'goal' => $totalGoals,
                        'milestoneRewardGiven' => $milestoneRewardGiven,
                    ];

                }
            }
            }
            //Count how many users have completed the campaign
            $campaign->completedUsersCount = $campaign->getCompletedUsersCount();
        }
        $interface->assign('userCampaigns', $userCampaigns);
        $interface->assign('campaignMilestones', $campaignMilestones);
        $this->display('dashboard.tpl', 'Dashboard');
    }

   
    function getBreadcrumbs(): array
    {
        $breadcrumbs = [];
        return $breadcrumbs;
    }

    function canView(): bool {
        return UserAccount::userHasPermission([
            'View Community Dashboard',
        ]);
    }

    function getActiveAdminSection(): string
    {
        return 'communityEngagement';
    }
}