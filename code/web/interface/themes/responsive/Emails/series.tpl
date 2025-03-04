{* This is a text-only email template; do not include HTML! *}
{$list->displayName}
{$list->author}
------------------------------------------------------------
{if !empty($from)}
	{translate text="This email was sent from %1%" 1=$from isPublicFacing=true}
{/if}
{if !empty($message)}
	{translate text="Message From Sender" isPublicFacing=true}
	{$message}
	------------------------------------------------------------
{/if}
{if !empty($error)}
	{$error}
	------------------------------------------------------------
{else}
	{foreach from=$titles item=title}

		{$title->getTitle()}
		{if !empty($title->getPrimaryAuthor())}
			{$title->getPrimaryAuthor()}
		{/if}
		{$title->getLinkUrl(true)}

		---------------------
	{/foreach}
{/if}
