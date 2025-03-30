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
                                <tr class="activity-row" data-start-date="<?php echo $activity->start_date; ?>" data-status="<?php echo $activity->status; ?>">
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

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="status-filter">Status</label>
                        <select id="status-filter" class="form-select">
                            <option value="">Select Status</option>
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const currentYear = new Date().getFullYear();
            const startYearDropdown = $('#start-year');
            const endYearDropdown = $('#end-year');
            const yearFilter = document.getElementById("year-filter");
            const statusFilter = document.getElementById("status-filter"); // Reference to status filter dropdown

            // Populate Start Year dropdown dynamically from the current year down to 1900
            for (let year = currentYear; year >= 1900; year--) {
                startYearDropdown.append(new Option(year, year));
            }

            // Update End Year based on selected Start Year
            startYearDropdown.on('change', function() {
                const selectedStartYear = parseInt(this.value);
                endYearDropdown.empty().append(new Option("Select End Year", "", true, true)); // Reset options

                if (selectedStartYear) {
                    // Automatically set end year as one year after the selected start year
                    endYearDropdown.append(new Option(selectedStartYear + 1, selectedStartYear + 1));
                }
            });

            // Apply filters based on semester, academic year, and status
            window.applyFilters = function() {
                const selectedStartYear = parseInt($('#start-year').val());
                const selectedEndYear = parseInt($('#end-year').val());
                const selectedSemester = $('#semester-filter').val();
                const selectedStatus = statusFilter.value; // Get selected status value
                let startDate, endDate;

                // Validate year range (must be exactly a one-year difference)
                if (!selectedStartYear || !selectedEndYear || selectedEndYear - selectedStartYear !== 1) {
                    $('#start-year, #end-year').addClass('is-invalid');
                    alert("Please select a valid academic year range with a one-year difference.");
                    return;
                } else {
                    $('#start-year, #end-year').removeClass('is-invalid');
                }

                // Define the exact date range for 1st and 2nd semesters
                if (selectedSemester === "1st-semester") {
                    startDate = new Date(selectedStartYear, 7, 1); // August 1, selected start year (e.g., Aug 1, 2024)
                    endDate = new Date(selectedStartYear, 11, 31); // December 31, selected start year (e.g., Dec 31, 2024)
                } else if (selectedSemester === "2nd-semester") {
                    startDate = new Date(selectedEndYear, 0, 1); // January 1, selected end year (e.g., Jan 1, 2025)
                    endDate = new Date(selectedEndYear, 6, 31); // July 31, selected end year (e.g., July 31, 2025)
                } else {
                    // Default to the full academic year (Jan 1, start year - Dec 31, end year)
                    startDate = new Date(selectedStartYear, 0, 1);
                    endDate = new Date(selectedEndYear, 11, 31);
                }

                filterActivitiesByDateAndStatus(startDate, endDate, selectedStatus);
            };

            // Function to filter activities based on the selected date range and status
            function filterActivitiesByDateAndStatus(startDate, endDate, status) {
                let activities = document.querySelectorAll('.activity-row'); // Target the table rows
                let hasVisibleActivity = false;

                activities.forEach(activity => {
                    let activityDateStr = activity.getAttribute('data-start-date');
                    let activityStatus = activity.getAttribute('data-status'); // Get status from data attribute

                    if (!activityDateStr) return; // Skip if no date

                    let activityDate = new Date(activityDateStr);

                    // Apply date and status filters
                    if (
                        activityDate >= startDate &&
                        activityDate <= endDate &&
                        (status === "" || activityStatus === status) // Filter by status only if selected
                    ) {
                        activity.style.display = 'table-row';
                        hasVisibleActivity = true;
                    } else {
                        activity.style.display = 'none';
                    }
                });

                toggleNoActivityMessage(hasVisibleActivity);

                // Close the filter modal after applying filters
                let filterModal = document.getElementById('filterModal');
                if (filterModal) {
                    let modalInstance = bootstrap.Modal.getInstance(filterModal);
                    if (modalInstance) modalInstance.hide();
                }
            }

            function toggleNoActivityMessage(hasVisibleActivity) {
                let fallbackMessage = document.getElementById("tickets-table-fallback");
                if (hasVisibleActivity) {
                    fallbackMessage.classList.add("d-none");
                } else {
                    fallbackMessage.classList.remove("d-none");
                }
            }
        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            displayPastAndNext15DaysActivities();
        });

        function displayPastAndNext15DaysActivities() {
            let today = new Date();

            // Define past 15 days range
            let past15Days = new Date();
            past15Days.setDate(today.getDate() - 15);

            // Define next 15 days range
            let next15Days = new Date();
            next15Days.setDate(today.getDate() + 15);

            let activities = document.querySelectorAll(".activity-row");
            let hasValidActivities = false;

            activities.forEach(function(activity) {
                let startDate = activity.getAttribute("data-start-date");

                if (startDate) {
                    let activityDate = new Date(startDate);

                    if (activityDate >= past15Days && activityDate <= next15Days) {
                        activity.style.display = "table-row";
                        hasValidActivities = true;
                    } else {
                        activity.style.display = "none";
                    }
                }
            });

            toggleNoActivityMessage(hasValidActivities);
        }

        function toggleNoActivityMessage(hasActivities) {
            let noActivityRow = document.getElementById('no-activity-row');
            if (noActivityRow) {
                noActivityRow.style.display = hasActivities ? 'none' : 'table-row';
            }
        }
    </script>