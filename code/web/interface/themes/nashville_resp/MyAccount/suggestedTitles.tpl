{strip}
	<div id="main-content">
		{if !empty($profile->_web_note)}
			<div class="row">
				<div id="web_note" class="alert alert-info text-center col-xs-12">{$profile->_web_note}</div>
			</div>
		{/if}

		<span class='availableHoldsNoticePlaceHolder'></span>

		<h1 class="myAccountTitle">{translate text='Recommended for you'}</h1>

		{if count($resourceList) > 0}
			<div id="pager" class="navbar form-inline">
				<label for="hideCovers" class="control-label checkbox pull-right"> {translate text='Hide Covers'} <input id="hideCovers" type="checkbox" onclick="AspenDiscovery.Account.toggleShowCovers(!$(this).is(':checked'))" {if $showCovers == false}checked="checked"{/if}></label>
			</div>
		{/if}

		<div class="striped">
			{foreach from=$resourceList item=suggestion name=recordLoop}
				{*<div class="result {if ($smarty.foreach.recordLoop.iteration % 2) == 0}alt{/if} record{$smarty.foreach.recordLoop.iteration}">*}
				<div class="result record{$smarty.foreach.recordLoop.iteration}">
					{$suggestion}
				</div>
				{foreachelse}
				<div class="alert alert-info">You have not rated any titles.  Please rate some titles so we can display suggestions for you. </div>
			{/foreach}
		</div>
	</div>
{/strip}