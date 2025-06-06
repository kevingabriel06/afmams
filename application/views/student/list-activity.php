<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<div class="card mb-3 mb-lg-0">
    <!-- Card Header with Filter Button -->
    <div class="card-header bg-body-tertiary d-flex justify-content-between">
        <h5 class="mb-0">List of Activities</h5>
        <button class="btn btn-falcon-default btn-sm mx-2" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
            <span class="fas fa-filter" data-fa-transform="shrink-3 down-2"></span>
            <span class="d-none d-sm-inline-block ms-1">Filter</span>
        </button>
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
            const yearFilter = document.getElementById("year-filter");

            // Populate Start Year dropdown dynamically from current year down to 1900
            for (let year = currentYear; year >= 1900; year--) {
                startYearDropdown.append(new Option(year, year));
            }

            // Update End Year based on selected Start Year
            startYearDropdown.on('change', function() {
                const selectedStartYear = parseInt(this.value);
                endYearDropdown.empty().append(new Option("Select End Year", "", true, true)); // Reset options

                if (selectedStartYear) {
                    // End year is exactly one year after the start year
                    endYearDropdown.append(new Option(selectedStartYear + 1, selectedStartYear + 1));
                }
            });

            // Apply filters based on selected semester and academic year
            window.applyFilters = function() {
                const selectedStartYear = parseInt($('#start-year').val());
                const selectedEndYear = parseInt($('#end-year').val());
                const selectedSemester = $('#semester-filter').val();
                let startDate, endDate;

                // Validate year range (must be exactly a one-year difference)
                if (!selectedStartYear || !selectedEndYear || selectedEndYear - selectedStartYear !== 1) {
                    $('#start-year, #end-year').addClass('is-invalid');
                    alert("Please select a valid academic year range with a one-year difference.");
                    return;
                } else {
                    $('#start-year, #end-year').removeClass('is-invalid');
                }

                // Define date range based on selected semester
                if (selectedSemester === "1st-semester") {
                    startDate = new Date(selectedStartYear, 7, 1); // August 1, start year
                    endDate = new Date(selectedStartYear, 11, 31); // December 31, start year
                } else if (selectedSemester === "2nd-semester") {
                    startDate = new Date(selectedEndYear, 0, 1); // January 1, end year
                    endDate = new Date(selectedEndYear, 6, 31); // July 31, end year
                } else {
                    // Default to full academic year
                    startDate = new Date(selectedStartYear, 0, 1);
                    endDate = new Date(selectedEndYear, 11, 31);
                }

                filterActivitiesByDateRange(startDate, endDate);
            };

            // Function to filter activities based on the selected date range
            function filterActivitiesByDateRange(startDate, endDate) {
                let activities = document.querySelectorAll('.activity');
                let hasVisibleActivity = false;

                activities.forEach(activity => {
                    let activityDateStr = activity.getAttribute('data-start-date');
                    if (!activityDateStr) return;

                    let activityDate = new Date(activityDateStr);

                    if (activityDate >= startDate && activityDate <= endDate) {
                        activity.style.display = 'block';
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
        });
    </script>




    <div class="card-body fs-10">
        <div class="row">
            <?php foreach ($activities as $activity): ?>
                <div class="col-md-6 h-100 activity" data-start-date="<?php echo $activity->start_date; ?>">
                    <div class="d-flex btn-reveal-trigger">
                        <div class="calendar">
                            <?php
                            $start_date = strtotime($activity->start_date);
                            $month = date('M', $start_date); // e.g., Mar
                            $day = date('j', $start_date);   // e.g., 26
                            $year = date('y', $start_date);  // e.g., 24
                            ?>
                            <span class="calendar-month"><?php echo $month; ?></span>
                            <span class="calendar-day"><?php echo $day; ?></span>
                            <span class="calendar-year" hidden><?php echo $year; ?></span>
                        </div>
                        <div class="flex-1 position-relative ps-3">
                            <p class="mb-1" hidden><?php echo htmlspecialchars($activity->activity_id); ?></p>
                            <h6 class="fs-9 mb-0">
                                <a href="<?php echo site_url('student/activity-details/' . $activity->activity_id); ?>">
                                    <?php echo htmlspecialchars($activity->activity_title); ?>
                                    <?php if ($activity->registration_fee == '0'): ?>
                                        <span class="badge badge-subtle-success rounded-pill">Free</span>
                                    <?php endif; ?>
                                    <?php if ($activity->status == 'Completed'): ?>
                                        <span class="badge badge-subtle-success rounded-pill">Completed</span>
                                    <?php elseif ($activity->status == 'Ongoing'): ?>
                                        <span class="badge badge-subtle-warning rounded-pill">Ongoing</span>
                                    <?php elseif ($activity->status == 'Upcoming'): ?>
                                        <span class="badge badge-subtle-danger rounded-pill">Upcoming</span>
                                    <?php endif; ?>
                                </a>
                            </h6>
                            <p class="mb-1">Organized by <?php echo $activity->organizer; ?></p>
                            <p class="text-1000 mb-0">Time: <?php echo date("h:i A", strtotime($activity->first_schedule)); ?></p>
                            <p class="text-1000 mb-0">Duration:
                                <?php
                                echo date('M j, Y', strtotime($activity->start_date)) . ' - ' . date('M j, Y', strtotime($activity->end_date));
                                ?>
                            </p>
                            <div class="border-bottom border-dashed my-3"></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- No Activities Message -->
            <div id="no-activity" class="card-body text-center" style="display: none">
                <span class="fas fa-calendar-times fa-3x text-muted"></span> <!-- Calendar icon -->
                <h5 class="mt-2 mb-1">No activities listed.</h5>
            </div>

        </div>
    </div>

</div>

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

        let activities = document.querySelectorAll(".activity");
        let hasPastActivities = false;
        let hasUpcomingActivities = false;

        activities.forEach(function(activity) {
            let startDate = activity.getAttribute("data-start-date");

            if (startDate) {
                let activityDate = new Date(startDate);

                if (activityDate >= past15Days && activityDate < today) {
                    activity.classList.add("past-activity"); // Style for past activities
                    activity.style.display = "block";
                    hasPastActivities = true;
                } else if (activityDate >= today && activityDate <= next15Days) {
                    activity.classList.add("upcoming-activity"); // Style for upcoming activities
                    activity.style.display = "block";
                    hasUpcomingActivities = true;
                } else {
                    activity.style.display = "none";
                }
            }
        });

        toggleNoActivityMessage(hasPastActivities, hasUpcomingActivities);
    }


    function formatDateToYMD(date) {
        let year = date.getFullYear();
        let month = String(date.getMonth() + 1).padStart(2, '0'); // Ensure 2-digit month
        let day = String(date.getDate()).padStart(2, '0'); // Ensure 2-digit day
        return `${year}-${month}-${day}`;
    }

    // Show/hide "No activities found" message
    function toggleNoActivityMessage(hasActivities) {
        let noActivityMessage = document.getElementById('no-activity');
        if (noActivityMessage) {
            noActivityMessage.style.display = hasActivities ? 'none' : 'block';
        }
    }
</script>