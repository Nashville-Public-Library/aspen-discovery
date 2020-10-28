{strip}
	<h1>{translate text="My Lists"}</h1>
	{if empty($lists)}
		<div class="alert alert-info">
			{translate text="You have not created any lists yet."}
		</div>
	{else}
		{foreach from=$lists item="list" key="resultIndex"}
			<div class="row">
				<div class="coversColumn col-xs-3 col-sm-3 col-md-3 col-lg-2 text-center">
					{if $disableCoverArt != 1}
						<a href="/MyAccount/MyList/{$list->id}" class="alignleft listResultImage" aria-hidden="true">
							<img src="/bookcover.php?type=list&amp;id={$list->id}&amp;size=medium" class="listResultImage img-thumbnail" alt="{translate text='Cover Image' inAttribute=true}">
						</a>
					{/if}
				</div>

				<div class="{if !$showCovers}col-xs-12{else}col-xs-9 col-sm-9 col-md-9 col-lg-10{/if}">{* May turn out to be more than one situation to consider here *}
					{* Title Row *}

					<div class="row">
						<div class="col-xs-12">
							<span class="result-index">{$resultIndex+1})</span>&nbsp;
							<a href="/MyAccount/MyList/{$list->id}" class="result-title notranslate">
								{$list->title}
							</a>
						</div>
					</div>

					<div class="row">
						<div class="result-label col-tn-3">{translate text="Number of Titles"} </div>
						<div class="result-value col-tn-9 notranslate">
							{translate text="%1% titles are in this list." 1=$list->numValidListItems()}
						</div>
					</div>

					{* Description Section *}
					{if $list->description}
						<div class="row visible-xs">
							<div class="result-label col-tn-3 col-xs-3">{translate text="Description"}</div>
							<div class="result-value col-tn-9 col-xs-9"><a id="descriptionLink{$list->id|escape}" href="#" onclick="$('#descriptionValue{$list->id|escape},#descriptionLink{$list->id|escape}').toggleClass('hidden-xs');return false;">Click to view</a></div>
						</div>

						<div class="row">
							{* Hide in mobile view *}
							<div class="result-value hidden-xs col-sm-12" id="descriptionValue{$list->id|escape}">
								{$list->description|truncate_html:450:"..."}
							</div>
						</div>
					{/if}

				</div>
			</div>
		{/foreach}
	{/if}
{/strip}