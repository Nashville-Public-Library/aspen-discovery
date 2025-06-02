{strip}
	<div id="main-content" class="col-sm-12">
		<h1>{translate text="Aspen Discovery Usage By IP Address" isAdminFacing=true}</h1>
		{include file="Admin/selectInterfaceForm.tpl"}
		{if !empty($canSort) && count($sortableFields) > 0}
			<div class="form-inline row">
				<div class="form-group col-tn-12">
					<label for="sort" class="control-label">{translate text='Sort by' isAdminFacing=true}</label>&nbsp;
					<select name="sort" id="sort" class="form-control input-sm" onchange="return AspenDiscovery.changeSort();">
						{foreach from=$sortableFields item=field}
							{capture assign=ascVal}{$field.property} asc{/capture}
							{capture assign=descVal}{$field.property} desc{/capture}
							<option value="{$ascVal}" {if $sort == $ascVal}selected{/if}>{translate text="%1% Ascending" 1=$field.label isAdminFacing=true translateParameters=true}</option>
							<option value="{$descVal}" {if $sort == $descVal}selected{/if}>{translate text="%1% Descending" 1=$field.label isAdminFacing=true translateParameters=true}</option>
						{/foreach}
					</select>
				</div>
			</div>
		{/if}
		<table class="adminTable table table-striped table-condensed smallText table-sticky" id="adminTable" aria-label="{translate text="Statistics by IP Address" isAdminFacing=true inAttribute=true}">
			<thead>
				<tr>
					<th>{translate text="IP Address" isAdminFacing=true}</th>
					<th>{translate text="Total Requests" isAdminFacing=true}</th>
					<th>{translate text="Blocked Requests" isAdminFacing=true}</th>
					<th>{translate text="Blocked API Requests" isAdminFacing=true}</th>
					<th>{translate text="Login Attempts" isAdminFacing=true}</th>
					<th>{translate text="Failed Logins" isAdminFacing=true}</th>
					<th>{translate text="Num Spammy Requests" isAdminFacing=true}</th>
					<th>{translate text="Last Request" isAdminFacing=true}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$allIpStats item="ipStats"}
					<tr>
						<td>{$ipStats->ipAddress}</td>
						<td>{$ipStats->numRequests|number_format}</td>
						<td>{$ipStats->numBlockedRequests|number_format}</td>
						<td>{$ipStats->numBlockedApiRequests|number_format}</td>
						<td>{$ipStats->numLoginAttempts|number_format}</td>
						<td>{$ipStats->numFailedLoginAttempts|number_format}</td>
						<td>{$ipStats->numSpammyRequests|number_format}</td>
						<td>{$ipStats->lastRequest|date_format:"%D %T"}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
		{if !empty($pageLinks.all)}<div class="text-center">{$pageLinks.all}</div>{/if}
	</div>
{/strip}