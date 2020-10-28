{strip}
	{if count($libraryLocations) > 1}
		<form role="form">
			<div class="form-group">
				<label for="selectLibrary">{translate text="Select a Location"}</label>
				<select name="selectLibrary" id="selectLibrary"
						onchange="return AspenDiscovery.showLocationHoursAndMap();" class="form-control">
					{foreach from=$libraryLocations item=curLocation}
						<option value="{$curLocation.id}">{$curLocation.name}</option>
					{/foreach}
				</select>
			</div>
		</form>
	{/if}
	{foreach from=$libraryLocations item=curLocation name=locationLoop}
		<div class="locationInfo" id="locationAddress{$curLocation.id}"
			 {if !$smarty.foreach.locationLoop.first}style="display:none"{/if}>
			<div class="row">
				<h3>{$curLocation.name}</h3>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-3">
					<dl>
						{if $curLocation.address}
							<dt>{translate text="Address"}</dt>
							<dd>
								<address>{$curLocation.address}
									{if !empty($curLocation.map_link)}
										<br/>
									<a href="{$curLocation.map_link}">{translate text="Directions"}</a>{/if}
								</address>
							</dd>
						{/if}
						{if $curLocation.phone}
							<dt>{translate text="Phone"}</dt>
							<dd><a href="tel:{$curLocation.phone}">{$curLocation.phone}</a></dd>
						{/if}
					</dl>
				</div>
			</div>
			{if $curLocation.hasValidHours}
				<h4>{translate text="Hours"}</h4>
				{assign var='lastDay' value="-1"}
				{foreach from=$curLocation.hours item=curHours}
					<div class="row">
						<div class="col-tn-4 col-md-2 result-label">
							{if $lastDay != $curHours->day}
								{if $curHours->day == 0}
									{translate text="Sunday"}
								{elseif $curHours->day == 1}
									{translate text="Monday"}
								{elseif $curHours->day == 2}
									{translate text="Tuesday"}
								{elseif $curHours->day == 3}
									{translate text="Wednesday"}
								{elseif $curHours->day == 4}
									{translate text="Thursday"}
								{elseif $curHours->day == 5}
									{translate text="Friday"}
								{elseif $curHours->day == 6}
									{translate text="Saturday"}
								{/if}
							{/if}
							{assign var='lastDay' value=$curHours->day}
						</div>
						<div class="col-tn-8 col-md-4">
							{if $curHours->closed}
								{translate text="Closed"}
							{else}
								{$curHours->open} - {$curHours->close}
							{/if}
						</div>
						<div class="col-tn-8 col-tn-offset-4 col-md-6 col-md-offset-0 ">
							<em>{$curHours->notes}</em>
						</div>
					</div>
				{/foreach}
			{/if}
		</div>
	{/foreach}
{/strip}