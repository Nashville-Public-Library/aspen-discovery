{strip}
<div id="listEntry{$listEntryId}" class="resultsList listEntry" data-order="{$resultIndex}" data-list_entry_id="{$listEntryId}">
	<div class="row">
		{if !empty($listEditAllowed)}
			<div class="selectTitle col-xs-12 col-sm-1">
				<input type="checkbox" name="selected[{$listEntryId}]" class="titleSelect" id="selected{$listEntryId}">
			</div>
		{/if}
		{if !empty($showCovers)}
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-2 text-center">

			</div>
		{/if}
		<div class="{if empty($showCovers)}col-xs-9 col-sm-9 col-md-9 col-lg-10{else}col-xs-6 col-sm-6 col-md-6 col-lg-7{/if}">
			<div class="row">
				<div class="col-xs-12">
					<span class="result-index">{$resultIndex})</span>&nbsp;
					{if !empty($deletedEntryTitle)}
						<span class="result-title">{$deletedEntryTitle}</span>
						<div>{translate text="This entry no longer exists in the catalog" isPublicFacing=true}</div>
					{else}
						<span class="result-title">{translate text="This entry no longer exists in the catalog" isPublicFacing=true}</span>
					{/if}
				</div>
			</div>
		</div>

		<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 text-right">
			{if !empty($listEditAllowed)}
				<div class="btn-group-vertical" role="group">
					<a href="/MyAccount/Edit?listEntryId={$listEntryId|escape:"url"}{if !is_null($listSelected)}&amp;listId={$listSelected|escape:"url"}{/if}" class="btn btn-default">{translate text='Edit' isPublicFacing=true}</a>
					<a href="#" onclick="AspenDiscovery.confirm('Delete Title?', 'Are you sure you want to delete this?', 'Yes', 'No', true, 'AspenDiscovery.Lists.deleteEntryFromList({$listSelected}, {$listEntryId})', 'btn-danger');" class="btn btn-danger">{translate text='Delete' isPublicFacing=true}</a>
				</div>

			{/if}
		</div>
	</div>
</div>
{/strip}
