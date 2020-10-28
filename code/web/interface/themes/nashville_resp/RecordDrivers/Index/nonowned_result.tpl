<div class="resultsList">
	<div class="row">
		{if $showCovers}
			<div class="coversColumn col-xs-3 col-sm-3{if !empty($viewingCombinedResults)} col-md-3 col-lg-2{/if} text-center">
				<img src="/bookcover.php?isn={$record.isbn|@formatISBN}&amp;issn={$record.issn}&amp;size=medium&amp;upc={$record.upc}" class="listResultImage img-thumbnail img-responsive" alt="{translate text='Cover Image' inAttribute=true}"/>
			</div>
		{/if}

		<div class="{if !$showCovers}col-xs-12{else}col-xs-9 col-sm-9{if !empty($viewingCombinedResults)} col-md-9 col-lg-10{/if}{/if}">{* May turn out to be more than one situation to consider here *}
			<div class="row">
				<div class="col-xs-12">
					<span class="result-index">{$resultIndex})</span>&nbsp;
					<span class="result-title notranslate">
					{if !$record.title|removeTrailingPunctuation}{translate text='Title not available'}{else}{$record.title|removeTrailingPunctuation|truncate:180:"..."|highlight}{/if}
					{if $record.volume}
						, {$record.series} {$record.volume}&nbsp;
					{/if}
					</span>
				</div>
			</div>

			{if $record.author}
				<div class="row">
					<div class="result-label col-md-3">{translate text="Author"} </div>
					<div class="col-md-9 result-value  notranslate">
						{if is_array($record.author)}
							{foreach from=$summAuthor item=author}
								<a href='/Author/Home?author="{$author|escape:"url"}"'>{$author|highlight}</a>
							{/foreach}
						{else}
							<a href='/Author/Home?author="{$record.author|escape:"url"}"'>{$record.author|highlight}</a>
						{/if}
					</div>
				</div>
			{/if}

			{if $record.publicationDate}
				<div class="row">
					<div class="result-label col-md-3">Published: </div>
					<div class="col-md-9 result-value">{$record.publicationDate|escape}</div>
				</div>
			{/if}

			<div class="row related-manifestations-header">
				<div class="col-xs-12 result-label related-manifestations-label">
					{translate text="Choose a Format"}
				</div>
			</div>
			<div class="row related-manifestation">
				<div class="col-sm-12">
					The library does not own any copies of this title.
				</div>
			</div>
		</div>
	</div>
</div>