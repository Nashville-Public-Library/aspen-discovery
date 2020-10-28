{* {strip} *}
	<div class="col-xs-12">
		{* Search Navigation *}
		{include file="Archive/search-results-navigation.tpl"}
		<h1>
			{$title}
			{*{$title|escape} // plb 3/8/2017 not escaping because some titles use &amp; *}
		</h1>

		{if $canView}
			<div class="large-image-wrapper">
				<div class="large-image-content" oncontextmenu="return false;">
					<div id="custom-openseadragon" class="openseadragon"></div>
				</div>
			</div>

			<div id="alternate-image-navigator">
				<img src="{$front_thumbnail}" class="img-responsive alternate-image" onclick="AspenDiscovery.Archive.openSeaDragonViewer.goToPage(0);">
				<img src="{$back_thumbnail}" class="img-responsive alternate-image" onclick="AspenDiscovery.Archive.openSeaDragonViewer.goToPage(1);">
			</div>

		{else}
			{include file="Archive/noAccess.tpl"}
		{/if}

		<div id="download-options">
			{if $canView}
				{if $anonymousLcDownload || ($loggedIn && $verifiedLcDownload)}
					<a class="btn btn-default" href="/Archive/{$pid}/DownloadLC">Download Large Image</a>
				{elseif (!$loggedIn && $verifiedLcDownload)}
					<a class="btn btn-default" onclick="return AspenDiscovery.Account.followLinkIfLoggedIn(this)" href="/Archive/{$pid}/DownloadLC">Sign in to Download Large Image</a>
				{/if}
				{if $anonymousOriginalDownload || ($loggedIn && $verifiedOriginalDownload)}
					<a class="btn btn-default" href="/Archive/{$pid}/DownloadOriginal">Download Original Image</a>
				{elseif (!$loggedIn && $verifiedLcDownload)}
					<a class="btn btn-default" onclick="return AspenDiscovery.Account.followLinkIfLoggedIn(this)" href="/Archive/{$pid}/DownloadOriginal">Sign in to Download Original Image</a>
				{/if}
			{/if}
			{if $allowRequestsForArchiveMaterials}
				<a class="btn btn-default" href="/Archive/RequestCopy?pid={$pid}">Request Copy</a>
			{/if}
			{if $showClaimAuthorship}
				<a class="btn btn-default" href="/Archive/ClaimAuthorship?pid={$pid}">Claim Authorship</a>
			{/if}
			{if $showFavorites == 1}
				<a onclick="return Account.Archive.showSaveToListForm(this, 'Islandora', '{$pid|escape}');" class="btn btn-default ">{translate text='Add to list'}</a>
			{/if}
		</div>

		{include file="Archive/metadata.tpl"}
	</div>
	<script src="/js/openseadragon/openseadragon.js" ></script>
	<script src="/js/openseadragon/djtilesource.js" ></script>
	{if $canView}
	<script type="text/javascript">
		$(document).ready(function(){ldelim}
			if (!$('#custom-openseadragon').hasClass('processed')) {ldelim}
				var openSeadragonSettings = {ldelim}
					"pid":"{$pid}",
					"resourceUri":{$front_image|@json_encode nofilter},
					"tileSize":256,
					"tileOverlap":0,
					"id":"custom-openseadragon",
					"settings": AspenDiscovery.Archive.openSeadragonViewerSettings()
				{rdelim};
				openSeadragonSettings.settings.tileSources = new Array();
				var frontTile = new OpenSeadragon.DjatokaTileSource(
						Globals.url + "/AJAX/DjatokaResolver",
						'{$front_image}',
						openSeadragonSettings.settings
				);
				openSeadragonSettings.settings.tileSources.push(frontTile);
				var backTile = new OpenSeadragon.DjatokaTileSource(
						Globals.url + "/AJAX/DjatokaResolver",
						'{$back_image}',
						openSeadragonSettings.settings
				);
				openSeadragonSettings.settings.tileSources.push(backTile);

				AspenDiscovery.Archive.openSeaDragonViewer = new OpenSeadragon(openSeadragonSettings.settings);
				$('#custom-openseadragon').addClass('processed');
			{rdelim}
		{rdelim});
	</script>
{/if}
{* {/strip} *}
<script type="text/javascript">
	$().ready(function(){ldelim}
		AspenDiscovery.Archive.loadExploreMore('{$pid|urlencode}');
		{rdelim});
</script>