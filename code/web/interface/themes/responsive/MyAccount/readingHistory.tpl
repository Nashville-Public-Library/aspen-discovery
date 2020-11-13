<div class="col-xs-12">
	{if $loggedIn}

		{if !empty($profile->_web_note)}
			<div class="row">
				<div id="web_note" class="alert alert-info text-center col-xs-12">{$profile->_web_note}</div>
			</div>
		{/if}

		<span class='availableHoldsNoticePlaceHolder'></span>
		<h1>{translate text='My Reading History'} {if $historyActive == true}
				<small><a id="readingListWhatsThis" href="#" onclick="$('#readingListDisclaimer').toggle();return false;">({translate text="What's This?"})</a></small>
			{/if}
		</h1>

		{if $offline}
			<div class="alert alert-warning">{translate text=offline_notice defaultText="<strong>The library system is currently offline.</strong> We are unable to retrieve information about any titles currently checked out."}</div>
		{/if}
		{strip}
			{if $masqueradeMode && !$allowReadingHistoryDisplayInMasqueradeMode}
				<div class="row">
					<div class="alert alert-warning">
						{translate text="Display of the patron's reading history is disabled in Masquerade Mode."}
					</div>
				</div>
{* MDN 7/26/2019 Do not allow access to reading history for linked users *}
{*				{else}*}
{*					{include file="MyAccount/switch-linked-user-form.tpl" label="Viewing Reading History for" actionPath="/MyAccount/ReadingHistory"}*}
			{/if}

			<div class="row">
				<div id="readingListDisclaimer" {if $historyActive == true}style="display: none"{/if} class="alert alert-info">
					{* some necessary white space in notice was previously stripped out when needed. *}
					{/strip}
					{translate text="ReadingHistoryNotice"}
					{strip}
				</div>
			</div>

			<div id="readingHistoryListPlaceholder">
				{translate text="Loading Reading History, this may take awhile the first time."}
			</div>

			<script type="text/javascript">
				{literal}
                $(document).ready(function() {
                    AspenDiscovery.Account.loadReadingHistory({/literal}{$selectedUser}, undefined, {$page}, undefined, '{$readingHistoryFilter|escape}'{literal});
                });
				{/literal}
			</script>
		{/strip}
	{else}
		<div class="page">
			You must sign in to view this information. Click <a href="/MyAccount/Login">here</a> to sign in.
		</div>
	{/if}
</div>
