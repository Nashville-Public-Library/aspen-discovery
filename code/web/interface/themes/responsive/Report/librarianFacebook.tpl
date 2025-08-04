{strip}
	<div id="main-content" class="col-md-12">
		<div class="doNotPrint">
			<h1>Librarian Facebook</h1>
			{if isset($errors)}
				{foreach from=$errors item=error}
					<div class="error">{$error}</div>
				{/foreach}
			{/if}
			{if !empty($reportData)}
				<br/>
				<p>
					There are a total of <strong>{$reportData|@count}</strong> librarians.
				</p>
			</div>

   {literal}
			<style>
				.librarian-grid {
					display: grid;
					grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
					grid-gap: 20px;
					margin-bottom: 30px;
				}
				.librarian-grid-item {
					display: flex;
				}
				.librarianCard {
					border: 1px solid #ccc;
					border-radius: 5px;
					padding: 15px;
					box-shadow: 0 2px 4px rgba(0,0,0,0.1);
					display: flex;
					flex-direction: column;
					align-items: center;
					width: 100%;
					height: 100%;
				}
				.librarianImage {
					width: 150px;
					height: 150px;
					border-radius: 50%;
					object-fit: cover;
				}
				.librarianName {
					font-weight: bold;
					font-size: 1.2em;
					margin-top: 10px;
					text-align: center;
				}
				.librarianInfo {
					margin-top: 5px;
					text-align: center;
				}
				@media print {
					.doNotPrint {
						display: none !important;
					}
				}
			</style>
			{/literal}

			<div class="librarian-grid">
				{foreach from=$reportData item=dataRow name=librarianData}
					<div class="librarian-grid-item">
						<div class="librarianCard">
       {if !empty($dataRow.CATALOGIMAGE)}
								<img src="{$dataRow.CATALOGIMAGE}" alt="{$dataRow.FIRSTNAME} {$dataRow.LASTNAME}" class="librarianImage">
							{/if}
							<div class="librarianName">{$dataRow.FIRSTNAME} {$dataRow.LASTNAME}</div>
							<div class="librarianInfo">
								<div>{$dataRow.BRANCHNAME}</div>
{*								<div><strong>ID:</strong> {$dataRow.PATRONID}</div>*}
							</div>
						</div>
					</div>
				{/foreach}
			</div>
		{/if}
	</div>
{/strip}
