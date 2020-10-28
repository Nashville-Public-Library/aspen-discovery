{strip}
	<div class="related-manifestations">
		<div class="row related-manifestations-header">
			<div class="col-xs-12 result-label related-manifestations-label">
				{translate text="Choose a Format"}
			</div>
		</div>
		{assign var=hasHiddenFormats value=false}
		{foreach from=$relatedManifestations item=relatedManifestation}
			{if $relatedManifestation->hasHiddenFormats()}
				{assign var=hasHiddenFormats value=true}
			{/if}
			{* Display the manifestation (the format being displayed) *}
			<div class="row related-manifestation {if $relatedManifestation->isHideByDefault()}hiddenManifestation_{$summId}{/if}" {if $relatedManifestation->isHideByDefault()}style="display: none"{/if}>
				{* Display inforamtion about the format *}
				{if $relatedManifestation->getNumVariations() == 1}
					{include file="GroupedWork/singleVariationManifestion.tpl" workId=$workId}
				{else}
					{include file="GroupedWork/multipleVariationManifestion.tpl" workId=$workId}
				{/if}
			</div>
		{foreachelse}
			<div class="row related-manifestation">
				<div class="col-sm-12">
					{translate text="no_copies_statement" defaultText="The library does not own any copies of this title."}
				</div>
			</div>
		{/foreach}
		{if $hasHiddenFormats}
			<div class="row related-manifestation" id="formatToggle_{$workId}">
				<div class="col-sm-12">
					<a href="#" onclick="$('.hiddenManifestation_{$workId}').show();$('#formatToggle_{$workId}').hide();return false;">{translate text="View all Formats"}</a>
				</div>
			</div>
		{/if}
	</div>
{/strip}