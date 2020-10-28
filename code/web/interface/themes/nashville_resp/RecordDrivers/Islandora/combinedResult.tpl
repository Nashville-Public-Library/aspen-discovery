{strip}
<div id="record{if $summShortId}{$summShortId}{else}{$summId|escape}{/if}" class="resultsList row">
	<div class="col-xs-12 col-sm-12">
		<div class="row">
			<div class="col-xs-12">
				<span class="result-index">{$resultIndex})</span>&nbsp;
				<a href="{$summUrl}" class="result-title notranslate">{if !$summTitle|removeTrailingPunctuation}{translate text='Title not available'}{else}{$summTitle|removeTrailingPunctuation|highlight|truncate:180:"..."}{/if}</a>
				{if isset($summScore)}
					&nbsp;(<a href="#" onclick="return AspenDiscovery.showElementInPopup('Score Explanation', '#scoreExplanationValue{$summId|escape}');">{$summScore}</a>)
				{/if}
			</div>
		</div>

		{if $summAuthor}
			<div class="row">
				<div class="result-label col-tn-3">Author: </div>
				<div class="col-tn-9 result-value  notranslate">
					{if is_array($summAuthor)}
						{foreach from=$summAuthor item=author}
							<a href='/Author/Home?author="{$author|escape:"url"}"'>{$author|highlight}</a>
						{/foreach}
					{else}
						<a href='/Author/Home?author="{$summAuthor|escape:"url"}"'>{$summAuthor|highlight}</a>
					{/if}
				</div>
			</div>
		{/if}

		{if $summFormat}
			<div class="row">
				<div class="result-label col-tn-3">Format: </div>
				<div class="col-tn-9 result-value">
					{$summFormat}
				</div>
			</div>
		{/if}

		<div class="row well-small">
			<div class="col-tn-12 result-value" id="descriptionValue{$summId|escape}">{$summDescription|highlight|html_entity_decode|truncate_html:450:"..."|strip_tags|htmlentities}</div>
		</div>

	</div>
</div>
{/strip}