{strip}
	<div id="main-content" class="col-md-12">
		<h1>{translate text='Events Indexing Log' isAdminFacing=true}</h1>

		{include file='Admin/exportLogFilters.tpl'}
		<div class="adminTableRegion fixed-height-table">
			<table class="adminTable table table-condensed table-hover table-condensed smallText table-sticky">
				<thead>
					<tr><th>{translate text="Id" isAdminFacing=true}</th><th>{translate text="Name" isAdminFacing=true}</th><th>{translate text="Started" isAdminFacing=true}</th><th>{translate text="Last Update" isAdminFacing=true}</th><th>{translate text="Finished" isAdminFacing=true}</th><th>{translate text="Elapsed" isAdminFacing=true}</th><th>{translate text="Total Events" isAdminFacing=true}</th><th>{translate text="Num Errors" isAdminFacing=true}</th><th>{translate text="Pages Added" isAdminFacing=true}</th><th>{translate text="Pages Deleted" isAdminFacing=true}</th><th>{translate text="Pages Updated" isAdminFacing=true}</th><th>{translate text="Notes" isAdminFacing=true}</th></tr>
				</thead>
				<tbody>
					{foreach from=$logEntries item=logEntry}
						<tr>
							<td>{$logEntry->id}</td>
							<td>{$logEntry->name}</td>
							<td>{$logEntry->startTime|date_format:"%D %T"}</td>
							<td>{$logEntry->lastUpdate|date_format:"%D %T"}</td>
							<td>{$logEntry->endTime|date_format:"%D %T"}</td>
							<td>{$logEntry->getElapsedTime()}</td>
							<td>{$logEntry->numEvents}</td>
							<td>{$logEntry->numErrors}</td>
							<td>{$logEntry->numAdded}</td>
							<td>{$logEntry->numDeleted}</td>
							<td>{$logEntry->numUpdated}</td>
							<td><a href="#" onclick="return AspenDiscovery.Admin.showExtractNotes('{$logEntry->id}', 'events');">{translate text="Show Notes" isAdminFacing=true}</a></td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>

		{if !empty($pageLinks.all)}<div class="text-center">{$pageLinks.all}</div>{/if}
	</div>
{/strip}