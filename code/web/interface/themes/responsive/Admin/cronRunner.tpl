{strip}
	<div id="main-content" class="col-md-12">
		<div class="row">
			<div class="col-xs-12">
				<h1 id="pageTitle">{$pageTitleShort}</h1>
			</div>
		</div>
		{if isset($results)}
			<div class="row">
				<div class="col-xs-12">
					<div class="alert {if !empty($results.success)}alert-success{else}alert-danger{/if}">
						{$results.message}
					</div>
				</div>
			</div>
		{elseif isset($error)}
			<div class="row">
				<div class="col-xs-12">
					<div class="alert alert-danger">
						{$error}
					</div>
				</div>
			</div>
		{/if}
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="alert alert-info">{translate text="This tool can be used to start a background process. Results can be checked in the Cron Log." isAdminFacing=true}</div>
		</div>
	</div>
	<form id="generateTestUsersForm" method="get" role="form">
		<div class='editor'>
			<div class="form-group">
				<label for="startingBarcode" class="control-label">{translate text='Process To Start' isPublicFacing=true}</label>
				<select name="processToRun" id="processToRun" class="form-control">
					<option value="">{translate text='Select a Process' isPublicFacing=true inAttribute=true}</option>
					{foreach from=$availableCronProcesses key=processName item=processDescription}
						<option value="{$processName}">{$processDescription}</option>
					{/foreach}
				</select>
			</div>

			<div class="form-group">
				<button type="submit" id="startProcess" name="startProcess" class="btn btn-primary">{translate text="Start Process" isAdminFacing=true}</button>
			</div>
		</div>
	</form>
{/strip}
