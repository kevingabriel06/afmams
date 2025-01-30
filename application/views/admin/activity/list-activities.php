<div class="card mb-3 mb-lg-0">
  <div class="card-header bg-body-tertiary d-flex justify-content-between">
    <h5 class="mb-0">Activities</h5>
    <form>
      <select id="category-filter" class="form-select form-select-sm" aria-label=".form-select-sm example">
        <option value="current-month">Current Month</option>
        <option value="last-month">Last Month</option>
        <option value="last-3-months">Last 3 Months</option>
        <option value="last-6-months">Last 6 Months</option>
        <option value="last-year">Last Year</option>
        <option value="all" selected="selected">All</option>
      </select>
    </form>
  </div>
  <div class="card-body fs-10">
    <div class="row">
      <?php foreach ($activities as $activity): ?>
        <div class="col-md-6 h-100 activity">
          <div class="d-flex btn-reveal-trigger">
            <div class="calendar">
              <?php
                  // Format the start date to get the month and day
                  $start_date = strtotime($activity->start_date);
                  $month = date('M', $start_date); // Get the abbreviated month (e.g., Mar)
                  $day = date('j', $start_date); // Get the day of the month (e.g., 26)
                  $year = date('y', $start_date);

                  // Output the formatted month and day
                  echo '<span class="calendar-month">' . $month .  '</span>';
                  echo '<span class="calendar-day">' . $day . '</span>';
                  echo '<span class="calendar-year" hidden>' . $year . '</span>';
              ?>
            </div>
            <div class="flex-1 position-relative ps-3">
              <p class="mb-1" hidden><?php echo $activity->activity_id ;?> </p>
              <h6 class="fs-9 mb-0"><a href="<?php echo site_url('admin/activity-details/'. $activity->activity_id);?>"><?php echo $activity->activity_title ;?> 
                <?php if ($activity->registration_fee == '0'): ?>
                  <span class="badge badge-subtle-success rounded-pill">Free</span>
                <?php else: ?>
                    <!-- Only show the "Free" badge if registration_fee is null -->
                <?php endif; ?>
              </a>
              </h6>
              <p class="mb-1">Organized by <a href="#!" class="text-700">Organizer dito</a></p>
              <p class="text-1000 mb-0"> Time: 
                <?php
                    // Check if 'am_in' is not null, and use it as the time
                    if (!empty($activity->am_in)) {
                        // Format 'am_in' to 12-hour format with AM/PM
                        $start_time = date('h:i A', strtotime($activity->am_in));
                    } else {
                        // Use 'pm_in' if 'am_in' is null, and format it to 12-hour format with AM/PM
                        $start_time = !empty($activity->pm_in) ? date('h:i A', strtotime($activity->pm_in)) : 'N/A'; // Default to 'N/A' if both are null
                    }

                    // Output the formatted dates and time
                    echo $start_time;
                ?>
              </p>
              <p class="text-1000 mb-0">Duration: 
                <?php
                  // Format the start and end dates to Month Day, Year
                  $start_date = date('M j, Y', strtotime($activity->start_date));
                  $end_date = date('M j, Y', strtotime($activity->end_date));
                  echo $start_date . ' - ' . $end_date;
                ?>
              </p>
              <div class="border-bottom border-dashed my-3"></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
// Function to filter activities based on selected period
function filterActivities() {
  const filterValue = document.getElementById('category-filter').value;
  const activities = document.querySelectorAll('.activity'); // Assuming each activity is in a .activity div

  const currentDate = new Date();

  activities.forEach(activity => {
    // Get the activity date from the calendar month, day, and year
    const monthText = activity.querySelector('.calendar-month').textContent.trim();
    const dayText = activity.querySelector('.calendar-day').textContent.trim();
    const yearText = activity.querySelector('.calendar-year').textContent.trim() || currentDate.getFullYear(); // Use current year if no year is available

    // Construct the full date using the month, day, and year
    const startDateStr = `${monthText} ${dayText}, ${yearText}`; // Format: "Month Day, Year"
    const startDate = new Date(startDateStr); // Convert to Date object

    // Determine if the activity should be shown based on the filter
    let shouldShow = false;

    switch (filterValue) {
      case 'current-month':
        shouldShow = isCurrentMonth(startDate, currentDate);
        break;
      case 'last-month':
        shouldShow = isLastMonth(startDate, currentDate);
        break;
      case 'last-3-months':
        shouldShow = isWithinLastMonths(startDate, currentDate, 3);
        break;
      case 'last-6-months':
        shouldShow = isWithinLastMonths(startDate, currentDate, 6);
        break;
      case 'last-year':
        shouldShow = isLastYear(startDate, currentDate);
        break;
      case 'all':
      default:
        shouldShow = true; // Show all
    }

    // Show or hide the activity
    activity.style.display = shouldShow ? 'block' : 'none';
  });
}

// Helper functions to check date ranges
function isCurrentMonth(date, currentDate) {
  const firstDayOfCurrentMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
  const lastDayOfCurrentMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);

  return date >= firstDayOfCurrentMonth && date <= lastDayOfCurrentMonth;
}

function isLastMonth(date, currentDate) {
  const firstDayOfCurrentMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
  const lastDayOfLastMonth = new Date(firstDayOfCurrentMonth - 1);
  const firstDayOfLastMonth = new Date(lastDayOfLastMonth.getFullYear(), lastDayOfLastMonth.getMonth(), 1);

  return date >= firstDayOfLastMonth && date <= lastDayOfLastMonth;
}

function isWithinLastMonths(date, currentDate, months) {
  // Calculate the past date by adjusting the month
  const pastDate = new Date(currentDate);
  pastDate.setMonth(currentDate.getMonth() - months); // Adjust by subtracting months

  // Ensure that if we go back to the previous year, it's correctly handled
  const startOfPeriod = new Date(pastDate.getFullYear(), pastDate.getMonth(), 1); // Start of the period
  const endOfPeriod = new Date(currentDate.getFullYear(), currentDate.getMonth(), 0); // End of the current month

  return date >= startOfPeriod && date <= endOfPeriod;
}

function isLastYear(date, currentDate) {
  const lastYear = currentDate.getFullYear() - 1;
  const startOfLastYear = new Date(lastYear, 0, 1);
  const endOfLastYear = new Date(lastYear, 11, 31);
  return date >= startOfLastYear && date <= endOfLastYear;
}

// Event listener to trigger the filter when the user selects a category
document.getElementById('category-filter').addEventListener('change', filterActivities);
</script>
