{strip}
	<input type="hidden" name="patronId" value="{$userId}"/>
	<div class="row">
		<div class="col-tn-12 col-sm-8 col-md-6 col-lg -3">
			<script src="https://www.paypal.com/sdk/js?client-id={$payPalClientId}&currency={$currencyCode}"></script>

			<div id="paypal-button-container{$userId}"></div>

			<script>
				$(document).ready(function () {ldelim}
					paypal.Buttons({ldelim}
						createOrder: function (data, actions) {ldelim}
							return AspenDiscovery.Account.createPayPalOrder('#fines{$userId}');
						{rdelim},
						onApprove: function (data, actions) {ldelim}
							{* This function captures the funds from the transaction. *}
							return actions.order.capture().then(
								function (details) {ldelim}
									{* This function shows a transaction success message to your buyer. *}
									AspenDiscovery.Account.completePayPalOrder(details.id, '{$userId}');
								{rdelim}
							);
						{rdelim}
					{rdelim}).render('#paypal-button-container{$userId}');
				{rdelim});
			</script>
		</div>
	</div>
{/strip}
