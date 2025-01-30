<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="card mb-3">
    <div class="card-body">
    <div class="row flex-between-center">
        <div class="col-md">
        <h5 class="mb-2 mb-md-0">Create Activity</h5>
        </div>
    </div>
    </div>
</div>
          
<!-- Card with Standard Size for Image -->
<div class="card cover-image mb-3" id="coverContainer">
    <img id="coverPhoto" class="card-img-top" src="<?php echo base_url(); ?>assets/image/OIP.jpg " alt="" />
</div>

<!-- Custom CSS to Set Standard Size -->
<style>
    /* Set fixed size for the image */
    #coverPhoto {
        width: 100%; /* Make the image width fill the container */
        height: 250px; /* Set a fixed height */
        object-fit: cover; /* Ensure the image covers the area without distortion */
    }

    /* Optional: Set specific dimensions for the card if necessary */
    .card {
        width: 100%; /* You can adjust the width of the card */
    }
</style>

<div class="row g-0">
    <div id="messages"></div>
        <div class="card mt-3">
            <div class="card-header"> 
                <h5 class="mb-1">Activity Details</h5>
            </div>
            <div class="card-body bg-body-tertiary">
                <form id="activityCreate" class="row g-3 needs-validation dropzone dropzone-multiple p-0" data-dropzone="data-dropzone" enctype="multipart/form-data">
                    <!-- activity details -->
                    <div class="row gx-2">
                        <div class="col-12 mb-3">
                        <label class="form-label" for="activity-title">Activity Title <label style="color: red;"> * </label> </label>
                        <input class="form-control" id="activity-title" type="text" placeholder="Activity Title" name="title"/>
                    </div> 
                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="date_start">Start Date <span style="color: red;">*</span></label>
                        <div class="input-group"><input class="form-control datetimepicker" id="date_start" type="text" placeholder="yyyy-mm-dd" name="date_start" pattern="\d{4}-\d{2}-\d{2}" aria-describedby="calendarHelp" data-options='{"dateFormat":"Y-m-d","disableMobile":true, "minDate": "today"}' /><span class="input-group-text" id="calendar-icon" title="Pick a date"><i class="fas fa-calendar-alt"></i></span></div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="date_end">End Date <span style="color: red;">*</span></label>
                        <div class="input-group"><input class="form-control datetimepicker" id="date_end" type="text" placeholder="yyyy-mm-dd" name="date_end" pattern="\d{4}-\d{2}-\d{2}" aria-describedby="calendarHelp" data-options='{"dateFormat":"Y-m-d","disableMobile":true, "minDate": "today"}' /><span class="input-group-text" id="calendar-icon" title="Pick a date"><i class="fas fa-calendar-alt"></i></span></div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="registration-deadline">Registration Deadline</label>
                        <div class="input-group"><input class="form-control datetimepicker" id="registration-deadline" type="text" placeholder="yyyy-mm-dd" name="registration_deadline" pattern="\d{4}-\d{2}-\d{2}" aria-describedby="calendarHelp" data-options='{"dateFormat":"Y-m-d","disableMobile":true, "minDate": "today"}' /><span class="input-group-text" id="calendar-icon" title="Pick a date"><i class="fas fa-calendar-alt"></i></span></div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="registration-fee">Registration Fee</label>
                        <input class="form-control" id="registration-fee" type="text" placeholder="₱ 00.00" name="registration_fee"/>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="department">Department</label>
                        <select class="form-control" id="dept" name="dept" onchange="toggleFields()">
                            <option value="">Select a Department</option>
                            <option value="HR">Human Resources</option>
                            <option value="Finance">Finance</option>
                            <option value="IT">Information Technology</option>
                            <option value="Marketing">Marketing</option>
                        </select>
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="org">Organization</label>
                        <select class="form-control" id="org" name="org" onchange="toggleFields()">
                            <option value="">Select an Organization</option>
                            <option value="Org1">Organization 1</option>
                            <option value="Org2">Organization 2</option>
                            <option value="Org3">Organization 3</option>
                            <option value="Org4">Organization 4</option>
                        </select>
                    </div>
                    <script>
                        // Function to enable/disable the Department and Organization fields
                        function toggleFields() {
                            var dept = document.getElementById("dept");
                            var org = document.getElementById("org");

                            // If a Department is selected, disable the Organization field
                            if (dept.value) {
                                org.disabled = true;
                            } else {
                                org.disabled = false;
                            }

                            // If an Organization is selected, disable the Department field
                            if (org.value) {
                                dept.disabled = true;
                            } else {
                                dept.disabled = false;
                            }
                        }

                        // Optional: To reset fields when either dropdown is set to the default "Select" option
                        function resetFields() {
                            var dept = document.getElementById("dept");
                            var org = document.getElementById("org");

                            if (!dept.value) {
                                org.disabled = false;
                            }

                            if (!org.value) {
                                dept.disabled = false;
                            }
                        }

                        // Listen for changes and reset both fields when necessary
                        document.getElementById("dept").addEventListener("change", resetFields);
                        document.getElementById("org").addEventListener("change", resetFields);
                    </script>

                    <div class="col-12">
                        <div class="form-text mt-0"><i> * Note: Choose the organization/department that is the main proponent.</i></div>
                        <div class="border-bottom border-dashed my-3"></div>
                    </div>

                    

                    <!-- script for the inpputted numbers only -->
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
                    </script>
                    
                    <!-- schedule details -->
                    <div class="card-header">
                        <h5 class="mb-1">Schedule Details</h5>
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="am_in"> Morning Time In <span style="color: blue;">*</span></label>
                        <div class="input-group"> <input class="form-control datetimepicker" id="am_in" type="text" placeholder="H:i" name="am_in" aria-describedby="timeHelp" data-options='{"enableTime":true,"noCalendar":true,"dateFormat":""h:i K"","disableMobile":true}' /><span class="input-group-text" id="time-icon" title="Pick a time"><i class="fas fa-clock"></i></span></div>
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="am_out"> Morning Time Out <span style="color: blue;">*</span></label>
                        <div class="input-group"> <input class="form-control datetimepicker" id="am_out" type="text" placeholder="H:i" name="am_out" aria-describedby="timeHelp" data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"h:i K","disableMobile":true}' /><span class="input-group-text" id="time-icon" title="Pick a time"><i class="fas fa-clock"></i></span></div>
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="pm_in"> Afternoon Time In <span style="color: blue;">*</span></label>
                        <div class="input-group"> <input class="form-control datetimepicker" id="pm_in" type="text" placeholder="H:i" name="pm_in" aria-describedby="timeHelp" data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"h:i K","disableMobile":true}' /><span class="input-group-text" id="time-icon" title="Pick a time"><i class="fas fa-clock"></i></span></div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="pm_out"> Afternoon Time Out <span style="color: blue;">*</span></label>
                        <div class="input-group"> <input class="form-control datetimepicker" id="pm_out" type="text" placeholder="H:i" name="pm_out" aria-describedby="timeHelp" data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"h:i K","disableMobile":true}' /><span class="input-group-text" id="time-icon" title="Pick a time"><i class="fas fa-clock"></i></span></div>
                    </div>
                    <div class="col-12">
                        <div class="form-text mt-0"><i> * Note: The default cut-off time for scheduling is 15 minutes, but it can be edited depending on the situation.</i></div>
                        <div class="border-bottom border-dashed my-3"></div>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="6"></textarea>
                    </div>

                    <div class="border-bottom border-dashed my-3"></div>

                    <!-- upload photos -->
                    <div class="card-header">
                        <h5 class="mb-1">Upload Photos</h5>
                    </div>
                    <div class="fallback">
                        <input id="fileInput" type="file" name="image" />
                    </div>
                    <div class="dz-message" data-dz-message="data-dz-message">
                        <img class="me-2" src="<?php echo base_url() ; ?>assets/img/icons/cloud-upload.svg " width="25" alt="" />
                        Drop your files here
                    </div>
                    <div class="dz-preview dz-preview-multiple m-0 p-0 d-flex flex-column" id="previewContainer"></div>

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
                    </script>

                    <!-- post privacy -->
                    <div class="border-bottom border-dashed my-3"></div>
                    <h6>Listing Privacy</h6>
                    <div class="mb-3 form-check">
                        <input class="form-check-input" id="customRadio4" type="radio" name="privacy" value="public" checked="checked" />
                        <label class="form-label mb-0" for="customRadio4"> <strong>Public</strong></label>
                        <div class="form-text mt-0">Discoverable by anyone on City College of Calapan.</div>
                    </div>
                    <div class="mb-3 form-check">
                        <input class="form-check-input" id="customRadio5" type="radio" name="privacy" value="private"/>
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
                            <button class="btn btn-falcon-default btn-sm me-2" type="button" data-bs-toggle="modal" data-bs-target="#confirmationModal">
                                Save
                            </button>
                            <!-- Confirmation Modal -->
                            <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="confirmationModalLabel">Confirm Submission</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure all the information is correct?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary" form="activityCreate">Yes, Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    
     $(document).ready(function(){
        $('#activityCreate').on('submit', function(e){
            e.preventDefault();

            var formData = new FormData(this);

            console.log("Form data: ", formData);
            
            $.ajax({
                url: '<?php echo site_url("create-activity/add"); ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response){
                    if (response.status == 'error'){
                        $('#messages').html('<div class="alert alert-danger">' + response.errors + '</div>');
                    } else if (response.status == 'success'){
                        $('#messages').html('<div class="alert alert-success">' + response.message + '</div>');
                        setTimeout(function(){
                            window.location.href = response.redirect;
                        }, 1000);
                    }
                }
            });
        });
    });

    //supplying data
    // $('.edit_type').click(function(){
    //     var cat = $('#roomtypeForm');
    //     cat.get(0).reset();
    //     cat.find("[name='id']").val($(this).attr('data-id'));
    //     cat.find("[name='name']").val($(this).attr('data-name'));
    //     cat.find("[name='price']").val($(this).attr('data-price'));
    //     cat.find("#cimg").attr('src','<?php echo base_url('assets/uploadImg/'); ?>'+$(this).attr('data-cover_img'));
    // });

    // function confirmDelete(url) {
    //     if (confirm("Are you sure you want to delete this Room Type?")) {
    //         $.ajax({
    //             url: url,
    //             method: 'POST',
    //             dataType: 'json', // Ensure the response is treated as JSON
    //             success: function(response) {
    //                 if (response.status === 'error') {
    //                     $('#messages').html('<div class="alert alert-danger">' + response.errors + '</div>');
    //                 } else if (response.status === 'success') {
    //                     $('#messages').html('<div class="alert alert-success">' + response.message + '</div>');
    //                     setTimeout(function() {
    //                         window.location.href = response.redirect;
    //                     }, 1000);
    //                 }
    //             },
    //             error: function(xhr, status, error) {
    //                 $('#messages').html('<div class="alert alert-danger">An error occurred: ' + error + '</div>');
    //             }
    //         });
    //     }
    // }
</script>


<!-- script for the calendars -->
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

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

    
</script>