{strip}
	<div id="main-content" class="col-md-12">
		<h1>{translate text='Cron Log' isAdminFacing=true}</h1>

		<form>
			<div class="row">
				<div class="col-sm-5 col-md-4">
					<div class="form-group">
						<label for="pageSize">{translate text='Entries Per Page' isAdminFacing=true}</label>
						<select id="pageSize" name="pageSize" class="pageSize form-control input-sm">
							<option value="30"{if $recordsPerPage == 30} selected="selected"{/if}>30</option>
							<option value="50"{if $recordsPerPage == 50} selected="selected"{/if}>50</option>
							<option value="75"{if $recordsPerPage == 75} selected="selected"{/if}>75</option>
							<option value="100"{if $recordsPerPage == 100} selected="selected"{/if}>100</option>
						</select>
					</div>
				</div>
				<div class="col-sm-4 col-md-4 col-lg-3">
					<div class="form-group">
						<label for="cronNamesToShow">{translate text='Show names containing' isAdminFacing=true}</label>
						<div class="input-group-sm input-group">
							<input id="cronNamesToShow" name="cronNamesToShow" class="form-control input-sm" {if !empty($cronNamesToShow)} value="{$cronNamesToShow}"{/if}>
						</div>
					</div>
				</div>
				<div class="col-sm-3 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="showErrorsOnly">{translate text='Show Errors Only' isAdminFacing=true}</label>
						<div class="input-group-sm input-group">
							<input type='checkbox' name='showErrorsOnly' id='showErrorsOnly' data-on-text="{translate text='Errors Only' inAttribute=true isAdminFacing=true}" data-off-text="{translate text='All Records' inAttribute=true isAdminFacing=true}" data-switch="" {if !empty($showErrorsOnly)}checked{/if}/>
						</div>
					</div>
				</div>

			</div>
			<div class="row">
				<div class="col-sm-2 col-md-4">
					<div class="form-group">
						<button class="btn btn-primary btn-sm" type="submit">{translate text="Apply" isAdminFacing=true}</button>
					</div>
				</div>
			</div>
		</form>
		<script type="text/javascript">
			{literal}
			$(function(){ $('input[type="checkbox"][data-switch]').bootstrapSwitch()});
			{/literal}
		</script>

		<div class="adminTableRegion fixed-height-table">
			<table class="adminTable table table-condensed table-hover table-condensed smallText table-sticky" aria-label="Cron Log">
				<thead>
					<tr><th>{translate text='Id' isAdminFacing=true}</th><th>{translate text='Name' isAdminFacing=true}</th><th>{translate text='Started' isAdminFacing=true}</th><th>{translate text='Finished' isAdminFacing=true}</th><th>{translate text='Elapsed' isAdminFacing=true}</th><th>{translate text='Processes Run' isAdminFacing=true}</th><th>{translate text='Num Errors' isAdminFacing=true}</th><th>{translate text='Had Errors?' isAdminFacing=true}</th><th>{translate text='Notes' isAdminFacing=true}</th></tr>
				</thead>
				<tbody>
					{foreach from=$logEntries item=logEntry}
						<tr{if $logEntry->getHadErrors()} class="danger"{/if}>
							<td>{if $logEntry->getNumProcesses() > 0}<a href="#" class="accordion-toggle collapsed" id="cronEntry{$logEntry->id}" onclick="AspenDiscovery.Admin.toggleCronProcessInfo('{$logEntry->id}');return false;">{/if}{$logEntry->id}{if $logEntry->getNumProcesses() > 0}</a>{/if}</td>
							<td>{$logEntry->name}</td>
							<td>{$logEntry->startTime|date_format:"%D %T"}</td>
							<td>{$logEntry->endTime|date_format:"%D %T"}</td>
							<td>{$logEntry->getElapsedTime()}</td>
							<td>{$logEntry->getNumProcesses()}</td>
							<td>{$logEntry->numErrors}</td>
							<td>{if $logEntry->getHadErrors()}{translate text='Yes' isAdminFacing=true}{else}{translate text='No' isAdminFacing=true}{/if}</td>
							<td><a href="#" onclick="return AspenDiscovery.Admin.showCronNotes('{$logEntry->id}');">{translate text='Show Notes' isAdminFacing=true}</a></td>
						</tr>
						<tr class="logEntryProcessDetails" id="processInfo{$logEntry->id}" style="display:none">
							<td colspan="7">
								<table class="logEntryProcessDetails table table-striped table-condensed">
									<thead>
										<tr><th>{translate text='Process Name' isAdminFacing=true}</th><th>{translate text='Started' isAdminFacing=true}</th><th>{translate text='End Time' isAdminFacing=true}</th><th>{translate text='Elapsed' isAdminFacing=true}</th><th>{translate text='Updates' isAdminFacing=true}</th><th>{translate text='Skipped' isAdminFacing=true}</th><th>{translate text='Errors' isAdminFacing=true}</th><th>{translate text='Notes' isAdminFacing=true}</th></tr>
									</thead>
									<tbody>
									{foreach from=$logEntry->processes() item=process}
										<tr>
											<td>{$process->processName}</td>
											<td>{$process->startTime|date_format:"%D %T"}</td>
											<td>{$process->endTime|date_format:"%D %T"}</td>
											<td>{$process->getElapsedTime()}</td>
											<td>{$process->numUpdates}</td>
											<td>{$process->numSkipped}</td>
											<td>{$process->numErrors}</td>
											<td><a href="#" onclick="return AspenDiscovery.Admin.showCronProcessNotes('{$process->id}');">{translate text='Show Notes' isAdminFacing=true}</a></td>
										</tr>
									{/foreach}
									</tbody>
								</table>
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>

		{if !empty($pageLinks.all)}<div class="text-center">{$pageLinks.all}</div>{/if}
	</div>
{/strip}
