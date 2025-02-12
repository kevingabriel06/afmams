<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<div class="row gx-3">
    <div class="col-xxl-10 col-xl-12">
        <div class="card" id="ticketsTable"
            data-list='{"valueNames":["activity","status"],"page":11,"pagination":true,"fallback":"tickets-table-fallback"}'>

            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h5 class="mb-0">All Excuse Form</h5>
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
                                <th class="text-900 px-6 py-2">Activity</th>
                                <th class="text-900 px-7 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="list" id="table-ticket-body">
                            <?php foreach ($activities as $activity) : ?>
                                <!-- ADMIN -->
                                <?php if ($role == 'Admin' && empty($activity->dept_id) && empty($activity->org_id)) : ?>
                                    <?php
                                    $startDate = new DateTime($activity->start_date);
                                    $month = $startDate->format('m');
                                    $year = $startDate->format('Y');
                                    $semester = ($month >= 8 && $month <= 12) ? '1st-semester' : '2nd-semester';
                                    $academicYear = ($semester === '1st-semester') ? $year . '-' . ($year + 1) : ($year - 1) . '-' . $year;
                                    ?>
                                    <tr class="activity-row" data-semester="<?php echo $semester; ?>" data-academic-year="<?php echo $academicYear; ?>" data-start-date="<?php echo $activity->start_date; ?>" data-status="<?php echo $activity->status; ?>">
                                        <td class="align-middle text-nowrap px-6 py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-xl">
                                                    <img class="rounded-circle" src="<?php echo base_url('assets/coverEvent/') . $activity->activity_image; ?>" alt="" />
                                                </div>
                                                <a class="ms-3 text-decoration-none text-dark fw-semibold d-block link-hover"
                                                    href="<?php echo site_url('admin/list-of-excuse-letter/' . $activity->activity_id); ?>">
                                                    <?php echo $activity->activity_title; ?>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="px-7 py-2">
                                            <?php if ($activity->status === 'Completed'): ?>
                                                <span class="badge badge rounded-pill d-block p-2 badge-subtle-success">Completed<span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span></span>
                                            <?php elseif ($activity->status === 'Upcoming'): ?>
                                                <span class="badge badge rounded-pill d-block p-2 badge-subtle-danger">Upcoming<span class="ms-1 fas fa-redo" data-fa-transform="shrink-2"></span></span>
                                            <?php elseif ($activity->status === 'Ongoing'): ?>
                                                <span class="badge badge rounded-pill d-block p-2 badge-subtle-warning">Ongoing<span class="ms-1 fas fa-stream" data-fa-transform="shrink-2"></span></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <!-- ORGANIZATION -->
                                <?php elseif ($role == 'Officer' && isset($activity, $organization) && is_object($activity) && is_object($organization) && isset($activity->org_id, $organization->org_id) && $activity->org_id == $organization->org_id && empty($activity->dept_id)): ?>
                                    <?php
                                    $startDate = new DateTime($activity->start_date);
                                    $month = $startDate->format('m');
                                    $year = $startDate->format('Y');
                                    $semester = ($month >= 8 && $month <= 12) ? '1st-semester' : '2nd-semester';
                                    $academicYear = ($semester === '1st-semester') ? $year . '-' . ($year + 1) : ($year - 1) . '-' . $year;
                                    ?>
                                    <tr class="activity-row" data-semester="<?php echo $semester; ?>" data-academic-year="<?php echo $academicYear; ?>" data-start-date="<?php echo $activity->start_date; ?>" data-status="<?php echo $activity->status; ?>">
                                        <td class="align-middle text-nowrap px-6 py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-xl">
                                                    <img class="rounded-circle" src="<?php echo base_url('assets/coverEvent/') . $activity->activity_image; ?>" alt="" />
                                                </div>
                                                <a class="ms-3 text-decoration-none text-dark fw-semibold d-block link-hover"
                                                    href="<?php echo site_url('admin/list-of-excuse-letter/' . $activity->activity_id); ?>">
                                                    <?php echo $activity->activity_title; ?>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="px-7 py-2">
                                            <?php if ($activity->status === 'Completed'): ?>
                                                <span class="badge badge rounded-pill d-block p-2 badge-subtle-success">Completed<span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span></span>
                                            <?php elseif ($activity->status === 'Upcoming'): ?>
                                                <span class="badge badge rounded-pill d-block p-2 badge-subtle-danger">Upcoming<span class="ms-1 fas fa-redo" data-fa-transform="shrink-2"></span></span>
                                            <?php elseif ($activity->status === 'Ongoing'): ?>
                                                <span class="badge badge rounded-pill d-block p-2 badge-subtle-warning">Ongoing<span class="ms-1 fas fa-stream" data-fa-transform="shrink-2"></span></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <!-- DEPARtMENT -->
                                <?php elseif ($role == 'Officer' && isset($activity, $department) && is_object($activity) && is_object($department) && isset($activity->dept_id, $department->dept_id) && $activity->dept_id == $department->dept_id && empty($activity->org_id)): ?>
                                    <?php
                                    $startDate = new DateTime($activity->start_date);
                                    $month = $startDate->format('m');
                                    $year = $startDate->format('Y');
                                    $semester = ($month >= 8 && $month <= 12) ? '1st-semester' : '2nd-semester';
                                    $academicYear = ($semester === '1st-semester') ? $year . '-' . ($year + 1) : ($year - 1) . '-' . $year;
                                    ?>
                                    <tr class="activity-row" data-semester="<?php echo $semester; ?>" data-academic-year="<?php echo $academicYear; ?>" data-start-date="<?php echo $activity->start_date; ?>" data-status="<?php echo $activity->status; ?>">
                                        <td class="align-middle text-nowrap px-6 py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-xl">
                                                    <img class="rounded-circle" src="<?php echo base_url('assets/coverEvent/') . $activity->activity_image; ?>" alt="" />
                                                </div>
                                                <a class="ms-3 text-decoration-none text-dark fw-semibold d-block link-hover"
                                                    href="<?php echo site_url('admin/list-of-excuse-letter/' . $activity->activity_id); ?>">
                                                    <?php echo $activity->activity_title; ?>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="px-7 py-2">
                                            <?php if ($activity->status === 'Completed'): ?>
                                                <span class="badge badge rounded-pill d-block p-2 badge-subtle-success">Completed<span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span></span>
                                            <?php elseif ($activity->status === 'Upcoming'): ?>
                                                <span class="badge badge rounded-pill d-block p-2 badge-subtle-danger">Upcoming<span class="ms-1 fas fa-redo" data-fa-transform="shrink-2"></span></span>
                                            <?php elseif ($activity->status === 'Ongoing'): ?>
                                                <span class="badge badge rounded-pill d-block p-2 badge-subtle-warning">Ongoing<span class="ms-1 fas fa-stream" data-fa-transform="shrink-2"></span></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <!-- "No activities listed" Row -->
                            <tr id="no-activity-row" style="display: none;">
                                <td colspan="2" class="text-center text-muted fs-8 fw-bold py-2 bg-light">
                                    <span class="fas fa-calendar-times fa-2x text-muted"></span> <!-- Calendar icon -->
                                    <h5 class="mt-2 mb-1">No activities listed.</h5>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-center d-none" id="tickets-table-fallback">
                        <span class="fas fa-calendar-times fa-2x text-muted"></span> <!-- Calendar icon -->
                        <p class="fw-bold fs-8 mt-3">No Activity Found</p>
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

                    <!-- Status Filter -->
                    <div class="mb-3">
                        <label for="status-filter" class="form-label">Activity Status</label>
                        <select id="status-filter" class="form-select">
                            <option value="" selected>Select Status</option>
                            <option value="Completed">Completed</option>
                            <option value="Ongoing">Ongoing</option>
                            <option value="Upcoming">Upcoming</option>
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
            var semester = document.getElementById("semester-filter").value;
            var yearRange = document.getElementById("year-filter").value;
            var status = document.getElementById("status-filter").value;

            // Get all activity rows
            var activityRows = document.querySelectorAll(".activity-row");
            var noActivityRow = document.getElementById("no-activity-row");
            var filteredRows = 0;

            // Loop through each activity row
            activityRows.forEach(function(row) {
                var rowSemester = row.getAttribute("data-semester");
                var rowYear = row.getAttribute("data-academic-year");
                var rowStatus = row.getAttribute("data-status"); // Add status attribute in PHP

                // Check if the row matches the selected filters
                if (
                    (semester === "" || semester === rowSemester) &&
                    (yearRange === "" || yearRange === rowYear) &&
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
                valueNames: ["activity", "status"],
                page: 11,
                pagination: true
            };

            var excuseList = new List("ticketsTable", options);

            document.getElementById("searchInput").addEventListener("keyup", function() {
                excuseList.search(this.value);
            });
        });
    </script>