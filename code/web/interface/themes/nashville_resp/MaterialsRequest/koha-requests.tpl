<div id="main-content">
	{if !empty($profile->_web_note)}
		<div class="row">
			<div id="web_note" class="alert alert-info text-center col-xs-12">{$profile->_web_note}</div>
		</div>
	{/if}

	<span class='availableHoldsNoticePlaceHolder'></span>

	<h1>{translate text='My Materials Requests'}</h1>

    {* MDN 7/26/2019 Do not allow access for linked users *}
    {*	{include file="MyAccount/switch-linked-user-form.tpl" label="Viewing Requests for" actionPath="/MyAccount/ReadingHistory"}*}

	{if !empty($error)}
		<div class="alert alert-danger">{$error}</div>
	{else}
		{if count($allRequests) > 0}
			<form method="post" action="/MaterialsRequest/IlsRequests">
				<table id="requestedMaterials" class="table table-striped table-condensed tablesorter">
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>{translate text="Summary"}</th>
							<th>{translate text="Suggested On"}</th>
							<th>{translate text="Note"}</th>
							<th>{translate text="Status"}</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$allRequests item=request}
							<tr>
								<td>
									<input type="checkbox" name="delete_field" value="{$request.id}" title="{translate text="Select Request" inAttribute=true}" aria-label="{translate text="Select Request" inAttribute=true}"/>
								</td>
								<td>{$request.summary}</td>
								<td>{$request.suggestedOn}</td>
								<td>{$request.note}</td>
								<td>{$request.status}</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
				<button type="submit" class="btn btn-sm btn-danger" name="submit">{translate text="Delete Selected"}</button>
			</form>
			<br/>
		{else}
			<div class="alert alert-warning">There are no {translate text='materials request'}s that meet your criteria.</div>
		{/if}
		<div id="createNewMaterialsRequest"><a href="/MaterialsRequest/NewRequestIls?patronId={$patronId}" class="btn btn-primary btn-sm">{translate text='Submit a New Materials Request'}</a></div>
	{/if}
</div>
<script type="text/javascript">
{literal}
$("#requestedMaterials").tablesorter({cssAsc: 'sortAscHeader', cssDesc: 'sortDescHeader', cssHeader: 'unsortedHeader', headers: {0: { sorter: false}, 2: {sorter : 'date'}, 6: { sorter: false} } });
{/literal}
</script>