<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<div class="row gx-3">
  <div class="col-xxl-10 col-xl-12">
    <div class="card" id="ticketsTable" data-list='{"valueNames":["client","subject","status","priority","agent"],"page":11,"pagination":true,"fallback":"tickets-table-fallback"}'>
      <div class="card-header border-bottom border-200 px-0">
        <div class="d-lg-flex justify-content-between">
          <div class="row flex-between-center gy-2 px-x1">
            <div class="col-auto pe-0">
              <h5 class="mb-0"><?php echo $activities['activity_title'] ;?> - Attendance List</h5>
            </div>
          </div>

          <!-- Bulk actions options -->
          <div class="border-bottom border-200 my-3"></div>
          <div class="d-flex align-items-center justify-content-between justify-content-lg-end px-x1">
            <div class="bg-300 mx-3 d-none d-lg-block d-xl-none" style="width:1px; height:29px"></div>
            <div class="d-flex align-items-center" id="table-ticket-replace-element">
              <div class="col-auto">
                <form>
                  <div class="input-group input-search-width">
                    <input class="form-control form-control-sm shadow-none search" type="search" placeholder="Search by Name" aria-label="search" />
                    <button class="btn btn-sm btn-outline-secondary border-300 hover-border-secondary" type="button">
                      <span class="fa fa-search fs-10"></span>
                    </button>
                  </div>
                </form>
              </div>
              <button class="btn btn-sm btn-falcon-default ms-2" type="button" data-bs-toggle="" data-bs-target="">
                <span class="fas fa-external-link-alt"></span>
              </button>
              <button class="btn btn-sm btn-falcon-default ms-2" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
                <span class="fas fa-filter"></span>
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <div class="card-body p-0">
        <div class="table-responsive scrollbar">
          <!-- Table of Activities -->
          <table class="table table-sm table-striped table-hover mb-0 fs-10">
            <thead class="bg-body-tertiary">
                <tr>
                    <th class="text-800 sort align-middle ps-2 w-auto">ID Number</th>
                    <th class="text-800 sort align-middle w-auto">Name</th>
                    <th class="text-800 sort align-middle w-auto">Department</th>
                    <th class="text-800 sort align-middle w-auto">Status</th>
                </tr>
            </thead>
            <tbody class="list" id="table-attendance-body">
                <?php if (!empty($students)): ?>
                    <?php foreach ($students as $student): ?>
                        <?php if($activities['activity_id'] == $student->activity_id):?>
                        <tr class="attendance-row" 
                            data-bs-toggle="modal" 
                            data-bs-target="#attendanceModal"
                            data-student-id="<?php echo $student->student_id; ?>"
                            data-name="<?php echo $student->first_name . ' ' . $student->last_name; ?>"
                            data-department="<?php echo $student->dept_name; ?>"
                            data-status="<?php echo $student->attendance_status; ?>"
                            data-am-in="<?php echo $student->am_in ?? 'N/A'; ?>"
                            data-am-out="<?php echo $student->am_out ?? 'N/A'; ?>"
                            data-pm-in="<?php echo $student->pm_in ?? 'N/A'; ?>"
                            data-pm-out="<?php echo $student->pm_out ?? 'N/A'; ?>">
                            
                            <td class="align-middle client white-space-nowrap pe-3 pe-xxl-4 ps-2">
                                <h6 class="mb-0"><?php echo htmlspecialchars($student->student_id); ?></h6>
                            </td>
                            <td class="align-middle client white-space-nowrap pe-3 pe-xxl-4 ps-2">
                                <h6 class="mb-0"><?php echo htmlspecialchars($student->first_name . ' ' . $student->last_name); ?></h6>
                            </td>
                            <td class="align-middle subject py-2 pe-4">
                                <h6 class="mb-0"><?php echo htmlspecialchars($student->dept_name); ?></h6>
                            </td>
                            <td class="align-middle status fs-9 pe-4">
                                <?php
                                $badgeClass = 'badge-subtle-secondary';
                                if ($student->attendance_status == 'Present') {
                                    $badgeClass = 'badge-subtle-success';
                                } elseif ($student->attendance_status == 'Absent') {
                                    $badgeClass = 'badge-subtle-danger';
                                } elseif ($student->attendance_status == 'Incomplete') {
                                    $badgeClass = 'badge-subtle-warning';
                                }
                                ?>
                                <small class="badge rounded <?php echo $badgeClass; ?>">
                                    <?php echo htmlspecialchars($student->attendance_status); ?>
                                </small>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr id="no-results">
                        <td colspan="4" class="fw-bold text-center fs-8">
                            No excuse letter listed
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
          <div class="text-center d-none" id="tickets-table-fallback">
            <p class="fw-bold fs-8 mt-3">No Student Found</p>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="d-flex justify-content-center"><button class="btn btn-sm btn-falcon-default me-1" type="button" title="Previous" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
          <ul class="pagination mb-0"></ul><button class="btn btn-sm btn-falcon-default ms-1" type="button" title="Next" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
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
                    <label for="semester-filter" class="form-label">Semester</label>
                    <select id="semester-filter" class="form-select">
                        <option value="" selected>Select Semester</option>
                        <option value="1st-semester">1st Semester</option>
                        <option value="2nd-semester">2nd Semester</option>
                    </select>
                </div>
                <!-- Year Filter with Range -->
                <div class="mb-3">
                    <label for="year-filter" class="form-label">Year Range</label>
                    <select id="year-filter" class="form-select">
                        <option value="" selected>Select Academic Year</option>
                        <option value="2024-2025">2024-2025</option>
                        <option value="2025-2026">2025-2026</option>
                        <option value="2026-2027">2026-2027</option>
                        <option value="2027-2028">2027-2028</option>
                        <option value="2028-2029">2028-2029</option>
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

<!-- Bootstrap Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attendanceModalLabel">Attendance Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>ID Number:</strong> <span id="modal-student-id"></span></p>
                <p><strong>Name:</strong> <span id="modal-name"></span></p>
                <p><strong>Department:</strong> <span id="modal-department"></span></p>
                <p><strong>Status:</strong> <span id="modal-status"></span></p>
                <hr>
                <p><strong>AM In:</strong> <span id="modal-am-in"></span></p>
                <p><strong>AM Out:</strong> <span id="modal-am-out"></span></p>
                <p><strong>PM In:</strong> <span id="modal-pm-in"></span></p>
                <p><strong>PM Out:</strong> <span id="modal-pm-out"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const attendanceRows = document.querySelectorAll(".attendance-row");

    attendanceRows.forEach(row => {
        row.addEventListener("click", function () {
            document.getElementById("modal-student-id").innerText = this.getAttribute("data-student-id");
            document.getElementById("modal-name").innerText = this.getAttribute("data-name");
            document.getElementById("modal-department").innerText = this.getAttribute("data-department");
            document.getElementById("modal-status").innerText = this.getAttribute("data-status");
            document.getElementById("modal-am-in").innerText = this.getAttribute("data-am-in");
            document.getElementById("modal-am-out").innerText = this.getAttribute("data-am-out");
            document.getElementById("modal-pm-in").innerText = this.getAttribute("data-pm-in");
            document.getElementById("modal-pm-out").innerText = this.getAttribute("data-pm-out");
        });
    });
});
</script>


<script>
function applyFilters() {
    // Get selected values from the modal filters
    var semester = document.getElementById("semester-filter").value;
    var yearRange = document.getElementById("year-filter").value;

    // Get all activity rows
    var activityRows = document.querySelectorAll(".activity-row");
    var noActivityRow = document.getElementById("no-activity-row");
    var filteredRows = 0;

    // Loop through each activity row
    activityRows.forEach(function(row) {
        var rowSemester = row.getAttribute("data-semester");
        var rowYear = row.getAttribute("data-academic-year");

        // Check if the row matches the selected filters
        if ((semester === "" || semester === rowSemester) && (yearRange === "" || yearRange === rowYear)) {
            row.style.display = ""; // Show the row if it matches
            filteredRows++;
        } else {
            row.style.display = "none"; // Hide the row if it doesn't match
        }
    });

    // Show or hide the "No events listed" row
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

</script>