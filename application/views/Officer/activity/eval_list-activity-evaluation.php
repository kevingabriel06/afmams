<div class="d-flex justify-content-end mb-3">
    <a href="<?php echo site_url('officer/create-evaluation-form'); ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Evaluation Form
    </a>
</div>

<div class="row gx-3">
    <div class="col-xxl-10 col-xl-12">
        <div class="card" id="evaluationTable"
            data-list='{"valueNames":["form","activity","status"],"page":10,"pagination":true}'>
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <h5 class="mb-0 px-x1">List of Evaluation Forms</h5>
                    <div class="d-flex align-items-center px-x1">
                        <div class="input-group input-search-width">
                            <input id="searchInput" class="form-control form-control-sm shadow-none search" type="search" placeholder="Search by Activity" />
                            <button class="btn btn-sm btn-outline-secondary" type="button">
                                <span class="fa fa-search fs-10"></span>
                            </button>
                        </div>
                        <button class="btn btn-sm btn-falcon-default ms-2" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <span class="fas fa-filter"></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive scrollbar">
                    <table class="table table-hover table-striped overflow-hidden">
                        <thead class="bg-200">
                            <tr>
                                <th>Form Title</th>
                                <th class="text-nowrap">Activity Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="list" id="table-evaluation-body">
                            <?php foreach ($evaluation as $eval): ?>
                                <tr class="evaluation-row" data-start-date="<?php echo $eval->start_date_evaluation; ?>" data-status="<?php echo $eval->status_evaluation; ?>">
                                    <td class="text-nowrap form"><?php echo $eval->title ?></td>
                                    <td class="text-nowrap activity"><?php echo $eval->activity_title; ?></td>
                                    <td class="text-nowrap">
                                        <?php echo date('Y-m-d', strtotime($eval->start_date_evaluation)) . ' | ' . date('h:i A', strtotime($eval->start_date_evaluation)); ?>
                                    </td>
                                    <td class="text-nowrap">
                                        <?php echo date('Y-m-d', strtotime($eval->end_date_evaluation)) . ' | ' . date('h:i A', strtotime($eval->end_date_evaluation)); ?>
                                    </td>
                                    <td class="status">
                                        <?php if ($eval->status_evaluation == 'Completed'): ?>
                                            <span class="badge rounded-pill badge-subtle-success">
                                                <?php echo $eval->status_evaluation; ?> <span class="ms-1 fas fa-check"></span>
                                            </span>
                                        <?php elseif ($eval->status_evaluation == 'Ongoing'): ?>
                                            <span class="badge rounded-pill badge-subtle-warning">
                                                <?php echo $eval->status_evaluation; ?> <span class="ms-1 fas fa-hourglass-half"></span>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge rounded-pill badge-subtle-danger">
                                                <?php echo $eval->status_evaluation; ?> <span class="ms-1 fas fa-clock"></span>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="dropdown font-sans-serif position-static">
                                            <button class="btn btn-link text-600 btn-sm dropdown-toggle btn-reveal border rounded-circle p-2"
                                                style="width: 36px; height: 36px;" type="button"
                                                data-bs-toggle="dropdown" data-boundary="window"
                                                aria-haspopup="true" aria-expanded="false">
                                                <span class="fas fa-ellipsis-h fs-10"></span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end border py-0">
                                                <div class="py-2">
                                                    <?php if ($eval->status_evaluation == 'Completed'): ?>
                                                        <a class="dropdown-item" href="<?php echo site_url('officer/list-evaluation-responses/' . $eval->form_id); ?>">View Responses</a>
                                                    <?php elseif ($eval->status_evaluation == 'Ongoing' || $eval->status_evaluation == 'Upcoming') : ?>
                                                        <a class="dropdown-item" href="<?php echo site_url('officer/view-evaluation-form/' . $eval->form_id); ?>">View Form</a>
                                                        <a class="dropdown-item text-danger" href="<?php echo site_url('officer/edit-evaluation-form/' . $eval->form_id); ?>">Edit</a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="text-center d-none" id="evaluation-table-fallback">
                        <span class="fas fa-calendar-times fa-2x text-muted"></span>
                        <p class="fw-bold fs-8 mt-3">No Activity Found</p>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-center">
                <button class="btn btn-sm btn-falcon-default me-1" type="button" data-list-pagination="prev">
                    <span class="fas fa-chevron-left"></span>
                </button>
                <ul class="pagination mb-0"></ul>
                <button class="btn btn-sm btn-falcon-default ms-1" type="button" data-list-pagination="next">
                    <span class="fas fa-chevron-right"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Filter -->
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
    document.addEventListener("DOMContentLoaded", function() {
        let options = {
            valueNames: ["form", "activity", "status"],
            page: 10,
            pagination: true,
        };
        let evalList = new List("evaluationTable", options); // Initialize List.js

        const searchInput = document.getElementById("searchInput");
        const fallbackMessage = document.getElementById("evaluation-table-fallback");

        searchInput.addEventListener("input", function() {
            evalList.search(this.value); // Perform the search

            // Check for visible rows after filtering
            const visibleRows = document.querySelectorAll(".list tr:not([style*='display: none'])").length;

            // Toggle the fallback message visibility
            fallbackMessage.classList.toggle("d-none", visibleRows > 0);
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
            const selectedStatus = $('#status-filter').val(); // Make sure this exists
            let startDate, endDate;

            // Validate year range (must be exactly a one-year difference)
            if (!selectedStartYear || !selectedEndYear || selectedEndYear - selectedStartYear !== 1) {
                $('#start-year, #end-year').addClass('is-invalid');
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Academic Year',
                    text: 'Please select a valid academic year range with a one-year difference.',
                    confirmButtonText: 'OK'
                });
                return;
            } else {
                $('#start-year, #end-year').removeClass('is-invalid');
            }

            // Define the exact date range for 1st and 2nd semesters
            if (selectedSemester === "1st-semester") {
                startDate = new Date(selectedStartYear, 7, 1); // August 1
                endDate = new Date(selectedStartYear, 11, 31); // December 31
            } else if (selectedSemester === "2nd-semester") {
                startDate = new Date(selectedEndYear, 0, 1); // January 1
                endDate = new Date(selectedEndYear, 6, 31); // July 31
            } else {
                // Full academic year (Jan 1 - Dec 31)
                startDate = new Date(selectedStartYear, 0, 1);
                endDate = new Date(selectedEndYear, 11, 31);
            }

            filterActivitiesByDateAndStatus(startDate, endDate, selectedStatus);
        }

        // Function to filter activities based on the selected date range and status
        function filterActivitiesByDateAndStatus(startDate, endDate, status) {
            let activities = document.querySelectorAll('.evaluation-row'); // Target the table rows
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
            let fallbackMessage = document.getElementById("evaluation-table-fallback");
            if (hasVisibleActivity) {
                fallbackMessage.classList.add("d-none");
            } else {
                fallbackMessage.classList.remove("d-none");
            }
        }
    });
</script>