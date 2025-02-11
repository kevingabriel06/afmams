<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<!-- Alertify CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs/build/css/alertify.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs/build/css/themes/default.min.css"/>
<!-- Alertify JS -->
<script src="https://cdn.jsdelivr.net/npm/alertifyjs/build/alertify.js"></script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<!-- Main Container -->
<div class="container">
    <div class="card mb-3">
    


		<div class="card-header">
			<div class="d-flex align-items-center">
				<h5 class="mb-0 font-weight-bold">Excuse Application</h5>
			</div>
		</div>
		<div class="border"></div>

        <!-- Excuse Application Form -->
        <div class="card">
            <div class="card-body">
			<form id="excuseForm" action="<?php echo site_url('student/excuse-application/submit'); ?>" method="POST" enctype="multipart/form-data">
    <!-- Name Fields -->
	   <input type="hidden" name="student_id" value="<?php echo $this->session->userdata('student_id'); ?>">
    <!-- <div class="row mb-3">
		
        <input type="hidden" name="student_id" value="<php echo $this->session->userdata('student_id'); ?>">
        <div class="col-md-6">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First name" value="<?php echo isset($first_name) ? $first_name : ''; ?>" required>
        </div>
        <div class="col-md-6">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last name" value="<?php echo isset($last_name) ? $last_name : ''; ?>" required>
        </div>
    </div> -->

    <!-- Department -->
    <!-- <div class="row mb-3">
        <div class="col-md-6">
            <label for="department_name" class="form-label">Department</label>
            <input type="text" class="form-control" id="department_name" name="department_name" placeholder="Department" value="<?php echo isset($department_name) ? $department_name : ''; ?>" readonly required>
        </div>
    </div> -->

                    <!-- Activity Selection -->
                    <h5 class="card-title font-weight-bold mb-3">Send a Message</h5>
                    <div class="row mb-3">
                        <div class="col-md-12">
						<label for="activitySelect" class="form-label">Activity</label>
						<select class="form-select" id="activitySelect" name="activity_id" required> <!-- Corrected name -->
							<option value="" selected disabled>Select Activity</option>
							<?php foreach ($activities as $activity): ?>
								<option value="<?php echo $activity['activity_id']; ?>"><?php echo $activity['activity_title']; ?></option>
							<?php endforeach; ?>
						</select>

                        </div>
                    </div>

                    <!-- Subject & Message -->
                    <div class="mb-3">
                        <label for="emailSubject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="emailSubject" name="emailSubject" placeholder="Subject" >
                    </div>
                    <div class="mb-3">
                        <label for="emailBody" class="form-label">Message</label>
                        <textarea class="form-control" id="emailBody" name="emailBody" rows="5" placeholder="Type your message here..." ></textarea>
                    </div>

                    <!-- File Upload Section -->
					<div class="mb-3">
						<label class="form-label">Upload Image or Document</label>
						<div class="d-flex align-items-center">
							<!-- Updated File Input to Accept Multiple Files -->
							<input type="file" class="d-none" id="fileUpload" name="fileUpload[]" accept="image/*" multiple onchange="updateFilePreview()">

							
							<!-- Pin Icon as File Upload Button -->
							<label for="fileUpload" class="btn btn-secondary d-flex align-items-center">
								<i class="fas fa-paperclip me-2"></i> Attach Files
							</label>
							
							<!-- File Name Display -->
							<span id="fileName" class="ms-2 text-muted">No files selected</span>
						</div>
						<div id="filePreview" class="mt-2"></div> <!-- Preview images -->
					</div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


<!-- EXCUSE APPLICATION -->
	<div class="card mb-3">
    <div class="row">
        <div class="col-lg-12" id="tableColumn" style="overflow-x: auto;">
            <div class="card" id="excuseApplicationTable" data-list='{"valueNames":["subject","activity","date","status","remarks"],"page":10,"pagination":true}'>
                <div class="card-header">
                    <div class="row flex-between-center">
                        <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                            <h5 class="fs-9 mb-0 text-nowrap py-2 py-xl-0">Excuse Application History</h5>
                        </div>
                        <div class="col-8 col-sm-auto text-end ps-2">
                            <div class="d-none" id="table-excuse-actions"></div>
                            <div id="table-excuse-replace-element">

							<!-- Filter Button to Open Modal -->
							<button class="btn btn-sm btn-falcon-default ms-2" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
								<span class="fas fa-filter"></span>
							</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive" id="excuseApplicationTableContainer">
                        <table class="table table-sm table-striped fs-10 mb-0 overflow-hidden">
                            <thead class="bg-200">
                                <tr>
                                    <th style="border: 1px solid #ddd;">Subject</th>
                                    <th style="border: 1px solid #ddd;">Activity Name</th>
                                    <th style="border: 1px solid #ddd;">Date Submitted</th>
                                    <th style="border: 1px solid #ddd;">Status</th>
                                    <th style="border: 1px solid #ddd;">Remarks</th>
                                </tr>
                            </thead>
                            <tbody id="excuseApplicationTableBody">
                                <?php if (!empty($excuseApplications)): ?>
                                    <?php foreach ($excuseApplications as $application): ?>
                                        <tr>
                                            <td style="border: 1px solid #ddd;"><?= $application['subject']; ?></td>
                                            <td style="border: 1px solid #ddd;"><?= $application['activity_title']; ?></td>
                                            <td style="border: 1px solid #ddd;"><?= date('m/d/Y', strtotime($application['created_at'])); ?></td>
                                            <td style="border: 1px solid #ddd;">
                                                <?php
                                                    // Status badge styling
                                                    switch ($application['status']) {
                                                        case 'Approved':
                                                            echo '<span class="badge rounded-pill d-block p-2" style="color: #155724; background-color: #d4edda;">
                                                                    <i class="fas fa-check-circle me-2"></i>Approved</span>';
                                                            break;
                                                        case 'Pending':
                                                            echo '<span class="badge rounded-pill d-block p-2" style="color: #856404; background-color: #fff3cd;">
                                                                    <i class="fas fa-hourglass-half me-2"></i>Pending</span>';
                                                            break;
                                                        case 'Disapproved':
                                                            echo '<span class="badge rounded-pill d-block p-2" style="color: #721c24; background-color: #f8d7da;">
                                                                    <i class="fas fa-times-circle me-2"></i>Rejected</span>';
                                                            break;
                                                        default:
                                                            echo '<span class="badge rounded-pill d-block p-2" style="color: #6c757d; background-color: #e2e3e5;">
                                                                    N/A</span>';
                                                    }
                                                ?>
                                            </td>

                                            <td>
												<div class="text-center">
												<button class="btn btn-primary btn-sm view-remarks" data-bs-toggle="modal" 
														data-bs-target="#remarksModal" 
														data-remarks="<?= htmlspecialchars($application['remarks'], ENT_QUOTES, 'UTF-8'); ?>"
														data-remarks-at="<?= htmlspecialchars($application['remarks_at'], ENT_QUOTES, 'UTF-8'); ?>">
														View Remarks
												</button>

												</div>
											</td>

                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-danger">No records found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="noDataMessage" style="display: none; text-align: center; color: red;">
                        No excuse applications found for the selected filter.
                    </div>
                </div>

                <div class="card-footer d-flex align-items-center justify-content-center">
                    <button class="btn btn-sm btn-falcon-default me-1" type="button" title="Previous" data-list-pagination="prev">
                        <span class="fas fa-chevron-left"></span>
                    </button>
                    <ul class="pagination mb-0"></ul>
                    <button class="btn btn-sm btn-falcon-default ms-1" type="button" title="Next" data-list-pagination="next">
                        <span class="fas fa-chevron-right"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

</div>




<script>
// Function to update file preview when files are selected
function updateFilePreview() {
    var fileInput = document.getElementById("fileUpload");
    var preview = document.getElementById("filePreview");
    var fileNames = document.getElementById("fileName");

    preview.innerHTML = ''; // Clear previous previews
    fileNames.textContent = fileInput.files.length ? `${fileInput.files.length} files selected` : "No files selected";

    // Loop through selected files and create previews
    for (var i = 0; i < fileInput.files.length; i++) {
        var reader = new FileReader();
        reader.onload = function (e) {
            var img = document.createElement("img");
            img.src = e.target.result;
            img.style.maxWidth = "100px"; // Set max width for preview
            img.style.marginRight = "10px";
            preview.appendChild(img);
        };
        reader.readAsDataURL(fileInput.files[i]);
    }
}

</script>

<script>
    // Display file name when a file is selected
    function updateFileName() {
        var fileInput = document.getElementById("fileUpload");
        var fileName = document.getElementById("fileName");
        fileName.textContent = fileInput.files[0] ? fileInput.files[0].name : "No file selected";
    }

    // Show success message
    document.getElementById("excuseForm").onsubmit = function (e) {
        e.preventDefault(); // Prevent form submission for demonstration
        document.getElementById("successMessage").style.display = "block";
    };

    function updateFileName() {
        var fileInput = document.getElementById("fileUpload");
        var fileName = document.getElementById("fileName");
        fileName.textContent = fileInput.files[0] ? fileInput.files[0].name : "No file selected";
    }
</script>


<script>
$(document).ready(function () {
    $("#excuseForm").submit(function (e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: "<?= base_url('student/excuse-application/submit') ?>",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                var res = JSON.parse(response);

                if (res.status === "success") {
                    alertify.alert("Success", res.message, function () {
                        window.location.reload();
                    });
                } else {
                    alertify.alert("Error", res.message);

                    // Restore form values if validation fails
                    if (res.form_data) {
                        $("#activitySelect").val(res.form_data.activity_id);
                        $("#emailSubject").val(res.form_data.emailSubject);
                        $("#emailBody").val(res.form_data.emailBody);
                    }
                }
            },
            error: function () {
                alertify.alert("Error", "An unexpected error occurred.");
            }
        });
    });
});

</script>


<!-- REMARKS MODAL START -->

<div class="modal fade" id="remarksModal" tabindex="-1" aria-labelledby="remarksModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="remarksModalLabel">View Remarks</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm">
                    <tbody>
                        <tr>
                            <td style="width: 30%;"><i class="fas fa-calendar-day me-2"></i><strong>Date:</strong></td>
                            <td id="remarksDate" class="text-nowrap"></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-comment-dots me-2"></i><strong>Remarks:</strong></td>
                            <td id="remarksContent"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
  // When the modal is shown, set the remarks and date
$('#remarksModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var remarks = button.data('remarks'); // Extract remarks from data-remarks attribute
    var remarksAt = button.data('remarks-at'); // Extract remarks_at from data-remarks-at attribute

    // Check if remarks is empty or undefined
    if (!remarks || remarks.trim() === '') {
        remarks = 'No remarks available'; // Set default message if no remarks are available
    }

    // Check if the date is valid
    var dateObj = new Date(remarksAt);

    if (isNaN(dateObj)) {
        console.error('Invalid date format:', remarksAt);
        return; // Exit if the date is invalid
    }

    // Format the datetime using toLocaleDateString
    var formattedDate = dateObj.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }); // Example format: January 10, 2025

    // Update the modal content
    $('#remarksContent').text(remarks);
    $('#remarksDate').text(formattedDate);
});


</script>



<!-- REMARKS MODAL END -->

<!-- SCRIPTS FOR FILTERS START -->

<!-- MODAL FILTER -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Activities</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Semester Filter -->
                <div class="mb-3">
                    <label for="semester-filter" class="form-label">Select Semester</label>
                    <select id="semester-filter" class="form-select">
                        <option value="" selected>All Semesters</option>
                        <option value="1">First Semester</option>
                        <option value="2">Second Semester</option>
                    </select>
                </div>
                <!-- Year Filter with Range -->
                <div class="mb-3">
                    <label for="year-filter" class="form-label">Select Academic Year</label>
                    <select id="year-filter" class="form-select">
                        <option value="" selected>All Years</option>
                        <option value="2024">2024-2025</option>
                        <option value="2025">2025-2026</option>
                        <option value="2026">2026-2027</option>
                        <option value="2027">2027-2028</option>
                        <option value="2028">2028-2029</option>
                    </select>
                </div>  
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="applyFilters()">Apply Filters</button>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {
    // Process each row in the excuse application table and add data attributes for filtering
    var rows = document.querySelectorAll("#evaluation-table-body tr, #excuseApplicationTableBody tr");

    rows.forEach(function(row) {
        // For Evaluation Table (assuming Date is in 3rd column)
        if (row.closest("#evaluation-table-body")) {
            var dateText = row.cells[2].textContent.trim(); // Assuming Date is in the 3rd column
            var dateParts = dateText.split(","); // Split on comma, since the date format is "Month Day, Year"
            var month = new Date(dateParts[0] + " 1").getMonth() + 1; // Get the month
            var year = parseInt(dateParts[1].trim()); // Get the year

            var semester = "";
            var academicYear = "";

            // Determine Semester
            if (month >= 8 && month <= 12) {
                semester = "1"; // First Semester
                academicYear = year; // Fall semester, academic year starts this year
            } else if (month >= 1 && month <= 7) {
                semester = "2"; // Second Semester
                academicYear = year - 1; // Spring semester belongs to the previous academic year
            }

            // Assign data attributes to row for filtering
            row.setAttribute("data-semester", semester);
            row.setAttribute("data-year", academicYear);
        }

        // For Excuse Application Table (Date Submitted column)
        if (row.closest("#excuseApplicationTableBody")) {
            var dateText = row.cells[2].textContent.trim(); // Assuming Date Submitted is in the 3rd column
            var dateParts = dateText.split("/"); // Split date by '/'
            var month = parseInt(dateParts[0], 10); // Get the month
            var year = parseInt(dateParts[2], 10); // Get the year

            var semester = "";
            var academicYear = "";

            // Determine Semester
            if (month >= 8 && month <= 12) {
                semester = "1"; // First Semester
                academicYear = year; // Fall semester, academic year starts this year
            } else if (month >= 1 && month <= 7) {
                semester = "2"; // Second Semester
                academicYear = year - 1; // Spring semester belongs to the previous academic year
            }

            // Assign data attributes to row for filtering
            row.setAttribute("data-semester", semester);
            row.setAttribute("data-year", academicYear);
        }
    });
});

// Function to filter based on selected semester and academic year
function applyFilters() {
    var selectedSemester = document.getElementById("semester-filter").value;
    var selectedYear = document.getElementById("year-filter").value;
    var rows = document.querySelectorAll("#evaluation-table-body tr, #excuseApplicationTableBody tr");
    var noDataMessage = document.getElementById("noDataMessage");
    var filteredRows = 0;

    rows.forEach(function(row) {
        var rowSemester = row.getAttribute("data-semester");
        var rowYear = row.getAttribute("data-year");

        var semesterMatch = !selectedSemester || rowSemester === selectedSemester;
        var yearMatch = !selectedYear || rowYear === selectedYear;

        if (semesterMatch && yearMatch) {
            row.style.display = "";
            filteredRows++;
        } else {
            row.style.display = "none";
        }
    });

    // Show "No evaluations available in this filter" message if no rows match the filter
    if (filteredRows === 0) {
        noDataMessage.style.display = "block";
    } else {
        noDataMessage.style.display = "none";
    }

    // Close the modal
    var modalElement = document.getElementById("filterModal");
    var modalInstance = bootstrap.Modal.getInstance(modalElement); // Correct way to get the instance
    modalInstance.hide(); // Close modal
}
</script>

<!-- SCRIPTS FOR FILTERS END -->


</html>
