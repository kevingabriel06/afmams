<!-- CUSTOM CSS TO SET STADARDIZE -->
<style>
	#coverPhoto {
		width: 100%;
		height: 250px;
		object-fit: cover;
	}

	.card {
		width: 100%;
	}
</style>

<div class="row g-0">
	<form id="activityCreate" class="row g-3 needs-validation dropzone dropzone-multiple p-0" data-dropzone="data-dropzone" enctype="multipart/form-data" novalidate>

		<div class="card mb-3">
			<div class="card-body">
				<div class="row flex-between-center">
					<div class="col-md">
						<h5 class="mb-2 mb-md-0">Create Activity</h5>
					</div>
				</div>
			</div>
		</div>

		<!-- COVER PHOTO SECTION -->
		<div class="card-header position-relative text-center" style="max-width: 100%; overflow: hidden;">
			<!-- Cover Photo -->
			<img id="coverPhoto" class="img-fluid w-100 rounded"
				src="<?php echo base_url(); ?>assets/coverEvent/default.jpg"
				alt="Cover Photo"
				style="height: 250px; object-fit: cover;">

			<button id="removeCover" class="btn btn-danger position-absolute top-0 end-0 m-2 px-2 py-1 shadow-sm"
				type="button" style="display: none; border-radius: 50%; font-size: 16px; line-height: 1;">
				<i class="fas fa-times"></i>
			</button>

			<!-- Hidden File Input -->
			<input type="file" id="coverUpload" accept="image/*" class="d-none" name="coverUpload">

			<!-- Upload Button (Overlay at Top Left) -->
			<label for="coverUpload" class="btn btn-dark position-absolute top-0 start-0 m-3 px-3 py-2 shadow-sm"
				style="border-radius: 8px; font-size: 14px;">
				<i class="fas fa-camera"></i> Change activity photo
			</label>
		</div>

		<div class="card mt-3">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h5 class="mb-0">Activity Details</h5>
				</div>

				<!-- Activity Title -->
				<div class="mb-3">
					<label class="form-label" for="activity-title">
						Activity Title <span class="text-danger">*</span>
					</label>
					<input class="form-control" id="activity-title" type="text" placeholder="Activity Title" name="title" required />
					<div class="invalid-feedback">Enter an activity title.</div>
				</div>

				<!-- Description -->
				<div class="mb-3">
					<label class="form-label" for="description">Description</label>
					<textarea class="form-control" id="description" name="description" rows="6"></textarea>
				</div>

				<div class="row">
					<!-- Start Date -->
					<div class="col-md-6 mb-3">
						<label class="form-label" for="date_start">
							Start Date <span class="text-danger">*</span>
						</label>
						<div class="input-group">
							<input class="form-control datetimepicker" id="date_start" type="text" placeholder="yyyy-mm-dd" name="date_start"
								pattern="\d{4}-\d{2}-\d{2}" aria-describedby="calendarHelp"
								data-options='{"dateFormat":"Y-m-d","disableMobile":true, "minDate": "today"}' required />
							<span class="input-group-text" id="calendar-icon" title="Pick a date">
								<i class="fas fa-calendar-alt"></i>
							</span>
							<div class="invalid-feedback">Enter a valid start date.</div>
						</div>
					</div>

					<!-- End Date -->
					<div class="col-md-6 mb-3">
						<label class="form-label" for="date_end">
							End Date <span class="text-danger">*</span>
						</label>
						<div class="input-group">
							<input class="form-control datetimepicker" id="date_end" type="text" placeholder="yyyy-mm-dd" name="date_end"
								pattern="\d{4}-\d{2}-\d{2}" aria-describedby="calendarHelp"
								data-options='{"dateFormat":"Y-m-d","disableMobile":true}' required />
							<span class="input-group-text">
								<i class="fas fa-calendar-alt"></i>
							</span>
							<div class="invalid-feedback" id="date-error">End date must be greater than or equal to the start date.</div>
						</div>
					</div>
				</div>
			</div>
		</div>


		<div class="card mb-3">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h5 class="mb-0">Schedule Details</h5>
					<button id="addTimeSlotButton" type="button" class="btn btn-primary">
						<i class="fa fa-plus-circle"></i> Add Time Slot
					</button>
				</div>

				<div id="time_slots_container">

				</div>

				<div class="form-text mt-3">
					<i>* Note: The default cut-off time for scheduling is 15 minutes, but it can be edited depending on the situation.</i>
				</div>
			</div>
		</div>

		<!-- START REGISTRATION -->
		<div class="card mb-3">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h5 class="mb-0">Registration Details</h5>

					<div class="d-flex justify-content-between align-items-center">
						<label class="form-label mb-0 me-3" for="registration-fee-switch">Has Registration Fee?</label>
						<div class="form-check form-switch">
							<input class="form-check-input" type="checkbox" id="registration-fee-switch" />
							<label class="form-check-label mb-0" for="registration-fee-switch">Yes</label>
						</div>
					</div>
				</div>

				<!-- Registration Details Section (hidden by default) -->
				<div id="registration-details" class="d-none">
					<div class="row">
						<!-- Left Column: Registration Deadline and Fee -->
						<div class="col-md-6 mb-3">
							<label class="form-label" for="registration-deadline">Registration Deadline</label>
							<div class="input-group">
								<input class="form-control datetimepicker" id="registration-deadline" type="text"
									placeholder="yyyy-mm-dd" name="registration_deadline" pattern="\d{4}-\d{2}-\d{2}"
									aria-describedby="calendarHelp"
									data-options='{"dateFormat":"Y-m-d","disableMobile":true, "minDate": "today"}' />
								<span class="input-group-text" id="calendar-icon" title="Pick a date">
									<i class="fas fa-calendar-alt"></i>
								</span>
							</div>
							<div class="invalid-feedback" id="registration-deadline-feedback">
								Please enter a registration deadline before the start date.
							</div>

							<label class="form-label mt-3" for="registration-fee">Registration Fee</label>
							<input class="form-control" id="registration-fee" type="text" placeholder="₱ 00.00" name="registration_fee" />
						</div>

						<!-- Right Column: QR Code Upload -->
						<div class="col-md-6 mb-3">
							<label class="form-label" for="qr-upload">QR Code</label>
							<div class="border p-2 d-flex justify-content-center align-items-center"
								style="height: 150px; cursor: pointer;" id="qr-upload-container">
								<img id="qr-preview" src="" alt="QR Code Preview" class="img-fluid d-none" style="max-height: 100%;" />
								<i id="qr-placeholder" class="fas fa-plus text-muted" style="font-size: 2rem;"></i>
							</div>
							<input type="file" id="qr-upload" name="qrcode" accept="image/*" class="d-none" />
							<div class="invalid-feedback text-danger d-none" id="qr-error">
								Invalid QR Code. Please upload a valid one.
							</div>
						</div>
					</div>

					<div class="border-bottom border-dashed my-3"></div>
				</div>
			</div>
		</div>

		<!-- START FINES & PRIVACY SECTION -->
		<div class="card mb-3">
			<div class="card-body">
				<div class="row">
					<!-- Fines Section -->
					<div class="col-md-6">
						<h5 class="mb-3">Fines Details</h5>

						<!-- Fines Input -->
						<div class="mb-3">
							<label class="form-label" for="fines">Fines <span class="text-danger">*</span></label>
							<input class="form-control" id="fines" type="text" placeholder="₱ 00.00" name="fines"
								pattern="^\₱\s?\d+(?:,\d{3})*(?:\.\d{2})?$" required />
							<div class="invalid-feedback">Enter a valid fines amount.</div>
						</div>
						<div class="form-text">
							<i>* Note: Input the fines amount per attendance.</i>
						</div>
					</div>

					<!-- Listing Privacy Section -->
					<div class="col-md-6">
						<h5 class="mb-3">Listing Privacy</h5>
						<div class="mb-3">
							<label class="form-label" for="audienceDropdown">Audience</label>
							<div class="dropdown">
								<button class="form-select text-start" type="button" id="audienceDropdown" data-bs-toggle="dropdown" aria-expanded="false">
									Select Audience
								</button>
								<ul class="dropdown-menu w-100 p-3" style="max-height: 300px; overflow-y: auto; color: black;">
									<li>
										<div class="form-check">
											<input class="form-check-input" type="checkbox" id="selectAllAudience">
											<label class="form-check-label" for="selectAllAudience">All</label>
										</div>
									</li>
									<hr>
									<?php foreach ($dept as $depts) : ?>
										<li>
											<div class="form-check">
												<input class="form-check-input audience-checkbox" type="checkbox" name="audience[]" value="<?php echo $depts->dept_name; ?>" id="aud_<?php echo md5($depts->dept_name); ?>">
												<label class="form-check-label" for="aud_<?php echo md5($depts->dept_name); ?>">
													<?php echo $depts->dept_name; ?>
												</label>
											</div>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
						<div class="form-text">
							<i>* Note: Select the target audience of the activity.</i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- JS to handle "Select All" logic -->
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				const selectAllCheckbox = document.getElementById('selectAllAudience');
				const audienceCheckboxes = document.querySelectorAll('.audience-checkbox');

				selectAllCheckbox.addEventListener('change', function() {
					audienceCheckboxes.forEach(cb => cb.checked = this.checked);
				});

				audienceCheckboxes.forEach(cb => {
					cb.addEventListener('change', function() {
						if (!this.checked) {
							selectAllCheckbox.checked = false;
						} else {
							const allChecked = Array.from(audienceCheckboxes).every(cb => cb.checked);
							selectAllCheckbox.checked = allChecked;
						}
					});
				});
			});
		</script>


		<div class="card mb-3">
			<div class="card-body">
				<div class="row justify-content-between align-items-center">
					<div class="col-md-auto">
						<h5 class="mb-2 mb-md-0">Nice Job! You're almost done</h5>
					</div>
					<div class="col-auto">
						<!-- Cancel Button -->
						<button class="btn btn-danger btn-sm me-2" type="button" onclick="$('#activityCreate').get(0).reset()">Cancel</button>

						<!-- Save Button -->
						<button class="btn btn-primary btn-sm" type="submit">Save</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<script>
	// SCRIPT FOR THE FIELDS ACCEPTING NUMBERS ONLY
	function formatCurrencyInput(event) {
		let inputValue = event.target.value;

		// Allow only numbers and decimal points
		inputValue = inputValue.replace(/[^0-9.]/g, '');

		// Ensure only one decimal point
		let decimalCount = (inputValue.match(/\./g) || []).length;
		if (decimalCount > 1) {
			inputValue = inputValue.replace(/\.+$/, '');
		}

		// Format it with commas for thousands
		let parts = inputValue.split('.');
		parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

		// Ensure only two decimal places
		if (parts[1] !== undefined) {
			parts[1] = parts[1].substring(0, 2); // Limit to two decimal places
		}

		event.target.value = parts.join('.');
	}

	// Attach event listener to both input fields
	document.getElementById('registration-fee').addEventListener('input', formatCurrencyInput);
	document.getElementById('fines').addEventListener('input', formatCurrencyInput);

	// SCRIPT FOR DATE AND TIME
	document.addEventListener('DOMContentLoaded', function() {
		function initializeFlatpickr(selector) {
			flatpickr(selector, {
				dateFormat: "Y-m-d", // Date format
				disableMobile: true, // Use desktop calendar even on mobile
				minDate: "today", // Disable dates before today
			});
		}

		// Initialize Flatpickr on multiple input fields
		["#date_start", "#date_end", "#registration-deadline"].forEach(initializeFlatpickr);
	});

	// SCRIPT FOR VALIDATION
	document.addEventListener("DOMContentLoaded", function() {
		const form = document.getElementById("activityCreate");
		const dateStart = document.getElementById("date_start");
		const dateEnd = document.getElementById("date_end");
		const dateError = document.getElementById("date-error");
		const registrationDeadline = document.getElementById('registration-deadline').value;

		// VALIDATION ON SUBMISSION
		form.addEventListener("submit", function(event) {
			let isValid = true; // Declare isValid only once

			// Get all required fields
			const requiredFields = form.querySelectorAll("[required]");

			requiredFields.forEach(field => {
				if (!field.value.trim()) {
					field.classList.add("is-invalid");
					field.classList.remove("is-valid");
					isValid = false;
				} else {
					field.classList.remove("is-invalid");
					field.classList.add("is-valid");
				}
			});

			// Validate End Date >= Start Date
			if (dateStart.value && dateEnd.value) {
				const startDate = new Date(dateStart.value);
				const endDate = new Date(dateEnd.value);

				if (endDate < startDate) {
					dateEnd.classList.add("is-invalid");
					dateError.textContent = "End date must be greater than or equal to start date.";
					isValid = false;
				} else {
					dateEnd.classList.remove("is-invalid");
					dateEnd.classList.add("is-valid");
				}
			}

			// Registration deadline validation
			if (registrationDeadline) { // Only validate if there's a registration deadline
				const startDate = new Date(dateStart.value); // Ensure startDate is defined before usage
				if (new Date(registrationDeadline) >= startDate) {
					event.preventDefault(); // Prevent form submission
					document.getElementById('registration-deadline').classList.add('is-invalid');
					document.getElementById('registration-deadline-feedback').style.display = 'block';
					isValid = false;
				} else {
					document.getElementById('registration-deadline').classList.remove('is-invalid');
					document.getElementById('registration-deadline-feedback').style.display = 'none';
				}
			}

			// Prevent form submission if any validation fails
			if (!isValid) {
				event.preventDefault();
				event.stopPropagation();
			}
		});

		// Remove error when user starts typing or changes the selection
		form.querySelectorAll("[required]").forEach(field => {
			field.addEventListener("input", function() {
				if (field.value.trim()) {
					field.classList.remove("is-invalid");
					field.classList.add("is-valid");
				} else {
					field.classList.remove("is-valid");
					field.classList.add("is-invalid");
				}
			});
		});

		// Remove error when user enters a valid date
		[dateStart, dateEnd].forEach(field => {
			field.addEventListener("input", function() {
				if (field.value.trim()) {
					field.classList.remove("is-invalid");
					field.classList.add("is-valid");
				} else {
					field.classList.remove("is-valid");
					field.classList.add("is-invalid");
				}
			});
		});
	});

	// FOR SCHEDULE FUNCTIONALITY
	document.addEventListener("DOMContentLoaded", function() {
		// Add a new time slot dynamically
		function addTimeSlot() {
			const container = document.getElementById("time_slots_container");
			const uniqueId = Date.now();
			const startId = `start_datetime_${uniqueId}`;
			const endId = `end_datetime_${uniqueId}`;

			// Create time slot row
			const div = document.createElement("div");
			div.classList.add("time-slot", "row", "g-3", "align-items-center");
			div.innerHTML = `
            <div class="row g-3 align-items-end">
                <!-- Date & Time Start -->
                <div class="col-md-4">
                    <label class="form-label">Date & Time Start:</label>
                    <div class="input-group">
                        <input type="text" class="form-control datetimepicker start_datetime" 
                            id="${startId}" placeholder="Select Date & Time" name="start_datetime[]" required />
                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    </div>
                </div>

                <!-- Date & Time End -->
                <div class="col-md-4">
                    <label class="form-label">Date & Time End:</label>
                    <div class="input-group">
                        <input type="text" class="form-control datetimepicker end_datetime" 
                            id="${endId}" placeholder="Select Date & Time" name="end_datetime[]" required />
                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    </div>
                </div>

                <!-- Session Type -->
                <div class="col-md-3">
                    <label class="form-label">Session:</label>
                    <select class="form-select session_type" name="session_type[]" required>
                        <option value="Morning">Morning</option>
                        <option value="Afternoon">Afternoon</option>
                        <option value="Evening">Evening</option>
                    </select>
                </div>

                <!-- Remove Button -->
                <div class="col-md-1 d-flex justify-content-center">
                    <button type="button" class="btn btn-danger remove-slot">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>`;

			container.appendChild(div);

			initializeFlatpickr(startId, endId);
		}

		// Initialize flatpickr with min/max date constraints
		function initializeFlatpickr(startId, endId) {
			const dateStartInput = document.getElementById("date_start");
			const dateEndInput = document.getElementById("date_end");

			const timeStart = flatpickr(`#${startId}`, {
				enableTime: true,
				dateFormat: "Y-m-d h:i K",
				disableMobile: true,
				onOpen: applyDateLimits,
				onChange: applyDateLimits
			});

			const timeEnd = flatpickr(`#${endId}`, {
				enableTime: true,
				dateFormat: "Y-m-d h:i K",
				disableMobile: true,
				onOpen: applyDateLimits,
				onChange: applyDateLimits
			});

			function applyDateLimits() {
				const minDate = dateStartInput.value || "today";
				const maxDate = dateEndInput.value || null;

				timeStart.set("minDate", minDate);
				timeStart.set("maxDate", maxDate);
				timeEnd.set("minDate", timeStart.input.value || minDate);
				timeEnd.set("maxDate", maxDate);
			}

			// Apply constraints on page load
			applyDateLimits();

			dateStartInput.addEventListener("input", applyDateLimits);
			dateEndInput.addEventListener("input", applyDateLimits);
		}

		function removeTimeSlot(event) {
			const button = event.target.closest(".remove-slot"); // More specific targeting
			if (button) {
				const slots = document.querySelectorAll(".time-slot");
				if (slots.length > 1) {
					// Confirmation prompt before removing the time slot
					Swal.fire({
						title: 'Are you sure?',
						text: "Do you want to remove this time slot?",
						icon: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Yes, remove it!'
					}).then((result) => {
						if (result.isConfirmed) {
							button.closest(".time-slot").remove(); // Remove slot if confirmed
							Swal.fire(
								'Removed!',
								'The time slot has been removed.',
								'success'
							);
						}
					});
				} else {
					// Warning if trying to remove the last slot
					Swal.fire({
						icon: 'warning',
						title: 'At least one time slot is required!',
						text: 'You must keep at least one time slot.',
						confirmButtonColor: '#3085d6',
					});
				}
			}
		}



		// Attach event listeners
		document.getElementById("addTimeSlotButton").addEventListener("click", addTimeSlot);
		document.getElementById("time_slots_container").addEventListener("click", removeTimeSlot);
	});


	// SCRIPT FOR COVER PHOTO UPLOAD & REMOVE
	document.addEventListener('DOMContentLoaded', function() {
		const coverPhoto = document.getElementById('coverPhoto');
		const coverUpload = document.getElementById('coverUpload');
		const removeCover = document.getElementById('removeCover');

		// Default image (in case of reset)
		const defaultImage = "<?php echo base_url(); ?>assets/image/OIP.jpg";

		// Function to handle image upload
		coverUpload.addEventListener('change', function(event) {
			const file = event.target.files[0];
			if (file) {
				const reader = new FileReader();
				reader.onload = function(e) {
					coverPhoto.src = e.target.result;
					removeCover.style.display = 'block'; // Show remove button
				};
				reader.readAsDataURL(file);
			}
		});

		// Function to remove image
		removeCover.addEventListener('click', function() {
			coverPhoto.src = defaultImage; // Reset to default image
			coverUpload.value = ""; // Clear file input
			removeCover.style.display = 'none'; // Hide remove button
		});

		// Show remove button if a custom image is loaded
		if (coverPhoto.src !== defaultImage) {
			removeCover.style.display = 'block';
		}
	});

	// SCRIPT FOR THE QR REGISTRATION
	document.addEventListener("DOMContentLoaded", function() {
		const uploadContainer = document.getElementById("qr-upload-container");
		const uploadInput = document.getElementById("qr-upload");

		// Trigger file upload when clicking on the preview area
		uploadContainer.addEventListener("click", function() {
			uploadInput.click();
		});

		// Preview the uploaded QR code
		uploadInput.addEventListener("change", function(event) {
			const file = event.target.files[0];
			if (file) {
				const reader = new FileReader();
				reader.onload = function(e) {
					document.getElementById("qr-preview").src = e.target.result;
					document.getElementById("qr-preview").classList.remove("d-none");
					document.getElementById("qr-placeholder").classList.add("d-none");
				};
				reader.readAsDataURL(file);
			}
		});
	});

	// SCRIPT FOR THE REGISTRATION
	document.addEventListener("DOMContentLoaded", function() {
		const feeSwitch = document.getElementById("registration-fee-switch");
		const feeDetails = document.getElementById("registration-details");

		// Show/hide registration details when switch is toggled
		feeSwitch.addEventListener("change", function() {
			if (feeSwitch.checked) {
				feeDetails.classList.remove("d-none");
			} else {
				feeDetails.classList.add("d-none");
			}
		});
	});

	// INSERTING ACTIVITY
	$(document).ready(function() {
		// Initialize the form submission process
		$('#activityCreate').on('submit', function(e) {
			e.preventDefault(); // Prevent default form submission behavior

			var formData = new FormData(this); // Collect form data

			$.ajax({
				url: '<?php echo site_url("admin/create-activity/add"); ?>',
				type: 'POST',
				data: formData,
				contentType: false,
				processData: false,
				dataType: 'json',
				success: function(response) {
					if (response.status === 'error') {
						// Display error using SweetAlert
						Swal.fire({
							icon: 'error',
							title: 'Error!',
							html: response.errors,
							confirmButtonColor: '#d33',
						});
					} else if (response.status === 'success') {
						// Display success using SweetAlert
						Swal.fire({
							icon: 'success',
							title: 'Success!',
							text: response.message,
							confirmButtonColor: '#3085d6',
						}).then(() => {
							// Redirect after clicking 'OK' on the alert
							window.location.href = response.redirect;
						});
					}
				},
				error: function() {
					// Handle unexpected errors with SweetAlert
					Swal.fire({
						icon: 'error',
						title: 'Oops!',
						text: 'Something went wrong, please try again.',
						confirmButtonColor: '#d33',
					});
				}
			});
		});
	});
</script>