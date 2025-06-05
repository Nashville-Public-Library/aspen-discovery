{if !empty($showCurbsidePickups)}
	{if !empty($loggedIn)}
		<div class="row">
			<div class="col-xs-12" id="curbside-pickups">
				{if !empty($instructionNewPickup)}
					<div class="alert alert-info" id="instructionNewPickup">
						{translate text=$instructionNewPickup isPublicFacing=true isAdminEnteredData=true}
					</div>
				{/if}

				<form id="newCurbsidePickupForm" class="curbside-pickup-form">
					<!-- Step 1: Select Date -->
					<div class="form-group">
						<label for="pickupDate" class="control-label">{translate text="Select a Pickup Date" isPublicFacing=true}</label>
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							<input type="text" class="form-control" name="pickupDate" id="pickupDate" placeholder="{translate text='Click to select a date' isPublicFacing=true}"/>
						</div>
					</div>

					<!-- Step 2: Select Time -->
					<div id="availableTimeSlots" style="display: none;">
						<div class="form-group">
							<label class="control-label">{translate text="Select a Pickup Time" isPublicFacing=true}</label>

							<div class="time-slots-container">
								<div class="time-slot-section" id="morningTimeSlotsAccordion" style="display: none;">
									<h3 class="time-slot-heading" data-toggle="collapse" data-target="#morningTimeSlotsGroup">
										{translate text="Morning" isPublicFacing=true}
										<span class="toggle-icon"><i class="fa fa-chevron-right"></i></span>
									</h3>
									<div id="morningTimeSlotsGroup" class="collapse">
										<div id="morningTimeSlots" class="time-slot-options" data-toggle="buttons"></div>
									</div>
								</div>

								<div class="time-slot-section" id="afternoonTimeSlotsAccordion" style="display: none;">
									<h3 class="time-slot-heading" data-toggle="collapse" data-target="#afternoonTimeSlotsGroup">
										{translate text="Afternoon" isPublicFacing=true}
										<span class="toggle-icon"><i class="fa fa-chevron-right"></i></span>
									</h3>
									<div id="afternoonTimeSlotsGroup" class="collapse">
										<div id="afternoonTimeSlots" class="time-slot-options" data-toggle="buttons"></div>
									</div>
								</div>

								<div class="time-slot-section" id="eveningTimeSlotsAccordion" style="display: none;">
									<h3 class="time-slot-heading" data-toggle="collapse" data-target="#eveningTimeSlotsGroup">
										{translate text="Evening" isPublicFacing=true}
										<span class="toggle-icon"><i class="fa fa-chevron-right"></i></span>
									</h3>
									<div id="eveningTimeSlotsGroup" class="collapse">
										<div id="eveningTimeSlots" class="time-slot-options" data-toggle="buttons"></div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Step 3: Additional Notes -->
					{if !empty($useNote)}
						<div class="form-group">
							<label for="pickupNote" class="control-label">{translate text="Note" isPublicFacing=true}</label>
							<textarea id="pickupNote" name="pickupNote" class="form-control" rows="3" maxlength="255"></textarea>
							<div class="help-block small">
								<span id="noteCharCount" class="pull-right">0/255</span>
								<i class="fas fa-info-circle" aria-hidden="true"></i>&nbsp;
								{translate text=$noteInstruction isPublicFacing=true isAdminEnteredData=true}
							</div>
						</div>
					{/if}

					<input type="hidden" name="patronId" id="patronId" value="{$patronId}">
					<input type="hidden" name="pickupLibrary" id="pickupLibrary" value="{$pickupLocation.code}">
				</form>
			</div>
		</div>
	{/if}
{else}
	<div class="alert alert-warning">
		<i class="fa fa-exclamation-triangle"></i> {translate text="Sorry, curbside pickups are not available at your library." isPublicFacing=true}
	</div>
{/if}

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
	{* Must be in the template file so it can target the calendar. *}
	.flatpickr-calendar {
		border: 1px solid #393737;
		box-shadow: 0 4px 12px rgba(0,0,0,0.15);
	}
</style>

<script type="text/javascript">
	$(document).ready(function() {
		AspenDiscovery.CurbsidePickup.curbsidePickupScheduler('{$pickupLocation.code}');
		const $collapseClass = $('.collapse');
		// Toggle icon rotation.
		$collapseClass.on('show.bs.collapse', function () {
			$(this).prev('.time-slot-heading').find('.toggle-icon i').addClass('rotated');
		});
		$collapseClass.on('hide.bs.collapse', function () {
			$(this).prev('.time-slot-heading').find('.toggle-icon i').removeClass('rotated');
		});

		// Ensure the user can only select one time slot across all sections.
		$(document).on('change', 'input[name="pickupTime"]', function() {
			if ($(this).is(':checked')) {
				// Uncheck all other time slots.
				const $inputPickupTime = $('input[name="pickupTime"]');
				$inputPickupTime.not(this).prop('checked', false);
				$inputPickupTime.not(this).closest('label').removeClass('active');
			}
		});

		// Limit the number of characters the user can input for notes.
		$(document).on('input', '#pickupNote', function() {
			const maxLength = 255;
			const currentLength = $(this).val().length;
			const $noteCharCount = $('#noteCharCount');
			$noteCharCount.text(currentLength + '/' + maxLength);

			if (currentLength >= maxLength) {
				$noteCharCount.addClass('text-danger');
			} else {
				$noteCharCount.removeClass('text-danger');
			}
		});
	});
</script>