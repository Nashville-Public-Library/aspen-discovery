{strip}
	<div class="row">
		<div class="col-xs-12">
			<div class="row">
				<h1>{$title}</h1>
			</div>
			<div class="row">
				<p>{$description}</p>
			</div>
		</div>
	</div>
	{foreach from=$webResources item=curResource}
		<div id="webPageResult" class="resultsList row">
			<div class="coversColumn col-xs-3 col-sm-3 col-md-3 col-lg-2 text-center" aria-hidden="true" role="presentation">
				<a href="{$curResource.link}" class="alignleft listResultImage">
					<img src="{$curResource.bookCoverUrl}" class="listResultImage img-thumbnail {$coverStyle}" alt="{$curResource.title|removeTrailingPunctuation|highlight|truncate:180:"..."}">
				</a>
			</div>
			<div class="col-xs-9 col-sm-9 col-md-9 col-lg-10">
				<div class="col-xs-12">
					<span class="result-index"></span>&nbsp;
					<a href="{$curResource.link}" class="result-title notranslate" onclick="AspenDiscovery.Websites.trackUsage('{$curResource.id}')">
						{if !$curResource.title|removeTrailingPunctuation} {translate text='Title not available' isPublicFacing=true}{else}{$curResource.title|removeTrailingPunctuation|highlight|truncate:180:"..."}{/if}
					</a>
				</div>
			</div>

			{* Description Section *}
			{if !empty($curResource.description)}
				<div class="result-value col-tn-9 col-xs-9">
					<div class="col-xs-12">
						{$curResource.description|highlight|truncate_html:450:"..."}
					</div>
				</div>
			{/if}
		</div>
	{/foreach}
{/strip}
