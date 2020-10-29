{if $showCollectionSpotlightTitle || $showViewMoreLink}
		<div id="list-{$wrapperId}Header" class="titleScrollerHeader">
			{if $scrollerTitle}
				<span class="listTitle resultInformationLabel">{if $scrollerTitle}{$scrollerTitle|escape:"html"}{/if}</span>
			{/if}
			{if $showViewMoreLink}
				<div id="titleScrollerViewMore{$scrollerName}" class="titleScrollerViewMore"><a href="{$fullListLink}">View More</a></div>
			{/if}
		</div>
	{/if}
<div class="jcarousel-wrapper horizontalCarouselSpotlightWrapper">
	<div class="jcarousel horizontalCarouselSpotlight" id="collectionSpotlightCarousel{$list->id}">
		<div class="loading">Loading carousel items...</div>
	</div>

	<a href="#" class="jcarousel-control-prev" aria-label="Previous Item"><i class="fas fa-caret-left"></i></a>
	<a href="#" class="jcarousel-control-next" aria-label="Next Item"><i class="fas fa-caret-right"></i></a>
</div>
<script type="text/javascript">
	$(document).ready(function(){ldelim}
		AspenDiscovery.CollectionSpotlights.loadCarousel('{$list->id}', '/Search/AJAX?method=getSpotlightTitles&id={$list->id}&scrollerName={$listName}&coverSize={$collectionSpotlight->coverSize}&showRatings={$collectionSpotlight->showRatings}&numTitlesToShow={$collectionSpotlight->numTitlesToShow}{if $reload}&reload=true{/if}');
	{rdelim});
</script>