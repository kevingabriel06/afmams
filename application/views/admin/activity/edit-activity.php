<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    <form id="activityEdit" class="row g-3 needs-validation dropzone dropzone-multiple p-0" data-dropzone="data-dropzone" enctype="multipart/form-data" novalidate>

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
                src="<?php echo base_url('assets/coverEvent/' . $activity['activity_image']); ?>"
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
                <div class="container mt-2">
                    <!-- Header Section -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Activity Details</h5>
                    </div>

                    <!-- Activity Title Section -->
                    <div class="mb-3">
                        <label class="form-label" for="activity-title">Activity Title <span class="text-danger">*</span></label>
                        <input class="form-control" id="activity-title" type="text" name="title" placeholder="Activity Title"
                            required value="<?php echo $activity['activity_title']; ?>" />
                        <div class="invalid-feedback">Enter an activity title.</div>
                    </div>

                    <!-- Description Section -->
                    <div class="mb-3">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="6"><?php echo $activity['description']; ?></textarea>
                    </div>

                    <div class="row">
                        <!-- Start Date Section -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="date_start">Start Date <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input class="form-control datetimepicker" id="date_start" type="text" name="date_start" placeholder="yyyy-mm-dd"
                                    pattern="\d{4}-\d{2}-\d{2}" aria-describedby="calendarHelp"
                                    data-options='{"dateFormat":"Y-m-d","disableMobile":true, "minDate": "today"}' required
                                    value="<?php echo $activity['start_date']; ?>" />
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                <div class="invalid-feedback">Enter a valid start date.</div>
                            </div>
                        </div>

                        <!-- End Date Section -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="date_end">End Date <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input class="form-control datetimepicker" id="date_end" type="text" name="date_end" placeholder="yyyy-mm-dd"
                                    pattern="\d{4}-\d{2}-\d{2}" aria-describedby="calendarHelp"
                                    data-options='{"dateFormat":"Y-m-d","disableMobile":true}' required
                                    value="<?php echo $activity['end_date']; ?>" />
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                <div class="invalid-feedback" id="date-error">End date must be greater than or equal to the start date.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="card mt-3">
            <div class="card-body">
                <!-- Schedule Details Header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Schedule Details</h5>
                    <button id="addTimeSlotButton" type="button" class="btn btn-primary">
                        <i class="fa fa-plus-circle"></i> Add Time Slot
                    </button>
                </div>

                <!-- Time Slots Container -->
                <div id="time_slots_container">
                    <div class="time-slot row g-3 align-items-center"></div>
                </div>

                <!-- Note Section -->
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

                    <div class="d-flex align-items-center">
                        <label class="form-label mb-0 me-3" for="registration-fee-switch">Has Registration Fee?</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="registration-fee-switch"
                                <?php if (!empty($activity['registration_fee']) && $activity['registration_fee'] != '0') echo 'checked'; ?> />
                            <label class="form-check-label mb-0" for="registration-fee-switch">Yes</label>
                        </div>
                    </div>
                </div>

                <!-- Registration Details Section -->
                <div id="registration-details" class="<?php echo (!empty($activity['registration_fee']) && $activity['registration_fee'] != '0') ? '' : 'd-none'; ?>">
                    <div class="row">
                        <!-- Left Column: Registration Deadline and Fee -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="registration-deadline">Registration Deadline</label>
                            <div class="input-group">
                                <input class="form-control datetimepicker" id="registration-deadline" type="text"
                                    placeholder="yyyy-mm-dd" name="registration_deadline"
                                    pattern="\d{4}-\d{2}-\d{2}" aria-describedby="calendarHelp"
                                    data-options='{"dateFormat":"Y-m-d","disableMobile":true, "minDate": "today"}'
                                    value="<?php echo $activity['registration_deadline']; ?>" />
                                <span class="input-group-text" id="calendar-icon" title="Pick a date">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                            </div>
                            <div class="invalid-feedback" id="registration-deadline-feedback">
                                Please enter a registration deadline before the start date.
                            </div>

                            <label class="form-label mt-3" for="registration-fee">Registration Fee</label>
                            <input class="form-control" id="registration-fee" type="text" placeholder="₱ 00.00"
                                name="registration_fee" value="<?php echo $activity['registration_fee']; ?>" />
                        </div>

                        <!-- Right Column: QR Code Upload & Preview -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="qr-upload">QR Code</label>
                            <div class="border p-2 d-flex justify-content-center align-items-center position-relative"
                                style="height: 150px; cursor: pointer;" id="qr-upload-container">

                                <!-- QR Code Preview -->
                                <img id="qr-preview"
                                    src="<?php echo !empty($activity['qr_code']) ? base_url('assets/qrcodeRegistration/' . $activity['qr_code']) : ''; ?>"
                                    alt="QR Code Preview"
                                    class="img-fluid position-absolute <?php echo empty($activity['qr_code']) ? 'd-none' : ''; ?>"
                                    style="max-height: 100%; max-width: 100%;" />

                                <!-- Plus Icon Placeholder -->
                                <i id="qr-placeholder" class="fas fa-plus text-muted position-absolute <?php echo !empty($activity['qr_code']) ? 'd-none' : ''; ?>"
                                    style="font-size: 2rem;"></i>
                            </div>

                            <input type="file" id="qr-upload" name="qrcode" accept="image/*" class="d-none" />

                            <p id="qr-placeholder-text" class="text-muted <?php echo !empty($activity['qr_code']) ? 'd-none' : ''; ?>">
                                No QR Code uploaded
                            </p>

                            <div class="invalid-feedback text-danger d-none" id="qr-error">
                                Invalid QR Code. Please upload a valid one.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- START FINES & PRIVACY SECTION -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <!-- Fines Section -->
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Fines Details</h5>
                        </div>
                        <!-- Fines Input -->
                        <div class="mb-3">
                            <label class="form-label" for="fines">Fines <span class="text-danger">*</span></label>
                            <input class="form-control" id="fines" type="text" placeholder="₱ 00.00" name="fines"
                                pattern="^\₱\s?\d+(?:,\d{3})*(?:\.\d{2})?$" required value="<?php echo $activity['fines']; ?>" />
                            <div class="invalid-feedback">Enter a valid fines amount.</div>
                        </div>
                        <div class="form-text"><i>* Note: Input the fines amount per attendance.</i></div>
                    </div>

                    <!-- Listing Privacy Section -->
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Listing Privacy</h5>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="audience">Audience</label>
                            <select class="form-control" id="audience" name="audience">
                                <option value="0" <?php echo ($activity['audience'] == 0) ? 'selected' : ''; ?>>All</option>
                                <?php foreach ($dept as $depts) : ?>
                                    <option value="<?php echo $depts->dept_id; ?>"
                                        <?php echo ($depts->dept_id == $activity['audience']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($depts->dept_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-text"><i>* Note: Select the target audience of the activity.</i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="row justify-content-between align-items-center">
                    <div class="col-md">
                        <h5 class="mb-2 mb-md-0">Nice Job! You're almost done</h5>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-danger btn-sm me-2" type="button" onclick="$('#activityCreate').get(0).reset()">Cancel</button>
                        <!-- Save Button -->
                        <button class="btn btn-primary btn-sm me-2" type="submit">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        // Toggle Registration Details Section based on checkbox
        $('#registration-fee-switch').on('change', function() {
            $('#registration-details').toggleClass('d-none', !$(this).is(':checked'));
        });

        // Handle QR Upload Logic and Click Event on Container
        $('#qr-upload').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#qr-preview').attr('src', e.target.result).removeClass('d-none');
                    $('#qr-placeholder, #qr-placeholder-text').addClass('d-none');
                };
                reader.readAsDataURL(file);
            }
        });

        $('#qr-upload-container').on('click', function() {
            $('#qr-upload').click();
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        const qrUpload = document.getElementById("qr-upload");
        const qrPreview = document.getElementById("qr-preview");
        const qrPlaceholder = document.getElementById("qr-placeholder");
        const qrError = document.getElementById("qr-error");

        // Store the initial QR code preview in case we need to restore it
        let originalQRPreviewSrc = qrPreview.src;

        qrUpload.addEventListener("change", function(event) {
            const file = event.target.files[0];

            if (file) {
                if (file.type.startsWith("image/")) {
                    // Temporarily hide the existing QR and placeholder
                    originalQRPreviewSrc = qrPreview.src; // Save the original QR preview
                    qrPreview.classList.add("d-none");
                    qrPlaceholder.classList.remove("d-none");

                    // SweetAlert confirmation BEFORE finalizing the change
                    Swal.fire({
                        title: 'Confirm Selection',
                        html: `Are you sure you want to upload <strong>${file.name}</strong>?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, upload it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // If confirmed, read and display the new file
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                qrPreview.src = e.target.result;
                                qrPreview.classList.remove("d-none");
                                qrPlaceholder.classList.add("d-none");
                                qrError.classList.add("d-none");

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Uploaded!',
                                    text: 'Your QR code has been successfully uploaded.',
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            };
                            reader.readAsDataURL(file); // Read file only AFTER confirmation
                        } else {
                            // If canceled, restore the previous QR code preview
                            qrPreview.src = originalQRPreviewSrc;
                            qrPreview.classList.remove("d-none");
                            qrPlaceholder.classList.add("d-none");
                            qrUpload.value = ""; // Reset the file input to avoid confusion

                            Swal.fire({
                                icon: 'info',
                                title: 'File selection canceled',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    });
                } else {
                    // File is not an image, show error
                    qrUpload.value = ""; // Reset file input
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid File',
                        text: 'Please upload a valid image file.',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            }
        });
    });






    document.addEventListener("DOMContentLoaded", function() {
        let timeSlotsContainer = document.getElementById("time_slots_container");

        // Function to add a time slot dynamically
        function addTimeSlot(startTime = "", startCut = "", endTime = "", endCut = "", session = "Morning", slotId = null) {
            let uniqueId = Date.now();
            let startId = `start_datetime_${uniqueId}`;
            let startCutId = `start_cutoff_${uniqueId}`;
            let endId = `end_datetime_${uniqueId}`;
            let endCutId = `end_cutoff_${uniqueId}`;

            let div = document.createElement("div");
            div.classList.add("time-slot", "row", "g-3", "align-items-center");
            if (slotId) div.setAttribute("data-id", slotId); // Store slot ID if available

            div.innerHTML = `
        <div class="row g-2 align-items-end">
            <!-- Timeslot ID (Hidden) -->
            <input type="hidden" name="timeslot_id[]" value="${slotId ?? ''}">

            <!-- Date & Time Start -->
            <div class="col-md-4">
                <label class="form-label">Date & Time Start:</label>
                <div class="input-group">
                    <input class="form-control datetimepicker start_datetime" id="${startId}" type="text"
                        placeholder="Select Date & Time" name="start_datetime[]" required value="${startTime}" />
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                </div>
                
                <label class="form-label">Date & Time End:</label>
                <div class="input-group">
                    <input class="form-control datetimepicker end_datetime" id="${endId}" type="text"
                        placeholder="Select Date & Time" name="end_datetime[]" required value="${endTime}" />
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                </div>
            </div>

            <!-- Cut-off Dates -->
            <div class="col-md-4">
                <label class="form-label">Start Cut-off:</label>
                <div class="input-group">
                    <input class="form-control datetimepicker start_cutoff" id="${startCutId}" type="text"
                        placeholder="Select Date & Time" name="start_cutoff[]" required value="${startCut}" />
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                </div>

                <label class="form-label">End Cut-off:</label>
                <div class="input-group">
                    <input class="form-control datetimepicker end_cutoff" id="${endCutId}" type="text"
                        placeholder="Select Date & Time" name="end_cutoff[]" required value="${endCut}" />
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                </div>
            </div>

            <!-- Session Type -->
            <div class="col-md-3">
                <label class="form-label">Session:</label>
                <select class="form-select session_type" name="session_type[]" required>
                    <option value="Morning" ${session === "Morning" ? "selected" : ""}>Morning</option>
                    <option value="Afternoon" ${session === "Afternoon" ? "selected" : ""}>Afternoon</option>
                    <option value="Evening" ${session === "Evening" ? "selected" : ""}>Evening</option>
                </select>
            </div>

            <!-- Remove Button -->
            <div class="col-md-1 d-flex justify-content-center">
                <button class="btn btn-danger d-flex align-items-center justify-content-center remove-slot" type="button">
                    <i class="fa fa-trash"></i>
                </button>
            </div>

            <!-- Separator -->
            <div class="border-bottom border-dashed my-3"></div>
        </div>
        `;

            timeSlotsContainer.appendChild(div);

            // Initialize Flatpickr for Date & Time fields
            flatpickr(`#${startId}`, {
                enableTime: true,
                dateFormat: "Y-m-d h:i K",
                time_24hr: false,
                defaultDate: startTime || null,
                disableMobile: true
            });
            flatpickr(`#${startCutId}`, {
                enableTime: true,
                dateFormat: "Y-m-d h:i K",
                time_24hr: false,
                defaultDate: startCut || null,
                disableMobile: true
            });
            flatpickr(`#${endId}`, {
                enableTime: true,
                dateFormat: "Y-m-d h:i K",
                time_24hr: false,
                defaultDate: endTime || null,
                disableMobile: true
            });
            flatpickr(`#${endCutId}`, {
                enableTime: true,
                dateFormat: "Y-m-d h:i K",
                time_24hr: false,
                defaultDate: endCut || null,
                disableMobile: true
            });
        }

        // Get schedules from PHP (JSON encoded)
        let schedules = <?php echo json_encode($schedules ?? []); ?>;
        let activity = <?php echo json_encode($activity ?? []); ?>;

        // Populate time slots from database
        if (Array.isArray(schedules) && schedules.length > 0) {
            schedules.forEach(schedule => {
                addTimeSlot(schedule.date_time_in, schedule.date_cut_in, schedule.date_time_out, schedule.date_cut_out, schedule.slot_name, schedule.timeslot_id);
            });
        } else {
            // Use default values if no schedule exists
            addTimeSlot(activity.date_time_in ?? "", activity.date_cut_in ?? "", activity.date_time_out ?? "", activity.date_cut_out ?? "", activity.slot_name ?? "Morning");
        }

        // Button to add new time slot manually
        document.getElementById("addTimeSlotButton").addEventListener("click", function() {
            addTimeSlot();
        });

        // Remove time slot with confirmation and database deletion
        timeSlotsContainer.addEventListener("click", function(event) {
            if (event.target.closest(".remove-slot")) {
                let slotElement = event.target.closest(".time-slot");
                let slotId = slotElement.getAttribute("data-id");
                let slots = document.querySelectorAll(".time-slot");

                if (slots.length > 1) {
                    Swal.fire({
                        title: "Confirm Deletion",
                        text: "Are you sure you want to remove this time slot?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Yes, remove it",
                        cancelButtonText: "Cancel",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (slotId) {
                                // Send an AJAX request to delete from the database
                                fetch(`<?= site_url('admin/delete-schedule/') ?>${slotId}`, {
                                        method: "DELETE",
                                        headers: {
                                            "Content-Type": "application/json",
                                        },
                                    })
                                    .then((response) => response.json())
                                    .then((data) => {
                                        if (data.success) {
                                            slotElement.remove();
                                            Swal.fire("Deleted!", "Time slot removed successfully.", "success");
                                        } else {
                                            Swal.fire("Error", "Failed to remove time slot.", "error");
                                        }
                                    })
                                    .catch((error) => {
                                        console.error("Error:", error);
                                        Swal.fire("Error", "An error occurred while deleting the time slot.", "error");
                                    });
                            } else {
                                slotElement.remove();
                                Swal.fire("Deleted!", "Time slot removed.", "success");
                            }
                        } else {
                            Swal.fire("Cancelled", "Time slot was not removed.", "info");
                        }
                    });
                } else {
                    Swal.fire("Warning", "At least one time slot is required.", "warning");
                }
            }
        });

    });
</script>

<script>
    $(document).ready(function() {
        // Handle Activity Edit Form Submission
        $('#activityEdit').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            var formData = new FormData(this);

            $.ajax({
                url: '<?php echo site_url('admin/edit-activity/update/') . $activity['activity_id']; ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'error') {
                        // Show error notification using SweetAlert2
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.errors,
                            showConfirmButton: true,
                            timer: 3000
                        });
                    } else if (response.status === 'success') {
                        // Show success notification using SweetAlert2
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        });

                        // Optional: Redirect after showing the success message
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 2000);
                    }
                },
                error: function() {
                    // Handle AJAX errors
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong, please try again!',
                        showConfirmButton: true
                    });
                }
            });
        });
    });
</script>





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
</script>