<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<div class="card mb-3 mb-lg-0">
    <div class="card-header bg-body-tertiary d-flex justify-content-between">
        <h5 class="mb-0"><?php echo $activities['activity_title']; ?> - Attendance List of
            <?php foreach ($department as $dept): ?>
                <?php if ($dept_id == $dept->dept_id): ?>
                    <?php echo $dept->dept_name; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </h5>
    </div>
</div>

<!-- Space Between Sections -->
<div class="space" style="height: 20px;"></div> <!-- Adds spacing between sections -->

<div class="row gx-3">
    <div class="col-xxl-10 col-xl-12">
        <div class="card" id="ticketsTable"
            data-list='{"valueNames":["name","status"],"page":11,"pagination":true,"fallback":"tickets-table-fallback"}'>

            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            
                        </div>
                    </div>

                    <!-- Search Input -->
                    <div class="d-flex align-items-center justify-content-between justify-content-lg-end px-x1">
                        <div class="d-flex align-items-center" id="table-ticket-replace-element">
                            <div class="col-auto">
                                <form>
                                    <div class="input-group input-search-width">
                                        <input id="searchInput" class="form-control form-control-sm shadow-none search"
                                            type="search" placeholder="Search by Activity" aria-label="search" />
                                        <button class="btn btn-sm btn-outline-secondary border-300 hover-border-secondary" type="button">
                                            <span class="fa fa-search fs-10"></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <button class="btn btn-sm btn-falcon-default ms-2" type="button">
                                <span class="fas fa-download"></span>
                            </button>
                            <button class="btn btn-sm btn-falcon-default ms-2" type="button"
                                data-bs-toggle="modal" data-bs-target="#filterModal">
                                <span class="fas fa-filter"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive scrollbar">
                    <table class="table table-hover table-striped overflow-hidden">
                        <thead class="bg-200">
                            <tr>
                                <th class="text-900 px-6 py-2">Student ID</th>
                                <th class="text-900 px-6 py-2">Name</th>
                                <th class="text-900 px-7 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="list" id="table-ticket-body">
                            <?php foreach ($students as $student) : ?>
                                <?php if($role = 'Officer' && !empty($organization->org_id) && empty($departments->dept_id)) :?>
                                    <?php if (
                                        isset($activities['activity_id']) && 
                                        $activities['activity_id'] == $student->activity_id && 
                                        $student->dept_id == $dept_id && 
                                        $student->org_id == $organization->org_id
                                    ): ?>
                                        <tr class="attendance-row"
                                            data-bs-toggle="modal"
                                            data-bs-target="#attendanceModal"
                                            data-student-id="<?php echo htmlspecialchars($student->student_id); ?>"
                                            data-name="<?php echo htmlspecialchars($student->first_name . ' ' . $student->last_name); ?>"
                                            data-department="<?php echo htmlspecialchars($student->dept_name); ?>"
                                            data-status="<?php echo htmlspecialchars($student->attendance_status); ?>"
                                            data-am-in="<?php echo htmlspecialchars($student->am_in ?? 'N/A'); ?>"
                                            data-am-out="<?php echo htmlspecialchars($student->am_out ?? 'N/A'); ?>"
                                            data-pm-in="<?php echo htmlspecialchars($student->pm_in ?? 'N/A'); ?>"
                                            data-pm-out="<?php echo htmlspecialchars($student->pm_out ?? 'N/A'); ?>">

                                            <td class="student_id align-middle text-nowrap px-6 py-2">
                                                <h6 class="mb-0"><?php echo htmlspecialchars($student->student_id); ?></h6>
                                            </td>
                                            <td class="name px-6 py-2">
                                                <h6 class="mb-0"><?php echo htmlspecialchars($student->first_name . ' ' . $student->last_name); ?></h6>
                                            </td>
                                            <td class="status px-7 py-2">
                                                <?php if ($student->attendance_status === 'Present'): ?>
                                                    <span class="badge badge rounded-pill d-block p-2 badge-subtle-success">Present
                                                        <span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span>
                                                    </span>
                                                <?php elseif ($student->attendance_status === 'Absent'): ?>
                                                    <span class="badge badge rounded-pill d-block p-2 badge-subtle-danger">Absent
                                                        <span class="ms-1 fas fa-redo" data-fa-transform="shrink-2"></span>
                                                    </span>
                                                <?php elseif ($student->attendance_status === 'Incomplete'): ?>
                                                    <span class="badge badge rounded-pill d-block p-2 badge-subtle-warning">Incomplete
                                                        <span class="ms-1 fas fa-stream" data-fa-transform="shrink-2"></span>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge rounded-pill d-block p-2 badge-subtle-warning">Default
                                                        <span class="ms-1 fas fa-stream" data-fa-transform="shrink-2"></span>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endif;?>
                                <?php elseif ($role = 'Admin') :?>
                                    <?php if (
                                        isset($activities['activity_id']) && 
                                        $activities['activity_id'] == $student->activity_id && 
                                        $student->dept_id == $dept_id
                                    ): ?>
                                    <tr class="attendance-row"
                                        data-bs-toggle="modal"
                                        data-bs-target="#attendanceModal"
                                        data-student-id="<?php echo htmlspecialchars($student->student_id); ?>"
                                        data-name="<?php echo htmlspecialchars($student->first_name . ' ' . $student->last_name); ?>"
                                        data-department="<?php echo htmlspecialchars($student->dept_name); ?>"
                                        data-status="<?php echo htmlspecialchars($student->attendance_status); ?>"
                                        data-am-in="<?php echo htmlspecialchars($student->am_in ?? 'N/A'); ?>"
                                        data-am-out="<?php echo htmlspecialchars($student->am_out ?? 'N/A'); ?>"
                                        data-pm-in="<?php echo htmlspecialchars($student->pm_in ?? 'N/A'); ?>"
                                        data-pm-out="<?php echo htmlspecialchars($student->pm_out ?? 'N/A'); ?>">

                                        <td class="student_id align-middle text-nowrap px-6 py-2">
                                            <h6 class="mb-0"><?php echo htmlspecialchars($student->student_id); ?></h6>
                                        </td>
                                        <td class="name px-6 py-2">
                                            <h6 class="mb-0"><?php echo htmlspecialchars($student->first_name . ' ' . $student->last_name); ?></h6>
                                        </td>
                                        <td class="status px-7 py-2">
                                            <?php if ($student->attendance_status === 'Present'): ?>
                                                <span class="badge badge rounded-pill d-block p-2 badge-subtle-success">Present
                                                    <span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span>
                                                </span>
                                            <?php elseif ($student->attendance_status === 'Absent'): ?>
                                                <span class="badge badge rounded-pill d-block p-2 badge-subtle-danger">Absent
                                                    <span class="ms-1 fas fa-redo" data-fa-transform="shrink-2"></span>
                                                </span>
                                            <?php elseif ($student->attendance_status === 'Incomplete'): ?>
                                                <span class="badge badge rounded-pill d-block p-2 badge-subtle-warning">Incomplete
                                                    <span class="ms-1 fas fa-stream" data-fa-transform="shrink-2"></span>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge rounded-pill d-block p-2 badge-subtle-warning">Default
                                                    <span class="ms-1 fas fa-stream" data-fa-transform="shrink-2"></span>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif;?>
                                <?php else: ?>
                                    <?php if (
                                        isset($activities['activity_id']) && 
                                        $activities['activity_id'] == $student->activity_id && 
                                        $student->dept_id == $dept_id
                                    ): ?>
                                    <tr class="attendance-row"
                                        data-bs-toggle="modal"
                                        data-bs-target="#attendanceModal"
                                        data-student-id="<?php echo htmlspecialchars($student->student_id); ?>"
                                        data-name="<?php echo htmlspecialchars($student->first_name . ' ' . $student->last_name); ?>"
                                        data-department="<?php echo htmlspecialchars($student->dept_name); ?>"
                                        data-status="<?php echo htmlspecialchars($student->attendance_status); ?>"
                                        data-am-in="<?php echo htmlspecialchars($student->am_in ?? 'N/A'); ?>"
                                        data-am-out="<?php echo htmlspecialchars($student->am_out ?? 'N/A'); ?>"
                                        data-pm-in="<?php echo htmlspecialchars($student->pm_in ?? 'N/A'); ?>"
                                        data-pm-out="<?php echo htmlspecialchars($student->pm_out ?? 'N/A'); ?>">

                                        <td class="student_id align-middle text-nowrap px-6 py-2">
                                            <h6 class="mb-0"><?php echo htmlspecialchars($student->student_id); ?></h6>
                                        </td>
                                        <td class="name px-6 py-2">
                                            <h6 class="mb-0"><?php echo htmlspecialchars($student->first_name . ' ' . $student->last_name); ?></h6>
                                        </td>
                                        <td class="status px-7 py-2">
                                            <?php if ($student->attendance_status === 'Present'): ?>
                                                <span class="badge badge rounded-pill d-block p-2 badge-subtle-success">Present
                                                    <span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span>
                                                </span>
                                            <?php elseif ($student->attendance_status === 'Absent'): ?>
                                                <span class="badge badge rounded-pill d-block p-2 badge-subtle-danger">Absent
                                                    <span class="ms-1 fas fa-redo" data-fa-transform="shrink-2"></span>
                                                </span>
                                            <?php elseif ($student->attendance_status === 'Incomplete'): ?>
                                                <span class="badge badge rounded-pill d-block p-2 badge-subtle-warning">Incomplete
                                                    <span class="ms-1 fas fa-stream" data-fa-transform="shrink-2"></span>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge rounded-pill d-block p-2 badge-subtle-warning">Default
                                                    <span class="ms-1 fas fa-stream" data-fa-transform="shrink-2"></span>
                                                </span>    
                                            <?php endif; ?>
                                        </td>
                                    <?php endif;?>
                                <?php endif;?>  
                            <?php endforeach; ?>

                            <!-- "No activities listed" Row -->
                            <tr id="no-attendance-row" style="display: none;">
                                <td colspan="3" class="text-center text-muted fs-8 fw-bold py-2 bg-light">
                                    <span class="fa fa-user-slash fa-2x text-muted"></span>
                                    <h5 class="mt-2 mb-1">No student listed.</h5>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-center d-none" id="tickets-table-fallback">
                        <span class="fa fa-user-slash fa-2x text-muted"></span>
                        <p class="fw-bold fs-8 mt-3">No Student Found</p>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="d-flex justify-content-center">
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


   <!-- Attendance Details Modal -->
    <div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attendanceModalLabel">Attendance Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Student ID:</strong> <span id="modal-student-id"></span></p>
                    <p><strong>Name:</strong> <span id="modal-name"></span></p>
                    <p><strong>Department:</strong> <span id="modal-department"></span></p>
                    <p><strong>Status:</strong> <span id="modal-status"></span></p>

                    <hr>

                    <!-- Morning Attendance -->
                    <div id="morning-attendance">
                        <p><strong>Morning Attendance</strong></p>
                        <p><strong>AM In:</strong> <span id="modal-am-in"></span></p>
                        <p><strong>AM Out:</strong> <span id="modal-am-out"></span></p>
                    </div>

                    <!-- Afternoon Attendance -->
                    <div id="afternoon-attendance">
                        <p><strong>Afternoon Attendance</strong></p>
                        <p><strong>PM In:</strong> <span id="modal-pm-in"></span></p>
                        <p><strong>PM Out:</strong> <span id="modal-pm-out"></span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <!-- JavaScript to Populate and Display Attendance -->
    <script>
       document.addEventListener("DOMContentLoaded", function () {
            var attendanceModal = document.getElementById("attendanceModal");

            attendanceModal.addEventListener("show.bs.modal", function (event) {
                var button = event.relatedTarget; // Button that triggered the modal

                // Extract data attributes
                var studentId = button.getAttribute("data-student-id");
                var name = button.getAttribute("data-name");
                var department = button.getAttribute("data-department");
                var status = button.getAttribute("data-status");
                var amIn = button.getAttribute("data-am-in");
                var amOut = button.getAttribute("data-am-out");
                var pmIn = button.getAttribute("data-pm-in");
                var pmOut = button.getAttribute("data-pm-out");

                // Update modal content
                document.getElementById("modal-student-id").textContent = studentId;
                document.getElementById("modal-name").textContent = name;
                document.getElementById("modal-department").textContent = department;
                document.getElementById("modal-status").textContent = status;
                document.getElementById("modal-am-in").textContent = amIn;
                document.getElementById("modal-am-out").textContent = amOut;
                document.getElementById("modal-pm-in").textContent = pmIn;
                document.getElementById("modal-pm-out").textContent = pmOut;

                // Hide both attendance sections initially
                document.getElementById("morning-attendance").style.display = "none";
                document.getElementById("afternoon-attendance").style.display = "none";

                // Determine which sections to show
                var isMorningEmpty = (!amIn || amIn === "N/A") && (!amOut || amOut === "N/A");
                var isAfternoonEmpty = (!pmIn || pmIn === "N/A") && (!pmOut || pmOut === "N/A");

                if (isMorningEmpty && !isAfternoonEmpty) {
                    // Only Afternoon Activity
                    document.getElementById("afternoon-attendance").style.display = "block";
                } else if (!isMorningEmpty && isAfternoonEmpty) {
                    // Only Morning Activity
                    document.getElementById("morning-attendance").style.display = "block";
                } else if (!isMorningEmpty && !isAfternoonEmpty) {
                    // Whole Day Activity
                    document.getElementById("morning-attendance").style.display = "block";
                    document.getElementById("afternoon-attendance").style.display = "block";
                }
            });
        });


    </script>


    <!-- MODAL FILTER -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Activities</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Status Filter -->
                    <div class="mb-3">
                        <label for="status-filter" class="form-label">Activity Status</label>
                        <select id="status-filter" class="form-select">
                            <option value="" selected>Select Status</option>
                            <option value="Present">Present</option>
                            <option value="Incomplete">Incomplete</option>
                            <option value="Absent">Absent</option>
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
        function applyFilters() {
            // Get selected values from the modal filters
            var status = document.getElementById("status-filter").value;

            // Get all activity rows
            var activityRows = document.querySelectorAll(".attendance-row");
            var noActivityRow = document.getElementById("no-attendance-row");
            var filteredRows = 0;

            // Loop through each activity row
            activityRows.forEach(function(row) {
                var rowStatus = row.getAttribute("data-status"); // Add status attribute in PHP

                // Check if the row matches the selected filters
                if (
                    (status === "" || status === rowStatus)
                ) {
                    row.style.display = ""; // Show the row if it matches
                    filteredRows++;
                } else {
                    row.style.display = "none"; // Hide the row if it doesn't match
                }
            });

            // Show or hide the "No activities listed" row
            if (filteredRows === 0) {
                noActivityRow.style.display = ""; // Show the no activity row
            } else {
                noActivityRow.style.display = "none"; // Hide the no activity row
            }

            // Close the modal properly
            var modalElement = document.getElementById("filterModal");
            var modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        }

        // FOR SEARCH
        document.addEventListener("DOMContentLoaded", function() {
            var options = {
                valueNames: ["name", "status"],
                page: 11,
                pagination: true
            };

            var excuseList = new List("ticketsTable", options);

            document.getElementById("searchInput").addEventListener("keyup", function() {
                excuseList.search(this.value);
            });
        });
    </script>