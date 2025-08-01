{strip}
{if count($whileYouWaitTitles) == 0}
	<div class="alert alert-info">Sorry, we could not find any additional titles.</div>
{else}
	<div class="row">
		{foreach from=$whileYouWaitTitles item=whileYouWaitTitle}
			<div class="col-tn-12 col-sm-4 text-center">
				<a href="{$whileYouWaitTitle.url}">
					<img src="{$whileYouWaitTitle.coverUrl}" class="listResultImage img-thumbnail {$coverStyle}" alt="{$whileYouWaitTitle.title|escape}">
				</a>
				{if !empty($showRatings)}
					<div class="browse-rating rater" data-average_rating="{$whileYouWaitTitle.ratingData.average}" data-id="{$whileYouWaitTitle.id}">
						<span class="ui-rater-starsOff" style="width:90px">
						{if !empty($ratingData.user)}
							<span class="ui-rater-starsOn userRated" style="width:{math equation="90*rating/5" rating=$whileYouWaitTitle.ratingData.user}px"></span>
						{else}
							<span class="ui-rater-starsOn" style="width:{math equation="90*rating/5" rating=$whileYouWaitTitle.ratingData.average}px"></span>
						{/if}
						</span>
					</div>
				{/if}
			</div>
		{/foreach}
	</div>
{/if}
{/strip}
{literal}<script type="text/javascript">AspenDiscovery.Ratings.initializeRaters()</script>{/literal}
