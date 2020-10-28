{strip}
	<div id="main-content" class="col-sm-12">
		<h1>Side Loading Dashboard</h1>
		
		{foreach from=$profiles item=profileName key=profileId}
			<h2>{$profileName}</h2>
			<div class="row">
				<div class="dashboardCategory col-sm-6">
					<div class="row">
						<div class="col-sm-10 col-sm-offset-1">
							<h3 class="dashboardCategoryLabel">Active Users</h3>
						</div>
					</div>
					<div class="row">
						<div class="col-tn-6">
							<div class="dashboardLabel">{translate text="This Month"}</div>
							<div class="dashboardValue">{$activeUsersThisMonth.$profileId}</div>
						</div>
						<div class="col-tn-6">
							<div class="dashboardLabel">{translate text="Last Month"}</div>
							<div class="dashboardValue">{$activeUsersLastMonth.$profileId}</div>
						</div>
						<div class="col-tn-6">
							<div class="dashboardLabel">{translate text="This Year"}</div>
							<div class="dashboardValue">{$activeUsersThisYear.$profileId}</div>
						</div>
						<div class="col-tn-6">
							<div class="dashboardLabel">{translate text="All Time"}</div>
							<div class="dashboardValue">{$activeUsersAllTime.$profileId}</div>
						</div>
					</div>
				</div>
	
				<div class="dashboardCategory col-sm-6">
					<div class="row">
						<div class="col-sm-10 col-sm-offset-1">
							<h3 class="dashboardCategoryLabel">Records Accessed Online</h3>
						</div>
					</div>
					<div class="row">
						<div class="col-tn-6">
							<div class="dashboardLabel">{translate text="This Month"}</div>
							<div class="dashboardValue">{$activeRecordsThisMonth.$profileId.numRecordsUsed}</div>
						</div>
						<div class="col-tn-6">
							<div class="dashboardLabel">{translate text="Last Month"}</div>
							<div class="dashboardValue">{$activeRecordsLastMonth.$profileId.numRecordsUsed}</div>
						</div>
						<div class="col-tn-6">
							<div class="dashboardLabel">{translate text="This Year"}</div>
							<div class="dashboardValue">{$activeRecordsThisYear.$profileId.numRecordsUsed}</div>
						</div>
						<div class="col-tn-6">
							<div class="dashboardLabel">{translate text="All Time"}</div>
							<div class="dashboardValue">{$activeRecordsAllTime.$profileId.numRecordsUsed}</div>
						</div>
					</div>
				</div>
			</div>
		{/foreach}
	</div>
{/strip}