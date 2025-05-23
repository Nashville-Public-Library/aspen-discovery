<div class="table-responsive">
	<table class="table table-striped table-align-middle">
		<thead>
		<tr>
			<th>{translate text="Date & Time" isPublicFacing=true}</th>
			<th>{translate text="Location" isPublicFacing=true}</th>
			{if !empty($useNote)}<th>{translate text=$noteLabel isPublicFacing=true isAdminEnteredData=true}</th>{/if}
			<th>{translate text="Actions" isPublicFacing=true}</th>
		</tr>
		</thead>
		<tbody>
		{foreach from=$currentCurbsidePickups.pickups item=pickup name="pickupLoop"}
			<tr>
				<td class="w-25">
					<div>{$pickup['scheduled_pickup_datetime']|date_format:"%b %e, %Y at %l:%M %p"}</div>
					{if $pickup['staged_datetime']}
						<span class="badge badge-success">{translate text="Ready" isPublicFacing=true}</span>
					{else}
						<span class="badge badge-warning">{translate text="Pending" isPublicFacing=true}</span>
					{/if}
				</td>
				<td class="w-25">{$pickup['branchname']}</td>
				{if !empty($useNote)}
					<td class="w-25"><small class="text-muted"><i>{$pickup['notes']}</i></small></td>
				{/if}
				<td class="w-25">
					{if $pickup['staged_datetime'] && empty($pickup['arrival_datetime'])}
						{if !empty($allowCheckIn)}
							<button class="btn btn-primary btn-sm mb-1" onclick="return AspenDiscovery.Account.checkInCurbsidePickup('{$patronId}', '{$pickup['id']}')">
								<i class="fas fa-check mr-1"></i> {translate text="I'm here" isPublicFacing=true inAttribute=true}
							</button>
						{else}
							{if !empty($pickupInstructions)}
								<a role="button" tabindex="0" class="btn btn-primary btn-sm mb-1" data-toggle="popover" data-trigger="focus" data-placement="left" data-title="{translate text='Checking-in' isPublicFacing=true}" data-content="{translate text=$pickupInstructions isPublicFacing=true isAdminEnteredData=true}" data-html="true" data-container="body">
									<i class="fas fa-check mr-1"></i> {translate text="I'm here" isPublicFacing=true inAttribute=true}
								</a>
							{/if}
						{/if}
					{/if}
					<button class="btn btn-outline-danger btn-sm" onclick="return AspenDiscovery.Account.getCancelCurbsidePickup('{$patronId}', '{$pickup['id']}')">
						{translate text="Cancel Pickup" isPublicFacing=true inAttribute=true}
					</button>
				</td>
			</tr>
			{foreachelse}
			<tr>
				<td colspan="4" class="text-center">
					{translate text="You don't have any scheduled curbside pickups." isPublicFacing=true}
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
</div>

<style>
	.table-align-middle th,
	.table-align-middle td {
		vertical-align: middle;
	}
</style>
