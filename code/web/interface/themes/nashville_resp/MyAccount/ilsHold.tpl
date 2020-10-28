{strip}
	{* Overall hold *}
	<div class="result row ilsHold_{$record.recordId|escapeCSS}_{$record.cancelId|escapeCSS}">
		{* Cover column *}
		{if $showCovers}
			<div class="col-xs-4 col-sm-3">
				<div class="{*col-xs-10 *}text-center">
					{if $record.coverUrl}
						{if $record.recordId && $record.linkUrl}
							<a href="{$record.linkUrl}" id="descriptionTrigger{$record.recordId|escape:"url"}" aria-hidden="true">
								<img src="{$record.coverUrl}" class="listResultImage img-thumbnail img-responsive" alt="{translate text='Cover Image' inAttribute=true}">
							</a>
						{else} {* Cover Image but no Record-View link *}
							<img src="{$record.coverUrl}" class="listResultImage img-thumbnail img-responsive" alt="{translate text='Cover Image' inAttribute=true}" aria-hidden="true">
						{/if}
					{/if}

				</div>
			</div>
		{/if}

		{* Details Column*}
		<div class="{if $showCovers}col-xs-8 col-sm-9{else}col-xs-12{/if}">
			{* Title *}
			<div class="row">
				<div class="col-xs-12">
					<span class="result-index">{$resultIndex})</span>&nbsp;
					{if $record.link}
						<a href="{$record.link}" class="result-title notranslate">
							{if !$record.title|removeTrailingPunctuation}{translate text='Title not available'}{else}{$record.title|removeTrailingPunctuation|truncate:180:"..."|highlight}{/if}
						</a>
					{else}
						<span class="result-title notranslate">
							{if !$record.title|removeTrailingPunctuation}{translate text='Title not available'}{else}{$record.title|removeTrailingPunctuation|truncate:180:"..."|highlight}{/if}
						</span>
					{/if}
					{if $record.title2}
						<div class="searchResultSectionInfo">
							{$record.title2|removeTrailingPunctuation|truncate:180:"..."|highlight}
						</div>
					{/if}
				</div>
			</div>

			{* 2 column row to show information and then actions*}
			<div class="row">
				{* Information column author, format, etc *}
				<div class="resultDetails col-xs-12 col-md-8 col-lg-9">
					{if $record.volume}
						<div class="row">
							<div class="result-label col-tn-4">{translate text='Volume'}</div>
							<div class="col-tn-8 result-value">
								{$record.volume}
							</div>
						</div>
					{/if}

					{if $record.author}
						<div class="row">
							<div class="result-label col-tn-4">{translate text='Author'}</div>
							<div class="col-tn-8 result-value">
								{if is_array($record.author)}
									{foreach from=$record.author item=author}
										<a href='/Author/Home?"author={$author|escape:"url"}"'>{$author|highlight}</a>
									{/foreach}
								{else}
									<a href='/Author/Home?author="{$record.author|escape:"url"}"'>{$record.author|highlight}</a>
								{/if}
							</div>
						</div>
					{/if}

					{if $record.callNumber}
						<div class="row">
							<div class="result-label col-tn-4">{translate text='Call Number'}</div>
							<div class="col-tn-8 result-value">
								{$record.callNumber}
							</div>
						</div>
					{/if}

					{if $record.format}
						<div class="row">
							<div class="result-label col-tn-4">{translate text='Format'}</div>
							<div class="col-tn-8 result-value">
								{implode subject=$record.format glue=", " translate=true}
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

					<div class="row">
						<div class="result-label col-tn-4">{translate text='Pickup Location'}</div>
						<div class="col-tn-8 result-value">
							{$record.location}
						</div>
					</div>

					{if $showPlacedColumn && $record.create}
						<div class="row">
							<div class="result-label col-tn-4">{translate text='Date Placed'}</div>
							<div class="col-tn-8 result-value">
								{$record.create|date_format:"%b %d, %Y"}
							</div>
						</div>
					{/if}

					{if $section == 'available'}
						{* Available Hold *}
						<div class="row">
							<div class="result-label col-tn-4">{translate text='Available'}</div>
							<div class="col-tn-8 result-value">
								{if $record.availableTime}
									{$record.availableTime|date_format:"%b %d, %Y at %l:%M %p"}
								{else}
									{if strcasecmp($record.status, 'Hold Being Shelved') === 0}
										<strong>{$record.status|translate}</strong>
									{else}
										{translate text=Now}
									{/if}
								{/if}
							</div>
						</div>

						{if $record.expire}
							<div class="row">
								<div class="result-label col-tn-4">{translate text='Pickup By'}</div>
								<div class="col-tn-8 result-value">
									<strong>{$record.expire|date_format:"%b %d, %Y"}</strong>
								</div>
							</div>
						{/if}
					{else}
						{* Unavailable hold *}
						<div class="row">
							<div class="result-label col-tn-4">{translate text='Status'}</div>
							<div class="col-tn-8 result-value">
								{if $record.frozen}
									<span class="frozenHold">
								{/if}
								{$record.status|translate}
								{if $record.frozen && $showDateWhenSuspending && !empty($record.reactivate)} until {$record.reactivate|date_format:"%b %d, %Y"}</span>{/if}
							</div>
						</div>

						{if $showPosition && $record.position}
							<div class="row">
								<div class="result-label col-tn-4">{translate text='Position'}</div>
								<div class="col-tn-8 result-value">
									{$record.position}
								</div>
							</div>
						{/if}

						{if !empty($record.automaticCancellation) && $showHoldCancelDate}
							<div class="row">
								<div class="result-label col-tn-4">{translate text='Cancels on'}</div>
								<div class="col-tn-8 result-value">
									{$record.automaticCancellation|date_format:"%b %d, %Y"}
								</div>
							</div>
						{/if}
					{/if}
				</div>

				{* Actions for Title *}
				<div class="col-xs-9 col-sm-8 col-md-4 col-lg-3">
					<div class="btn-group btn-group-vertical btn-block">
						{if $section == 'available'}
							{if $record.cancelable}
								{* First step in cancelling a hold is now fetching confirmation message, with better labeled buttons. *}
								<button onclick="return AspenDiscovery.Account.confirmCancelHold('{$record.userId}', '{$record.id}', '{$record.cancelId}');" class="btn btn-sm btn-warning">{translate text="Cancel Hold"}</button>
							{/if}
						{else}
							{if $record.cancelable}
								{* First step in cancelling a hold is now fetching confirmation message, with better labeled buttons. *}
								<button onclick="return AspenDiscovery.Account.confirmCancelHold('{$record.userId}', '{$record.id}', '{$record.cancelId}');" class="btn btn-sm btn-warning">{translate text="Cancel Hold"}</button>
							{/if}
							{if $record.allowFreezeHolds}
								{if $record.frozen}
									<button onclick="return AspenDiscovery.Account.thawHold('{$record.userId}', '{$record.id}', '{$record.cancelId}', this);" class="btn btn-sm btn-default">{translate text="Thaw Hold"}</button>
								{elseif $record.canFreeze}
									<button onclick="return AspenDiscovery.Account.freezeHold('{$record.userId}', '{$record.id}', '{$record.cancelId}', {if $suspendRequiresReactivationDate}true{else}false{/if}, this);" class="btn btn-sm btn-default">{translate text="Freeze Hold"}</button>
								{/if}
							{/if}
							{if $record.locationUpdateable}
								<button onclick="return AspenDiscovery.Account.changeHoldPickupLocation('{$record.userId}', '{$record.id}', '{$record.cancelId}', '{$record.currentPickupId}');" class="btn btn-sm btn-default">{translate text="Change Pickup Loc."}</button>
							{/if}
						{/if}
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