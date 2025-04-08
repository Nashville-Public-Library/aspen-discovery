	<div class="holdsWithSelected{$sectionKey}">
		<form id="withSelectedHoldsFormBottom{$sectionKey}" action="{$fullPath}">
			<div class="btn-group">
				<a href="#" onclick="AspenDiscovery.Account.confirmCancelHoldSelected()" class="btn btn-sm btn-default btn-warning">{translate text="Cancel Selected" isPublicFacing=true}</a>
				<a href="#" onclick="AspenDiscovery.Account.confirmCancelHoldAll()" class="btn btn-sm btn-default btn-warning">{translate text="Cancel All" isPublicFacing=true}</a>
				{if !empty($allowFreezeAllHolds)}
					<a href="#" onclick="AspenDiscovery.Account.confirmFreezeHoldSelected('', '', '', {if !empty($suspendRequiresReactivationDate)}true{else}false{/if})" class="btn btn-sm btn-default">{translate text="Freeze Selected" isPublicFacing=true}</a>
					<a href="#" onclick="AspenDiscovery.Account.confirmFreezeHoldAll('{$userId}', {if !empty($suspendRequiresReactivationDate)}true{else}false{/if})" class="btn btn-sm btn-default">{translate text="Freeze All" isPublicFacing=true}</a>
					<a href="#" onclick="AspenDiscovery.Account.confirmThawHoldSelected()" class="btn btn-sm btn-default">{translate text="Thaw Selected" isPublicFacing=true}</a>
					<a href="#" onclick="AspenDiscovery.Account.confirmThawHoldAll('{$userId}')" class="btn btn-sm btn-default">{translate text="Thaw All" isPublicFacing=true}</a>
				{/if}

				{if $allowSelectingHoldsToExport}
					<a href="#" onclick="return AspenDiscovery.Account.exportOnlySelectedHolds('{$source}', $('#availableHoldSort_{$source} option:selected').val(), $('#unavailableHoldSort_{$source} option:selected').val());" class="btn btn-sm btn-default">{translate text="Export Selected to CSV" isPublicFacing=true}</a>
				{/if}
			</div>
			<div class="btn-group">
				<input type="hidden" name="withSelectedAction" value="">
				<div id="holdsUpdateSelected{$sectionKey}Bottom" class="holdsUpdateSelected{$sectionKey}">
					<button type="submit" class="btn btn-sm btn-default" id="exportToExcel" name="exportToExcel" onclick="return AspenDiscovery.Account.exportHolds('{$source}', $('#availableHoldSort_{$source} option:selected').val(), $('#unavailableHoldSort_{$source} option:selected').val());">{translate text="Export to CSV" isPublicFacing=true}</button>
				</div>
			</div>
		</form>
	</div>
