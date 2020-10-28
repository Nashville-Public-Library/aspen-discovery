{strip}
	<div id="main-content" class="col-sm-12">
		<h1>{translate text="RBdigital Dashboard"}</h1>
		<div>
			<div class="dashboardCategory col-sm-6">
				<div class="row">
					<div class="col-sm-10 col-sm-offset-1">
						<h2 class="dashboardCategoryLabel">{translate text="Active Users"}</h2>
					</div>
				</div>
				<div class="row">
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="This Month"}</div>
						<div class="dashboardValue">{$activeUsersThisMonth|number_format}</div>
					</div>
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="Last Month"}</div>
						<div class="dashboardValue">{$activeUsersLastMonth|number_format}</div>
					</div>
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="This Year"}</div>
						<div class="dashboardValue">{$activeUsersThisYear|number_format}</div>
					</div>
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="All Time"}</div>
						<div class="dashboardValue">{$activeUsersAllTime|number_format}</div>
					</div>
				</div>
			</div>
	
			<div class="dashboardCategory col-sm-6">
				<div class="row">
					<div class="col-sm-10 col-sm-offset-1">
						<h2 class="dashboardCategoryLabel">{translate text="Records With Usage"}</h2>
					</div>
				</div>
				<div class="row">
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="This Month"}</div>
						<div class="dashboardValue">{$activeRecordsThisMonth|number_format}</div>
					</div>
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="Last Month"}</div>
						<div class="dashboardValue">{$activeRecordsLastMonth|number_format}</div>
					</div>
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="This Year"}</div>
						<div class="dashboardValue">{$activeRecordsThisYear|number_format}</div>
					</div>
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="All Time"}</div>
						<div class="dashboardValue">{$activeRecordsAllTime|number_format}</div>
					</div>
				</div>
			</div>
	
			<div class="dashboardCategory col-sm-6">
				<div class="row">
					<div class="col-sm-10 col-sm-offset-1">
						<h2 class="dashboardCategoryLabel">{translate text="Loans"}</h2>
					</div>
				</div>
				<div class="row">
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="This Month"}</div>
						<div class="dashboardValue">{$loansThisMonth|number_format}</div>
					</div>
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="Last Month"}</div>
						<div class="dashboardValue">{$loansLastMonth|number_format}</div>
					</div>
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="This Year"}</div>
						<div class="dashboardValue">{$loansThisYear|number_format}</div>
					</div>
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="All Time"}</div>
						<div class="dashboardValue">{$loansAllTime|number_format}</div>
					</div>
				</div>
			</div>
	
			<div class="dashboardCategory col-sm-6">
				<div class="row">
					<div class="col-sm-10 col-sm-offset-1">
						<h2 class="dashboardCategoryLabel">{translate text="Holds"}</h2>
					</div>
				</div>
				<div class="row">
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="This Month"}</div>
						<div class="dashboardValue">{$holdsThisMonth|number_format}</div>
					</div>
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="Last Month"}</div>
						<div class="dashboardValue">{$holdsLastMonth|number_format}</div>
					</div>
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="This Year"}</div>
						<div class="dashboardValue">{$holdsThisYear|number_format}</div>
					</div>
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="All Time"}</div>
						<div class="dashboardValue">{$holdsAllTime|number_format}</div>
					</div>
				</div>
			</div>
	
			<div class="dashboardCategory col-sm-6">
				<div class="row">
					<div class="col-sm-10 col-sm-offset-1">
						<h2 class="dashboardCategoryLabel">{translate text="Magazines With Usage"}</h2>
					</div>
				</div>
				<div class="row">
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="This Month"}</div>
						<div class="dashboardValue">{$activeMagazinesThisMonth|number_format}</div>
					</div>
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="Last Month"}</div>
						<div class="dashboardValue">{$activeMagazinesLastMonth|number_format}</div>
					</div>
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="This Year"}</div>
						<div class="dashboardValue">{$activeMagazinesThisYear|number_format}</div>
					</div>
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="All Time"}</div>
						<div class="dashboardValue">{$activeMagazinesAllTime|number_format}</div>
					</div>
				</div>
			</div>
	
			<div class="dashboardCategory col-sm-6">
				<div class="row">
					<div class="col-sm-10 col-sm-offset-1">
						<h2 class="dashboardCategoryLabel">{translate text="Magazine Checkouts"}</h2>
					</div>
				</div>
				<div class="row">
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="This Month"}</div>
						<div class="dashboardValue">{$magazineLoansThisMonth|number_format}</div>
					</div>
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="Last Month"}</div>
						<div class="dashboardValue">{$magazineLoansLastMonth|number_format}</div>
					</div>
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="This Year"}</div>
						<div class="dashboardValue">{$magazineLoansThisYear|number_format}</div>
					</div>
					<div class="col-tn-6">
						<div class="dashboardLabel">{translate text="All Time"}</div>
						<div class="dashboardValue">{$magazineLoansAllTime|number_format}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
{/strip}