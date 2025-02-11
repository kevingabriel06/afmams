<div class="container mb-3">
    <div class="row" style="display: flex; flex-wrap: wrap;">
        <!-- FETCHING ALL THE AVAILABLE AND OPEN FORMS -->
		<?php if (!empty($evaluation_forms)): ?>
    <?php foreach ($evaluation_forms as $form): ?>
        <div class="col-md-6" style="display: flex; justify-content: stretch; margin-bottom: 1rem;">
            <div class="card" style="display: flex; flex-direction: column; flex-grow: 1; border: 1px solid #ddd; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
                <div class="card-header">
                    <div class="row flex-between-center">
                        <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                            <h5 class="fs-7 mb-0 text-nowrap py-2 py-xl-0">Evaluation Form</h5>
                        </div>
                    </div>
                </div>
                <div class="border"></div>
                <div class="card-body p-4" style="flex-grow: 1; overflow-y: auto; max-height: 350px;">
                    <div class="mb-3">
                        <i class="fas fa-calendar-check me-2"></i>
                        <strong class="text-muted">Form Title:</strong>
                        <span><?= $form->form_title; ?></span>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-users me-2"></i>
                        <strong class="text-muted">From:</strong>
                        <span><?= $form->organizer; ?></span>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong class="text-muted">Description:</strong>
                        <p><?= $form->form_description; ?></p>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-clock me-2"></i>
                        <strong class="text-muted">Duration:</strong>
                        <span><?= date('F d, Y, h:i A', strtotime($form->start_date)) . ' - ' . date('F d, Y, h:i A', strtotime($form->end_date)); ?></span>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        <!-- Disable button if the form is closed -->
                        <a href="<?= $form->is_form_open ? base_url('student/evaluation-form-questions/' . $form->form_id) : '#'; ?>" class="btn btn-primary w-100" style="margin-top: auto;" <?= !$form->is_form_open ? 'disabled' : ''; ?>>
                            <?= !$form->is_form_open ? 'Form Closed' : 'Open Form'; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No available evaluation forms at the moment.</p>
<?php endif; ?>



    </div>
</div>



<!-- FETCHING ALL THE EVALUATION FORMS ANSWERED IN TABLE FORMAT -->
<div class="container mb-4">
    <div class="row">
        <div class="col-lg-12" id="tableColumn" style="overflow-x: auto;">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="fs-9 mb-0">Evaluation Forms Answered</h5>
                    <!-- Filter Button to Open Modal -->
                    <button class="btn btn-sm btn-falcon-default ms-2" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <span class="fas fa-filter"></span>
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped fs-10 mb-0">
                            <thead class="bg-200">
                                <tr>
                                    <th>Activity</th>
                                    <th>Organizer</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="evaluation-table-body">
								<?php if (!empty($answered_forms)): ?>
									<?php foreach ($answered_forms as $answered): ?>
										<tr>
											<td>
												<a href="<?= base_url('student/evaluation-answers/' . $answered->form_id); ?>">
													<?= $answered->activity_title; ?>
												</a>
											</td>
											<td><?= $answered->organizer; ?></td>
											<td><?= date('F d, Y', strtotime($answered->answered_date)); ?></td>
											<td>
												<span class="badge rounded-pill d-block p-2" style="color: #155724; background-color: #d4edda;">
													<i class="fas fa-check-circle me-2"></i> Answered
												</span>
											</td>

											<td>
												<a class="btn btn-sm btn-primary" href="<?= base_url('student/evaluation-answers/' . $answered->form_id); ?>">View Form</a>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr>
										<td colspan="5" class="text-center" id="noDataMessage" style="display: none;">No evaluations available in this filter.</td>
									</tr>
								<?php endif; ?>
							</tbody>

                        </table>

						<!-- No Data Message -->
						<div id="noDataMessage" style="display: none; text-align: center; color: red;">
                        No activities found for the selected filter.
                    </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
    // Process each row and add data attributes for filtering
    var rows = document.querySelectorAll("#evaluation-table-body tr");

    rows.forEach(function(row) {
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
    });
});

// Function to filter based on selected semester and academic year
// Function to filter based on selected semester and academic year
function applyFilters() {
    var selectedSemester = document.getElementById("semester-filter").value;
    var selectedYear = document.getElementById("year-filter").value;
    var rows = document.querySelectorAll("#evaluation-table-body tr");
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

