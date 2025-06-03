<div>
		<h2>{translate text={$msg} isPublicFacing=true}</h2>
</div>
<div id ="debug">
	{if !empty($debug)}
		<h4>{translate text="Debug Information" isAdminFacing=true}</h4>
		<p>{translate text="Backtrace" isAdminFacing=true}</p>
		{$error->backtrace}
	{/if}
</div>
