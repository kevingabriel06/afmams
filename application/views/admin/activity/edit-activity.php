<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

<div class="card mb-3">
    <div class="card-body">
    <div class="row flex-between-center">
        <div class="col-md">
        <h5 class="mb-2 mb-md-0">Edit Activity</h5>
        </div>
    </div>
    </div>
</div>
          
<!-- CARD WITH STANDARDIZE IMAGE -->
<div class="card cover-image mb-3" id="coverContainer">
    <img id="coverPhoto" class="card-img-top" src="<?php echo base_url("assets/coverEvent/".$activity['activity_image']); ?> " alt="" />
</div>

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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("activityCreate");
        const dateStart = document.getElementById("date_start");
        const dateEnd = document.getElementById("date_end");
        const dateError = document.getElementById("date-error");
        const registrationDeadline = document.getElementById('registration-deadline').value;

        // Department and Organization elements
        const deptSelect = document.getElementById('dept');
        const orgSelect = document.getElementById('org');
        const deptError = document.getElementById('dept-error');
        const orgError = document.getElementById('org-error');

        // Time input elements
        const amIn = document.getElementById('am_in');
        const amOut = document.getElementById('am_out');
        const pmIn = document.getElementById('pm_in');
        const pmOut = document.getElementById('pm_out');

        // VALIDATION ON SUBMISSION
        form.addEventListener("submit", function (event) {
            let isValid = true;  // Declare isValid only once

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

            // VALIDATION FOR ORGANIZATION
            if (orgSelect.value === "") {
                orgError.style.display = "block";
                orgSelect.classList.add("is-invalid");
                event.preventDefault(); // Prevent form submission
            } else {
                orgError.style.display = "none";
                orgSelect.classList.remove("is-invalid");
            }

            // VALIDATION FOR DEPARTMENT
            if (deptSelect.value === "") {
                deptError.style.display = "block";
                deptSelect.classList.add("is-invalid");
            } else {
                deptError.style.display = "none";
                deptSelect.classList.remove("is-invalid");
            }

 
            // Registration deadline validation
            if (registrationDeadline) { // Only validate if there's a registration deadline
                const startDate = new Date(dateStart.value);  // Ensure startDate is defined before usage
                if (new Date(registrationDeadline) >= startDate) {
                    event.preventDefault();  // Prevent form submission
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
            field.addEventListener("input", function () {
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
            field.addEventListener("input", function () {
                if (field.value.trim()) {
                    field.classList.remove("is-invalid");
                    field.classList.add("is-valid");
                } else {
                    field.classList.remove("is-valid");
                    field.classList.add("is-invalid");
                }
            });
        });

        // Remove error when a valid option is selected
        orgSelect.addEventListener("change", function () {
            if (orgSelect.value !== "") {
                orgSelect.classList.remove("is-invalid");
                orgError.style.display = "none";
            }
        });

        // Remove error when a valid option is selected
        deptSelect.addEventListener("change", function () {
            if (deptSelect.value !== "") {
                deptSelect.classList.remove("is-invalid");
                deptSelect.style.display = "none";
            }
        });
    });
</script>

<div class="row g-0">
    <div class="card mt-3">
        <div class="card-header"> 
            <h5 class="mb-1">Activity Details</h5>
        </div>
        <div class="card-body bg-body-tertiary">
            <form id="activityCreate" class="row g-3 needs-validation dropzone dropzone-multiple p-0" data-dropzone="data-dropzone" enctype="multipart/form-data" novalidate>
                <!-- Activity Title -->
                <div class="col-12 mb-3">
                    <label class="form-label" for="activity-title">Activity Title <span style="color: red;">*</span></label>
                    <input class="form-control" id="activity-title" type="text" placeholder="Activity Title" name="title" value="<?php echo $activity['activity_title'];?>" required/>
                    <div class="invalid-feedback">Enter an activity title.</div>
                </div> 

                <div class="col-sm-6 mb-3">
                    <label class="form-label" for="date_start">Start Date <span style="color: red;">*</span></label>
                    <div class="input-group">
                        <input class="form-control datetimepicker" id="date_start" type="text" placeholder="yyyy-mm-dd" name="date_start"
                            pattern="\d{4}-\d{2}-\d{2}" aria-describedby="calendarHelp"
                            data-options='{"dateFormat":"Y-m-d","disableMobile":true, "minDate": "today"}' value="<?php echo $activity['start_date'];?>" required />
                        <span class="input-group-text" id="calendar-icon" title="Pick a date">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        <div class="invalid-feedback">Enter a valid start date.</div>
                    </div>
                </div>

                <!-- End Date -->
                <div class="col-sm-6 mb-3">
                    <label class="form-label" for="date_end">End Date <span style="color: red;">*</span></label>
                    <div class="input-group">
                        <input class="form-control datetimepicker" id="date_end" type="text" placeholder="yyyy-mm-dd" name="date_end" value="<?php echo $activity['end_date'];?>"required>
                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        <div class="invalid-feedback" id="date-error">End date must be greater than or equal to start date.</div>
                    </div>
                </div>

                <!-- REGISTRATION -->
                <div class="col-sm-6 mb-3">
                    <label class="form-label" for="registration-deadline">Registration Deadline</label>
                    <div class="input-group">
                        <input class="form-control datetimepicker" id="registration-deadline" type="text" placeholder="yyyy-mm-dd" name="registration_deadline" pattern="\d{4}-\d{2}-\d{2}" aria-describedby="calendarHelp" data-options='{"dateFormat":"Y-m-d","disableMobile":true, "minDate": "today"}' value="<?php echo $activity['registration_deadline'];?>"/>
                        <span class="input-group-text" id="calendar-icon" title="Pick a date"><i class="fas fa-calendar-alt"></i></span>
                    </div>
                    <div class="invalid-feedback" id="registration-deadline-feedback">Please enter a registration deadline before the start date.</div>
                </div>

                <div class="col-sm-6 mb-3">
                    <label class="form-label" for="registration-fee">Registration Fee</label>
                    <input class="form-control" id="registration-fee" type="text" placeholder="₱ 00.00" name="registration_fee" value="<?php echo $activity['registration_fee'];?>"/>
                </div>

                <?php if ($role == 'Officer'): ?>

                <!-- FOR ORGANIZER PART -->
                <?php if (!empty($department)): ?>
                <div class="col-sm-6 mb-3">
                    <label class="form-label" for="dept">Department</label>
                    <select class="form-control" id="dept" name="dept" onchange="toggleFields()" required>
                        <option value="0">Select a Department</option>
                        <option value="<?php echo $activity['dept_id']; ?>">
                            <?php echo $department->dept_name; ?>
                        </option>
                    </select>
                    <div id="dept-error" class="invalid-feedback" style="display: none;">Select a department.</div>
                </div>
                <?php endif; ?>

                <?php if (!empty($organization)): ?>
                <div class="col-sm-6 mb-3">
                    <label class="form-label" for="org">Organization</label>
                    <select class="form-control" id="org" name="org" onchange="toggleFields()" required>
                        <option value="">Select an Organization</option>
                            <option value="<?php echo $activity['org_id']; ?>">
                                <?php echo $organization->org_name; ?>
                            </option>
                    </select>
                    <div id="org-error" class="invalid-feedback" style="display: none;">Select an organization.</div>
                </div>
                <?php endif; ?>

                <?php elseif($role == 'Admin') :?>
                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="dept">Organizer</label>
                        <select class="form-control" id="dept" name="dept" onchange="toggleFields()">
                            <option value="0">Student Parliament</option>
                        </select>
                    </div>
                    <div class="col-sm-6 mb-3" hidden>
                        <label class="form-label" for="org">Organization</label>
                        <select class="form-control" id="org" name="org" onchange="toggleFields()">
                            <option value="0">Select an Organization</option>
                        </select>
                    </div>
                <?php endif ;?>

                <!-- SCHEDULE DETAILS -->
                <div class="card-header bg-body-tertiary d-flex justify-content-between">
                    <h5 class="mb-0">Schedule Details</h5>
                    <div>
                        <select class="btn btn-outline-primary btn-sm text-start" id="schedule_type" name="schedule_type" style="width: auto;">
                            <option value="" selected>Select Schedule Category</option>
                            <option value="whole_day">Whole Day</option>
                            <option value="half_day_am">Half Day - AM</option>
                            <option value="half_day_pm">Half Day - PM</option>
                        </select>
                    </div>
                </div>

                <div class="col-sm-6 mb-3" id="am_in_div">
                    <label class="form-label" for="am_in">Morning Time In <span style="color: blue;">*</span></label>
                    <div class="input-group">
                        <input class="form-control datetimepicker" id="am_in" type="text" placeholder="H:i" name="am_in" aria-describedby="timeHelp" data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"h:i K","disableMobile":true}' value="<?php echo $activity['am_in'];?>" />
                        <span class="input-group-text" id="time-icon" title="Pick a time"><i class="fas fa-clock"></i></span>
                        <div id="am_in_error" class="invalid-feedback" style="display: none;">Please provide both morning time in and time out between 1 AM and 1 PM.</div>
                    </div>
                </div>

                <div class="col-sm-6 mb-3" id="am_out_div">
                    <label class="form-label" for="am_out">Morning Time Out <span style="color: blue;">*</span></label>
                    <div class="input-group">
                        <input class="form-control datetimepicker" id="am_out" type="text" placeholder="H:i" name="am_out" aria-describedby="timeHelp" data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"h:i K","disableMobile":true}' value="<?php echo $activity['am_in'];?>" />
                        <span class="input-group-text" id="time-icon" title="Pick a time"><i class="fas fa-clock"></i></span>
                        <div id="am_out_error" class="invalid-feedback" style="display: none;">Morning time out must be later than morning time in.</div>
                    </div>
                </div>

                <div class="col-sm-6 mb-3" id="pm_in_div">
                    <label class="form-label" for="pm_in">Afternoon Time In <span style="color: blue;">*</span></label>
                    <div class="input-group">
                        <input class="form-control datetimepicker" id="pm_in" type="text" placeholder="H:i" name="pm_in" aria-describedby="timeHelp" data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"h:i K","disableMobile":true}' value="<?php echo $activity['pm_in'];?>" />
                        <span class="input-group-text" id="time-icon" title="Pick a time"><i class="fas fa-clock"></i></span>
                        <div id="pm_in_error" class="invalid-feedback" style="display: none;">Please provide both afternoon time in and time out between 12 PM and 1 AM.</div>
                    </div>
                </div>

                <div class="col-sm-6 mb-3" id="pm_out_div">
                    <label class="form-label" for="pm_out">Afternoon Time Out <span style="color: blue;">*</span></label>
                    <div class="input-group">
                        <input class="form-control datetimepicker" id="pm_out" type="text" placeholder="H:i" name="pm_out" aria-describedby="timeHelp" data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"h:i K","disableMobile":true}' value="<?php echo $activity['pm_out'];?>"/>
                        <span class="input-group-text" id="time-icon" title="Pick a time"><i class="fas fa-clock"></i></span>
                        <div id="pm_out_error" class="invalid-feedback" style="display: none;">Afternoon time out must be later than afternoon time in.</div>
                    </div>
                </div>

                <!-- Cut-off Times -->
                <div class="col-sm-6 mb-3" id="customFieldsAInC" style="display: none;">
                    <label class="form-label" for="am_inC">Morning Time In Cut-off <label style="color: blue;"> * </label></label>
                    <div class="input-group">
                        <input class="form-control datetimepicker" id="am_inC" type="text" placeholder="H:i" name="am_in_cut" data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"H:i","disableMobile":true}' value="<?php echo $activity['am_in_cut'];?>"/>
                        <span class="input-group-text" id="time-icon" title="Pick a time"><i class="fas fa-clock"></i></span>
                    </div>
                </div>

                <div class="col-sm-6 mb-3" id="customFieldsAOutC" style="display: none;">
                    <label class="form-label" for="am_outC">Morning Time Out Cut-off <label style="color: blue;"> * </label></label>
                    <div class="input-group">
                        <input class="form-control datetimepicker" id="am_outC" type="text" placeholder="H:i" name="am_out_cut" data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"H:i","disableMobile":true}' value="<?php echo $activity['am_out_cut'];?>"/>
                        <span class="input-group-text" id="time-icon" title="Pick a time"><i class="fas fa-clock"></i></span>
                    </div>
                </div>

                <div class="col-sm-6 mb-3" id="customFieldsPInC" style="display: none;">
                    <label class="form-label" for="pm_inC">Afternoon Time In Cut-off <label style="color: blue;"> * </label></label>
                    <div class="input-group">
                        <input class="form-control datetimepicker" id="pm_inC" type="text" placeholder="H:i" name="pm_in_cut" data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"H:i","disableMobile":true}' value="<?php echo $activity['pm_in_cut'];?>"/>
                        <span class="input-group-text" id="time-icon" title="Pick a time"><i class="fas fa-clock"></i></span>
                    </div>
                </div>

                <div class="col-sm-6 mb-3" id="customFieldsPOutC" style="display: none;">
                    <label class="form-label" for="pm_outC">Afternoon Time Out Cut-off <label style="color: blue;"> * </label></label>
                    <div class="input-group">
                        <input class="form-control datetimepicker" id="pm_outC" type="text" placeholder="H:i" name="pm_out_cut" data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"H:i","disableMobile":true}' value="<?php echo $activity['pm_out_cut'];?>"/>
                        <span class="input-group-text" id="time-icon" title="Pick a time"><i class="fas fa-clock"></i></span>
                    </div>
                </div>

                <button class="btn btn-falcon-default btn-sm me-2" type="button" id="showCustomFieldsBtn">
                    <span class="far fa-clock text-danger me-1"></span>Edit Cut-Off Time
                </button>

                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        // Get the button and custom fields containers
                        var showCustomFieldsBtn = document.getElementById("showCustomFieldsBtn");
                        var customFieldsContainer1 = document.getElementById("customFieldsAInC");
                        var customFieldsContainer2 = document.getElementById("customFieldsAOutC");
                        var customFieldsContainer3 = document.getElementById("customFieldsPInC");
                        var customFieldsContainer4 = document.getElementById("customFieldsPOutC");

                        // Add event listener to the button
                        showCustomFieldsBtn.addEventListener("click", function() {
                            // Toggle the visibility of the custom fields containers
                            if (customFieldsContainer1.style.display === "none") {
                                customFieldsContainer1.style.display = "block";
                                customFieldsContainer2.style.display = "block";
                                customFieldsContainer3.style.display = "block";
                                customFieldsContainer4.style.display = "block";
                            } else {
                                // customFieldsContainer1.style.display = "none";
                                // customFieldsContainer2.style.display = "none";
                                // customFieldsContainer3.style.display = "none";
                                // customFieldsContainer4.style.display = "none";
                            }
                        });
                    });
                </script>

                <div class="col-12">
                    <div class="form-text mt-0"><i> * Note: The default cut-off time for scheduling is 15 minutes, but it can be edited depending on the situation.</i></div>
                </div>

                <!-- FOR TIME VALIDATION -->
                <script>
                    // Helper function to convert time to minutes for easy comparison
                    function timeToMinutes(time) {
                        const timeParts = time.split(' '); // Time and period (AM/PM)
                        const [hours, minutes] = timeParts[0].split(':');
                        const period = timeParts[1]; // AM or PM

                        let totalMinutes = parseInt(hours) * 60 + parseInt(minutes);
                        if (period === "PM" && parseInt(hours) !== 12) {
                            totalMinutes += 12 * 60; // Add 12 hours if PM, except for 12 PM
                        } else if (period === "AM" && parseInt(hours) === 12) {
                            totalMinutes -= 12 * 60; // Subtract 12 hours if 12 AM
                        }

                        return totalMinutes;
                    }

                    // Helper function to check if time is within valid range
                    function isValidTime(time, rangeStart, rangeEnd) {
                        const timeMinutes = timeToMinutes(time);
                        return timeMinutes >= rangeStart && timeMinutes <= rangeEnd;
                    }

                    // Morning time validation
                    document.getElementById('am_in').addEventListener('change', function () {
                        const amIn = this.value;
                        const amOut = document.getElementById('am_out').value;

                        // Check if AM in time is valid within 1 AM to 1 PM
                        if (!isValidTime(amIn, 60, 780)) { // 1 AM to 1 PM (60 minutes to 780 minutes)
                            document.getElementById('am_in_error').style.display = 'block';
                        } else {
                            document.getElementById('am_in_error').style.display = 'none';
                        }

                        // Check if AM out time is later than AM in time
                        if (amIn && amOut && timeToMinutes(amIn) >= timeToMinutes(amOut)) {
                            document.getElementById('am_out_error').style.display = 'block';
                        } else {
                            document.getElementById('am_out_error').style.display = 'none';
                        }
                    });

                    document.getElementById('am_out').addEventListener('change', function () {
                        const amOut = this.value;
                        const amIn = document.getElementById('am_in').value;

                        // Check if AM out time is later than AM in time
                        if (amIn && amOut && timeToMinutes(amIn) >= timeToMinutes(amOut)) {
                            document.getElementById('am_out_error').style.display = 'block';
                        } else {
                            document.getElementById('am_out_error').style.display = 'none';
                        }
                    });

                    // Afternoon time validation
                    document.getElementById('pm_in').addEventListener('change', function () {
                        const pmIn = this.value;
                        const pmOut = document.getElementById('pm_out').value;

                        // Check if PM in time is valid within 12 PM to 1 AM
                        if (!isValidTime(pmIn, 720, 1440) && !isValidTime(pmIn, 0, 60)) { // 12 PM to 1 AM (720 minutes to 1440 minutes or 0 to 60 minutes)
                            document.getElementById('pm_in_error').style.display = 'block';
                        } else {
                            document.getElementById('pm_in_error').style.display = 'none';
                        }

                        // Check if PM out time is later than PM in time
                        if (pmIn && pmOut && timeToMinutes(pmIn) >= timeToMinutes(pmOut)) {
                            document.getElementById('pm_out_error').style.display = 'block';
                        } else {
                            document.getElementById('pm_out_error').style.display = 'none';
                        }
                    });

                    document.getElementById('pm_out').addEventListener('change', function () {
                        const pmOut = this.value;
                        const pmIn = document.getElementById('pm_in').value;

                        // Check if PM out time is later than PM in time
                        if (pmIn && pmOut && timeToMinutes(pmIn) >= timeToMinutes(pmOut)) {
                            document.getElementById('pm_out_error').style.display = 'block';
                        } else {
                            document.getElementById('pm_out_error').style.display = 'none';
                        }
                    });
                </script>

                <!-- JavaScript to filter the fields, clear the inputs, and apply required validation -->
                <script>
                    // Get dropdown and schedule input fields
                    const scheduleTypeDropdown = document.getElementById('schedule_type');
                    const amInDiv = document.getElementById('am_in_div');
                    const amOutDiv = document.getElementById('am_out_div');
                    const pmInDiv = document.getElementById('pm_in_div');
                    const pmOutDiv = document.getElementById('pm_out_div');
                    
                    // Get time input fields
                    const amInInput = document.getElementById('am_in');
                    const amOutInput = document.getElementById('am_out');
                    const pmInInput = document.getElementById('pm_in');
                    const pmOutInput = document.getElementById('pm_out');
                    
                    // Get error feedback elements
                    const amInError = document.getElementById('am_in_error');
                    const amOutError = document.getElementById('am_out_error');
                    const pmInError = document.getElementById('pm_in_error');
                    const pmOutError = document.getElementById('pm_out_error');

                    // Function to reset error messages and input fields
                    function resetFields() {
                        // Clear input values
                        amInInput.value = '';
                        amOutInput.value = '';
                        pmInInput.value = '';
                        pmOutInput.value = '';
                        
                        // Reset required attribute
                        amInInput.removeAttribute('required');
                        amOutInput.removeAttribute('required');
                        pmInInput.removeAttribute('required');
                        pmOutInput.removeAttribute('required');
                        
                        // Hide error messages
                        amInError.style.display = 'none';
                        amOutError.style.display = 'none';
                        pmInError.style.display = 'none';
                        pmOutError.style.display = 'none';
                    }

                    // Event listener for dropdown change
                    scheduleTypeDropdown.addEventListener('change', function() {
                        const selectedType = this.value;

                        // Reset fields and error messages
                        resetFields();

                        if (selectedType === 'half_day_am') {
                            // Half Day - AM: Show AM fields, hide PM fields, make AM fields required
                            pmInDiv.style.display = 'none';
                            pmOutDiv.style.display = 'none';
                            amInDiv.style.display = 'block';
                            amOutDiv.style.display = 'block';
                            amInInput.setAttribute('required', true);
                            amOutInput.setAttribute('required', true);
                        } else if (selectedType === 'half_day_pm') {
                            // Half Day - PM: Show PM fields, hide AM fields, make PM fields required
                            amInDiv.style.display = 'none';
                            amOutDiv.style.display = 'none';
                            pmInDiv.style.display = 'block';
                            pmOutDiv.style.display = 'block';
                            pmInInput.setAttribute('required', true);
                            pmOutInput.setAttribute('required', true);
                        } else {
                            // Whole Day: Show both AM and PM fields, make both AM and PM fields required
                            pmInDiv.style.display = 'block';
                            pmOutDiv.style.display = 'block';
                            amInDiv.style.display = 'block';
                            amOutDiv.style.display = 'block';
                            amInInput.setAttribute('required', true);
                            amOutInput.setAttribute('required', true);
                            pmInInput.setAttribute('required', true);
                            pmOutInput.setAttribute('required', true);
                        }
                    });
                </script>


                <div class="border-bottom border-dashed my-3"></div>
                <div class="col-12">
                    <label class="form-label" for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="6"><?php echo $activity['description']; ?></textarea>
                </div>


                <div class="border-bottom border-dashed my-3"></div>

                <div class="card-header">
                    <h5 class="mb-1">Fines Details</h5>
                </div>

                <div class="row gx-2">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="fines">Fines <label style="color: red;">* </label></label>
                        <input class="form-control" id="fines" type="text" placeholder="₱ 00.00" name="fines" required pattern="^\₱\s?\d+(?:,\d{3})*(?:\.\d{2})?$" value="<?php echo $activity['fines'];?>"/>
                        <div class="invalid-feedback">Enter a fines amount.</div>
                    </div>
                </div>
                <div class="form-text mt-0"><i> * Note: Input the fines amount per attendance.</i></div>
                <div class="border-bottom border-dashed my-3"></div>

                <!-- Upload Photos -->
                <div class="card-header">
                    <h5 class="mb-1">Upload Photos</h5>
                </div>
                <div class="fallback">
                    <input id="fileInput" type="file" name="image" required/>

                    <div class="invalid-feedback">Upload a cover image.</div>
                </div>
                <div class="dz-message" data-dz-message="data-dz-message">
                    <img class="me-2" src="<?php echo base_url(); ?>assets/img/icons/cloud-upload.svg" width="25" alt="" />
                    Drop your files here
                </div>

                
                <div class="dz-preview dz-preview-multiple m-0 p-0 d-flex flex-column" id="previewContainer"></div>

                    <!-- post privacy -->
                    <div class="border-bottom border-dashed my-3"></div>
                    <h6>Listing Privacy</h6>
                    <div class="mb-3 form-check">
                        <input class="form-check-input" id="customRadio4" type="radio" name="privacy" value="Public" checked="checked" />
                        <label class="form-label mb-0" for="customRadio4"> <strong>Public</strong></label>
                        <div class="form-text mt-0">Discoverable by anyone on City College of Calapan.</div>
                    </div>
                    <div class="mb-3 form-check">
                        <input class="form-check-input" id="customRadio5" type="radio" name="privacy" value="Private"/>
                        <label class="form-label mb-0" for="customRadio5"> <strong>Private</strong></label>
                        <div class="form-text mt-0">Accessible only by organization and department specified. </div>
                    </div>
                </div>

                <div class="border-bottom border-dashed my-3"></div>

                <div class="card-body">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-md">
                            <h5 class="mb-2 mb-md-0">Nice Job! You're almost done</h5>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-danger btn-sm me-2" type="button" onclick="$('#activityCreate').get(0).reset()">Cancel</button>
                            <!-- Save Button -->
                            <button class="btn btn-falcon-default btn-sm me-2" type="submit"> Save </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SCRIPT FOR INPUTTED NUMBERS -->
<script>
    document.getElementById('registration-fee').addEventListener('input', function(event) {
        let inputValue = event.target.value;
        
        // Allow only numbers and decimal points, and format it as ₱ 00.00
        inputValue = inputValue.replace(/[^0-9.]/g, ''); // Remove non-numeric characters
        if (inputValue.indexOf('.') !== -1) {
            // Ensure only one decimal point
            inputValue = inputValue.replace(/\.+$/, '');
        }
        
        // Format it as ₱ xx.xx (two decimal places)
        if (inputValue !== '') {
            let parts = inputValue.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Add commas for thousands
            inputValue = parts.join('.');
        }

        // Set the value back to the input field with the correct format
        event.target.value = inputValue;
    });

    document.getElementById('fines').addEventListener('input', function(event) {
        let inputValue = event.target.value;
        
        // Allow only numbers and decimal points, and format it as ₱ 00.00
        inputValue = inputValue.replace(/[^0-9.]/g, ''); // Remove non-numeric characters
        if (inputValue.indexOf('.') !== -1) {
            // Ensure only one decimal point
            inputValue = inputValue.replace(/\.+$/, '');
        }
        
        // Format it as ₱ xx.xx (two decimal places)
        if (inputValue !== '') {
            let parts = inputValue.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Add commas for thousands
            inputValue = parts.join('.');
        }

        // Set the value back to the input field with the correct format
        event.target.value = inputValue;
    });
</script>

<!-- SCRIPT FOR PREVIEW OF IMAGE AND OTHER FUNCTIONALITY -->
<script>
    document.getElementById("fileInput").addEventListener("change", function() {
        var files = this.files;
        var previewContainer = document.getElementById("previewContainer");
        previewContainer.innerHTML = ""; // Clear previous previews

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var reader = new FileReader();

            reader.onload = function(e) {
                var thumbnail = document.createElement("div");
                thumbnail.classList.add("d-flex", "media", "align-items-center", "mb-3", "pb-3", "btn-reveal-trigger");

                thumbnail.innerHTML = `
                    <img class="dz-image" src="${e.target.result}" alt="${file.name}" />
                    <div class="flex-1 d-flex flex-between-center">
                        <div>
                            <h6>${file.name}</h6>
                            <div class="d-flex align-items-center">
                                <p class="mb-0 fs-10 text-400 lh-1">${(file.size / (1024 * 1024)).toFixed(2)} MB</p>
                                <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress=""></span></div>
                            </div>
                        </div>
                        <div class="dropdown font-sans-serif">
                            <button class="btn btn-link text-600 btn-sm dropdown-toggle btn-reveal dropdown-caret-none" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="fas fa-ellipsis-h"></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end border">
                                <a class="dropdown-item remove-file" href="#!" data-dz-remove="data-dz-remove">Remove File</a>
                            </div>
                        </div>
                    </div>
                `;
                previewContainer.appendChild(thumbnail);
            };

            reader.readAsDataURL(file);
        }
    });

    // Event delegation to handle remove file
    document.getElementById("previewContainer").addEventListener("click", function(e) {
        if (e.target && e.target.classList.contains("remove-file")) {
            e.preventDefault();
            e.target.closest(".media").remove();

            // Clear file input value
            document.getElementById("fileInput").value = "";

            // If no files are left, restore default cover photo
            if (document.getElementById("fileInput").children.length === 0) {
                restoreDefaultCover();
            }
            
        }
    });

    document.getElementById("fileInput").addEventListener("change", function() {
        var file = this.files[0];
        var reader = new FileReader();

        reader.onload = function(e) {
            document.getElementById("coverPhoto").src = e.target.result;
        };

        if (file) {
            reader.readAsDataURL(file);
        }
    });

    function restoreDefaultCover() {
        const coverContainer = document.getElementById("coverContainer");
        if (coverContainer) {
            coverContainer.innerHTML = `
                <img id="coverPhoto" class="card-img-top" src="<?php echo base_url(); ?>assets/image/OIP.jpg" alt="Default Image" />
            `;
        }
    }
</script>

<script>
    $(document).ready(function(){
        // Set Alertify default position to top-right
        alertify.set('notifier', 'position', 'top-right');
        
        $('#activityCreate').on('submit', function(e){
            e.preventDefault();

            var formData = new FormData(this);

            console.log("Form data: ", formData);

            $.ajax({
                url: '<?php echo site_url("admin/create-activity/add"); ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response){
                    if (response.status == 'error') {
                        // Display error using Alertify (top-right)
                        alertify.error('Error: ' + response.errors);
                    } else if (response.status == 'success') {
                        // Display success using Alertify (top-right)
                        alertify.success('Success: ' + response.message);
                        
                        // Redirect after a short delay
                        setTimeout(function(){
                            window.location.href = response.redirect;
                        }, 1000);
                    }
                },
                error: function() {
                    alertify.error('Something went wrong, please try again.');
                }
            });
        });
    });
</script>



<!-- SCRIPT FOR DATES  -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Flatpickr on the input field
        const datePicker = flatpickr("#date_start", {
            dateFormat: "Y-m-d", // Date format
            disableMobile: true, // Use desktop calendar even on mobile
            minDate: "today", // Disable dates before today
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Flatpickr on the input field
        const datePicker = flatpickr("#date_end", {
            dateFormat: "Y-m-d", // Date format
            disableMobile: true, // Use desktop calendar even on mobile
            minDate: "today", // Disable dates before today
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Flatpickr on the input field
        const datePicker = flatpickr("#registration-deadline", {
            dateFormat: "Y-m-d", // Date format
            disableMobile: true, // Use desktop calendar even on mobile
            minDate: "today", // Disable dates before today
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const timePicker = flatpickr("#am_in", {
            enableTime: true, // Enable time selection
            noCalendar: true, // Disable calendar, show only time picker
            dateFormat: "h:i K", // Format as hour:minute
            disableMobile: true // Force desktop-style picker
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const timePicker = flatpickr("#am_out", {
            enableTime: true, // Enable time selection
            noCalendar: true, // Disable calendar, show only time picker
            dateFormat: "h:i K", // Format as hour:minute
            disableMobile: true // Force desktop-style picker
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const timePicker = flatpickr("#pm_in", {
            enableTime: true, // Enable time selection
            noCalendar: true, // Disable calendar, show only time picker
            dateFormat: "h:i K", // Format as hour:minute
            disableMobile: true // Force desktop-style picker
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const timePicker = flatpickr("#pm_out", {
            enableTime: true, // Enable time selection
            noCalendar: true, // Disable calendar, show only time picker
            dateFormat: "h:i K", // Format as hour:minute
            disableMobile: true // Force desktop-style picker
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize flatpickr for Morning Time In Cut-off
        flatpickr("#am_inC", {
            enableTime: true, // Enable time selection
            noCalendar: true, // Disable calendar, show only time picker
            dateFormat: "h:i K", // Format as hour:minute
            disableMobile: true // Force desktop-style picker
        });

        // Initialize flatpickr for Morning Time Out Cut-off
        flatpickr("#am_outC", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "h:i K",
            disableMobile: true
        });

        // Initialize flatpickr for Afternoon Time In Cut-off
        flatpickr("#pm_inC", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "h:i K",
            disableMobile: true
        });

        // Initialize flatpickr for Afternoon Time Out Cut-off
        flatpickr("#pm_outC", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "h:i K",
            disableMobile: true
        });
    });

    
</script>