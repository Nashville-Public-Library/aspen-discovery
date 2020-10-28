{strip}
{if $numTitles == 0}
	<div class="alert alert-info">Sorry, we could not find any additional titles.</div>
{else}
	<div class="row">
		{foreach from=$youMightAlsoLikeTitles item=title}
			<div class="col-tn-12 col-sm-4" style="text-align: center">
				<div class="row">
					<div class="col-tn-12">
						<a href="{$title->getLinkUrl()}">
							<img src="{$title->getBookcoverUrl('medium')}" class="listResultImage img-thumbnail" alt="{$title->getTitle()|escape}">
						</a>
					</div>
				</div>
				<div class="row">
					<div class="col-tn-12" style="padding-top: 5px">
					<a href="{$title->getLinkUrl()}" class="btn btn-primary btn-sm">{translate text="More Info"}</a>
					</div>
				</div>
			</div>
		{/foreach}
	</div>
{/if}
{/strip}