{strip}
	<div id="groupedRecord{$summId|escape}" class="resultsList">
		<a id="record{$summId|escape}"></a>
		{if isset($summExplain)}
			<div class="hidden" id="scoreExplanationValue{$summId|escape}">{$summExplain}</div>
		{/if}

		<div class="row">
			{if $showCovers}
				<div class="coversColumn col-xs-3 col-sm-3{if !empty($viewingCombinedResults)} col-md-3 col-lg-2{/if} text-center">
					{if $disableCoverArt != 1}
						<a href="{$summUrl}" aria-hidden="true">
							<img src="{$bookCoverUrlMedium}" class="listResultImage img-thumbnail" alt="{translate text='Cover Image' inAttribute=true}">
						</a>
					{/if}

					{if $showRatings}
						{include file="GroupedWork/title-rating.tpl" id=$summId ratingData=$summRating}
					{/if}
				</div>
			{/if}

			<div class="{if !$showCovers}col-xs-12{else}col-xs-9 col-sm-9{if !empty($viewingCombinedResults)} col-md-9 col-lg-10{/if}{/if}">{* May turn out to be more than one situation to consider here *}
				{* Title Row *}
				<div class="row">
					<div class="col-xs-12">
						<span class="result-index">{$resultIndex})</span>&nbsp;
						<a href="{$summUrl}" class="result-title notranslate">
							{if !$summTitle|removeTrailingPunctuation}{translate text='Title not available'}{else}{$summTitle|removeTrailingPunctuation|highlight|truncate:180:"..."}{/if}
							{if $summSubTitle|removeTrailingPunctuation}: {$summSubTitle|removeTrailingPunctuation|highlight|truncate:180:"..."}{/if}
						</a>
						{if isset($summScore)}
							&nbsp;(<a href="#" onclick="return AspenDiscovery.showElementInPopup('Score Explanation', '#scoreExplanationValue{$summId|escape}');">{$summScore}</a>)
						{/if}
					</div>
				</div>

				{if $summAuthor}
					<div class="row">
						<div class="result-label col-tn-3">{translate text="Author"} </div>
						<div class="result-value col-tn-8 notranslate">
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

				{if $showSeries}
					{assign var=indexedSeries value=$recordDriver->getIndexedSeries()}
					{if $summSeries || $indexedSeries}
						<div class="series{$summISBN} row">
							<div class="result-label col-tn-3">{translate text="Series"} </div>
							<div class="result-value col-tn-8">
								{if $summSeries}
									{if $summSeries.fromNovelist}
										<a href="/GroupedWork/{$summId}/Series">{$summSeries.seriesTitle}</a>{if $summSeries.volume} {translate text=volume} {$summSeries.volume}{/if}<br>
									{else}
										<a href="/Search/Results?searchIndex=Series&lookfor={$summSeries.seriesTitle}">{$summSeries.seriesTitle}</a>{if $summSeries.volume} {translate text=volume} {$summSeries.volume}{/if}
									{/if}
								{/if}
								{if $indexedSeries}
									{assign var=showMoreSeries value=false}
									{if count($indexedSeries) > 4}
										{assign var=showMoreSeries value=true}
									{/if}
									{foreach from=$indexedSeries item=seriesItem name=loop}
										{if !isset($summSeries.seriesTitle) || ((strpos(strtolower($seriesItem.seriesTitle), strtolower($summSeries.seriesTitle)) === false) && (strpos(strtolower($summSeries.seriesTitle), strtolower($seriesItem.seriesTitle)) === false))}
											<a href="/Search/Results?searchIndex=Series&lookfor=%22{$seriesItem.seriesTitle|escape:"url"}%22">{$seriesItem.seriesTitle|escape}</a>{if $seriesItem.volume} {translate text=volume} {$seriesItem.volume}{/if}<br>
											{if $showMoreSeries && $smarty.foreach.loop.iteration == 3}
												<a onclick="$('#moreSeries_{$summId}').show();$('#moreSeriesLink_{$summId}').hide();" id="moreSeriesLink_{$summId}">{translate text='More Series...'}</a>
												<div id="moreSeries_{$summId}" style="display:none">
											{/if}
										{/if}
									{/foreach}
									{if $showMoreSeries}
										</div>
									{/if}
								{/if}
							</div>
						</div>
					{/if}
				{/if}

				{if $showPublisher}
				{if $alwaysShowSearchResultsMainDetails || $summPublisher}
					<div class="row">
						<div class="result-label col-tn-3">{translate text="Publisher"} </div>
						<div class="result-value col-tn-8">
							{if $summPublisher}
								{$summPublisher}
							{elseif $alwaysShowSearchResultsMainDetails}
								{translate text="Not Supplied"}
							{/if}
						</div>
					</div>
				{/if}
				{/if}

				{if $showPublicationDate}
					{if $alwaysShowSearchResultsMainDetails || $summPubDate}
						<div class="row">
							<div class="result-label col-tn-3">{translate text="Pub. Date"} </div>
							<div class="result-value col-tn-8">
								{if $summPubDate}
									{$summPubDate|escape}
								{elseif $alwaysShowSearchResultsMainDetails}
									{translate text="Not Supplied"}
								{/if}
							</div>
						</div>
					{/if}
				{/if}

				{if !empty($showEditions)}
					{if $alwaysShowSearchResultsMainDetails || $summEdition}
						<div class="row">
							<div class="result-label col-tn-3">{translate text="Edition"} </div>
							<div class="result-value col-tn-8">
								{if $summEdition}
									{$summEdition}
								{elseif $alwaysShowSearchResultsMainDetails}
									{translate text="Not Supplied"}
								{/if}
							</div>
						</div>
					{/if}
				{/if}

				{if !empty($showArInfo) && $summArInfo}
					<div class="row">
						<div class="result-label col-tn-3">{translate text='Accelerated Reader'} </div>
						<div class="result-value col-tn-8">
							{$summArInfo}
						</div>
					</div>
				{/if}

				{if !empty($showLexileInfo) && $summLexileInfo}
					<div class="row">
						<div class="result-label col-tn-3">{translate text='Lexile measure'} </div>
						<div class="result-value col-tn-8">
							{$summLexileInfo}
						</div>
					</div>
				{/if}

				{if !empty($showFountasPinnell) && $summFountasPinnell}
					<div class="row">
						<div class="result-label col-tn-3">{translate text='Fountas &amp; Pinnell'} </div>
						<div class="result-value col-tn-8">
							{$summFountasPinnell}
						</div>
					</div>
				{/if}

				{if !empty($showPhysicalDescriptions)}
					{if $alwaysShowSearchResultsMainDetails || $summPhysicalDesc}
						<div class="row">
							<div class="result-label col-tn-3">{translate text='Physical Desc'} </div>
							<div class="result-value col-tn-8">
								{if $summPhysicalDesc}
									{$summPhysicalDesc}
								{elseif $alwaysShowSearchResultsMainDetails}
									{translate text="Not Supplied"}
								{/if}
							</div>
						</div>
					{/if}
				{/if}

				{if $showLanguages && $summLanguage}
					<div class="row">
						<div class="result-label col-tn-3">{translate text="Language"} </div>
						<div class="result-value col-tn-8">
							{if is_array($summLanguage)}
								{implode subject=$summLanguage glue=', ' translate=true}
							{else}
								{$summLanguage|translate}
							{/if}
						</div>
					</div>
				{/if}

				{if $summSnippets}
					{foreach from=$summSnippets item=snippet}
						<div class="row">
							<div class="result-label col-tn-3">{translate text=$snippet.caption} </div>
							<div class="result-value col-tn-8">
								{if !empty($snippet.snippet)}<span class="quotestart">&#8220;</span>...{$snippet.snippet|highlight}...<span class="quoteend">&#8221;</span><br>{/if}
							</div>
						</div>
					{/foreach}
				{/if}

				{include file="GroupedWork/relatedLists.tpl"}

				{* Short Mobile Entry for Formats when there aren't hidden formats *}
				<div class="row visible-xs">

					{* Determine if there were hidden Formats for this entry *}
					{assign var=hasHiddenFormats value=false}
					{foreach from=$relatedManifestations item=relatedManifestation}
					{if $relatedManifestation->hasHiddenFormats()}
						{assign var=hasHiddenFormats value=true}
					{/if}
					{/foreach}

					{* If there weren't hidden formats, show this short Entry (mobile view only). The exception is single format manifestations, they
					   won't have any hidden formats and will be displayed *}
					{if !$hasHiddenFormats && count($relatedManifestations) != 1}
						<div class="hidethisdiv{$summId|escape} result-label col-tn-3">
							{translate text="Formats"}
						</div>
						<div class="hidethisdiv{$summId|escape} result-value col-tn-8">
							<a href="#" onclick="$('#relatedManifestationsValue{$summId|escape},.hidethisdiv{$summId|escape}').toggleClass('hidden-xs');return false;">
								{implode subject=$relatedManifestations|@array_keys glue=", "}
							</a>
						</div>
					{/if}

				</div>

				{* Formats Section *}
				<div class="row">
					<div class="{if !$hasHiddenFormats && count($relatedManifestations) != 1}hidden-xs {/if}col-sm-12" id="relatedManifestationsValue{$summId|escape}">
						{* Hide Formats section on mobile view, unless there is a single format or a format has been selected by the user *}
						{* relatedManifestationsValue ID is used by the Formats button *}

						{include file="GroupedWork/relatedManifestations.tpl" id=$summId workId=$summId}
					</div>
				</div>

				{if empty($viewingCombinedResults)}
					{* Description Section *}
					{if $summDescription}
						{* Standard Description *}
						<div class="row visible-xs">
							<div class="result-label col-tn-3">Description</div>
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
							{include file='GroupedWork/result-tools-horizontal.tpl' ratingData=$summRating recordUrl=$summUrl showMoreInfo=true}
						</div>
					</div>
				{/if}

			</div>

		</div>
	</div>
{/strip}
