<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- FOR SHARING THE ACTIVITY IN THE FEED -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Include SweetAlert2 -->

<script>
  $(document).ready(function() {
    // Attach the click event handler to the button using its id
    $('#share').click(function() {
      var activityId = <?php echo htmlspecialchars($activity['activity_id']); ?>;
      confirmShare(activityId);
    });
  });

  function confirmShare(activityId) {
    Swal.fire({
      title: 'Confirm Share',
      text: 'Are you sure you want to share this activity?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, share it!',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        // AJAX request to share the activity
        $.ajax({
          url: '<?php echo site_url('admin/activity-details/activity-share'); ?>', // Ensure PHP outputs the correct URL
          type: 'POST',
          contentType: 'application/json', // Ensure JSON format is sent
          data: JSON.stringify({
            activity_id: activityId
          }), // Send JSON data
          dataType: 'json', // Expect JSON response
          success: function(response) {
            if (response.success) {
              Swal.fire({
                icon: 'success',
                title: 'Shared!',
                text: 'Activity shared successfully!',
                timer: 2000,
                showConfirmButton: false
              });

              // Redirect after success alert
              setTimeout(function() {
                window.location.href = "<?= site_url('admin/community'); ?>";
              }, 2000); // Redirect after 2 seconds
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: response.message || 'Failed to share the activity.'
              });
            }
          },
          error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'An error occurred. Please try again.'
            });
          }
        });
      } else {
        Swal.fire({
          icon: 'info',
          title: 'Cancelled',
          text: 'The activity was not shared.',
          timer: 2000,
          showConfirmButton: false
        });
      }
    });
  }
</script>


<!-- Custom CSS to Set Standard Size -->
<style>
  /* Set fixed size for the image */
  #coverPhoto {
    width: 100%;
    /* Make the image width fill the container */
    height: 250px;
    /* Set a fixed height */
    object-fit: cover;
    /* Ensure the image covers the area without distortion */
  }

  /* Optional: Set specific dimensions for the card if necessary */
  .card {
    width: 100%;
    /* You can adjust the width of the card */
  }
</style>

<div class="card mb-3">
  <img id="coverPhoto" class="card-img-top"
    src="<?php echo base_url('assets/coverEvent/') . htmlspecialchars($activity['activity_image']); ?>"
    alt="Event Cover" />
  <div class="card-body">
    <div class="row justify-content-between align-items-center">
      <div class="col">
        <div class="d-flex">
          <div class="calendar me-2">
            <?php
            // Format the start date
            $start_date = strtotime($activity['start_date']);
            $month = date('M', $start_date);
            $day = date('j', $start_date);
            $year = date('Y', $start_date); // Full year

            // Output formatted date
            echo '<span class="calendar-month">' . $month . '</span>';
            echo '<span class="calendar-day">' . $day . '</span>';
            echo '<span class="calendar-year" hidden>' . $year . '</span>';
            ?>
          </div>
          <div class="flex-1 fs-10">
            <h5 class="fs-9"><?php echo htmlspecialchars($activity['activity_title']); ?></h5>
            <p class="mb-0">by <?php echo $activity['organizer']; ?></p>
            <span class="fs-9 text-warning fw-semi-bold">
              <?php echo ($activity['registration_fee'] > 0) ? 'Php ' . htmlspecialchars($activity['registration_fee']) : 'Free Event'; ?>
            </span>
          </div>
        </div>
      </div>
      <div class="col-md-auto mt-4 mt-md-0">
        <button class="btn btn-falcon-default btn-sm me-2" type="button">
          <span class="fas fa-users text-danger me-1"></span>
          <?php if ($activity['registration_fee'] == '0') : ?>
            <?php echo $activity['interested']; ?> Interested
          <?php else : ?>
            <?php echo $activity['interested']; ?> Registered
          <?php endif; ?>
        </button>
        <?php if ($activity['organizer'] == 'Student Parliament') : ?>
          <?php if ($activity['status'] == 'Upcoming' || $activity['status'] == 'Ongoing') : ?>
            <?php if ($activity['is_shared'] == 'No') : ?>
              <button id="share" class="btn btn-falcon-default btn-sm me-2" type="button">
                <span class="fas fa-share-alt me-1"></span> Share
              </button>
            <?php endif; ?>
            <a href="<?php echo site_url('admin/edit-activity/' . $activity['activity_id']); ?>" class="btn btn-falcon-default btn-sm me-2">
              <span class="fas fa-edit me-1"></span> Edit
            </a>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="row g-0">
  <div class="col-lg-8 pe-lg-2">
    <div class="card mb-4"> <!-- Added mb-4 for more space -->
      <div class="card-body">
        <h5 class="fs-9 mb-3">Description</h5>
        <p><?php echo htmlspecialchars($activity['description']); ?></p>
      </div>
    </div>

    <? //php if ($activity['status'] == 'Ongoing') : 
    ?>
    <div class="card mb-4"> <!-- Added mb-4 for spacing -->
      <div class="card-body">
        <h5 class="fs-9 mb-3">Scans</h5>
        <!-- Scan Section -->

        <div class="row mb-3 gap 2">
          <?php foreach ($schedules as $schedule) : ?>
            <?php
            date_default_timezone_set('Asia/Manila');

            // Get current datetime
            $currentDateTime = new DateTime();

            // Convert schedule times to DateTime objects
            $dateTimeIn = !empty($schedule['date_time_in']) ? new DateTime($schedule['date_time_in']) : null;
            $dateCutIn = !empty($schedule['date_cut_in']) ? new DateTime($schedule['date_cut_in']) : null;
            $dateTimeOut = !empty($schedule['date_time_out']) ? new DateTime($schedule['date_time_out']) : null;
            $dateCutOut = !empty($schedule['date_cut_out']) ? new DateTime($schedule['date_cut_out']) : null;

            // // Time In: Allows early scan but must not exceed date_cut_in
            // $isTimeInValid = ($dateTimeIn && $dateCutIn && $currentDateTime <= $dateCutIn);

            // // Time Out: Must be strictly within the time-out range
            // $isTimeOutValid = ($dateTimeOut && $dateCutOut && $currentDateTime >= $dateTimeOut && $currentDateTime <= $dateCutOut);
            ?>

            <div class="col-md-6">
              <div class="border-bottom border-dashed my-3"></div>
              <label class="fw-bold">Scan Options: <?php echo htmlspecialchars($schedule['slot_name']); ?></label>
              <div class="d-flex flex-column gap-2">

                <!-- Time In Section -->
                <div class="d-flex flex-column align-items-start gap-1">
                  <div class="d-flex align-items-center gap-3">
                    <label class="small fw-semibold mb-0">Time In:</label>
                    <h6 class="fw-bold fst-italic text-muted mb-0">
                      <?= $dateTimeIn ? $dateTimeIn->format('Y-m-d h:i A') : 'N/A' ?>
                    </h6>
                  </div>
                  <a class="btn btn-falcon-success btn-sm px-4 px-sm-7 scan-btn"
                    data-url="<?= site_url('admin/activity/scan-qr/time-in/' . $schedule['activity_id']); ?>"
                    data-valid="<//?= $isTimeInValid ? 'true' : 'false' ?>">
                    Scan QR
                  </a>
                </div>

                <!-- Time Out Section -->
                <div class="d-flex flex-column align-items-start gap-1">
                  <div class="d-flex align-items-center gap-3">
                    <label class="small fw-semibold mb-0">Time Out:</label>
                    <h6 class="fw-bold fst-italic text-muted mb-0">
                      <?= !empty($schedule['date_time_out']) ? date('Y-m-d h:i A', strtotime($schedule['date_time_out'])) : 'N/A' ?>
                    </h6>
                  </div>
                  <a class="btn btn-falcon-danger btn-sm px-4 px-sm-7 scan-btn"
                    data-url="<?= site_url('admin/activity/scan-qr/time-out/' . $schedule['activity_id']); ?>"
                    data-valid="<//?= $isTimeOutValid ? 'true' : 'false' ?>">
                    Scan QR
                  </a>
                </div>
              </div>
              <div class="border-bottom border-dashed my-3"></div>
            </div>
          <?php endforeach; ?>

          <script>
            document.addEventListener("DOMContentLoaded", function() {
              document.querySelectorAll(".scan-btn").forEach(function(button) {
                button.addEventListener("click", function(event) {
                  event.preventDefault(); // Prevent default link action
                  let isValid = this.getAttribute("data-valid") == "true";
                  let scanUrl = this.getAttribute("data-url");
                  if (!isValid) {
                    window.location.href = scanUrl; // Proceed with scanning
                  } else {
                    alertify.set('notifier', 'position', 'top-right'); // Change position
                    alertify.error("Scan Not Allowed: You are outside the allowed scanning time!");
                  }
                });
              });
            });
          </script>

        </div>

      </div>
    </div>
    <? //php endif; 
    ?>
  </div>


  <div class="col-lg-4 ps-lg-2">
    <div class="sticky-sidebar">
      <div class="card mb-3 fs-10">
        <div class="card-body">
          <h6>Date and Time</h6>
          <p class="mb-1">
            Start Date: <?= !empty($activity['first_schedule']) ? date('F j, Y h:i A', strtotime($activity['first_schedule'])) : 'N/A' ?> <br />
            End Date: <?= !empty($activity['last_schedule']) ? date('F j, Y h:i A', strtotime($activity['last_schedule'])) : 'N/A' ?>
          </p>

          <h6 class="mt-4">Registration Details</h6>
          <?php
          $registration_date_formatted = date('M d, Y', strtotime($activity['registration_deadline']));
          if ($registration_date_formatted === '0000-00-00' || $activity['registration_fee'] == 0) {
            echo '<p class="fs-10 mb-0">Remarks: Free Event</p>';
          } else {
          ?>
            <p class="fs-10 mb-0">Registration Deadline: <?= $registration_date_formatted ?></p>
            <p class="fs-10 mb-0">Registration Fee: <?= htmlspecialchars($activity['registration_fee']); ?></p>
          <?php } ?>

          <h6 class="mt-4 fs-10">Status:
            <?php if ($activity['status'] == 'Completed'): ?>
              <span class="badge badge-subtle-success rounded-pill fs-10">Completed</span>
            <?php elseif ($activity['status'] == 'Ongoing'): ?>
              <span class="badge badge-subtle-warning rounded-pill fs-10">Ongoing</span>
            <?php elseif ($activity['status'] == 'Upcoming'): ?>
              <span class="badge badge-subtle-danger rounded-pill fs-10">Upcoming</span>
            <?php endif; ?>
          </h6>

        </div>
      </div>

      <div class="card mb-3 mb-lg-0">
        <div class="card-header bg-body-tertiary">
          <h5 class="mb-0">Upcoming Activities</h5>
        </div>
        <div class="card-body fs-10">
          <?php if (!empty($activities)) {
            shuffle($activities); // Shuffle only if activities exist 
          }

          $hasUpcomingActivities = false; // Default to false

          foreach ($activities as $act) {
            // Skip the current activity being viewed
            if ($act->activity_id == $activity['activity_id']) {
              continue;
            }

            $hasUpcomingActivities = true; // Mark that at least one upcoming activity exists
          ?>

            <div class="d-flex btn-reveal-trigger mb-3">
              <div class="calendar text-center me-3">
                <?php
                $start_date = strtotime($act->start_date);
                echo '<span class="calendar-month d-block">' . date('M', $start_date) . '</span>';
                echo '<span class="calendar-day d-block">' . date('j', $start_date) . '</span>';
                ?>
              </div>
              <div class="flex-1 position-relative">
                <h6 class="fs-9 mb-1">
                  <a href="<?= site_url('admin/activity-details/' . htmlspecialchars($act->activity_id)) ?>">
                    <?= htmlspecialchars($act->activity_title) ?>
                    <?php if ($act->registration_fee == '0') : ?>
                      <span class="badge badge-subtle-success rounded-pill">Free</span>
                    <?php endif; ?>
                  </a>
                </h6>
                <p class="mb-1">Organized by <?= htmlspecialchars($act->organizer); ?></p>
                <p class="text-1000 mb-0">Date: <?= htmlspecialchars(date('M j, Y', strtotime($act->start_date))) ?></p>
                <div class="border-bottom border-dashed my-3"></div>
              </div>
            </div>

          <?php } // End foreach 
          ?>

          <?php if (!$hasUpcomingActivities): ?>
            <div class="text-center my-4">
              <h5 class="mb-0 text-muted">No Upcoming Activity</h5>
            </div>
          <?php endif; ?>
        </div>
        <div class="card-footer bg-body-tertiary p-0 border-top">
          <a class="btn btn-link d-block w-100 text-center" href="<?= site_url('admin/list-of-activity') ?>">
            All Events <span class="fas fa-chevron-right ms-1 fs-11"></span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>