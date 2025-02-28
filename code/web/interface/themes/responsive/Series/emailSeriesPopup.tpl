<div align="left">
	{if !empty($message)}<div class="error">{translate text=$message isPublicFacing=true}</div>{/if}

	<form id="emailSeriesForm" class="form form-horizontal">
		<div class="form-group">
			<input type="hidden" name="seriesId" value="{$seriesId|escape}">
			<label for="to" class="control-label col-xs-2">{translate text='To' isPublicFacing=true}</label>
			<div class="col-xs-10">
				<input type="text" name="to" id="to" size="40" class="required email form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="from" class="control-label col-xs-2">{translate text='From' isPublicFacing=true}</label>
			<div class="col-xs-10">
				<input type="text" name="from" id="from" size="40" maxlength="100" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="message" class="control-label col-xs-2">{translate text='Message' isPublicFacing=true}</label>
			<div class="col-xs-10">
				<textarea name="message" id="message" rows="3" cols="40" class="form-control"></textarea>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
	{literal}
	$("#emailSeriesForm").validate({
		submitHandler: function(){
			AspenDiscovery.Series.sendEmail();
		}
	});
	{/literal}
</script>
