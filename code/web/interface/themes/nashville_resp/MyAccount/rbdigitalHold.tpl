{strip}
	<div class="result row" id="rbdigitalHold_{$record.id}">
		{* Cover column *}
		{if $showCovers}
		<div class="col-xs-4 col-sm-3">
			{*<div class="row">*}
				<div class="{*col-xs-10 *}text-center">
					{if $record.coverUrl}
						{if $record.transactionId && $record.linkUrl}
							<a href="{$record.linkUrl}" id="descriptionTrigger{$record.transactionId|escape:"url"}" aria-hidden="true">
								<img src="{$record.coverUrl}" class="listResultImage img-thumbnail img-responsive" alt="{translate text='Cover Image' inAttribute=true}">
							</a>
						{else} {* Cover Image but no Record-View link *}
							<img src="{$record.coverUrl}" class="listResultImage img-thumbnail img-responsive" alt="{translate text='Cover Image' inAttribute=true}" aria-hidden="true">
						{/if}
					{/if}
				</div>
			{*</div>*}
		</div>

		{/if}
		{* Details Column*}
		<div class="{if $showCovers}col-xs-8 col-sm-9{else}col-xs-12{/if}">
			{* Title *}
			<div class="row">
				<div class="col-xs-12">
					<span class="result-index">{$resultIndex})</span>&nbsp;
					{if $record.linkUrl}
						<a href="{$record.linkUrl}" class="result-title notranslate">
							{*{if !$record.title}{translate text='Title not available'}{else}{$record.title|removeTrailingPunctuation}{/if}*}
							{if !$record.title|removeTrailingPunctuation}{translate text='Title not available'}{else}{$record.title|removeTrailingPunctuation|truncate:180:"..."|highlight}{/if}
						</a>
					{else}
						<span class="result-title notranslate">
							{if !$record.title|removeTrailingPunctuation}{translate text='Title not available'}{else}{$record.title|removeTrailingPunctuation|truncate:180:"..."|highlight}{/if}
						</span>
					{/if}
				</div>
			</div>

			<div class="row">
				<div class="resultDetails col-xs-12 col-md-8 col-lg-9">
					{if $record.author}
						<div class="row">
							<div class="result-label col-tn-4">{translate text='Author'}</div>
							<div class="col-tn-8 result-value">
								<a href='/Author/Home?author="{$record.author|escape:"url"}"'>{$record.author|highlight}</a>
							</div>
						</div>
					{/if}

					<div class="row">
						<div class="result-label col-tn-4">{translate text='Source'}</div>
						<div class="col-tn-8 result-value">
							RBdigital
						</div>
					</div>

					{if $record.format}
						<div class="row">
							<div class="result-label col-tn-4">{translate text='Format'}</div>
						<div class="col-tn-8 result-value">
								{implode subject=$record.format glue=", "}
							</div>
						</div>
					{/if}

					{if $hasLinkedUsers}
					<div class="row">
						<div class="result-label col-tn-4">{translate text='On Hold For'}</div>
						<div class="col-tn-8 result-value">
							{$record.user}
						</div>
					</div>
					{/if}
				</div>

				{* Actions for Title *}
				<div class="col-xs-9 col-sm-8 col-md-4 col-lg-3">
					<div class="btn-group btn-group-vertical btn-block">
						<button onclick="return AspenDiscovery.RBdigital.cancelHold('{$record.userId}', '{$record.id}');" class="btn btn-sm btn-warning">Cancel Hold</button>
					</div>
					{if $showWhileYouWait}
						<div class="btn-group btn-group-vertical btn-block">
							{if !empty($record.groupedWorkId)}
								<button onclick="return AspenDiscovery.GroupedWork.getWhileYouWait('{$record.groupedWorkId}');" class="btn btn-sm btn-default btn-wrap">{translate text="While You Wait"}</button>
							{/if}
						</div>
					{/if}
				</div>
			</div>
		</div>
	</div>
{/strip}