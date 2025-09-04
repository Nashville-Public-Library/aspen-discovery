{strip}
	<div class="donationFields" style="padding-top: 1em">
		{foreach from=$donationFormFields key=category item=formFields name=categories}
			<fieldset class="row" style="margin-top: .5em" id="{$smarty.foreach.categories.index}">
				<legend>{translate text=$category isPublicFacing=true}</legend>
				{foreach from=$formFields item=formField}
					{* DONATION INFORMATION *}

					<div id="{$formField->textId}Row">
					{* Donation Value Options *}
					{if $formField->textId == 'valueList'}
						<div class="col-md-12">
							<div class="btn-group btn-group-justified" data-toggle="buttons">
								{foreach from=$donationValues item=value key=valueKey}
										<label class="btn btn-default btn-lg predefinedAmount bold">
											<input type="radio" id="amount{$value}" class="predefinedAmount" name="predefinedAmount" value="{$value}" onchange="return AspenDiscovery.Account.getDonationValuesForDisplay();"> {$currencySymbol}{$value}
										</label>
								{/foreach}
							</div>
						</div>
						<div class="col-md-12" style="padding-top:.5em; padding-bottom: 2em">
							<label class="sr-only" for="amount">{translate text='Other amount' isPublicFacing=true}</label>
							<div class="input-group input-group-lg">
								<div class="input-group-addon" >{$currencySymbol}</div>
								<input type="number" step="0.01" class="form-control" name="customAmount" id="customAmount" placeholder="{translate text='Other amount' isPublicFacing=true inAttribute=true}" onchange="return AspenDiscovery.Account.getDonationValuesForDisplay();">
							</div>
						</div>
					{* Donation Earmark *}
					{elseif $formField->textId == 'earmarkList'}
						{if $allowDonationEarmark == 1}
						<div class="col-xs-12">
						<div class="form-group {$formField->textId}">
							<label id="{$formField->textId}Label" for="{$formField->textId}" class="control-label">{translate text=$formField->label isPublicFacing=true isAdminEnteredData=true}{if $formField->required}<span class="requiredIndicator">*</span>{/if}</label>
							<select name="earmark" id="{$formField->textId}" class="form-control input-lg">
								<option value="null" selected></option>
								{foreach from=$donationEarmarks item=value key=earmarkKey}
									<option value={$value}>{$earmarkKey}</option>
								{/foreach}
							</select>
						</div>
						</div>
						{/if}

					{* Donation to Specific Location *}
					{elseif $formField->textId == 'locationList'}
						{if $allowDonationsToBranch == 1}
						<div class="col-xs-12">
						<div class="form-group {$formField->textId}">
							<label id="{$formField->textId}Label" for="{$formField->textId}" class="control-label">{translate text=$formField->label isPublicFacing=true isAdminEnteredData=true}{if $formField->required}<span class="requiredIndicator">*</span>{/if}</label>
							<select name="toLocation" id="{$formField->textId}" class="form-control input-lg">
								<option value=0 selected></option>
								{foreach from=$donationLocations item=value key=locationKey}
									<option value="{$value|escape}">{$locationKey|escape}</option>
								{/foreach}
							</select>
						</div>
						</div>
						{/if}

					{* Donation Dedication *}
					{elseif $formField->textId == 'shouldBeDedicated'}
						{if $allowDonationDedication == 1}
						<div class="col-xs-12">
						<div class="checkbox">
							<label id="{$formField->textId}Label" for="{$formField->textId}" class="control-label">
								<input type="checkbox" name="{$formField->textId}" id="{$formField->textId}">
								{translate text=$formField->label isPublicFacing=true isAdminEnteredData=true}
							</label>
						</div>
						</div>
						{/if}

					{elseif $formField->textId == 'dedicationType'}
						{if $allowDonationDedication == 1}
						<div class="col-xs-12">
						<div class="form-group {$formField->textId}">
							{foreach from=$donationDedications item=value key=dedicationKey}
								<div class="radio-inline">
									<label class="control-label">
										<input type="radio" name="{$formField->textId}" id="{$formField->textId}-{$value}" value="{$value}">
										{$dedicationKey}
									</label>
								</div>
							{/foreach}
						</div>
						</div>
						{/if}

					{elseif $formField->textId == 'honoreeFirstName' || $formField->textId == 'honoreeLastName'}
						{if $allowDonationDedication == 1}
						<div class="col-xs-6">
						<div class="form-group {$formField->textId}">
							<label id="{$formField->textId}Label" for="{$formField->textId}" class="control-label">{translate text=$formField->label isPublicFacing=true isAdminEnteredData=true}</label>
							<input type="text" name="{$formField->textId}" id="{$formField->textId}" class="form-control input-lg">
						</div>
						</div>
						{/if}

					{elseif $formField->textId == 'shouldBeNotified'}
						{if $allowDonationDedication == 1}
							<div class="col-xs-12">
							<div class="checkbox">
								<label id="{$formField->textId}Label" for="{$formField->textId}" class="control-label">
									<input type="checkbox" name="{$formField->textId}" id="{$formField->textId}">
									{translate text=$formField->label isPublicFacing=true isAdminEnteredData=true}
								</label>
							</div>
							</div>
						{/if}

					{elseif $formField->textId == 'notificationFirstName' || $formField->textId == 'notificationLastName'}
						{if $allowDonationDedication == 1}
						<div class="col-xs-6">
						<div class="form-group {$formField->textId}">
							<label id="{$formField->textId}Label" for="{$formField->textId}" class="control-label">{translate text=$formField->label isPublicFacing=true isAdminEnteredData=true}</label>
							<input type="text" name="{$formField->textId}" id="{$formField->textId}" class="form-control input-lg">
						</div>
						</div>
						{/if}

					{elseif $formField->textId == 'notificationAddress' || $formField->textId == 'notificationCity' || $formField->textId == 'notificationState' || $formField->textId == 'notificationZip'}
						{if $allowDonationDedication == 1}
						<div class="{if $formField->textId == 'notificationAddress'}col-md-7{elseif $formField->textId == 'notificationState'}col-md-1{else}col-md-2{/if}">
						<div class="form-group {$formField->textId}">
							<label id="{$formField->textId}Label" for="{$formField->textId}" class="control-label">{translate text=$formField->label isPublicFacing=true isAdminEnteredData=true}</label>
							<input type="text" name="{$formField->textId}" id="{$formField->textId}" class="form-control input-lg">
						</div>
						</div>
						{/if}

					{* USER INFORMATION *}
					{elseif $formField->textId == 'firstName' || $formField->textId == 'lastName'}
						<div class="col-xs-6">
						<div class="form-group {$formField->textId}">
							<label id="{$formField->textId}Label" for="{$formField->textId}" class="control-label">{translate text=$formField->label isPublicFacing=true isAdminEnteredData=true}</label>
							<input type="text" name="{$formField->textId}" id="{$formField->textId}" class="form-control input-lg" {if $formField->textId == 'firstName' && $newDonation->firstName}value="{$newDonation->firstName|escape}"{/if}{if $formField->textId == 'lastName' && $newDonation->lastName}value="{$newDonation->lastName|escape}"{/if} autocomplete>
						</div>
						</div>

					{elseif $formField->textId == 'makeAnonymous'}
						<div class="col-xs-12">
						<div class="checkbox">
							<label id="{$formField->textId}Label" for="{$formField->textId}" class="control-label">
								<input type="checkbox" name="{$formField->textId}" id="{$formField->textId}">
								{translate text=$formField->label isPublicFacing=true isAdminEnteredData=true}
							</label>
						</div>
						</div>

					{elseif $formField->textId == 'emailAddress'}
						<div class="col-xs-12">
						<div class="form-group {$formField->textId}">
							<label id="{$formField->textId}Label" class="control-label" for="{$formField->textId}">{translate text=$formField->label isPublicFacing=true isAdminEnteredData=true}</label>
							<input type="email" name="{$formField->textId}" id="{$formField->textId}" class="form-control input-lg" value="{if $newDonation->email}{$newDonation->email}{/if}" autocomplete>
							{if $formField->note}<span id="{$formField->textId}_helpBlock" class="help-block">{$formField->note}</span>{/if}
						</div>
						</div>

					{elseif $formField->textId == 'firstName' || $formField->textId == 'lastName'}
						<div class="col-xs-6">
							<div class="form-group {$formField->textId}">
								<label id="{$formField->textId}Label" for="{$formField->textId}" class="control-label">{translate text=$formField->label isPublicFacing=true isAdminEnteredData=true}</label>
								<input type="text" name="{$formField->textId}" id="{$formField->textId}" class="form-control input-lg" {if $formField->textId == 'firstName' && $newDonation->firstName}value="{$newDonation->firstName|escape}"{/if}{if $formField->textId == 'lastName' && $newDonation->lastName}value="{$newDonation->lastName|escape}"{/if} autocomplete>
							</div>
						</div>

					{elseif ($formField->textId == 'address' || $formField->textId == 'address2' || $formField->textId == 'city' || $formField->textId == 'state' || $formField->textId == 'zip')}
						<div class="{if $formField->textId == 'address' || $formField->textId == 'address2'}col-md-12{else}col-md-4{/if}">
							<div class="form-group {$formField->textId}">
								<label id="{$formField->textId}Label" for="{$formField->textId}"
									   class="control-label">{translate text=$formField->label isPublicFacing=true isAdminEnteredData=true}</label>
								<input type="text" name="{$formField->textId}" id="{$formField->textId}"
									   class="form-control input-lg"
									   {if $formField->textId == 'address' && $newDonation->address}value="{$newDonation->address}"{/if}
										{if $formField->textId == 'address2' && $newDonation->address2}value="{$newDonation->address2}"{/if}
										{if $formField->textId == 'city' && $newDonation->city}value="{$newDonation->city}"{/if}
										{if $formField->textId == 'state' && $newDonation->state}value="{$newDonation->state}"{/if}
										{if $formField->textId == 'zip' && $newDonation->zip}value="{$newDonation->zip}"{/if}
									   autocomplete="on">
							</div>
						</div>

							{* ADDITIONAL FIELDS *}
					{elseif $formField->type == 'text'}
						<div class="col-xs-12">
						<div class="form-group {$formField->textId}">
							<label id="{$formField->textId}Label" class="control-label" for="{$formField->textId}">{translate text=$formField->label isPublicFacing=true isAdminEnteredData=true}</label>
							<input name="{$formField->textId}" id="{$formField->textId}" class="form-control input-lg">
							{if $formField->note}<span id="{$formField->textId}_helpBlock" class="help-block">{$formField->note}</span>{/if}
						</div>
						</div>

					{elseif $formField->type == 'textbox'}
						<div class="col-xs-12">
						<div class="form-group {$formField->textId}">
							<label id="{$formField->textId}Label" class="control-label" for="{$formField->textId}">{translate text=$formField->label isPublicFacing=true isAdminEnteredData=true}</label>
							<textarea id="{$formField->textId}" class="form-control" rows="3"></textarea>
							{if $formField->note}<span id="{$formField->textId}_helpBlock" class="help-block">{$formField->note}</span>{/if}
						</div>
						</div>

					{/if}
					</div>
				{/foreach}
			</fieldset>
		{/foreach}
		{* Make Sure Id is always included when set, even if it isn't displayed *}
		{if empty($hasId) && !empty($newDonation->id)}
			<input type="hidden" name="id" id="id" value="{$newDonation->id}">
		{/if}
		{if $newDonation->donationSettingId}
			<input type="hidden" name="settingId" id="settingId" value="{$newDonation->donationSettingId}">
		{/if}
	</div>
{/strip}
{literal}
<script type="text/javascript">

	$("#shouldBeDedicated").on('change', function() {
		if ($(this).is(':checked')) {
			$('#1').show();
		} else {
			$('#1').hide();
			$('#2').hide();
		}
	});
	$("#shouldBeDedicated").trigger("change");

	$("#shouldBeNotified").on('change', function() {
		if ($(this).is(':checked')) {
			$('#2').show();
		} else {
			$('#2').hide();
		}
	});
	$("#shouldBeNotified").trigger("change");

	$('#customAmount').on('click', function () {
		$('.btn.btn-default.btn-lg.predefinedAmount').removeClass("active");
		$('input[name="amount"]').attr("checked", false);
	});

</script>
{/literal}
