{strip}
	<div id="main-content">
		{if $loggedIn}
			{if !empty($profile->_web_note)}
				<div class="row">
					<div id="web_note" class="alert alert-info text-center col-xs-12">{$profile->_web_note}</div>
				</div>
			{/if}

			<span class='availableHoldsNoticePlaceHolder'></span>

			<h1>{translate text='Staff Settings'}</h1>
			{if $offline}
				<div class="alert alert-warning">{translate text=offline_notice defaultText="<strong>The library system is currently offline.</strong> We are unable to retrieve information about your account at this time."}</div>
			{else}
{* MDN 7/26/2019 Do not allow access for linked users *}
{*				{include file="MyAccount/switch-linked-user-form.tpl" label="View Account Settings for" actionPath="/MyAccount/StaffSettings"}*}

				{* Display user roles if the user has any roles*}
				{if count($profile->roles) > 0}
					<div class="row">
						<div class="col-tn-12 lead">{translate text="Roles"}</div>
					</div>
					<div class="row">
						<div class="col-tn-12">
							<ul>
								{foreach from=$profile->roles item=role}
									<li>{$role->name} - {$role->description}</li>
								{/foreach}
							</ul>
						</div>
					</div>
				{/if}

				<form action="" method="post" class="form-horizontal" id="staffSettingsForm">
					<input type="hidden" name="updateScope" value="staffSettings">

					{if $userIsStaff}
						<div class="row">
							<div class="col-tn-12 lead">{translate text="General"}</div>
						</div>
						<div class="form-group row">
							<div class="col-xs-4"><label for="bypassAutoLogout" class="control-label">{translate text='Bypass Automatic Logout'}</label></div>
							<div class="col-xs-8">
								{if $edit == true}
									<input type="checkbox" name="bypassAutoLogout" id="bypassAutoLogout" {if $profile->bypassAutoLogout==1}checked='checked'{/if} data-switch="">
								{else}
									{if $profile->bypassAutoLogout==0}{translate text="No"}{else}{translate text="Yes"}{/if}
								{/if}
							</div>
						</div>
					{/if}

					{if $profile->hasRole('library_material_requests') && ($materialRequestType == 1)}
						<div class="row">
							<div class="lead col-tn-12">{translate text="Materials Request Management"}</div>
						</div>
						<div class="form-group row">
							<div class="col-xs-4">
								<label for="materialsRequestReplyToAddress" class="control-label">{translate text="Reply-To Email Address"}</label>
							</div>
							<div class="col-xs-8">
								{if $edit == true}
									<input type="text" id="materialsRequestReplyToAddress" name="materialsRequestReplyToAddress" class="form-control multiemail" value="{$user->materialsRequestReplyToAddress}">
								{else}
									{$user->materialsRequestReplyToAddress}
								{/if}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-xs-4">
								<label for="materialsRequestEmailSignature" class="control-label">{translate text="Email Signature"}</label>
							</div>
							<div class="col-xs-8">
								{if $edit == true}
									<textarea id="materialsRequestEmailSignature" name="materialsRequestEmailSignature" class="form-control">{$user->materialsRequestEmailSignature}</textarea>
								{else}
									{$user->materialsRequestEmailSignature}
								{/if}
							</div>
						</div>
					{/if}


					{if !$offline && $edit == true}
						<div class="form-group">
							<div class="col-xs-8 col-xs-offset-4">
								<button type="submit" name="updateStaffSettings" class="btn btn-sm btn-primary">{translate text="Update Settings"}</button>
							</div>
						</div>
					{/if}
				</form>

				<script type="text/javascript">
					{* Initiate any checkbox with a data attribute set to data-switch=""  as a bootstrap switch *}
					{literal}
					$(function(){ $('input[type="checkbox"][data-switch]').bootstrapSwitch()});
					{/literal}
				</script>
			{/if}
		{else}
			<div class="page">
				You must sign in to view this information. Click <a href="/MyAccount/Login">here</a> to sign in.
			</div>
		{/if}
	</div>
{/strip}
