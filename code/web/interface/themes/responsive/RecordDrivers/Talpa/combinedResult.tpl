{strip}
<div id="record{if !empty($talpaShortId)}{$talpaShortId}{else}{$talpaId|escape}{/if}" class="resultsList row">
	{if !empty($showCovers)}
		<div class="coversColumn col-xs-3 text-center">
			{if $disableCoverArt != 1}
				<a href="{$talpaUrl}" aria-hidden="true">
					<img src="{$bookCoverUrl}" class="listResultImage img-thumbnail {$coverStyle}" alt="{translate text='Cover Image' inAttribute=true isPublicFacing=true}">
				</a>
			{/if}
		</div>
	{/if}
	<div class="{if !empty($showCovers)}col-xs-9{else}col-xs-12{/if}">
		<div class="row">
			<div class="col-xs-12">
				<span class="result-index">{$resultIndex})</span>&nbsp;
				<a href="{$talpaUrl}" class="result-title notranslate">
					{if !$talpaTitle|removeTrailingPunctuation} {translate text='Title not available' isPublicFacing=true}{else}{$talpaTitle|removeTrailingPunctuation|truncate:180:"..."|highlight}{/if}
				</a>
			</div>
		</div>

		{if !empty($talpaAuthor)}
			<div class="row">
				<div class="result-label col-tn-3"> {translate text='Author' isPublicFacing=true}</div>
				<div class="col-tn-9 result-value">{$talpaAuthor|escape}</div>
			</div>
		{/if}

		{if strlen($talpaFormats)}
			<div class="row">
				<div class="result-label col-tn-3">{translate text="Format" isPublicFacing=true}</div>
				<div class="col-tn-9 result-value">
					<span>{translate text=$talpaFormats isPublicFacing=true}</span>
				</div>
			</div>
		{/if}

		{if !empty($talpaDescription)}
			{* Standard Description *}
			<div class="row visible-xs">
				<div class="result-label col-tn-3">{translate text='Description' isPublicFacing=true}</div>
				<div class="result-value col-tn-8"><a id="descriptionLink{$talpaId|escape}" href="#" onclick="$('#descriptionValue{$talpaId|escape},#descriptionLink{$talpaId|escape}').toggleClass('hidden-xs');return false;">{translate text="Click to view" isPublicFacing=true}</a></div>
			</div>

			{* Mobile Description *}
			<div class="row">
				{* Hide in mobile view *}
				<div class="hidden-xs result-value col-sm-12" id="descriptionValue{$talpaId|escape}">
					{$talpaDescription|highlight|truncate_html:450:"..."}
				</div>
			</div>
		{/if}
	</div>
</div>
{/strip}