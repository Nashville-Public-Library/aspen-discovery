{* This is a text-only email template; do not include HTML! *}
{$list->title}
{$list->description}
------------------------------------------------------------
{if !empty($message)}
{translate text="Message From Sender"}:
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

{if !empty($title->getListNotes())}
{$title->getListNotes()}

{/if}
---------------------
{/foreach}
{/if}

