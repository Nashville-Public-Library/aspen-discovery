{strip}
{* Add availability as needed *}
{if $showAvailability && $availability && count($availability) > 0}
	<div>
		<table class="holdingsTable">
			<thead>
				<tr><th>{translate text="Library"}</th><th>{translate text="Owned"}</th><th>{translate text="Available"}</th><th>{translate text="Shared?"}</th></tr>
			</thead>
			<tbody>
				{foreach from=$availability item=availabilityItem}
					<tr><td>{$availabilityItem->getLibraryName()}</td><td>{$availabilityItem->copiesOwned}</td><td>{$availabilityItem->copiesAvailable}</td><td>{if $availabilityItem->shared}Yes{else}No{/if}</td></tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{/if}
{if $showAvailabilityOther && $availabilityOther && count($availabilityOther) > 0}
	<div>
		<h3>{translate text="Other Libraries that own this title"}</h3>
		<table class="holdingsTable">
			<thead>
			<tr><th>Library</th><th>Owned</th><th>Available</th><th>Shared?</th></tr>
			</thead>
			<tbody>
			{foreach from=$availabilityOther item=availabilityItem}
				<tr><td>{$availabilityItem->getLibraryName()}</td><td>{$availabilityItem->copiesOwned}</td><td>{$availabilityItem->copiesAvailable}</td><td>{if $availabilityItem->shared}Yes{else}No{/if}</td></tr>
			{/foreach}
			</tbody>
		</table>
		<br/>
		<div class="note">
			{if strcasecmp($source, 'OverDrive') == 0}
				Note: Copies owned by the Digital library are available to patrons of any Marmot Library.  Titles owned by a specific library are only available for use by patrons of that library.
			{/if}
		</div>
	</div>
{/if}
{if $numberOfHolds > 0}
	<p>There {if $numberOfHolds > 1}are{else}is{/if} {$numberOfHolds} hold{if $numberOfHolds > 1}s{/if} on this title.</p>
{/if}
{/strip}