<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<div class="row gx-3">
    <div class="col-xxl-10 col-xl-12">
        <div class="card" id="attendanceTable"
            data-list='{"valueNames":["activity","status"],"page":11,"pagination":true,"fallback":"attendance-table-fallback"}'>

            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h5 class="mb-0">Attendance - All Activities</h5>
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
                                <th scope="col" class="text-900 px-6 py-2">Activity</th>
                                <th scope="col" class="text-900 px-7 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="list" id="table-ticket-body">
                            <?php foreach ($activities as $activity) : ?>
                                <?php
                                $startDate = new DateTime($activity->start_date);
                                $month = $startDate->format('m');
                                $year = $startDate->format('Y');
                                $semester = ($month >= 8 && $month <= 12) ? '1st-semester' : '2nd-semester';
                                $academicYear = ($semester === '1st-semester') ? $year . '-' . ($year + 1) : ($year - 1) . '-' . $year;
                                ?>
                                <tr class="activity-row" data-semester="<?php echo $semester; ?>" data-academic-year="<?php echo $academicYear; ?>" data-start-date="<?php echo $activity->start_date; ?>" data-status="<?php echo $activity->status; ?>">
                                    <td class="align-middle text-nowrap px-6 py-2 activity">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xl">
                                                <img class="rounded-circle"
                                                    src="<?php echo !empty($activity->activity_image)
                                                                ? base_url('assets/coverEvent/' . $activity->activity_image)
                                                                : base_url('assets/image/OIP.jpg'); ?>" />
                                            </div>
                                            <?php if ($activity->status == 'Upcoming'): ?>
                                                <a class="ms-3 text-decoration-none text-dark fw-semibold d-block link-hover"
                                                    href="javascript:void(0);"
                                                    onclick="Swal.fire({
                                                        icon: 'info',
                                                        title: 'Activity Not Available',
                                                        text: 'This activity is upcoming and not yet available to view.',
                                                        confirmButtonColor: '#3085d6'
                                                    })">
                                                    <?php echo $activity->activity_title; ?>
                                                </a>
                                            <?php else: ?>
                                                <a class="ms-3 text-decoration-none text-dark fw-semibold d-block link-hover"
                                                    href="<?php echo site_url('admin/list-attendees/' . $activity->activity_id); ?>">
                                                    <?php echo $activity->activity_title; ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-nowrap px-7 py-2 status">
                                        <?php if ($activity->status === 'Completed'): ?>
                                            <span class="badge badge rounded-pill d-block p-2 badge-subtle-success">Completed<span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span></span>
                                        <?php elseif ($activity->status === 'Upcoming'): ?>
                                            <span class="badge badge rounded-pill d-block p-2 badge-subtle-danger">Upcoming<span class="ms-1 fas fa-redo" data-fa-transform="shrink-2"></span></span>
                                        <?php elseif ($activity->status === 'Ongoing'): ?>
                                            <span class="badge badge rounded-pill d-block p-2 badge-subtle-warning">Ongoing<span class="ms-1 fas fa-stream" data-fa-transform="shrink-2"></span></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
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
                    <div class="text-center d-none" id="attendance-table-fallback">
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

                    <!-- Year Picker for Academic Year -->
                    <div class="mb-3">
                        <label for="year-picker" class="form-label">Academic Year</label>
                        <div class="input-group">
                            <select id="start-year" class="form-select">
                                <option value="" selected>Select Start Year</option>
                            </select>
                            <span class="input-group-text">-</span>
                            <select id="end-year" class="form-select">
                                <option value="" selected>Select End Year</option>
                            </select>
                        </div>
                        <div class="invalid-feedback">
                            Please select a valid academic year range with a 1-year difference.
                        </div>
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
        document.addEventListener("DOMContentLoaded", function() {
            const currentYear = new Date().getFullYear();
            const startYearDropdown = $('#start-year');
            const endYearDropdown = $('#end-year');

            // Populate Start Year dropdown
            for (let year = currentYear; year >= 1900; year--) {
                startYearDropdown.append(new Option(year, year));
            }

            // Update End Year dropdown based on selected start year
            startYearDropdown.on('change', function() {
                const selectedStartYear = parseInt(this.value);
                endYearDropdown.empty().append(new Option("Select End Year", "", true, true));

                if (selectedStartYear) {
                    endYearDropdown.append(new Option(selectedStartYear + 1, selectedStartYear + 1));
                }
            });

            // Apply filters when button is clicked
            window.applyFilters = function() {
                const selectedStartYear = parseInt($('#start-year').val());
                const selectedEndYear = parseInt($('#end-year').val());
                const selectedSemester = $('#semester-filter').val();
                const selectedStatus = $('#status-filter').val();

                let isValid = true;

                if (!selectedStartYear || !selectedEndYear || selectedEndYear - selectedStartYear !== 1) {
                    $('#start-year, #end-year').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#start-year, #end-year').removeClass('is-invalid');
                }

                if (!isValid) {
                    alert("Please select a valid academic year range with a one-year difference.");
                    return;
                }

                let startDate, endDate;

                if (selectedSemester === "1st-semester") {
                    startDate = new Date(selectedStartYear, 7, 1); // August 1
                    endDate = new Date(selectedStartYear, 11, 31); // December 31
                } else if (selectedSemester === "2nd-semester") {
                    startDate = new Date(selectedEndYear, 0, 1); // January 1
                    endDate = new Date(selectedEndYear, 6, 31); // July 31
                } else {
                    startDate = new Date(selectedStartYear, 0, 1);
                    endDate = new Date(selectedEndYear, 11, 31);
                }

                let rows = document.querySelectorAll("tr.activity-row");
                let hasVisibleActivity = false;

                rows.forEach(row => {
                    let activityDateStr = row.getAttribute("data-start-date");
                    let activitySemester = row.getAttribute("data-semester");
                    let activityYear = row.getAttribute("data-academic-year");
                    let activityStatus = row.getAttribute("data-status");

                    if (!activityDateStr) return;

                    let activityDate = new Date(activityDateStr);
                    let matchesSemester = selectedSemester === "" || selectedSemester === activitySemester;
                    let matchesYear = `${selectedStartYear}-${selectedEndYear}` === activityYear;
                    let matchesStatus = selectedStatus === "" || selectedStatus === activityStatus;
                    let matchesDate = activityDate >= startDate && activityDate <= endDate;

                    if (matchesSemester && matchesYear && matchesStatus && matchesDate) {
                        row.style.display = "";
                        hasVisibleActivity = true;
                    } else {
                        row.style.display = "none";
                    }
                });

                toggleNoActivityMessage(hasVisibleActivity);

                const modalElement = document.getElementById("filterModal");
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();
            };

            // Automatically show past and upcoming 15-day activities
            displayPastAndNext15DaysActivities();
        });

        function displayPastAndNext15DaysActivities() {
            const today = new Date();
            const past15 = new Date(today);
            const next15 = new Date(today);
            past15.setDate(today.getDate() - 15);
            next15.setDate(today.getDate() + 15);

            let rows = document.querySelectorAll("tr.activity-row");
            let hasVisibleActivity = false;

            rows.forEach(row => {
                const dateStr = row.getAttribute("data-start-date");
                if (!dateStr) return;

                const activityDate = new Date(dateStr);

                if (activityDate >= past15 && activityDate <= next15) {
                    row.style.display = "";
                    hasVisibleActivity = true;
                } else {
                    row.style.display = "none";
                }
            });

            toggleNoActivityMessage(hasVisibleActivity);
        }

        function toggleNoActivityMessage(hasActivity) {
            const messageRow = document.getElementById('no-activity-row');
            if (messageRow) {
                messageRow.style.display = hasActivity ? 'none' : 'table-row';
            }
        }
    </script>