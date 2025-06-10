{strip}
	<div class="col-xs-12">
		<div class="row">
			<div class="col-xs-12 col-md-9">
				<h1 id="pageTitle">SnapPay Transaction History API Test Results</h1>
			</div>
			<div class="col-xs-12 col-md-3 help-link">
				<a href="https://help.aspendiscovery.org/help/admin/ecommerce"><i class="fas fa-question-circle" role="presentation"></i>&nbsp;{translate text="Documentation" isAdminFacing=true}</a>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<a class="btn btn-default" href='/Admin/SnapPaySettings?objectAction=edit&id={$snapPaySetting->id}'><i class="fas fa-arrow-alt-circle-left" role="presentation"></i> {translate text="Return to Settings" isAdminFacing=true}</a>
			</div>
		</div>

		<style>
			/* Increase font size for content sections */
			.content-section {
				font-size: 14px;
			}
			.content-section .well, .content-section .table {
				font-size: 14px;
			}
			.content-section pre {
				font-size: 12px;
			}
		</style>

		<div class="content-section">
			<div class="row">
				<div class="col-xs-12">
					<h2>Settings Used</h2>
					<div class="well">
						<div><strong>Name:</strong> {$snapPaySetting->name}</div>
						<div><strong>Environment:</strong> {if $snapPaySetting->sandboxMode}Sandbox{else}Production{/if}</div>
						<div><strong>Account ID:</strong> {$snapPaySetting->accountId}</div>
						<div><strong>API URL:</strong> {$results.api_url}</div>
						<div><strong>Last Reconciliation Time:</strong> {if $snapPaySetting->lastReconciliationTime}{$snapPaySetting->lastReconciliationTime|date_format:"%D %T"}{else}Never{/if}</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<h2>Test Parameters</h2>
					<form action="/Admin/SnapPaySettings" method="get" class="form-inline">
						<input type="hidden" name="objectAction" value="testTransactionHistoryAPI">
						<input type="hidden" name="id" value="{$snapPaySetting->id}">

						<div class="form-group">
							<label for="startDate">Start Date:</label>
							<input type="text" id="startDate" name="startDate" value="{$startDate}" placeholder="mm/dd/yyyy hh:mm:ss" class="form-control">
						</div>

						<div class="form-group">
							<label for="endDate">End Date:</label>
							<input type="text" id="endDate" name="endDate" value="{$endDate}" placeholder="mm/dd/yyyy hh:mm:ss" class="form-control">
						</div>

						<div class="checkbox">
							<label>
								<input type="checkbox" name="verbose" value="true" {if $verbose}checked{/if}> Show Raw Response
							</label>
						</div>

						<button type="submit" class="btn btn-primary">Run Test</button>
					</form>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<h2>Test Results</h2>
					<div class="alert {if $results.success}alert-success{else}alert-danger{/if}">
						<strong>Status:</strong> {if $results.success}Success{else}Failed{/if}<br>
						<strong>Message:</strong> {$results.message}<br>
						<strong>HTTP Status Code:</strong> {$results.http_code}<br>
						<strong>Transaction Count:</strong> {$results.transaction_count}<br>
						<strong>Timestamp:</strong> {$results.timestamp|date_format:"%D %T"}
					</div>
				</div>
			</div>

			{* Rest of the template remains the same but wrapped in the content-section div *}
			{if $verbose}
				<div class="row">
					<div class="col-xs-12">
						<h3>Request Details</h3>
						<div class="well">
							<h4>Request Parameters:</h4>
							<pre>{foreach from=$results.request_params key=key item=value}{$key}: {$value}<br>
{/foreach}</pre>

							<h4>Request Headers:</h4>
							<pre>{foreach from=$results.request_headers item=header}{$header}<br>
{/foreach}</pre>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12">
						<h3>Raw Response</h3>
						<div class="well">
							<pre>{$results.response_raw|escape}</pre>
						</div>
					</div>
				</div>
			{/if}

			{if $results.success && $results.transaction_count > 0}
				<div class="row">
					<div class="col-xs-12">
						<h3>Transactions</h3>
						<div class="table-responsive">
							<table class="table table-striped table-bordered">
								<thead>
								<tr>
									<th>Transaction ID</th>
									<th>Amount</th>
									<th>Status</th>
									<th>Date</th>
									<th>Reference ID</th>
									{if $verbose}<th>Details</th>{/if}
								</tr>
								</thead>
								<tbody>
								{foreach from=$results.transactions item=transaction}
									<tr>
										<td>{$transaction.paymenttransactionid|escape}</td>
										<td>{$transaction.transactionamount|escape}</td>
										<td>{$transaction.status|escape}</td>
										<td>{$transaction.transactiondate|escape}</td>
										<td>{if !empty($transaction.udf1)}{$transaction.udf1|escape}{elseif !empty($transaction.udf9)}{$transaction.udf9|escape}{else}N/A{/if}</td>
										{if $verbose}
											<td>
												<button class="btn btn-default btn-xs" type="button" data-toggle="collapse" data-target="#transaction-{$transaction.paymenttransactionid}" aria-expanded="false">
													Show Details
												</button>
												<div class="collapse" id="transaction-{$transaction.paymenttransactionid}">
													<div class="well">
                                            <pre>{foreach from=$transaction key=key item=value}{$key}: {$value}
{/foreach}</pre>
													</div>
												</div>
											</td>
										{/if}
									</tr>
								{/foreach}
								</tbody>
							</table>
						</div>
					</div>
				</div>
			{/if}
		</div>
	</div>
{/strip}
