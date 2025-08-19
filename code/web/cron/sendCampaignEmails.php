<?php

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../bootstrap_aspen.php';
require_once ROOT_DIR . '/sys/CommunityEngagement/Campaign.php';
require_once ROOT_DIR . '/sys/CommunityEngagement/UserCampaign.php';
require_once ROOT_DIR . '/sys/Account/User.php';
require_once ROOT_DIR . '/sys/Email/EmailTemplate.php';
require_once ROOT_DIR . '/sys/CronLogEntry.php';
$cronLogEntry = new CronLogEntry();
$cronLogEntry->startTime = time();
$cronLogEntry->name = 'Send Campaign Emails';
$cronLogEntry->insert();

$today = date('Y-m-d');

$campaign = new Campaign();
$campaign->startDate = $today;

$numEmailsSent = 0;
if ($campaign->find()) {
	while ($campaign->fetch()) {
		$campaignId = $campaign->id;
		$campaignName = $campaign->name;

		$userCampaign = new UserCampaign();
		$userCampaign->campaignId = $campaignId;

		if ($userCampaign->find()) {
			while($userCampaign->fetch()) {
				if ($userCampaign->optInToCampaignEmailNotifications == 1) {
					$user = new User();
					$user->id = $userCampaign->userId;

					if ($user->find(true) && !empty($user->email)) {
						$numEmailsSent++;
						sendCampaignEmail($user, $campaignId);
					}

				}

			}
		}
	}
}
$cronLogEntry->notes .= "Sent $numEmailsSent emails";
$cronLogEntry->endTime = time();
$cronLogEntry->update();

function sendCampaignEmail($user, $campaignId) : void {
	require_once ROOT_DIR . '/sys/CommunityEngagement/Campaign.php';
	$emailTemplate = EmailTemplate::getActiveTemplate('campaignStart');
	if ($emailTemplate) {
		$campaign = new Campaign();
		$campaign->id = $campaignId;
		if (!$campaign->find(true)) {
			return;
		}

		$parameters = $campaign->getCampaignEmailParameters($user, $campaignId);
		$emailTemplate->sendEmail($user->email, $parameters);
	}
}
