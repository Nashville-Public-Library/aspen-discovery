<div id="main-content">
	{if !empty($profile->_web_note)}
		<div class="row">
			<div id="web_note" class="alert alert-info text-center col-xs-12">{$profile->_web_note}</div>
		</div>
	{/if}
	{if !empty($accountMessages)}
		{include file='systemMessages.tpl' messages=$accountMessages}
	{/if}
	{if !empty($ilsMessages)}
		{include file='ilsMessages.tpl' messages=$ilsMessages}
	{/if}

	<h1>{translate text='My Materials Requests' isPublicFacing=true}</h1>

	{if isset($deleteResult)}
		{if $deleteResult.success}
			<div class="alert alert-success">{$deleteResult.message nofilter}</div>
		{else}
			<div class="alert alert-danger">{$deleteResult.message nofilter}</div>
		{/if}
	{/if}

	{if !empty($error)}
		<div class="alert alert-danger">{$error}</div>
	{else}
		{if $user->canSuggestMaterials()}
			{if count($allRequests) > 0}
				<form method="post" action="/MaterialsRequest/IlsRequests">
					<table id="requestedMaterials" class="table table-striped table-condensed tablesorter">
						<thead>
						<tr>
							{if !empty($allowDeletingILSRequests)}
								<th>&nbsp;</th>
							{/if}
							<th>{translate text="Summary" isPublicFacing=true}</th>
							<th>{translate text="Suggested On" isPublicFacing=true}</th>
							<th>{translate text="Note" isPublicFacing=true}</th>
							<th>{translate text="Status" isPublicFacing=true}</th>
						</tr>
						</thead>
						<tbody>
						{foreach from=$allRequests item=request}
							<tr>
								{if !empty($allowDeletingILSRequests)}
									<td>
										<input type="checkbox" name="delete_field[]" value="{$request.id}" title="{translate text="Select Request" inAttribute=true isPublicFacing=true}" aria-label="{translate text="Select Request" inAttribute=true isPublicFacing=true}"
											   onchange="document.getElementById('deleteSelectedBtn').disabled = document.querySelectorAll('input[name=\'delete_field[]\']:checked').length === 0;"
										/>
									</td>
								{/if}
								<td>{$request.summary}</td>
								<td>{$request.suggestedOn}</td>
								<td>{$request.note}</td>
								<td>{$request.status}</td>
							</tr>
						{/foreach}
						</tbody>
					</table>
					{if !empty($allowDeletingILSRequests)}
						<button type="submit" class="btn btn-sm btn-danger" id="deleteSelectedBtn" name="submit" disabled>{translate text="Delete Selected" isPublicFacing=true}</button>
					{/if}
				</form>
				<br/>
			{else}
				<div class="alert alert-warning">{translate text='There are no materials requests that meet your criteria.' isPublicFacing=true}</div>
			{/if}
			<div id="createNewMaterialsRequest"><a href="/MaterialsRequest/NewRequestIls?patronId={$patronId}" class="btn btn-primary btn-sm">{translate text='Submit a New Materials Request' isPublicFacing=true}</a></div>
		{else}
			<div class="alert alert-warning">{translate text='You are not eligible to make Materials Requests at this time.' isPublicFacing=true}</div>
		{/if}
	{/if}
</div>
<script type="text/javascript">
	{literal}
	$("#requestedMaterials").tablesorter({cssAsc: 'sortAscHeader', cssDesc: 'sortDescHeader', cssHeader: 'unsortedHeader', headers: {0: { sorter: false}, 2: {sorter : 'date'}, 6: { sorter: false} } });
	{/literal}
</script>