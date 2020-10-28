<div id="main-content">
	{if !empty($profile->_web_note)}
		<div class="row">
			<div id="web_note" class="alert alert-info text-center col-xs-12">{$profile->_web_note}</div>
		</div>
	{/if}

	<span class='availableHoldsNoticePlaceHolder'></span>

	<h1>{translate text='Messaging Settings'}</h1>

	{* MDN 7/26/2019 Do not allow access for linked users *}
	{*	{include file="MyAccount/switch-linked-user-form.tpl" label="Viewing Requests for" actionPath="/MyAccount/ReadingHistory"}*}

	{if !empty($result)}
		{if ($result.success)}
			<div class="alert alert-info">{$result.message}</div>
		{else}
			<div class="alert alert-danger">{$result.message}</div>
		{/if}
	{/if}
	<form method="post" action="/MyAccount/MessagingSettings" name="kohaMessaging">
		<input type="hidden" name="modify" value="yes">

		<table class="table table-bordered table-condensed table-striped" id="messagingTable">
			<thead>
			<tr>
				<th>&nbsp;</th>
				<th>Days in advance</th>
				{if $enableSmsMessaging}
				<th>SMS</th>
				{/if}
				{if $enablePhoneMessaging}
				<th>Phone</th>
				{/if}
				<th>Email</th>
				<th>Digests only <i id="info_digests" data-toggle="tooltip" title="" data-placement="right" class="fa fa-info-circle" data-original-title="You can ask for a digest to reduce the number of messages. Messages will be saved and sent as a single message."></i></th>
				{if $canSave}
				<!-- <th>RSS</th> --><th>Do not notify</th>
				{/if}
			</tr>
			</thead>
			<tbody>

			{foreach from=$messageAttributes item=messageType}
				{assign var=messageTypeId value=$messageType.message_attribute_id}
				<tr id="messageType{$messageTypeId}Row">
					<td>
						{$messageType.label}
					</td>
					<td>
						{if ($messageType.takes_days)}
							{if $canSave}
								<select class="form-control-sm" name="{$messageTypeId}-DAYS" aria-label="Number of days for {$messageType.label}">
									{foreach from=$validNoticeDays item="i"}
										<option value="{$i}" {if $messagingSettings.$messageTypeId.daysInAdvance == $i}selected="selected"{/if}>{$i}</option>
									{/foreach}
								</select>
							{else}
								{$messagingSettings.$messageTypeId.daysInAdvance}
							{/if}
						{else}
							-
						{/if}
					</td>
					{if $enableSmsMessaging}
						<td>
						{if $messagingSettings.$messageTypeId.allowableTransports.sms}
							{if $canSave}
								<input type="checkbox" id="sms{$messageTypeId}" name="{$messageTypeId}[]" value="sms" aria-label="Send SMS Message for {$messageType.label}" onclick="$('#none{$messageTypeId}').attr('checked', false)" {if $messagingSettings.$messageTypeId.selectedTransports.sms}checked="checked"{/if}>
							{else}
								{if $messagingSettings.$messageTypeId.selectedTransports.sms}{translate text="Yes"}{else}{translate text="No"}{/if}
							{/if}
						{else}
							-
						{/if}
						</td>
					{/if}
					{if $enablePhoneMessaging}
						<td>
						{if $messagingSettings.$messageTypeId.allowableTransports.phone}
							{if $canSave}
								<input type="checkbox" id="phone{$messageTypeId}" name="{$messageTypeId}[]" value="phone" aria-label="Receive Phone Call for {$messageType.label}" onclick="$('#none{$messageTypeId}').attr('checked', false)" {if $messagingSettings.$messageTypeId.selectedTransports.phone}checked="checked"{/if}>
							{else}
								{if $messagingSettings.$messageTypeId.selectedTransports.phone}{translate text="Yes"}{else}{translate text="No"}{/if}
							{/if}
						{else}
							-
						{/if}
						</td>
					{/if}
					<td>
					{if $messagingSettings.$messageTypeId.allowableTransports.email}
						{if $canSave}
							<input type="checkbox" id="email{$messageTypeId}" name="{$messageTypeId}[]" value="email" aria-label="Send Email for {$messageType.label}" onclick="$('#none{$messageTypeId}').attr('checked', false)" {if $messagingSettings.$messageTypeId.selectedTransports.email}checked="checked"{/if}>
						{else}
							{if $messagingSettings.$messageTypeId.selectedTransports.email}{translate text="Yes"}{else}{translate text="No"}{/if}
						{/if}
					{else}
						-
					{/if}
					</td>
					<td>
						{if $messagingSettings.$messageTypeId.allowDigests}
							{if $canSave}
								<input type="checkbox" id="digest{$messageTypeId}" value="{$messageTypeId}" name="digest" aria-label="Send Message for {$messageType.label} as digest" {if $messagingSettings.$messageTypeId.wantsDigest}checked="checked"{/if}>
							{else}
								{if  $messagingSettings.$messageTypeId.allowDigests}{translate text="Yes"}{else}{translate text="No"}{/if}
							{/if}
						{else}
							-
						{/if}
					</td>
					{if $canSave}
						<td>
							<input type="checkbox" class="none" id="none{$messageTypeId}" aria-label="Send No Message for {$messageType.label}">
						</td>
					{/if}
				</tr>
			{/foreach}

			</tbody>
		</table>

		{if $enableSmsMessaging}
			<div class="row form-group" id="smsNoticeRow">
				<div class="col-md-3">
				<label class="control-label">Notice</label>
				</div>
				<div class="col-md-9">
					Some charges for text messages may be incurred when using this service. Please check with your mobile service provider if you have questions.
				</div>
			</div>
			<div class="row form-group" id="smsNumberRow">
				<div class="col-md-3">
					<label for="SMSnumber" class="control-label">SMS number</label>
				</div>
				<div class="col-md-9">
					<input type="text" id="SMSnumber" name="SMSnumber" value="{$smsAlertNumber}" class="form-control" {if !$canSave}readonly{/if}>
					<i>Please enter numbers only. <b>(123) 456-7890</b> would be entered as <b>1234567890</b>.</i>
				</div>
			</div>
			<div class="row form-group" id="smsProviderRow">
				<div class="col-md-3">
					<label for="sms_provider_id" class="control-label">SMS provider</label>
				</div>
				<div class="col-md-9">
					<select id="sms_provider_id" name="sms_provider_id" class="form-control" {if !$canSave}readonly{/if}>
						<option value="">Unknown</option>
						{foreach from=$smsProviders item=provider key=id}
						<option value="{$id}" {if $smsProviderId==$id}selected="selected"{/if}>{$provider}</option>
						{/foreach}
					</select>
					<i>Please contact a library staff member if you are unsure of your mobile service provider, or you do not see your provider in this list.</i>
				</div>
			</div>
		{/if}
		{if $canSave}
			<button type="submit" class="btn btn-sm btn-primary" name="submit">{translate text="Update Settings"}</button>
		{/if}
	</form>
</div>
{literal}
<script type="application/javascript">
$(document).ready(function(){
	$(".none").click(function(){
		if($(this).prop("checked")){
			let rowId = $(this).attr("id");
			let newId = Number(rowId.replace("none",""));
			$("#sms"+newId).removeAttr("checked");
			$("#email"+newId).removeAttr("checked");
			$("#digest"+newId).removeAttr("checked");
			$("#rss"+newId).removeAttr("checked");
		}
	});
});
</script>
{/literal}