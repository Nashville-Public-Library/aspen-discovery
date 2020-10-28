{strip}
<div class="col-xs-12">
	{if $parentExhibitUrl}
		{* Search/Archive Navigation for Exhibits within an exhibit *}
		{include file="Archive/search-results-navigation.tpl"}
	{/if}


	{if $main_image}
		<div class="main-project-image">
			<img src="{$main_image}" class="img-responsive" usemap="#map">
		</div>
	{/if}

	<h1>
		{$title}
	</h1>

	<div class="row">
		<div class="col-tn-12">
		{if $thumbnail && !$main_image}
			{if $exhibitThumbnailURL}<a href="{$exhibitThumbnailURL}">{/if}
			<img src="{$thumbnail}" class="img-responsive exhibit-thumbnail">
			{if $exhibitThumbnailURL}</a>{/if}
			<span id="exhibitMainDescriptionContainer" class="lead">
				{$description}
			</span>
		{else}
			{$description}
		{/if}
		</div>
		<div class="clear-both"></div>
	</div>

	<div class="row">
		{foreach from=$collectionTemplates item=template}
			{$template}
		{/foreach}
	</div>

	{if $repositoryLink && $loggedIn && in_array('Administer Islandora Archive', $userPermissions)}
		<div class="row">
			<div id="more-details-accordion" class="panel-group">
				<div class="panel {*active*}{*toggle on for open*}" id="staffViewPanel">
					<a href="#staffViewPanelBody" data-toggle="collapse">
						<div class="panel-heading">
							<div class="panel-title">
								{translate text="Staff View"}
							</div>
						</div>
					</a>
					<div id="staffViewPanelBody" class="panel-collapse collapse {*in*}{*toggle on for open*}">
						<div class="panel-body">
							<a class="btn btn-small btn-default" href="{$repositoryLink}" target="_blank">
								{translate text="View in Islandora"}
							</a>
							<a class="btn btn-small btn-default" href="{$repositoryLink}/datastream/MODS/view" target="_blank">
								{translate text="View MODS Record"}
							</a>
							<a class="btn btn-small btn-default" href="{$repositoryLink}/datastream/MODS/edit" target="_blank">
								{translate text="Edit MODS Record"}
							</a>
							<a class="btn btn-small btn-default" href="#" onclick="return AspenDiscovery.Archive.clearCache('{$pid}');" target="_blank">
								{translate text="Clear Cache"}
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	{/if}
</div>
{/strip}

<script type="text/javascript">
	$().ready(function(){ldelim}
		AspenDiscovery.Archive.loadExploreMore('{$pid|urlencode}');
		{rdelim});
</script>