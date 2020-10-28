{strip}
	{if !empty($selectedRole)}
	<h1>{translate text="Permissions for %1%" 1=$selectedRole->name}</h1>
	{else}
	<h1>{translate text="Permissions"}</h1>
	{/if}

	<form class="form-inline row">
		<div class="form-group col-tn-12">
			<label for="roleId" class="control-label">{translate text="Role to edit"}</label>&nbsp;
			<select id="roleId" name="roleId" class="form-control input-sm">
				{foreach from=$roles key=roleId item=role}
					<option value="{$roleId}" {if $roleId == $selectedRole->roleId}selected{/if}>{$role->name}</option>
				{/foreach}
			</select>
			<button class="btn btn-primary btn-sm" type="submit">{translate text="Select"}</button>
			&nbsp; <a class="btn btn-danger btn-sm" onclick="return AspenDiscovery.Admin.deleteRole({$selectedRole->roleId})">{translate text="Delete"}</a>
			&nbsp; <a class="btn btn-default btn-sm" onclick="return AspenDiscovery.Admin.showCreateRoleForm()">{translate text="Create New Role"}</a>
		</div>
	</form>

	<form>
		<input type="hidden" name="roleId" value="{$selectedRole->roleId}"/>
		<table id="permissionsTable" class="table-striped table-condensed table-sticky" style="display:block; overflow: auto;">
			<thead class="permissionsHeader">
				<tr>
					<th>{translate text="Permission"}</th>
					<th>{$selectedRole->name}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$permissions item=sectionPermissions key=sectionName}
					<tr class="permissionSection">
						<td colspan="2">
							{$sectionName|translate}
						</td>
					</tr>
					{foreach from=$sectionPermissions item=permission}
						<tr class="permissionRow">
							<td>
								<div class="permissionName">{$permission->name}</div>
								<div class="permissionDescription">{$permission->description}</div>
							</td>
							<td><input type="checkbox" name="permission[{$permission->id}]" title="Toggle {$permission->name} for {$selectedRole->name}" {if $selectedRole->hasPermission($permission->name)}checked{/if}/></td>
						</tr>
					{/foreach}
				{/foreach}
			</tbody>
		</table>
		<div>
			<button type="submit" name="submit" value="save" class="btn btn-primary">{translate text="Save Changes"}</button>
		</div>
	</form>
{/strip}