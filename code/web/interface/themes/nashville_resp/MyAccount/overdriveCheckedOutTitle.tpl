{strip}
	<div class="result row overdrive_checkout_{$record.recordId|escape}">

		{* Cover Column *}
		{if $showCovers}
			{*<div class="col-xs-4">*}
			<div class="col-xs-3 col-sm-4 col-md-3 checkedOut-covers-column">
				<div class="row">
					<div class="selectTitle hidden-xs col-sm-1">
						&nbsp;{* Can't renew overdrive titles*}
					</div>
					<div class="{*coverColumn *}text-center col-xs-12 col-sm-10">
						{if $disableCoverArt != 1}{*TODO: should become part of $showCovers *}
							{if $record.coverUrl}
								{if $record.recordId && $record.linkUrl}
									<a href="{$record.linkUrl}" id="descriptionTrigger{$record.recordId|escape:"url"}" aria-hidden="true">
										<img src="{$record.coverUrl}" class="listResultImage img-thumbnail img-responsive" alt="{translate text='Cover Image' inAttribute=true}">
									</a>
								{else} {* Cover Image but no Record-View link *}
									<img src="{$record.coverUrl}" class="listResultImage img-thumbnail img-responsive" alt="{translate text='Cover Image' inAttribute=true}" aria-hidden="true">
								{/if}
							{/if}
						{/if}
					</div>
				</div>
			</div>
		{else}
			<div class="col-xs-1">
				&nbsp;{* Can't renew overdrive titles*}
			</div>
		{/if}

		{* Title Details Column *}
		<div class="{if $showCovers}col-xs-9 col-sm-8 col-md-9{else}col-xs-11{/if}">
			{* Title *}
			<div class="row">
				<div class="col-xs-12">
					<span class="result-index">{$resultIndex})</span>&nbsp;
					{if $record.linkUrl}
						<a href="{$record.linkUrl}" class="result-title notranslate">
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
				<div class="resultDetails col-xs-12 col-md-9">
					{if strlen($record.author) > 0}
						<div class="row">
							<div class="result-label col-tn-4 col-lg-3">{translate text='Author'}</div>
							<div class="result-value col-tn-8 col-lg-9">{$record.author}</div>
						</div>
					{/if}

					{if $record.checkoutDate}
						<div class="row">
							<div class="result-label col-tn-4 col-lg-3">{translate text='Checked Out'}</div>
							<div class="result-value col-tn-8 col-lg-9">{$record.checkoutDate|date_format}</div>
						</div>
					{/if}

					<div class="row">
						<div class="result-label col-tn-4 col-lg-3">{translate text='Format'}</div>
						<div class="result-value col-tn-8 col-lg-9">{$record.format|translate} - Overdrive</div>
					</div>

					{if $showRatings && $record.groupedWorkId && $record.ratingData}
						<div class="row">
							<div class="result-label col-tn-4 col-lg-3">{translate text='Rating'}&nbsp;</div>
							<div class="result-value col-tn-8 col-lg-9">
								{include file="GroupedWork/title-rating.tpl" id=$record.groupedWorkId ratingData=$record.ratingData showNotInterested=false}
							</div>
						</div>
					{/if}

					{if $hasLinkedUsers}
						<div class="row">
							<div class="result-label col-tn-4 col-lg-3">{translate text='Checked Out To'}</div>
							<div class="result-value col-tn-8 col-lg-9">
								{$record.user}
							</div>
						</div>
					{/if}

					<div class="row">
						<div class="result-label col-tn-4 col-lg-3">{translate text='Expires'}</div>
						<div class="result-value col-tn-8 col-lg-9">{$record.dueDate|date_format}</div>
					</div>

					{if $record.allowDownload}
						<div class="row econtent-download-row">
							<div class="result-label col-md-4 col-lg-3">{translate text='Download'}</div>
							<div class="result-value col-md-8 col-lg-9">
								{if $record.formatSelected}
									{translate text="overdrive_locked_in_format" defaultText="You downloaded the <strong>%1%</strong> format of this title." 1=$record.selectedFormat.name}
								{else}
									<div class="form-inline">
										<label for="downloadFormat_{$record.overDriveId}">{translate text="Select one format to download."}</label>
										<br>
										<select name="downloadFormat_{$record.overDriveId}" id="downloadFormat_{$record.overDriveId}_{$smarty.now}" class="input-sm form-control">
											<option value="-1">{translate text="Select a Format"}</option>
											{foreach from=$record.formats item=format}
												<option value="{$format.id}">{$format.name|translate}</option>
											{/foreach}
										</select>
										<a href="#" onclick="AspenDiscovery.OverDrive.selectOverDriveDownloadFormat('{$record.userId}', '{$record.overDriveId}', '{$smarty.now}')" class="btn btn-sm btn-primary">{translate text="Download"}</a>
									</div>
								{/if}
							</div>
						</div>
					{/if}
				</div>

				{* Actions for Title *}
				<div class="col-xs-9 col-sm-8 col-md-4 col-lg-3">
					<div class="btn-group btn-group-vertical btn-block">
						{if $record.overdriveRead}
							<a href="#" onclick="return AspenDiscovery.OverDrive.followOverDriveDownloadLink('{$record.userId}', '{$record.overDriveId}', 'ebook-overdrive')" class="btn btn-sm btn-action">{translate text="Read&nbsp;Online"}</a>
						{/if}
						{if $record.overdriveListen}
							<a href="#" onclick="return AspenDiscovery.OverDrive.followOverDriveDownloadLink('{$record.userId}', '{$record.overDriveId}', 'audiobook-overdrive')" class="btn btn-sm btn-action">{translate text="Listen&nbsp;Online"}</a>
						{/if}
						{if !empty($record.overdriveVideo)}
							<a href="#" onclick="return AspenDiscovery.OverDrive.followOverDriveDownloadLink('{$record.userId}', '{$record.overDriveId}', 'video-streaming')" class="btn btn-sm btn-action">{translate text="Watch&nbsp;Online"}</a>
						{/if}
						{if $record.overdriveMagazine}
							<a href="#" onclick="return AspenDiscovery.OverDrive.followOverDriveDownloadLink('{$record.userId}', '{$record.overDriveId}', 'magazine-overdrive')" class="btn btn-sm btn-action">{translate text="Read&nbsp;Online"}</a>
						{/if}
						{if $record.formatSelected && empty($record.overdriveVideo)}
							<a href="#" onclick="return AspenDiscovery.OverDrive.followOverDriveDownloadLink('{$record.userId}', '{$record.overDriveId}', '{$record.selectedFormat.format}')" class="btn btn-sm btn-action">{translate text="Download&nbsp;Again"}</a>
						{/if}
						{if !empty($record.supplementalMaterials)}
							{foreach from=$record.supplementalMaterials item=supplement}
								<a href="#" onclick="return AspenDiscovery.OverDrive.followOverDriveDownloadLink('{$record.userId}', '{$supplement.overDriveId}', '{$supplement.selectedFormat.format}')" class="btn btn-sm btn-default btn-wrap">{translate text="Download Supplemental %1%" 1=$supplement.selectedFormat.name}</a>
							{/foreach}
						{/if}
						{if $record.canRenew}
							<a href="#" onclick="return AspenDiscovery.OverDrive.renewCheckout('{$record.userId}', '{$record.overDriveId}');" class="btn btn-sm btn-info">{translate text='Renew Checkout'}</a>
						{/if}
						{if $record.earlyReturn}
							<a href="#" onclick="return AspenDiscovery.OverDrive.returnCheckout('{$record.userId}', '{$record.overDriveId}');" class="btn btn-sm btn-warning">{translate text="Return&nbsp;Now"}</a>
						{/if}
					</div>
					{if $showWhileYouWait}
						<div class="btn-group btn-group-vertical btn-block">
							{if !empty($record.groupedWorkId)}
								<button onclick="return AspenDiscovery.GroupedWork.getYouMightAlsoLike('{$record.groupedWorkId}');" class="btn btn-sm btn-default btn-wrap">{translate text="You Might Also Like"}</button>
							{/if}
						</div>
					{/if}
				</div>
			</div>
		</div>
	</div>
{/strip}