{strip}
	{* Recommendations *}
	{if $topRecommendations}
		{foreach from=$topRecommendations item="recommendations"}
			{include file=$recommendations}
		{/foreach}
	{/if}

	<h1>{translate text='nohit_heading'}</h1>

	<p class="alert alert-info">{translate text='nohit_prefix'} - <b>{if $lookfor}{$lookfor|escape:"html"}{else}&lt;empty&gt;{/if}</b> - {translate text='nohit_suffix'}</p>

	{* Return to Advanced Search Link *}
	{if $searchType == 'advanced'}
		<h5>
			<a href="/Search/Advanced">Edit This Advanced Search</a>
		</h5>
	{/if}

	{if !empty($solrSearchDebug)}
		<div id="solrSearchOptionsToggle" onclick="$('#solrSearchOptions').toggle()">Show Search Options</div>
		<div id="solrSearchOptions" style="display:none">
			<pre>Search options: {$solrSearchDebug}</pre>
		</div>
	{/if}

	{if !empty($solrLinkDebug)}
		<div id='solrLinkToggle' onclick='$("#solrLink").toggle()'>Show Solr Link</div>
		<div id='solrLink' style='display:none'>
			<pre>{$solrLinkDebug}</pre>
		</div>
	{/if}

	<div>
		{if !empty($parseError)}
			<div class="alert alert-danger">
				{$parseError}
			</div>
		{/if}

		{if !empty($keywordResultsLink)}
			<div class="correction">
			<h3>{translate text="Try a Keyword Search?"}</h3>
				Your search type is not set to Keyword.  There are <strong>{$keywordResultsCount}</strong> results if you <a class='btn btn-xs btn-primary' href="{$keywordResultsLink}">Search by Keyword</a>.
			</div>
		{/if}

		{if $placard}
			{include file="Search/placard.tpl"}
		{/if}

		{include file="Search/searchSuggestions.tpl"}

		{include file="Search/spellingSuggestions.tpl"}

		{if $showExploreMoreBar}
			<div id="explore-more-bar-placeholder"></div>
			<script type="text/javascript">
				$(document).ready(
						function () {ldelim}
	                        AspenDiscovery.Searches.loadExploreMoreBar('{$exploreMoreSection}', '{$exploreMoreSearchTerm|escape:"html"}');
							{rdelim}
				);
			</script>
		{/if}

		{if $showProspectorLink}
			{* Prospector Results *}
			<div id='prospectorSearchResultsPlaceholder'></div>
			{* javascript call for content at bottom of page*}
		{elseif !empty($interLibraryLoanName) && !empty($interLibraryLoanUrl)}
			{include file="Search/interLibraryLoanSearch.tpl"}
		{/if}

		{if $showDplaLink}
			{* DPLA Results *}
			<div id='dplaSearchResultsPlaceholder'></div>
		{/if}

		{if $materialRequestType == 1}
			<h2>{translate text="Didn't find it?"}</h2>
			<p>{translate text="Can't find what you are looking for?"} <a href="/MaterialsRequest/NewRequest?lookfor={$lookfor}&searchIndex={$searchIndex}" onclick="return AspenDiscovery.Account.followLinkIfLoggedIn(this);">{translate text='Suggest a purchase'}</a>.</p>
		{elseif $materialRequestType == 2}
			<h2>{translate text="Didn't find it?"}</h2>
			<p>{translate text="Can't find what you are looking for?"} <a href="/MaterialsRequest/NewRequestIls?lookfor={$lookfor}&searchIndex={$searchIndex}" onclick="return AspenDiscovery.Account.followLinkIfLoggedIn(this);">{translate text='Suggest a purchase'}</a>.</p>
		{elseif $materialRequestType == 3}
			<h2>{translate text="Didn't find it?"}</h2>
			<p>{translate text="Can't find what you are looking for?"} <a href="{$externalMaterialsRequestUrl}">{translate text='Suggest a purchase'}</a>.</p>
		{/if}

		{if $showSearchTools || ($loggedIn && count($userPermissions) > 0)}
			<br/>
			<div class="search_tools well small">
				<strong>{translate text='Search Tools'} </strong>
				{if $showSearchTools}
					<a href="{$rssLink|escape}">{translate text='Get RSS Feed'}</a>
					<a href="#" onclick="return AspenDiscovery.Account.ajaxLightbox('/Search/AJAX?method=getEmailForm', true);">{translate text='Email this Search'}</a>
					{if $savedSearch}
						<a href="#" onclick="return AspenDiscovery.Account.saveSearch('{$searchId}')">{translate text='save_search_remove'}</a>
					{else}
						<a href="#" onclick="return AspenDiscovery.Account.saveSearch('{$searchId}')">{translate text='save_search'}</a>
					{/if}
					<a href="{$excelLink|escape}">{translate text='Export To Excel'}</a>
				{/if}
			</div>
		{/if}

	</div>

	<script type="text/javascript">
		$(function(){ldelim}
			{if $showProspectorLink}
			AspenDiscovery.Prospector.getProspectorResults(5, {$prospectorSavedSearchId});
			{/if}
			{if $showDplaLink}
			AspenDiscovery.DPLA.getDPLAResults('{$lookfor}');
			{/if}
		{rdelim});
	</script>
{/strip}