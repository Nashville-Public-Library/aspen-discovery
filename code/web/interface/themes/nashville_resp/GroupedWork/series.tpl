{strip}
	{if $recordDriver}
	<div id="seriesInfo" style="display:none" class="row">
		<div class="col-sm-12">
			{assign var="scrollerName" value="Series"}
			{assign var="scrollerTitle" value=""}
			{assign var="wrapperId" value="series"}
			{assign var="scrollerVariable" value="seriesScroller"}
			{assign var="permanentId" value=$recordDriver->getPermanentId()}
			{assign var="fullListLink" value= "/GroupedWork/$permanentId/Series"}
			{include file='CollectionSpotlight/titleScroller.tpl'}
		</div>
	</div>
	{/if}
{/strip}