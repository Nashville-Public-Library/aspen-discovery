{strip}
	{if !empty($loggedIn)}
		{if !empty($profile->_web_note)}
			<div class="row">
				<div id="web_note" class="alert alert-info text-center col-xs-12">{$profile->_web_note}</div>
			</div>
		{/if}
		{if !empty($accountMessages)}
			{include file='systemMessages.tpl' messages=$accountMessages}
		{/if}
		{if !empty($ilsMessages)}
			{include file='ilsMessages.tpl' messages=$ilsMessages}
		{/if}

		<h1>{translate text='Checked Out Titles' isPublicFacing=true}</h1>
		{if !empty($libraryHoursMessage)}
			<div class="libraryHours alert alert-success">{$libraryHoursMessage}</div>
		{/if}

		{if !empty($enableCostSavings) && $userHasCatalogConnection}
			<div id="costSavingsPlaceholder"></div>
		{/if}

		{if !empty($offline) && !$enableEContentWhileOffline}
			<div class="alert alert-warning"><strong>{translate text=$offlineMessage isPublicFacing=true}</strong></div>
		{else}
			{if count($linkedUsers) > 0 && $allowFilteringOfLinkedAccountsInCheckouts}
				<div class="row">
					<div class="col-tn-12">
						<div id="linkedUserOptions" class="form-group">
							<label class="control-label" for="linkedUsersDropdown">{translate text="Linked Users" isPublicFacing=true}&nbsp;</label>
							<select name="selectedUser" id="linkedUsersDropdown" class="form-control" onchange="AspenDiscovery.Account.filterOutLinkedUsers('checkouts');">
								<option value="" {if $selectedUser == ""}selected{/if}>All</option>
								<option value="{$currentUserId}" {if $selectedUser == $currentUserId} selected="selected"{/if}>
									{$currentUserName}
								</option>
								{foreach from=$linkedUsers item=user}
									<option value="{$user->id}"{if $selectedUser == $user->id} selected="selected"{/if}>{$user->displayName}</option>
								{/foreach}
							</select>
						</div>
					</div>
				</div>
			{/if}
			<ul class="nav nav-tabs" role="tablist" id="checkoutsTab">
				{if empty($offline)}
					<li role="presentation"{if $tab=='all'} class="active"{/if}><a href="#all" aria-controls="all" role="tab" data-toggle="tab">{translate text="All" isPublicFacing=true} <span class="badge"><span class="checkouts-placeholder">&nbsp;</span></span></a></li>
					<li role="presentation"{if $tab=='ils'} class="active"{/if}><a href="#ils" aria-controls="ils" role="tab" data-toggle="tab">{translate text="Physical Materials" isPublicFacing=true} <span class="badge"><span class="ils-checkouts-placeholder">&nbsp;</span></span></a></li>
				{/if}
				{if $user->isValidForEContentSource('overdrive')}
					<li role="presentation"{if $tab=='overdrive'} class="active"{/if}><a href="#overdrive" aria-controls="overdrive" role="tab" data-toggle="tab">{$readerName} <span class="badge"><span class="overdrive-checkouts-placeholder">&nbsp;</span></span></a></li>
				{/if}
				{if $user->isValidForEContentSource('hoopla')}
					<li role="presentation"{if $tab=='hoopla'} class="active"{/if}><a href="#hoopla" aria-controls="hoopla" role="tab" data-toggle="tab">{translate text="Hoopla" isPublicFacing=true} <span class="badge"><span class="hoopla-checkouts-placeholder">&nbsp;</span></span></a></li>
				{/if}
				{if $user->isValidForEContentSource('palace_project')}
					<li role="presentation"{if $tab=='palace_project'} class="active"{/if}><a href="#palace_project" aria-controls="palace_project" role="tab" data-toggle="tab">{translate text="Palace Project" isPublicFacing=true} <span class="badge"><span class="palace_project-checkouts-placeholder">&nbsp;</span></span></a></li>
				{/if}
				{if $user->isValidForEContentSource('cloud_library')}
					<li role="presentation"{if $tab=='cloud_library'} class="active"{/if}><a href="#cloud_library" aria-controls="cloud_library" role="tab" data-toggle="tab">{translate text="cloudLibrary" isPublicFacing=true} <span class="badge"><span class="cloud_library-checkouts-placeholder">&nbsp;</span></span></a></li>
				{/if}
				{if $user->isValidForEContentSource('axis360')}
					<li role="presentation"{if $tab=='axis360'} class="active"{/if}><a href="#axis360" aria-controls="axis360" role="tab" data-toggle="tab">{translate text="Boundless" isPublicFacing=true} <span class="badge"><span class="axis360-checkouts-placeholder">&nbsp;</span></span></a></li>
				{/if}
			</ul>
			<div class="refresh-indicator small pull-right">
				{translate text="Last Loaded <span id='accountLoadTime'>%1%</span>" 1=$profile->getFormattedCheckoutInfoLastLoaded() isPublicFacing=true} <a onclick="return AspenDiscovery.Account.reloadCheckouts();" title="{translate text="Refresh" isPublicFacing=true inAttribute=true}" class="btn btn-default btn-sm">{translate text="Refresh" isPublicFacing=true inAttribute=true} <i class="fas fa-sync-alt"></i></a>
			</div>

			<!-- Tab panes -->
			<div class="tab-content" id="checkouts">
				{if empty($offline)}
					<div role="tabpanel" class="tab-pane{if $tab=='all'} active{/if}" id="all" aria-label="All Checkouts List"><div id="allCheckoutsPlaceholder">{translate text="Loading checkouts from all sources" isPublicFacing=true}</div></div>
					<div role="tabpanel" class="tab-pane{if $tab=='ils'} active{/if}" id="ils" aria-label="Physical Checkouts List"><div id="ilsCheckoutsPlaceholder">{translate text="Loading checkouts of physical materials" isPublicFacing=true}</div></div>
				{/if}
				{if $user->isValidForEContentSource('overdrive')}
					<div role="tabpanel" class="tab-pane{if $tab=='overdrive'} active{/if}" id="overdrive" aria-label="OverDrive Checkouts List"><div id="overdriveCheckoutsPlaceholder">{translate text="Loading checkouts from %1%" 1=$readerName isPublicFacing=true}</div></div>
				{/if}
				{if $user->isValidForEContentSource('hoopla')}
					<div role="tabpanel" class="tab-pane{if $tab=='hoopla'} active{/if}" id="hoopla" aria-label="Hoopla Checkouts List"><div id="hooplaCheckoutsPlaceholder">{translate text="Loading checkouts from Hoopla" isPublicFacing=true}</div></div>
				{/if}
				{if $user->isValidForEContentSource('palace_project')}
					<div role="tabpanel" class="tab-pane{if $tab=='palace_project'} active{/if}" id="palace_project" aria-label="Palace Project Checkouts List"><div id="palace_projectCheckoutsPlaceholder">{translate text="Loading checkouts from Palace Project" isPublicFacing=true}</div></div>
				{/if}
				{if $user->isValidForEContentSource('cloud_library')}
					<div role="tabpanel" class="tab-pane{if $tab=='cloud_library'} active{/if}" id="cloud_library" aria-label="cloudLibrary Checkouts List"><div id="cloud_libraryCheckoutsPlaceholder">{translate text="Loading checkouts from cloudLibrary" isPublicFacing=true}</div></div>
				{/if}
				{if $user->isValidForEContentSource('axis360')}
					<div role="tabpanel" class="tab-pane{if $tab=='axis360'} active{/if}" id="axis360" aria-label="Boundless Checkouts List"><div id="axis360CheckoutsPlaceholder">{translate text="Loading checkouts from Boundless" isPublicFacing=true}</div></div>
				{/if}
			</div>
			<script type="text/javascript">
				{literal}
				$(document).ready(function() {
					$("a[href='#all']").on('show.bs.tab', function () {
						AspenDiscovery.Account.loadCheckouts('all');
					});
					$("a[href='#ils']").on('show.bs.tab', function () {
						AspenDiscovery.Account.loadCheckouts('ils');
					});
					$("a[href='#overdrive']").on('show.bs.tab', function () {
						AspenDiscovery.Account.loadCheckouts('overdrive');
					});
					$("a[href='#hoopla']").on('show.bs.tab', function () {
						AspenDiscovery.Account.loadCheckouts('hoopla');
					});
					$("a[href='#cloud_library']").on('show.bs.tab', function () {
						AspenDiscovery.Account.loadCheckouts('cloud_library');
					});
					$("a[href='#axis360']").on('show.bs.tab', function () {
						AspenDiscovery.Account.loadCheckouts('axis360');
					});
					$("a[href='#palace_project']").on('show.bs.tab', function () {
						AspenDiscovery.Account.loadCheckouts('palace_project');
					});
					{/literal}
					AspenDiscovery.Account.loadCheckouts('{$tab}');
					{literal}
				});
				{/literal}
			</script>
		{/if}
	{else}
		{translate text="You must sign in to view this information." isPublicFacing=true}<a href='/MyAccount/Login' class="btn btn-primary">{translate text="Sign In" isPublicFacing=true}</a>
	{/if}
{/strip}