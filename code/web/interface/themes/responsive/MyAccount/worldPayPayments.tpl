{strip}
	<input type="hidden" name="MerchantCode" value="{$merchantCode}" />
	<input type="hidden" name="SettleCode" value="{$settleCode}" />
	<input type="hidden" name="patronId" value="{$userId}"/>
	<input type="hidden" name="BillingName" value="{$profile->_fullname}" />
	<input type="hidden" name="BillingAddress" value="{$profile->_address1}" />
	<input type="hidden" name="BillingCity" value="{$profile->_city}" />
	<input type="hidden" name="BillingState" value="{$profile->_state}" />
	<input type="hidden" name="BillingPostalCode" value="{$profile->_zip}" />
	<input type="hidden" name="BillingPhone" value="{$profile->phone}" />
	<input type="hidden" name="BillingEmail" value="{$profile->email}" />
	<input type="hidden" name="PaymentAmount" id="{$userId}FineAmount" value="{$fineTotalsVal.$userId}" />
	<input type="hidden" name="PaymentMethod" value="CreditOrDebit" />
	<input type="hidden" name="ReturnUrl" id="{$userId}ReturnUrl" value="{$aspenUrl}/MyAccount/WorldPayCompleted?payment=" />
	<input type="hidden" name="CancelUrl" id="{$userId}CancelUrl" value="{$aspenUrl}/MyAccount/WorldPayCancel?payment=" />
	<input type="hidden" name="PostUrl" id="{$userId}PostUrl" value="{$aspenUrl}/WorldPay/Complete" />
	<input type="hidden" name="UserPart1" id="PaymentId" value="0" />
	<input type="hidden" name="UserPart2" value="{$profile->firstname|escape}" />
	<input type="hidden" name="UserPart3" value="{$profile->lastname|escape}" />
	<input type="hidden" name="UserPart4" value="{$profile->getBarcode()}" />
	{if !empty($useLineItems)}
		<input type="hidden" name="LineItems" id="{$userId}LineItems" value="[]"/>
	{/if}
	<div class="row">
		<div class="col-tn-12 col-sm-8 col-md-6 col-lg -3">
			<div id="msb-button-container{$userId}">
				<button type="submit" id="{$userId}PayFines" class="btn btn-sm btn-primary">{if !empty($payFinesLinkText)}{$payFinesLinkText}{else}{translate text = 'Go to payment form' isPublicFacing=true}{/if}</button>
			</div>
		</div>
	</div>

	<script>
	$(document).ready(function () {ldelim}
		$('#fines{$userId}').attr('action', '{$paymentSite}');
	{rdelim});
	</script>
	<script>
		$(document).ready(function () {ldelim}
			$('formattedTotal{$userId}').on('change', function(){ldelim}
				document.getElementById("{$userId}FineAmount").value = document.getElementById("formattedTotal{$userId}").text;
			{rdelim})
		{rdelim});
	</script>
	{if $finesToPay == 1}
		<script>
			$('#fines{$userId}').on('submit', function() {ldelim}
				var totalFineAmt = 0;
				var totalOutstandingAmt = 0;
				var lineItems = "";
				var lineItemNum = 0;
				$("#fines{$userId} .selectedFine:checked").each(
					function() {ldelim}
						lineItemNum += 1;
						var fineId = $(this).data('fine_id');
						var fineDescription = $(this).attr("aria-label");
						var fineAmount =  $(this).data('fine_amt');
						var lineItem = "["+lineItemNum+"*"+fineId+"*"+fineDescription+"*"+fineAmount+"]";
						totalFineAmt += fineAmount * 1;
						totalOutstandingAmt += fineAmount * 1;
						if(lineItems === '') {ldelim}
							lineItems = lineItems.concat(lineItem);
							{rdelim} else {ldelim}
							lineItems = lineItems.concat(",", lineItem);
							{rdelim}
						{rdelim}
				);
				document.getElementById("{$userId}FineAmount").value = totalFineAmt;
				{if !empty($useLineItems)}
				document.getElementById("{$userId}LineItems").value = lineItems;
				{/if}

				var paymentId = AspenDiscovery.Account.createWorldPayOrder('#fines{$userId}', '#formattedTotal{$userId}', 'fine');
				var returnUrl = document.getElementById("{$userId}ReturnUrl").value;
				var cancelUrl = document.getElementById("{$userId}CancelUrl").value;

				returnUrl = returnUrl.concat(paymentId);
				cancelUrl = cancelUrl.concat(paymentId);

				document.getElementById("{$userId}CancelUrl").value = cancelUrl;
				document.getElementById("{$userId}ReturnUrl").value = returnUrl;
				document.getElementById("PaymentId").value = paymentId;

				{rdelim});
		</script>
	{/if}
	{if $finesToPay == 2}
	<script>
		$('#fines{$userId}').on('submit', function() {ldelim}
			var totalFineAmt = 0;
			var totalOutstandingAmt = 0;
			var lineItems = "";
			var lineItemNum = 0;
			$("#fines{$userId} .selectedFine:checked").each(
				function() {ldelim}
					lineItemNum += 1;
					var fineId = $(this).data('fine_id');
					var fineDescription = $(this).attr("aria-label");
					var fineAmountInput = $("#amountToPay" + fineId);
					var lineItem = "["+lineItemNum+"*"+fineId+"*"+fineDescription+"*"+fineAmountInput.val()+"]";
					totalFineAmt += fineAmountInput.val() * 1;
					totalOutstandingAmt += fineAmountInput.val() * 1;
						if(lineItems === '') {ldelim}
							lineItems = lineItems.concat(lineItem);
						{rdelim} else {ldelim}
							lineItems = lineItems.concat(",", lineItem);
						{rdelim}
					{rdelim}
			);
			document.getElementById("{$userId}FineAmount").value = totalFineAmt;

			{if !empty($useLineItems)}
			document.getElementById("{$userId}LineItems").value = lineItems;
			{/if}


			var paymentId = AspenDiscovery.Account.createWorldPayOrder('#fines{$userId}', '#formattedTotal{$userId}', 'fine');
			var returnUrl = document.getElementById("{$userId}ReturnUrl").value;
			var cancelUrl = document.getElementById("{$userId}CancelUrl").value;

			returnUrl = returnUrl.concat(paymentId);
			cancelUrl = cancelUrl.concat(paymentId);

			document.getElementById("{$userId}CancelUrl").value = cancelUrl;
			document.getElementById("{$userId}ReturnUrl").value = returnUrl;
			document.getElementById("PaymentId").value = paymentId;

			{rdelim});
	</script>
	{/if}
{/strip}


