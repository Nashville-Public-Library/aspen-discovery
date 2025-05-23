<h1>{translate text="Curbside Pickups" isPublicFacing=true}</h1>
{if !empty($showCurbsidePickups)}
	{if !empty($loggedIn)}
		<div class="row">
			<div class="col-xs-12" id="curbside-pickups">
				{if !empty($instructionSchedule)}
					<div id="instructionSchedule" style="margin-bottom: 3em;">
						{translate text=$instructionSchedule isPublicFacing=true isAdminEnteredData=true}
					</div>
				{/if}
				<p class="alert alert-info"> {translate text="Hold(s) Ready for Pickup:" isPublicFacing=true}
					<a href="/MyAccount/Holds"><strong><span class="ils-available-holds-placeholder"></span></strong></a>
				</p>

				<h2>{translate text="Scheduled Pickups" isPublicFacing=true}</h2>
				{include file='MyAccount/curbsidePickupsSchedule.tpl' pickups=$currentCurbsidePickups}
			</div>
			{if !empty($hasHolds)}
				<div class="col-xs-12">
					<h2>{translate text="Ready for Pickup" isPublicFacing=true}</h2>
					<div id="holds-ready-table" style="margin-bottom: 2em">
						{foreach from=$holdsReadyForPickup item=location name="locationGroup"}
							<table class="table table-striped">
								<thead>
									<tr>
										<th style="vertical-align: middle;">
											<h4 class="margin: 0">{$location.name} </h4>
										</th>
										<th class="text-right">
											{if !empty($location.pickupScheduled)}
												<button class="btn btn-primary" disabled>{translate text="Pickup already scheduled at %1%" 1=$location.name|escape isPublicFacing=true inAttribute=true}</button>
											{else}
												<button class="btn btn-primary" onclick="return AspenDiscovery.Account.getCurbsidePickupScheduler('{$location.id}')">{translate text="Schedule a pickup at %1%" 1=$location.name|escape isPublicFacing=true inAttribute=true}</button>
											{/if}
										</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td colspan="2">
											<div class="striped-content">
												{foreach from=$location.holds item=record name="recordLoop"}
													{include file="MyAccount/curbsidePickupsHoldsReady.tpl" record=$record resultIndex=$smarty.foreach.recordLoop.iteration}
													{if !$smarty.foreach.recordLoop.last}<hr class="margin-top-0 margin-bottom-1">{/if}
												{/foreach}
											</div>
										</td>
									</tr>
								</tbody>
							</table>

						{/foreach}
					</div>
				</div>
			{else}
				{if !empty($showScheduleButton)}
					<div class="col-xs-12">
						<button class="btn btn-primary" onclick="return AspenDiscovery.Account.getCurbsidePickupScheduler('{$userHomePickupLocation->locationId}')">{translate text="Schedule a pickup at %1%" 1=$userHomePickupLocation->displayName|escape isPublicFacing=true inAttribute=true}</button>
					</div>
				{/if}
			{/if}
		</div>
	{else}
		<div class="row">
			<div class="col-xs-12" id="curbside-pickups">
				<p class="h3">{translate text="You must sign in to view this information." isPublicFacing=true}</p>
				<a href='/MyAccount/Login' class="btn btn-lg btn-primary">{translate text="Sign In" isPublicFacing=true}</a>
			</div>
		</div>
	{/if}
{else}
	<div class="row">
		<div class="col-xs-12" id="curbside-pickups">
			<p class="h3">{translate text="Sorry, curbside pickups are not available at your library." isPublicFacing=true}</p>
		</div>
	</div>
{/if}
