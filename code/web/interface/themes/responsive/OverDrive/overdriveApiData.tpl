{strip}
	<div id="main-content" class="col-md-12">
		<h1>{translate text="%1% API Data" 1=$readerName isAdminFacing=true}</h1>
		{if count($allSettings) > 1}
			<form name="selectSettings" id="selectSettings" class="form-inline row">
				<div class="form-group col-tn-12">
					<label for="settingId" class="control-label">{translate text="Instance to show stats for" isAdminFacing=true}</label>&nbsp;
					<select id="settingId" name="settingId" class="form-control input-sm" onchange="$('#selectSettings').trigger('submit')">
						{foreach from=$allSettings key=settingId item=setting}
							<option value="{$settingId}" {if $settingId == $selectedSettingId}selected{/if}>{$setting->__toString()}</option>
						{/foreach}
					</select>
				</div>
			</form>
		{/if}
		<form class="navbar form-inline row">
			<div class="form-group col-xs-12">
				<label for="overDriveId" class="control-label">{translate text="%1% ID" 1=$readerName isAdminFacing=true}</label>
				<input id ="overDriveId" type="text" name="id" class="form-control" value="{if !empty($overDriveId)}{$overDriveId}{/if}">
				<input type="hidden" name="settingId" value="{$selectedSettingId}">
				<button class="btn btn-primary" type="submit">{translate text=Go isAdminFacing=true}</button>
			</div>
		</form>
		{$overDriveAPIData}
	</div>
{/strip}