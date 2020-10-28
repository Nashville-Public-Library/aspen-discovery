{assign var="hideCoversFormDisplayed" value=false}
{foreach from=$recordList item=sectionData key=sectionKey}
	<h2>{if $sectionKey == 'available'}{translate text="Holds Ready For Pickup"}{else}{translate text="Pending Holds"}{/if}</h2>
	<p class="alert alert-info">
		{if $sectionKey == 'available'}
			{translate text="available hold summary" defaultText="These titles have arrived at the library or are available online for you to use."}
			{*These titles have arrived at the library or are available online for you to use.*}
		{else}
			{if not $notification_method or $notification_method eq 'Unknown'}
				{translate text="unavailable_hold_summary_no_notification" defaultText="These titles are currently checked out to other patrons. We will notify you when a title is available."}
			{else}
				{translate text="unavailable_hold_summary_with_notification" defaultText="These titles are currently checked out to other patrons. We will notify you via %1% when a title is available." 1=$notification_method}
			{/if}
		{/if}
	</p>
	{if is_array($recordList.$sectionKey) && count($recordList.$sectionKey) > 0}
		<div id="pager" class="navbar form-inline">
			<label for="{$sectionKey}HoldSort_{$source}" class="control-label">{translate text='Sort by'}&nbsp;</label>
			<select name="{$sectionKey}HoldSort_{$source}" id="{$sectionKey}HoldSort_{$source}" class="form-control" onchange="AspenDiscovery.Account.loadHolds('{$source}', $('#availableHoldSort_{$source} option:selected').val(), $('#unavailableHoldSort_{$source} option:selected').val());">
				{foreach from=$sortOptions[$sectionKey] item=sortDesc key=sortVal}
					<option value="{$sortVal}"{if $defaultSortOption[$sectionKey] == $sortVal} selected="selected"{/if}>{translate text=$sortDesc}</option>
				{/foreach}
			</select>

			{if !$hideCoversFormDisplayed}
				{* Display the Hide Covers switch above the first section that has holds; and only display it once *}
				<label for="hideCovers_{$source}" class="control-label checkbox pull-right"> {translate text="Hide Covers"} <input id="hideCovers_{$source}" type="checkbox" onclick="AspenDiscovery.Account.loadHolds('{$source}', $('#availableHoldSort_{$source} option:selected').val(), $('#unavailableHoldSort option:selected').val(), !$('#hideCovers_{$source}').is(':checked'));" {if $showCovers == false}checked="checked"{/if}></label>
				{assign var="hideCoversFormDisplayed" value=true}
			{/if}
		</div>
		<div class="striped">
			{foreach from=$recordList.$sectionKey item=record name="recordLoop"}
				{if $record.holdSource == 'ILS'}
					{include file="MyAccount/ilsHold.tpl" record=$record section=$sectionKey resultIndex=$smarty.foreach.recordLoop.iteration}
				{elseif $record.holdSource == 'OverDrive'}
					{include file="MyAccount/overdriveHold.tpl" record=$record section=$sectionKey resultIndex=$smarty.foreach.recordLoop.iteration}
				{elseif $record.holdSource == 'RBdigital'}
					{include file="MyAccount/rbdigitalHold.tpl" record=$record section=$sectionKey resultIndex=$smarty.foreach.recordLoop.iteration}
				{elseif $record.holdSource == 'CloudLibrary'}
					{include file="MyAccount/cloudLibraryHold.tpl" record=$record section=$sectionKey resultIndex=$smarty.foreach.recordLoop.iteration}
				{elseif $record.holdSource == 'Axis360'}
					{include file="MyAccount/axis360Hold.tpl" record=$record section=$sectionKey resultIndex=$smarty.foreach.recordLoop.iteration}
				{else}
					<div class="row">
						Unknown record source {$record.holdSource}
					</div>
				{/if}
			{/foreach}
		</div>
	{else} {* Check to see if records are available *}
		{if $sectionKey == 'available'}
			{translate text="no_holds_ready_pickup" defaultText='You do not have any holds that are ready to be picked up.'}
		{else}
			{translate text='You do not have any pending holds.'}
		{/if}
	{/if}
{/foreach}
<br>
<div class="holdsWithSelected{$sectionKey}">
	<form id="withSelectedHoldsFormBottom{$sectionKey}" action="{$fullPath}">
		<div>
			<input type="hidden" name="withSelectedAction" value="">
			<div id="holdsUpdateSelected{$sectionKey}Bottom" class="holdsUpdateSelected{$sectionKey}">
				<button type="submit" class="btn btn-sm btn-default" id="exportToExcel" name="exportToExcel" onclick="return AspenDiscovery.Account.exportHolds('{$source}', $('#availableHoldSort_{$source} option:selected').val(), $('#unavailableHoldSort_{$source} option:selected').val());">{translate text="Export to Excel"}</button>
			</div>
		</div>
	</form>
</div>