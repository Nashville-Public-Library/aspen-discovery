{strip}
	{if $loggedIn}
		<div id="account-menu-label" class="sidebar-label row">
			<div class="col-xs-12">{translate text='Administration Options'}</div>
		</div>
		<div id="home-account-links" class="sidebar-links row">
			<div class="panel-group accordion" id="account-link-accordion">
				{foreach from=$adminActions item=adminSection key=adminSectionKey}
					{if !empty($adminSection->actions)}
						<div class="panel{if $adminSectionKey==$activeAdminSection} active{/if}">
							<a href="#{$adminSectionKey}Group" data-toggle="collapse" data-parent="#adminMenuAccordion" aria-label="{translate text="%1% Menu" 1=$adminSection->label inAttribute=true}">
								<div class="panel-heading">
									<div class="panel-title">
										{$adminSection->label|translate}
									</div>
								</div>
							</a>
							<div id="{$adminSectionKey}Group" class="panel-collapse collapse {if $adminSectionKey==$activeAdminSection}in{/if}">
								<div class="panel-body">
									{foreach from=$adminSection->actions item=adminAction key=adminActionKey}
										<div class="adminMenuLink "><a href="{$adminAction->link}">{$adminAction->label|translate}</a></div>
										{if !empty($adminAction->subActions)}
											{foreach from=$adminAction->subActions item=adminSubAction}
												<div class="adminMenuLink ">&nbsp;&raquo;&nbsp;<a href="{$adminSubAction->link}">{$adminSubAction->label|translate}</a></div>
											{/foreach}
										{/if}
									{/foreach}
								</div>
							</div>
						</div>
					{/if}
				{/foreach}
			</div>
		</div>
	{/if}
{/strip}