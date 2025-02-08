<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<div class="card mb-3 mb-lg-0">
    <!-- Card Header with Filter Button -->
  <div class="card-header bg-body-tertiary d-flex justify-content-between">
      <h5 class="mb-0">Activities</h5>
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

  <script>
        // Apply filters based on the selected semester and year range
        function applyFilters() {
            let semester = document.getElementById('semester-filter').value;
            let yearRange = document.getElementById('year-filter').value;

            // Extract start and end years from the selected range
            let [startYear, endYear] = yearRange.split('-').map(Number);

            let startDate, endDate;
            
            // Define date range based on semester selection
            if (semester === "1st-semester") {
                startDate = new Date(startYear, 7, 1);  // August 1, startYear
                endDate = new Date(startYear, 11, 31); // December 31, startYear
            } else if (semester === "2nd-semester") {
                startDate = new Date(endYear, 0, 1);  // January 1, endYear
                endDate = new Date(endYear, 6, 31);  // July 31, endYear
            } else {
                startDate = new Date(startYear, 0, 1); // Default to January 1 of start year
                endDate = new Date(endYear, 11, 31);  // Default to December 31 of end year
            }

            // Get all activity elements
            let activities = document.querySelectorAll('.activity');

            let hasVisibleActivity = false; // Track if any activity matches the filter

            activities.forEach(activity => {
                let activityDateStr = activity.getAttribute('data-start-date'); // Assuming data-start-date is added to div
                if (!activityDateStr) return; // Skip if no date is found

                let activityDate = new Date(activityDateStr);

                // Show/hide based on date range
                if (activityDate >= startDate && activityDate <= endDate) {
                    activity.style.display = 'block';
                    hasVisibleActivity = true; // Activity is visible
                } else {
                    activity.style.display = 'none'; // Activity is hidden
                }
            });

            // Show/hide "No activities listed" message based on `hasVisibleActivity`
            let noActivityMessage = document.getElementById('no-activity');
            if (noActivityMessage) {
                noActivityMessage.style.display = hasVisibleActivity ? 'none' : 'block';
            }

            // Close the modal after applying filters
            var filterModal = document.getElementById('filterModal');
            var modalInstance = bootstrap.Modal.getInstance(filterModal);
            modalInstance.hide();
        }

        // Function to filter activities for the current month by default (not used here)
        function isCurrentMonth(date, currentDate) {
            const firstDayOfCurrentMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const lastDayOfCurrentMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);

            return date >= firstDayOfCurrentMonth && date <= lastDayOfCurrentMonth;
        }

        // Main function to filter activities based on a date range (this may not be necessary with the above)
        function filterActivities(startMonth, startYear, endMonth, endYear) {
            let activities = document.querySelectorAll('.activity');
            let hasVisibleActivity = false;

            activities.forEach(activity => {
                let activityDateStr = activity.getAttribute('data-start-date');
                if (!activityDateStr) return; // Skip if no date is found

                let activityDate = new Date(activityDateStr);
                let activityMonth = activityDate.getMonth();  // 0-11 for months (January = 0)
                let activityYear = activityDate.getFullYear(); // 4-digit year

                // Check if the activity is within the date range
                if (
                    (activityYear > startYear || (activityYear === startYear && activityMonth >= startMonth)) &&
                    (activityYear < endYear || (activityYear === endYear && activityMonth <= endMonth))
                ) {
                    activity.style.display = 'block';
                    hasVisibleActivity = true; // Set to true if at least one activity is visible
                } else {
                    activity.style.display = 'none';
                }
            });

            // Show/hide the "No activities listed" message based on `hasVisibleActivity`
            let noActivityMessage = document.getElementById('no-activity');
            if (noActivityMessage) {
                noActivityMessage.style.display = hasVisibleActivity ? 'none' : 'block';
            }
        }
    </script>

  <div class="card-body fs-10">
      <div class="row">
        <?php foreach ($activities as $activity): ?>
            <?php if ($role == 'Admin'): ?>
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
                                <a href="<?php echo site_url('admin/activity-details/' . $activity->activity_id); ?>">
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
                            <p class="mb-1">Organized by 
                                <?php 
                                    $displayText = "Institution"; // Default text
                                    if (!empty($activity->dept_id)) {
                                        $displayText = htmlspecialchars($activity->dept_name);
                                    } elseif (!empty($activity->org_id)) {
                                        $displayText = htmlspecialchars($activity->org_name);
                                    }
                                ?>
                                <a href="#!" class="text-700"><?php echo $displayText; ?></a>
                            </p>
                            <p class="text-1000 mb-0">Time: 
                                <?php
                                    $start_time = !empty($activity->am_in) ? date('h:i A', strtotime($activity->am_in)) : (!empty($activity->pm_in) ? date('h:i A', strtotime($activity->pm_in)) : 'N/A');
                                    echo $start_time;
                                ?>
                            </p>
                            <p class="text-1000 mb-0">Duration: 
                                <?php
                                    echo date('M j, Y', strtotime($activity->start_date)) . ' - ' . date('M j, Y', strtotime($activity->end_date));
                                ?>
                            </p>
                            <div class="border-bottom border-dashed my-3"></div>
                        </div>
                    </div>
                </div>
            <?php elseif (
                $role == 'Officer' && 
                isset($activity, $department) && 
                is_object($activity) && is_object($department) && 
                isset($activity->dept_id, $department->dept_id) && 
                $activity->dept_id == $department->dept_id && 
                empty($activity->org_id)
            ): ?>
                <div class="col-md-6 h-100 activity" data-start-date="<?php echo $activity->start_date; ?>">
                    <div class="d-flex btn-reveal-trigger">
                        <div class="calendar">
                            <?php
                                $start_date = strtotime($activity->start_date);
                                $month = date('M', $start_date);
                                $day = date('j', $start_date);
                                $year = date('y', $start_date);
                            ?>
                            <span class="calendar-month"><?php echo $month; ?></span>
                            <span class="calendar-day"><?php echo $day; ?></span>
                            <span class="calendar-year" hidden><?php echo $year; ?></span>
                        </div>
                        <div class="flex-1 position-relative ps-3">
                            <p class="mb-1" hidden><?php echo htmlspecialchars($activity->activity_id); ?></p>
                            <h6 class="fs-9 mb-0">
                                <a href="<?php echo site_url('admin/activity-details/' . $activity->activity_id); ?>">
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
                            <p class="mb-1">Organized by 
                                <a href="#!" class="text-700"><?php echo htmlspecialchars($activity->dept_name); ?></a>
                            </p>
                            <p class="text-1000 mb-0">Time:
                                <?php
                                    if (!empty($activity->am_in)) {
                                        $start_time = date('h:i A', strtotime($activity->am_in));
                                    } else {
                                        $start_time = !empty($activity->pm_in) ? date('h:i A', strtotime($activity->pm_in)) : 'N/A';
                                    }
                                    echo $start_time;
                                ?>
                            </p>
                            <p class="text-1000 mb-0">Duration: 
                                <?php
                                    echo date('M j, Y', strtotime($activity->start_date)) . ' - ' . date('M j, Y', strtotime($activity->end_date));
                                ?>
                            </p>
                            <div class="border-bottom border-dashed my-3"></div>
                        </div>
                    </div>
                </div>

            <?php elseif (
                $role == 'Officer' && 
                isset($activity, $organization) && 
                is_object($activity) && is_object($organization) && 
                isset($activity->org_id, $organization->org_id) && 
                $activity->org_id == $organization->org_id && 
                empty($activity->dept_id)
            ): ?>
                <div class="col-md-6 h-100 activity" data-start-date="<?php echo $activity->start_date; ?>">
                    <div class="d-flex btn-reveal-trigger">
                        <div class="calendar">
                            <?php
                                $start_date = strtotime($activity->start_date);
                                $month = date('M', $start_date);
                                $day = date('j', $start_date);
                                $year = date('y', $start_date);
                            ?>
                            <span class="calendar-month"><?php echo $month; ?></span>
                            <span class="calendar-day"><?php echo $day; ?></span>
                            <span class="calendar-year" hidden><?php echo $year; ?></span>
                        </div>
                        <div class="flex-1 position-relative ps-3">
                            <p class="mb-1" hidden><?php echo htmlspecialchars($activity->activity_id); ?></p>
                            <h6 class="fs-9 mb-0">
                                <a href="<?php echo site_url('admin/activity-details/' . $activity->activity_id); ?>">
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
                            <p class="mb-1">Organized by 
                                <a href="#!" class="text-700"><?php echo htmlspecialchars($activity->org_name); ?></a>
                            </p>
                            <p class="text-1000 mb-0">Time:
                                <?php
                                    if (!empty($activity->am_in)) {
                                        $start_time = date('h:i A', strtotime($activity->am_in));
                                    } else {
                                        $start_time = !empty($activity->pm_in) ? date('h:i A', strtotime($activity->pm_in)) : 'N/A';
                                    }
                                    echo $start_time;
                                ?>
                            </p>
                            <p class="text-1000 mb-0">Duration: 
                                <?php
                                    echo date('M j, Y', strtotime($activity->start_date)) . ' - ' . date('M j, Y', strtotime($activity->end_date));
                                ?>
                            </p>
                            <div class="border-bottom border-dashed my-3"></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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
        let currentDate = new Date();
        let currentMonth = currentDate.getMonth() + 1; // JavaScript months are 0-based
        let currentYear = currentDate.getFullYear();
        let activities = document.querySelectorAll(".activity");
        let hasActivities = false;

        activities.forEach(function(activity) {
            let startDate = activity.getAttribute("data-start-date");

            if (startDate) {
                let activityDate = new Date(startDate);
                let activityMonth = activityDate.getMonth() + 1;
                let activityYear = activityDate.getFullYear();

                if (activityMonth === currentMonth && activityYear === currentYear) {
                    hasActivities = true; // At least one activity matches
                } else {
                    activity.style.display = "none"; // Hide other activities
                }
            }
        });

        // If no activities are found, display a message
        if (!hasActivities) {
            let noActivityMessage = document.createElement("p");
            noActivityMessage.textContent = "No activities for this month.";
            noActivityMessage.style.textAlign = "center";
            noActivityMessage.style.fontWeight = "bold";
            noActivityMessage.style.color = "gray";

            document.getElementById("activity-container").appendChild(noActivityMessage);
        }
    });
</script>
