{strip}
	<div id="page-content" class="col-xs-12">

		<h1>{translate text='Reset My PIN'}</h1>
		<div class="alert alert-info">
			{translate text="Please enter a new 4 digit PIN number."}
		</div>

		<form id="resetPin" method="POST" action="/MyAccount/ResetPin" class="form-horizontal">
			{if $resetToken}
				<input type="hidden" name="resetToken" value="{$resetToken}">
			{/if}
			{if $userID}
				<input type="hidden" name="uid" value="{$userID}">
			{/if}
			<div class="form-group">
				<div class="col-xs-4"><label for="pin1" class="control-label">{translate text='New PIN'}:</label></div>
				<div class="col-xs-8">
					<input type="password" name="pin1" id="pin1" value="" minlength="{$pinValidationRules.minLength}" maxlength="{$pinValidationRules.maxLength}" class="form-control required {if $pinValidationRules.onlyDigitsAllowed}digits{/if}">
				</div>
			</div>
			<div class="form-group">
				<div class="col-xs-4"><label for="pin2" class="control-label">{translate text='Re-enter New PIN'}:</label></div>
				<div class="col-xs-8">
					<input type="password" name="pin2" id="pin2" value="" minlength="{$pinValidationRules.minLength}" maxlength="{$pinValidationRules.maxLength}" class="form-control required {if $pinValidationRules.onlyDigitsAllowed}digits{/if}">
				</div>
			</div>
			<div class="form-group">
				<div class="col-xs-8 col-xs-offset-4">
					<input id="resetPinSubmit" name="submit" class="btn btn-primary" type="submit" value="Reset My Pin">
				</div>
			</div>
		</form>
	</div>
{/strip}
<script type="text/javascript">
	{literal}
	$(function () {
		$("#resetPin").validate({
			rules: {
				pin2: {
					equalTo: "#pin1"
				}
			}
		});
	});
	{/literal}
</script>
