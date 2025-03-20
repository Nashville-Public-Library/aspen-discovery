{strip}
	{if !empty($hooplaUser)}{* Linked User that is not the main user *}
		<p>
			{translate text="Using card for %1%" 1=$hooplaUser->getNameAndLibraryLabel()|escape isPublicFacing=true}
		</p>
	{/if}
	{if !empty($hooplaPatronStatus)}
		<div class="alert alert-info">
			{if $hooplaType == 'Flex'}
				{translate text="Your Hoopla Flex title was checked out successfully." isPublicFacing=true}
			{else}
				{if $hooplaPatronStatus->numCheckedOut == 1}
					{translate text="You have 1 Hoopla Instant title currently checked out." isPublicFacing=true}
				{else}
					{translate text="You have %1% Hoopla Instant titles currently checked out." 1=$hooplaPatronStatus->numCheckedOut isPublicFacing=true}
				{/if}
				<br>
				{if $hooplaPatronStatus->numCheckoutsRemaining == 1}
					{translate text="You can borrow 1 more Hoopla Instant title this month." isPublicFacing=true}
				{else}
					{translate text="You can borrow %1% more Hoopla Instant titles this month." 1=$hooplaPatronStatus->numCheckoutsRemaining isPublicFacing=true}
				{/if}
			{/if}
		</div>
	{/if}
{/strip}