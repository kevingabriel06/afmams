<div class="container">
    <div class="row">
        <!-- Table -->
        <div class="col-lg-12" id="tableColumn" style="overflow-x: auto;">
            <div class="card" id="customersTable" data-list='{"valueNames":["name","email","phone","address","joined"],"page":10,"pagination":true}'>
                <div class="card-header">
                    <div class="row flex-between-center">
                        <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                            <h5 class="fs-9 mb-0 text-nowrap py-2 py-xl-0">Attendance History</h5>
                        </div>
                        <div class="col-8 col-sm-auto text-end ps-2">
                            <div class="d-none" id="table-customers-actions"></div>
                            <div id="table-customers-replace-element">

							<!-- Filter Button to Open Modal -->
							<button class="btn btn-sm btn-falcon-default ms-2" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
								<span class="fas fa-filter"></span>
							</button>
                                <!-- Dropdown for Semester Filter -->
                                <!-- <div class="dropdown d-inline-block mx-2" id="semesterDropdown">
                                    <button class="btn btn-falcon-default btn-sm" type="button" id="semesterDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="fas fa-calendar" data-fa-transform="shrink-3 down-2"></span>
                                        <span class="d-none d-sm-inline-block ms-1">Semester</span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="semesterDropdown">
										<li><a class="dropdown-item" href="#" data-filter="all">All</a></li>
										<li><a class="dropdown-item" href="#" data-filter="1">First Semester</a></li>
										<li><a class="dropdown-item" href="#" data-filter="2">Second Semester</a></li>
									</ul>

                                </div>

                                Dropdown for Time Range Filter -->
                                <!-- <div class="dropdown d-inline-block mx-2" id="timeRangeDropdown">
                                    <button class="btn btn-falcon-default btn-sm" type="button" id="timeRangeDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="fas fa-clock" data-fa-transform="shrink-3 down-2"></span>
                                        <span class="d-none d-sm-inline-block ms-1">Filter</span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="timeRangeDropdown">
										<li><a class="dropdown-item" href="#" data-filter="all">All Time</a></li>
										<li><a class="dropdown-item" href="#" data-filter="1">This Month</a></li>
										<li><a class="dropdown-item" href="#" data-filter="2">Last Month</a></li>
										<li><a class="dropdown-item" href="#" data-filter="3">Last 3 Months</a></li>
									</ul>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" id="attendanceTable">
                        <table class="table table-sm table-striped fs-10 mb-0 overflow-hidden">
                            <thead class="bg-200">
                                <tr>
                                    <th style="border: 1px solid #ddd;">Activity</th>
                                    <th style="border: 1px solid #ddd;">Organizer</th>
                                    <th style="border: 1px solid #ddd;">Date</th>
                                    <th style="border: 1px solid #ddd;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                                <?php foreach ($attendances as $attendance): ?>
                                    <tr data-attendance-id="<?= isset($attendance->attendance_id) ? $attendance->attendance_id : 'N/A' ?>" 
                                        data-am-in="<?= $attendance->AM_IN ? date('g:i A', strtotime($attendance->AM_IN)) : 'N/A' ?>" 
                                        data-am-out="<?= $attendance->AM_OUT ? date('g:i A', strtotime($attendance->AM_OUT)) : 'N/A' ?>" 
                                        data-pm-in="<?= $attendance->PM_IN ? date('g:i A', strtotime($attendance->PM_IN)) : 'N/A' ?>" 
                                        data-pm-out="<?= $attendance->PM_OUT ? date('g:i A', strtotime($attendance->PM_OUT)) : 'N/A' ?>" 
                                        data-bs-toggle="modal" data-bs-target="#attendanceModal">
                                        
                                        <!-- Display the activity -->
                                        <td style="border: 1px solid #ddd; word-wrap: break-word;"><?= $attendance->Activity ?></td>

                                        <!-- Display the organizer -->
                                        <td style="border: 1px solid #ddd; word-wrap: break-word;"><?= $attendance->organizer ?></td>

                                        <!-- Display the date (formatted without time) -->
                                        <td style="border: 1px solid #ddd;"><?= date('m/d/Y', strtotime($attendance->Date)) ?></td>

                                        <!-- Display the attendance status with badges -->
                                        <td style="border: 1px solid #ddd;">
                                            <?php
                                                // Check the attendance status and add respective badges with icons
                                                if (isset($attendance->attendance_status)) {
                                                    switch ($attendance->attendance_status) {
                                                        case 'Present':
                                                            echo '<span class="badge rounded-pill d-block p-2" style="color: #155724; background-color: #d4edda;">
                                                                    <i class="fas fa-check-circle me-2"></i>' . ucfirst($attendance->attendance_status) . '</span>';
                                                            break;
                                                        case 'Absent':
                                                            echo '<span class="badge rounded-pill d-block p-2" style="color: #721c24; background-color: #f8d7da;">
                                                                    <i class="fas fa-times-circle me-2"></i>Absent</span>';
                                                            break;
                                                        case 'Incomplete':
                                                            echo '<span class="badge rounded-pill d-block p-2" style="color: #856404; background-color: #fff3cd;">
                                                                    <i class="fas fa-exclamation-circle me-2"></i>' . ucfirst($attendance->attendance_status) . '</span>';
                                                            break;
                                                        default:
                                                            echo '<span class="badge rounded-pill d-block p-2" style="color: #6c757d; background-color: #e2e3e5;">
                                                                    N/A</span>';
                                                    }
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- No Data Message -->
                    <div id="noDataMessage" style="display: none; text-align: center; color: red;">
                        No activities found for the selected filter.
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

<!-- Floating Form Modal (For Viewing Only) -->
<div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attendanceModalLabel">Attendance Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Table inside Modal -->
                <table class="table-container" style="width: 100%; table-layout: fixed; border: 1px solid #ddd;">
                    <thead class="bg-200">
                        <tr>
                            <th style="border: 1px solid #ddd;">AM IN</th>
                            <th style="border: 1px solid #ddd;">AM OUT</th>
                            <th style="border: 1px solid #ddd;">PM IN</th>
                            <th style="border: 1px solid #ddd;">PM OUT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border: 1px solid #ddd;" id="modalAMIN"></td>
                            <td style="border: 1px solid #ddd;" id="modalAMOUT"></td>
                            <td style="border: 1px solid #ddd;" id="modalPMIN"></td>
                            <td style="border: 1px solid #ddd;" id="modalPMOUT"></td>
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
// Event listener for row click to populate modal
const tableRows = document.querySelectorAll('#attendanceTableBody tr');
const modalAMIN = document.getElementById('modalAMIN');
const modalAMOUT = document.getElementById('modalAMOUT');
const modalPMIN = document.getElementById('modalPMIN');
const modalPMOUT = document.getElementById('modalPMOUT');

tableRows.forEach(row => {
    row.addEventListener('click', function() {
        // Get the data attributes
        const amIn = this.getAttribute('data-am-in');
        const amOut = this.getAttribute('data-am-out');
        const pmIn = this.getAttribute('data-pm-in');
        const pmOut = this.getAttribute('data-pm-out');
        
        // Set the values in the modal
        modalAMIN.textContent = amIn;
        modalAMOUT.textContent = amOut;
        modalPMIN.textContent = pmIn;
        modalPMOUT.textContent = pmOut;
    });
});
</script>

<!-- SCRIPT FOR FILTERS START-->

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
    var rows = document.querySelectorAll("#attendanceTableBody tr");

    rows.forEach(function(row) {
        var dateText = row.cells[2].textContent.trim(); // Assuming Date is in the 3rd column
        var dateParts = dateText.split("/"); // Assuming format MM/DD/YYYY
        var month = parseInt(dateParts[0]);
        var year = parseInt(dateParts[2]);

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

    // Apply filters when the modal button is clicked
    document.querySelector("#applyFiltersButton").addEventListener("click", applyFilters);
});

// Function to filter based on selected semester and academic year
function applyFilters() {
    var selectedSemester = document.getElementById("semester-filter").value;
    var selectedYear = document.getElementById("year-filter").value;
    var rows = document.querySelectorAll("#attendanceTableBody tr");
    var noDataMessage = document.getElementById("noDataMessage");
    var filteredRows = 0;

    rows.forEach(function(row) {
        var rowSemester = row.getAttribute("data-semester");
        var rowYear = row.getAttribute("data-year");

        // Ensure data attributes are properly read
        var semesterMatch = !selectedSemester || rowSemester === selectedSemester;
        var yearMatch = !selectedYear || rowYear === selectedYear;

        if (semesterMatch && yearMatch) {
            row.style.display = "";
            filteredRows++;
        } else {
            row.style.display = "none";
        }
    });

    // Show "No Data" message if no rows match the filter
    noDataMessage.style.display = filteredRows === 0 ? "block" : "none";

    // Close modal after applying filters
    var modalElement = document.getElementById("filterModal");
    var modal = bootstrap.Modal.getInstance(modalElement);
    if (modal) {
        modal.hide();
    }
}

</script>


<!-- SCRIPT FOR FILTERS END -->
