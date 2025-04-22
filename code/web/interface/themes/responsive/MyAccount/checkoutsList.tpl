{strip}
{if !empty($transList)}
	<form id="renewForm_{$source}" action="/MyAccount/CheckedOut">
		<div id="pager" class="row">
			<div class="col-xs-6 form-inline">
				<label for="accountSort_{$source}" class="control-label">{translate text='Sort by' isPublicFacing=true}&nbsp;</label>
				<select name="accountSort" id="accountSort_{$source}" class="form-control" onchange="AspenDiscovery.Account.loadCheckouts('{$source}', $('#accountSort_{$source} option:selected').val(), !$('#hideCovers_{$source}').is(':checked'));">
					{foreach from=$sortOptions item=sortDesc key=sortVal}
						<option value="{$sortVal}"{if $defaultSortOption == $sortVal} selected="selected"{/if}>{translate text=$sortDesc isPublicFacing=true inAttribute=true}</option>
					{/foreach}
				</select>
			</div>
			<div class="col-xs-6">
				<label for="hideCovers_{$source}" class="control-label checkbox pull-right"> {translate text="Hide Covers" isPublicFacing=true} <input id="hideCovers_{$source}" type="checkbox" onclick="AspenDiscovery.Account.loadCheckouts('{$source}', $('#accountSort_{$source} option:selected').val(), !$('#hideCovers_{$source}').is(':checked'));" {if $showCovers == false}checked="checked"{/if}></label>
			</div>
		</div>

		{if count($transList) > 1 && ($source=='all' || $source=='ils')}
			<div class="row">
				<div class="col-xs-12">
					<label for="selectAll_{$source}" class="control-label checkbox"> {translate text="Select/Deselect All" isPublicFacing=true} <input id="selectAll_{$source}" type="checkbox" onclick="$('#renewForm_{$source} .titleSelect').prop('checked', $('#selectAll_{$source}').is(':checked'));"></label>
				</div>
			</div>
		{/if}

		{if $renewableCheckouts >= 5}
			<div class="btn-group">
				{if $source=='all' || $source=='ils'}
					<a href="#" onclick="AspenDiscovery.Account.renewSelectedTitles()" class="btn btn-sm btn-default">{translate text="Renew Selected Items" isPublicFacing=true}</a>
					<a href="#" onclick="AspenDiscovery.Account.renewAll()" class="btn btn-sm btn-default">{translate text="Renew All" isPublicFacing=true}</a>
				{/if}
			</div>
		{/if}

		<div class="striped">
			{foreach from=$transList item=checkedOutTitle name=checkedOutTitleLoop key=checkedOutKey}
				{if $checkedOutTitle->type == 'ils'}
					{include file="MyAccount/ilsCheckedOutTitle.tpl" record=$checkedOutTitle resultIndex=$smarty.foreach.checkedOutTitleLoop.iteration}
				{elseif $checkedOutTitle->type == 'overdrive'}
					{include file="MyAccount/overdriveCheckedOutTitle.tpl" record=$checkedOutTitle resultIndex=$smarty.foreach.checkedOutTitleLoop.iteration}
				{elseif $checkedOutTitle->type == 'hoopla'}
					{include file="MyAccount/hooplaCheckedOutTitle.tpl" record=$checkedOutTitle resultIndex=$smarty.foreach.checkedOutTitleLoop.iteration}
				{elseif $checkedOutTitle->type == 'cloud_library'}
					{include file="MyAccount/cloudLibraryCheckedOutTitle.tpl" record=$checkedOutTitle resultIndex=$smarty.foreach.checkedOutTitleLoop.iteration}
				{elseif $checkedOutTitle->type == 'axis360'}
					{include file="MyAccount/axis360CheckedOutTitle.tpl" record=$checkedOutTitle resultIndex=$smarty.foreach.checkedOutTitleLoop.iteration}
				{elseif $checkedOutTitle->type == 'palace_project'}
					{include file="MyAccount/palaceProjectCheckedOutTitle.tpl" record=$checkedOutTitle resultIndex=$smarty.foreach.checkedOutTitleLoop.iteration}
				{else}
					<div class="row">
						{translate text="Unknown record source %1%" 1=$checkedOutTitle->type isPublicFacing=true}
					</div>
				{/if}
			{/foreach}
		</div>

		<br/>

		<div class="btn-group">
			{if $renewableCheckouts >= 1}
				{if $source=='all' || $source=='ils'}
					<a href="#" onclick="AspenDiscovery.Account.renewSelectedTitles()" class="btn btn-sm btn-default">{translate text="Renew Selected Items" isPublicFacing=true}</a>
					<a href="#" onclick="AspenDiscovery.Account.renewAll()" class="btn btn-sm btn-default">{translate text="Renew All" isPublicFacing=true}</a>
				{/if}
			{/if}
			<a class="btn btn-sm btn-default" id="exportToExcel" onclick="return AspenDiscovery.Account.exportCheckouts('{$source}', $('#accountSort_{$source} option:selected').val());">{translate text="Export to CSV" isPublicFacing=true}</a>
			{if $allowSelectingCheckoutsToExport}
				<a class="btn btn-sm btn-default" id="exportSelectedToExcel" onclick="return AspenDiscovery.Account.exportOnlySelectedCheckouts('{$source}', $('#accountSort_{$source} option:selected').val());">{translate text="Export Selected Titles to CSV" isPublicFacing=true}</a>
			{/if}
		</div>
	</form>
{else}
	{translate text='You do not have any items checked out' isPublicFacing=true}.
{/if}
{/strip}
<script type="text/javascript">
	AspenDiscovery.Ratings.initializeRaters();
</script>
