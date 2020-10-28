{strip}
<form method="post" action="" name="popupForm" class="form-horizontal" id="groupWithForm">
	<div class="alert alert-info">
		This form will allow you to group the current work with another work.  The other work will become the primary record and this work will be removed from the index and added to the primary.
	</div>
	<div class="alert alert-info">
		<div class="row">
			<div class="col-tn-12">
				You are grouping {$id}
			</div>
		</div>
		<div class="row">
			<div class="col-tn-3">
				Title
			</div>
			<div class="col-tn-9">
				<strong>{$groupedWork->full_title}</strong>
			</div>
		</div>
		<div class="row">
			<div class="col-tn-3">
				Author
			</div>
			<div class="col-tn-9">
				<strong>{$groupedWork->author}</strong>
			</div>
		</div>
	</div>
	<input type="hidden" name="id" id="id" value="{$id}"/>
	<div class="form-group">
		<label for="searchResultToGroupWith" class="col-sm-12">{translate text="Enter the search result number to be the primary work"} </label>
		<div class="col-tn-12">
			<select name="searchResultToGroupWith" id="searchResultToGroupWith" class="form-control" onchange="$('#workToGroupWithId').val($('#searchResultToGroupWith option:selected').val());">
				{foreach from=$availableRecords item="recordDescription" key="recordId"}
					<option value="{$recordId}">{$recordDescription}</option>
				{/foreach}
			</select>
			<input type="hidden" name="workToGroupWithId" id="workToGroupWithId">
		</div>
	</div>
	<div id="groupWithInfo">

	</div>
</form>
{/strip}