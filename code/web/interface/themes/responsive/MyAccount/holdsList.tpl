{assign var="hideCoversFormDisplayed" value=false}
{foreach from=$recordList item=sectionData key=sectionKey}
	<h2>{if $sectionKey == 'available'}{translate text="Holds Ready For Pickup" isPublicFacing=true}{else}{if $source=='interlibrary_loan'}{translate text="Pending Requests" isPublicFacing=true}{else}{translate text="Pending Holds" isPublicFacing=true}{/if}{/if}</h2>
	<p class="alert alert-info">
		{if $sectionKey == 'available'}
			{translate text="These titles have arrived at the library or are available online for you to use." isPublicFacing=true}
			{*These titles have arrived at the library or are available online for you to use.*}
		{else}
			{if $source == 'interlibrary_loan'}
				{translate text="These requests will be filled by another library and sent to your library. We will notify you when a title is available." isPublicFacing=true}
			{else}
				{if not $notification_method or $notification_method eq 'Unknown'}
					{translate text="These titles are currently checked out to other patrons. We will notify you when a title is available." isPublicFacing=true}
				{else}
					{translate text="These titles are currently checked out to other patrons. We will notify you via %1% when a title is available." 1=$notification_method isPublicFacing=true}
				{/if}
			{/if}
		{/if}
	</p>
	{if is_array($recordList.$sectionKey) && count($recordList.$sectionKey) > 0}
		{if $source == 'ils' && $sectionKey == 'available' && $showCurbsidePickups}
			<div id="curbsidePickupButton" style="margin-bottom: 1em">
				<a href="/MyAccount/CurbsidePickups" class="btn btn-primary">{translate text="Schedule a Curbside Pickup" isPublicFacing=true}</a>
			</div>
		{/if}

		<div id="pager" class="navbar form-inline">
			<label for="{$sectionKey}HoldSort_{$source}" class="control-label">{translate text='Sort by' isPublicFacing=true}&nbsp;</label>
			<select name="{$sectionKey}HoldSort_{$source}" id="{$sectionKey}HoldSort_{$source}" class="form-control" onchange="AspenDiscovery.Account.loadHolds('{$source}', $('#availableHoldSort_{$source} option:selected').val(), $('#unavailableHoldSort_{$source} option:selected').val());">
				{foreach from=$sortOptions[$sectionKey] item=sortDesc key=sortVal}
					<option value="{$sortVal}"{if $defaultSortOption[$sectionKey] == $sortVal} selected="selected"{/if}>{translate text=$sortDesc isPublicFacing=true}</option>
				{/foreach}
			</select>

			{if empty($hideCoversFormDisplayed)}
				{* Display the Hide Covers switch above the first section that has holds; and only display it once *}
				<label for="hideCovers_{$source}" class="control-label checkbox pull-right"> {translate text="Hide Covers" isPublicFacing=true} <input id="hideCovers_{$source}" type="checkbox" onclick="AspenDiscovery.Account.loadHolds('{$source}', $('#availableHoldSort_{$source} option:selected').val(), $('#unavailableHoldSort option:selected').val(), !$('#hideCovers_{$source}').is(':checked'));" {if $showCovers == false}checked="checked"{/if}></label>
				{assign var="hideCoversFormDisplayed" value=true}
			{/if}
		</div>
		<div class="striped">
			{foreach from=$recordList.$sectionKey item=record name="recordLoop"}
				{if $record->type == 'ils'}
					{include file="MyAccount/ilsHold.tpl" record=$record section=$sectionKey resultIndex=$smarty.foreach.recordLoop.iteration}
				{elseif $record->type == 'overdrive'}
					{include file="MyAccount/overdriveHold.tpl" record=$record section=$sectionKey resultIndex=$smarty.foreach.recordLoop.iteration}
				{elseif $record->type == 'cloud_library'}
					{include file="MyAccount/cloudLibraryHold.tpl" record=$record section=$sectionKey resultIndex=$smarty.foreach.recordLoop.iteration}
				{elseif $record->type == 'axis360'}
					{include file="MyAccount/axis360Hold.tpl" record=$record section=$sectionKey resultIndex=$smarty.foreach.recordLoop.iteration}
				{elseif $record->type == 'hoopla'}
					{include file="MyAccount/hooplaHold.tpl" record=$record section=$sectionKey resultIndex=$smarty.foreach.recordLoop.iteration}
				{elseif $record->type == 'palace_project'}
					{include file="MyAccount/palaceProjectHold.tpl" record=$record section=$sectionKey resultIndex=$smarty.foreach.recordLoop.iteration}
				{elseif $record->type == 'interlibrary_loan' && $record->source == 'vdx'}
					{include file="MyAccount/vdxRequest.tpl" record=$record section=$sectionKey resultIndex=$smarty.foreach.recordLoop.iteration}
				{else}
					<div class="row">
						Unknown record type {$record->type}
					</div>
				{/if}
			{/foreach}
		</div>
	{else} {* Check to see if records are available *}
		{if $sectionKey == 'available'}
			{translate text='You do not have any holds that are ready to be picked up.' isPublicFacing=true}
		{else}
			{if $source == 'interlibrary_loan'}
				{translate text='You do not have any pending requests.' isPublicFacing=true}
			{else}
				{translate text='You do not have any pending holds.' isPublicFacing=true}
			{/if}
		{/if}
	{/if}
{/foreach}
<br>
{if empty($offline)}
<div class="holdsWithSelected{$sectionKey}">
	<form id="withSelectedHoldsFormBottom{$sectionKey}" action="{$fullPath}">
		<div class="btn-group">
			<a href="#" onclick="AspenDiscovery.Account.confirmCancelHoldSelected()" class="btn btn-sm btn-default btn-warning">{translate text="Cancel Selected" isPublicFacing=true}</a>
			<a href="#" onclick="AspenDiscovery.Account.confirmCancelHoldAll()" class="btn btn-sm btn-default btn-warning">{translate text="Cancel All" isPublicFacing=true}</a>
			{if !empty($allowFreezeAllHolds)}
			<a href="#" onclick="AspenDiscovery.Account.confirmFreezeHoldSelected('', '', '', {if !empty($suspendRequiresReactivationDate)}true{else}false{/if})" class="btn btn-sm btn-default">{translate text="Freeze Selected" isPublicFacing=true}</a>
			<a href="#" onclick="AspenDiscovery.Account.confirmFreezeHoldAll('{$userId}', {if !empty($suspendRequiresReactivationDate)}true{else}false{/if})" class="btn btn-sm btn-default">{translate text="Freeze All" isPublicFacing=true}</a>
			<a href="#" onclick="AspenDiscovery.Account.confirmThawHoldSelected()" class="btn btn-sm btn-default">{translate text="Thaw Selected" isPublicFacing=true}</a>
			<a href="#" onclick="AspenDiscovery.Account.confirmThawHoldAll('{$userId}')" class="btn btn-sm btn-default">{translate text="Thaw All" isPublicFacing=true}</a>
			{/if}

			{if $allowSelectingHoldsToExport}
				<a href="#" onclick="return AspenDiscovery.Account.exportOnlySelectedHolds('{$source}', $('#availableHoldSort_{$source} option:selected').val(), $('#unavailableHoldSort_{$source} option:selected').val());" class="btn btn-sm btn-default">{translate text="Export Selected to CSV" isPublicFacing=true}</a>
			{/if}
		</div>
		<div class="btn-group">
			<input type="hidden" name="withSelectedAction" value="">
			<div id="holdsUpdateSelected{$sectionKey}Bottom" class="holdsUpdateSelected{$sectionKey}">
				<button type="submit" class="btn btn-sm btn-default" id="exportToExcel" name="exportToExcel" onclick="return AspenDiscovery.Account.exportHolds('{$source}', $('#availableHoldSort_{$source} option:selected').val(), $('#unavailableHoldSort_{$source} option:selected').val());">{translate text="Export to CSV" isPublicFacing=true}</button>
			</div>
		</div>
	</form>
</div>
{/if}
