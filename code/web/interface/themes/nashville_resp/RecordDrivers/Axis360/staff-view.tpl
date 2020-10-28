{strip}
	{if $recordDriver}
		<div class="row">
			<div class="col-xs-12">
				<a href="/GroupedWork/{$recordDriver->getPermanentId()}" class="btn btn-sm btn-default">Go To Grouped Work</a>
				<button onclick="return AspenDiscovery.GroupedWork.reloadCover('{$recordDriver->getPermanentId()}')" class="btn btn-sm btn-default">Reload Cover</button>
				{if $loggedIn && in_array('Upload Covers', $userPermissions)}
					<button onclick="return AspenDiscovery.GroupedWork.getUploadCoverForm('{$recordDriver->getPermanentId()}')" class="btn btn-sm btn-default">Upload Cover</button>
				{/if}
				<button onclick="return AspenDiscovery.GroupedWork.reloadEnrichment('{$recordDriver->getPermanentId()}')" class="btn btn-sm btn-default" >Reload Enrichment</button>
				{if $loggedIn && in_array('Force Reindexing of Records', $userPermissions)}
					<button onclick="return AspenDiscovery.GroupedWork.forceReindex('{$recordDriver->getPermanentId()}')" class="btn btn-sm btn-default">Force Reindex</button>
				{/if}
				{if $loggedIn && in_array('Set Grouped Work Display Information', $userPermissions)}
					<button onclick="return AspenDiscovery.GroupedWork.getDisplayInfoForm('{$recordDriver->getPermanentId()}')" class="btn btn-sm btn-default">{translate text="Set Display Info"}</button>
					<button onclick="return AspenDiscovery.GroupedWork.getGroupWithForm(this, '{$recordDriver->getPermanentId()}')" class="btn btn-sm btn-default">Group With Work</button>
				{/if}
				{if $loggedIn && in_array('Manually Group and Ungroup Works', $userPermissions)}
					<button onclick="return AspenDiscovery.GroupedWork.ungroupRecord(this, '{$recordDriver->getIdWithSource()}')" class="btn btn-sm btn-default">Ungroup</button>
				{/if}
				{if $loggedIn && $enableArchive && in_array('Administer Islandora Archive', $userPermissions)}
					<button onclick="return AspenDiscovery.GroupedWork.reloadIslandora('{$recordDriver->getPermanentId()}')" class="btn btn-sm btn-default">Clear Islandora Cache</button>
				{/if}
			</div>
		</div>
	{/if}

    {include file="RecordDrivers/GroupedWork/grouping-information.tpl"}

	{if $axis360Extract}
	<h3>Axis 360 Extract Information</h3>
	<pre>
		{$axis360Extract|print_r}
	</pre>
	{/if}
{/strip}