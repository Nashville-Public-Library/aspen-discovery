{strip}
<div id="listEntry{$listEntryId}" class="resultsList listEntry" data-order="{$resultIndex}" data-list_entry_id="{$listEntryId}">
	<div class="row">
		{if $showCovers}
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-2 text-center">
				{if $disableCoverArt != 1 && !empty($bookCoverUrlMedium)}
					<a href="{$summUrl}" onclick="AspenDiscovery.EBSCO.trackEdsUsage('{$summId}')" target="_blank" aria-hidden="true">
						<img src="{$bookCoverUrlMedium}" class="listResultImage img-thumbnail" alt="{translate text='Cover Image' inAttribute=true}">
					</a>
				{/if}
			</div>
		{/if}

		<div class="{if !$showCovers}col-xs-10 col-sm-10 col-md-10 col-lg-11{else}col-xs-7 col-sm-7 col-md-7 col-lg-8{/if}">
			<div class="row">
				<div class="col-xs-12">
					<span class="result-index">{$resultIndex})</span>&nbsp;
					<a href="{$summUrl}" class="result-title notranslate" onclick="AspenDiscovery.EBSCO.trackEdsUsage('{$summId}')" target="_blank">
						{if !$summTitle|removeTrailingPunctuation}{translate text='Title not available'}{else}{$summTitle|removeTrailingPunctuation|truncate:180:"..."|highlight}{/if}
					</a>
				</div>
			</div>

			{if $summAuthor}
				<div class="row">
					<div class="result-label col-sm-3">{translate text='Author'}:</div>
					<div class="col-sm-9 result-value">{$summAuthor|escape}</div>
				</div>
			{/if}

			{if strlen($summSourceDatabase)}
				<div class="row hidden-phone">
					<div class="result-label col-sm-3">{translate text='Found in'}:</div>
					<div class="col-sm-9 result-value">{$summSourceDatabase|escape}</div>
				</div>
			{/if}

			{if $summPublicationDates || $summPublishers || $summPublicationPlaces}
				<div class="row">

					<div class="result-label col-sm-3">{translate text='Published'}</div>
					<div class="col-sm-9 result-value">
						{$summPublicationPlaces.0|escape}{$summPublishers.0|escape}{$summPublicationDates.0|escape}
					</div>
				</div>
			{/if}

			{if strlen($summFormats)}
				<div class="row">
					<div class="result-label col-sm-3">{translate text='Format'}</div>
					<div class="col-sm-9 result-value">
						<span>{translate text=$summFormats}</span>
					</div>
				</div>
			{/if}

			{if $summPhysical}
				<div class="row hidden-phone">
					<div class="result-label col-sm-3">{translate text='Physical Desc'}</div>
					<div class="col-sm-9 result-value">{$summPhysical.0|escape}</div>
				</div>
			{/if}

			<div class="row hidden-phone">
				<div class="result-label col-sm-3">{translate text='Full Text'}</div>
				<div class="col-sm-9 result-value">{if $summHasFullText}Yes{else}No{/if}</div>
			</div>

			{if $listEntryNotes}
				<div class="row">
					<div class="result-label col-sm-3">{translate text="Notes"} </div>
					<div class="user-list-entry-note result-value col-sm-9">
						{$listEntryNotes}
					</div>
				</div>
			{/if}

			{if $summDescription}
				{* Standard Description *}
				<div class="row visible-xs">
					<div class="result-label col-tn-3">{translate text='Description'}</div>
					<div class="result-value col-tn-8"><a id="descriptionLink{$summId|escape}" href="#" onclick="$('#descriptionValue{$summId|escape},#descriptionLink{$summId|escape}').toggleClass('hidden-xs');return false;">Click to view</a></div>
				</div>

				{* Mobile Description *}
				<div class="row hidden-xs">
					{* Hide in mobile view *}
					<div class="result-value col-sm-12" id="descriptionValue{$summId|escape}">
						{$summDescription|highlight|truncate_html:450:"..."}
					</div>
				</div>
			{/if}

			<div class="row">
				<div class="col-xs-12">
					{include file='EBSCO/result-tools-horizontal.tpl' recordUrl=$summUrl showMoreInfo=true}
				</div>
			</div>
		</div> {* End of main section *}

		{* List actions *}
		<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 text-right">
			{if $listEditAllowed}
				<div class="btn-group-vertical" role="group">
					<a href="/MyAccount/Edit?listEntryId={$listEntryId|escape:"url"}{if !is_null($listSelected)}&amp;listId={$listSelected|escape:"url"}{/if}" class="btn btn-default">{translate text='Edit'}</a>
					{* Use a different delete URL if we're removing from a specific list or the overall favorites: *}
					<a href="/MyAccount/MyList/{$listSelected|escape:"url"}?delete={$listEntryId|escape:"url"}" onclick="return confirm('Are you sure you want to delete this?');" class="btn btn-default">{translate text='Delete'}</a>
				</div>

			{/if}
		</div>
	</div>
</div>
{/strip}