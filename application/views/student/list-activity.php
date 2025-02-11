<div class="card mb-3 mb-lg-0">
  <div class="card-header bg-body-tertiary d-flex justify-content-between">
    <h5 class="mb-0">Activities</h5>
    <form class="d-flex">

		<!-- Filter Button to Open Modal -->
		<button class="btn btn-sm btn-falcon-default ms-2" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
								<span class="fas fa-filter"></span>
		</button>
      <!-- Semester Filter -->
      <!-- <select id="semester-filter" class="form-select form-select-sm me-2" aria-label="Semester Filter">
        <option value="all">All Semesters</option>
        <option value="1">First Sem</option>
        <option value="2">Second Sem</option>
      </select> -->
      <!-- Category Filter -->
      <!-- <select id="category-filter" class="form-select form-select-sm" aria-label="Category Filter">
        <option value="current-month">Current Month</option>
        <option value="last-month">Last Month</option>
        <option value="last-3-months">Last 3 Months</option>
        <option value="all" selected="selected">All</option>
      </select> -->
    </form>
  </div>

  <div class="card-body fs-10">
    <div class="row" id="activity-list">
    <?php if (!empty($activities)): ?>
      <?php
        // Define a custom comparison function to sort by status
        function sortByStatus($a, $b) {
            $statusOrder = ['upcoming' => 1, 'ongoing' => 2, 'completed' => 3]; // Define the order

            $statusA = strtolower($a['status']);
            $statusB = strtolower($b['status']);

            // Use traditional comparison if PHP < 7.0
            if ($statusOrder[$statusA] < $statusOrder[$statusB]) {
                return -1; // A comes before B
            } elseif ($statusOrder[$statusA] > $statusOrder[$statusB]) {
                return 1;  // B comes before A
            } else {
                return 0;  // They are equal
            }
        }

        // Sort the activities array by status
        usort($activities, 'sortByStatus');
        
        foreach ($activities as $activity):
            $start_date = date_create($activity['start_date']);
            $month = date_format($start_date, "M");
            $day = date_format($start_date, "d");
            $year = date_format($start_date, "Y");

            // Define status badge colors (using subtle styles)
            switch (strtolower($activity['status'])) {
                case "ongoing":
                    $status_class = "badge-subtle-warning"; // Light Yellow
                    break;
                case "upcoming":
                    $status_class = "badge-subtle-primary"; // Light Blue
                    break;
                case "completed":
                    $status_class = "badge-subtle-secondary"; // Light Gray
                    break;
                default:
                    $status_class = "badge-subtle-dark"; // Default Subtle Color
                    break;
            }
      ?>
        <div class="col-md-6 h-100 activity" data-date="<?= $activity['start_date'] ?>">
            <div class="d-flex btn-reveal-trigger">
                <div class="calendar">
                    <span class="calendar-month"><?= $month ?></span>
                    <span class="calendar-day"><?= $day ?></span>
                    <span class="calendar-year" hidden><?= $year ?></span>
                </div>
                <div class="flex-1 position-relative ps-3">
                    <h6 class="fs-9 mb-0">
                        <!-- Activity title clickable -->
                        <a href="<?php echo site_url('student/activity-details/' . $activity['activity_id']);?>">
                            <?= $activity['activity_title'] ?>
                            <?php if ($activity['registration_fee'] == "0"): ?>
                                <span class="badge badge-subtle-success rounded-pill">Free</span>
                            <?php endif; ?>
                            <span class="badge <?= $status_class ?> rounded-pill"><?= ucfirst($activity['status']) ?></span>
                        </a>
                    </h6>
                    <p class="mb-1">Organized by <a href="#" class="text-700"><?= $activity['organizer'] ?></a></p>
                    <!-- Updated time format -->
                    <p class="text-1000 mb-0">Time: <?= $activity['start_time'] ?> - <?= $activity['end_time'] ?></p>
                    <p class="text-1000 mb-0">Date: <?= $activity['activity_date'] ?></p> <!-- Updated to show the date -->
                    <div class="border-bottom border-dashed my-3"></div>
                </div>
            </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
        <p>No activities available.</p>
    <?php endif; ?>
    </div>
  </div>
</div>

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
    // Get all activity divs (instead of rows in a table)
    var activities = document.querySelectorAll("#activity-list .activity");

    activities.forEach(function(activity) {
        // Get date attributes from activity
        var activityDate = activity.getAttribute("data-date");
        var dateObj = new Date(activityDate);
        var month = dateObj.getMonth() + 1;  // getMonth() is 0-indexed
        var year = dateObj.getFullYear();

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

        // Assign data attributes to activity for filtering
        activity.setAttribute("data-semester", semester);
        activity.setAttribute("data-year", academicYear);
    });

    // Apply filters when the modal button is clicked
    document.querySelector(".btn-primary").addEventListener("click", applyFilters);
});

// Function to filter based on selected semester and academic year
function applyFilters() {
    var selectedSemester = document.getElementById("semester-filter").value;
    var selectedYear = document.getElementById("year-filter").value;
    var activities = document.querySelectorAll("#activity-list .activity");
    var filteredActivities = 0;
    var noDataMessage = document.getElementById("noDataMessage");

    // Remove any existing "No activities match" message before applying new filters
    if (noDataMessage) {
        noDataMessage.remove();
    }

    activities.forEach(function(activity) {
        var activitySemester = activity.getAttribute("data-semester");
        var activityYear = activity.getAttribute("data-year");

        // Check if activity matches the selected semester and year
        var semesterMatch = !selectedSemester || activitySemester === selectedSemester;
        var yearMatch = !selectedYear || activityYear === selectedYear;

        if (semesterMatch && yearMatch) {
            activity.style.display = "";
            filteredActivities++;
        } else {
            activity.style.display = "none";
        }
    });

    // Show "No Data" message if no activities match the filter
    if (filteredActivities === 0) {
        var noDataMessage = document.createElement("p");
        noDataMessage.id = "noDataMessage";
        noDataMessage.textContent = "No activities match your filter.";
        document.getElementById("activity-list").appendChild(noDataMessage);
    }

    // Close the modal after applying filters
    var modalElement = document.getElementById("filterModal");
    var modal = bootstrap.Modal.getInstance(modalElement);
    if (modal) {
        modal.hide();
    }
}


</script>


<!-- SCRIPT FOR FILTERS END -->

