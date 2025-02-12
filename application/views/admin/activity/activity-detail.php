<!-- Alertify CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css"/>

<!-- Alertify JS -->
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- FOR SHARING THE ACTIVITY IN THE FEED -->
<script>
    $(document).ready(function() {
        // Attach the click event handler to the button using its id
        $('#share').click(function() {
            // Get the activity ID dynamically
            var activityId = <?php echo htmlspecialchars($activity['activity_id']); ?>;

            // Trigger the confirmation dialog when the button is clicked
            confirmShare(activityId);
        });
    });

    function confirmShare(activityId) {
        // Align Alertify toast notifications to the right
        alertify.set('notifier', 'position', 'top-right');

        alertify.confirm('Confirm Share', 'Are you sure you want to share this activity?',
            function() {
                // AJAX request to share the activity
                $.ajax({
                    url: '<?php echo site_url('admin/activity-details/activity-share'); ?>', // Ensure PHP outputs the correct URL
                    type: 'POST',
                    contentType: 'application/json', // Ensure JSON format is sent
                    data: JSON.stringify({ activity_id: activityId }), // Send JSON data
                    dataType: 'json', // Expect JSON response
                    success: function(response) {
                        if (response.success) {
                            alertify.success('Activity shared successfully!', 5);
                            
                            // Wait for the alert to show before reloading
                            setTimeout(function() {
                                window.location.href = "<?= site_url('admin/community'); ?>";
                            }, 2000);  // Redirect after 2 seconds for better UX
                        } else {
                            alertify.error(response.message || 'Failed to share the activity', 5);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        alertify.error('An error occurred. Please try again.', 5);
                    }
                });
            },
            function() {
                // Action if cancelled
                alertify.error('Share cancelled', 5);
            });
    }
</script>

<!-- Custom CSS to Set Standard Size -->
<style>
    /* Set fixed size for the image */
    #coverPhoto {
        width: 100%; /* Make the image width fill the container */
        height: 250px; /* Set a fixed height */
        object-fit: cover; /* Ensure the image covers the area without distortion */
    }

    /* Optional: Set specific dimensions for the card if necessary */
    .card {
        width: 100%; /* You can adjust the width of the card */
    }
</style>



<?php if ($role == 'Admin') : ?>
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
              <p class="mb-0">by 
                <?php if (empty($activity['org_id']) && empty($activity['dept_id'])): ?>
                    <a href="#!" class="text-700">Institution</a>
                <?php else: ?>
                    <?php if (!empty($activity['org_id'])): ?>
                        <a href="#!" class="text-700"><?php echo htmlspecialchars($activity['org_name']); ?></a>
                    <?php endif; ?>
                    <?php if (!empty($activity['dept_id'])): ?>
                        <a href="#!" class="text-700"><?php echo htmlspecialchars($activity['dept_name']); ?></a>
                    <?php endif; ?>
                <?php endif; ?>
              </p>
              <span class="fs-9 text-warning fw-semi-bold">
                <?php echo ($activity['registration_fee'] > 0) ? 'Php ' . htmlspecialchars($activity['registration_fee']) : 'Free Event'; ?>
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-auto mt-4 mt-md-0">
          <button class="btn btn-falcon-default btn-sm me-2" type="button">
            <span class="fas fa-users text-danger me-1"></span>
            <?= isset($attendee_count) ? $attendee_count : '0' ?> Registered Attendees
          </button>
          <?php if (empty($activity['org_id']) && empty($activity['dept_id'])) :?>
            <?php if ($activity['status'] == 'Upcoming' || $activity['status'] == 'Ongoing') : ?>
              <?php if ($activity['is_shared'] == 'No') :?>
                <button id="share" class="btn btn-falcon-default btn-sm me-2" type="button">
                    <span class="fas fa-share-alt me-1"></span> Share
                </button>
              <?php endif;?>
              <a href="<?php echo site_url('admin/edit-activity/'.$activity['activity_id']);?>" class="btn btn-falcon-default btn-sm me-2">
                  <span class="fas fa-edit me-1"></span> Edit
              </a>
            <?php endif ;?>
          <?php endif ;?>
          <?php if (empty($activity['org_id']) && empty($activity['dept_id'])) :?>
            <?php if ($activity['status'] == 'Ongoing') : ?> 
            <a class="btn btn-falcon-primary btn-sm px-4 px-sm-5" href="<?php echo site_url('admin/activity/scan-qr/'.$activity['activity_id']); ?>">Scan QR</a>
            <?php endif; ?>
          <?php endif ;?>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-0">
    <div class="col-lg-8 pe-lg-2">
      <div class="card mb-3 mb-lg-0">
        <div class="card-body">
          <h5 class="fs-9 mb-3">Description</h5>
          <p><?php echo htmlspecialchars($activity['description']); ?></p>
        </div>
      </div>
    </div>
    
    <div class="col-lg-4 ps-lg-2">
      <div class="sticky-sidebar">
        <div class="card mb-3 fs-10">
          <div class="card-body">
            <h6>Date And Time</h6>
            <?php
              $start_date_formatted = date('M d, Y', strtotime($activity['start_date']));
              $end_date_formatted = date('M d, Y', strtotime($activity['end_date']));

              $start_time = !empty($activity['am_in']) ? date('h:i A', strtotime($activity['am_in'])) : date('h:i A', strtotime($activity['pm_in']));
              $end_time = !empty($activity['pm_out']) ? date('h:i A', strtotime($activity['pm_out'])) : date('h:i A', strtotime($activity['am_out']));
            ?>
            <p class="mb-1">
              Start Date: <?= $start_date_formatted ?>, <?= $start_time ?> <br />
              End Date: <?= $end_date_formatted ?>, <?= $end_time ?>
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
            <?php shuffle($activities); ?>
            
            <?php
            $count = 0;
            $hasUpcomingActivities = false;

            foreach ($activities as $activity):
              $is_admin_viewable = ($role == 'Admin' && strtotime($activity->start_date) > time());
              $is_officer_viewable = (
                $role == 'Officer' && strtotime($activity->start_date) > time() &&
                ($activity->privacy == "Public" || 
                (isset($department->dept_id) && $activity->dept_id == $department->dept_id) || 
                (isset($organization) && is_array($organization) && in_array($activity->org_id, array_column($organization, 'org_id'))))
              );
              
              if ($is_admin_viewable || $is_officer_viewable):
                $count++;
                if ($count > 2) break;
                $hasUpcomingActivities = true;
            ?>
                <div class="d-flex btn-reveal-trigger mb-3">
                  <div class="calendar text-center me-3">
                    <?php
                      $start_date = strtotime($activity->start_date);
                      echo '<span class="calendar-month d-block">' . date('M', $start_date) . '</span>';
                      echo '<span class="calendar-day d-block">' . date('j', $start_date) . '</span>';
                    ?>
                  </div>
                  <div class="flex-1 position-relative">
                    <h6 class="fs-9 mb-1">
                      <a href="<?= site_url('admin/activity-details/' . htmlspecialchars($activity->activity_id)) ?>">
                        <?= htmlspecialchars($activity->activity_title) ?>
                        <?php if ($activity->registration_fee == '0'): ?>
                          <span class="badge badge-subtle-success rounded-pill">Free</span>
                        <?php endif; ?>
                      </a>
                    </h6>
                    <p class="mb-1">Organized by 
                      <?php
                        if ($activity->dept_id == '0' && $activity->org_id == '0') {
                          echo htmlspecialchars("Institution");
                        } elseif (empty($activity->dept_id)) {
                          echo htmlspecialchars($activity->org_name);
                        } elseif (empty($activity->org_id)) {
                          echo htmlspecialchars($activity->dept_name);
                        }
                      ?>
                    </p>
                    <p class="text-1000 mb-0">Date: 
                      <?= htmlspecialchars(date('M j, Y', strtotime($activity->start_date))) ?>
                    </p>
                    <div class="border-bottom border-dashed my-2"></div>
                  </div>
                </div>
            <?php endif; endforeach; ?>

            <?php if (!$hasUpcomingActivities): ?>
              <h5 class="mb-0">No Upcoming Activity</h5>
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

<?php elseif ($role == 'Officer') : ?>
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
              <p class="mb-0">by 
                <?php if (!empty($activity['org_id'])): ?>
                    <a href="#!" class="text-700"><?php echo htmlspecialchars($activity['org_name']); ?></a>
                <?php elseif (!empty($activity['dept_id'])): ?>
                    <a href="#!" class="text-700"><?php echo htmlspecialchars($activity['dept_name']); ?></a>
                <?php endif; ?>
              </p>
              <span class="fs-9 text-warning fw-semi-bold">
                <?php echo ($activity['registration_fee'] > 0) ? 'Php ' . htmlspecialchars($activity['registration_fee']) : 'Free Event'; ?>
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-auto mt-4 mt-md-0">
          <button class="btn btn-falcon-default btn-sm me-2" type="button">
            <span class="fas fa-users text-danger me-1"></span> 235 Attendees
          </button>
          <?php if ($activity['org_id'] == $organization || $activity['dept_id'] == $department) :?>
            <?php if ($activity['status'] == 'Upcoming' || $activity['status'] == 'Ongoing') : ?> 
              <?php if ($activity['is_shared'] == 'No') :?>
              <button id="share" class="btn btn-falcon-default btn-sm me-2" type="button">
                  <span class="fas fa-share-alt me-1"></span> Share
              </button>
              <?php endif; ?>
              <a href="<?php echo site_url('admin/edit-activity/'.$activity['activity_id']);?>" class="btn btn-falcon-default btn-sm me-2">
                  <span class="fas fa-edit me-1"></span> Edit
              </a>
            <?php endif ;?>
          <?php endif ;?>
          <?php if ($activity['org_id'] == $organization || $activity['dept_id'] == $department) :?>
            <?php if ($activity['status'] == 'Ongoing') : ?> 
            <a class="btn btn-falcon-primary btn-sm px-4 px-sm-5" href="<?php echo site_url('admin/activity/scan-qr/'.$activity['activity_id']); ?>">Scan QR</a>
            <?php endif; ?>
          <?php endif ;?>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-0">
    <div class="col-lg-8 pe-lg-2">
      <div class="card mb-3 mb-lg-0">
        <div class="card-body">
          <h5 class="fs-9 mb-3">Description</h5>
          <p><?php echo htmlspecialchars($activity['description']); ?></p>
        </div>
      </div>
    </div>
    
    <div class="col-lg-4 ps-lg-2">
      <div class="sticky-sidebar">
        <div class="card mb-3 fs-10">
          <div class="card-body">
            <h6>Date And Time</h6>
            <?php
              $start_date_formatted = date('M d, Y', strtotime($activity['start_date']));
              $end_date_formatted = date('M d, Y', strtotime($activity['end_date']));

              $start_time = !empty($activity['am_in']) ? date('h:i A', strtotime($activity['am_in'])) : date('h:i A', strtotime($activity['pm_in']));
              $end_time = !empty($activity['pm_out']) ? date('h:i A', strtotime($activity['pm_out'])) : date('h:i A', strtotime($activity['am_out']));
            ?>
            <p class="mb-1">
              Start Date: <?= $start_date_formatted ?>, <?= $start_time ?> <br />
              End Date: <?= $end_date_formatted ?>, <?= $end_time ?>
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
            <?php shuffle($activities); ?>
            
            <?php
            $count = 0;
            $hasUpcomingActivities = false;

            foreach ($activities as $activity):
              $is_officer_viewable = (
                $role == 'Officer' && strtotime($activity->start_date) > time() &&
                ($activity->privacy == "Public" || 
                (isset($department->dept_id) && $activity->dept_id == $department->dept_id) || 
                (isset($organization) && is_array($organization) && in_array($activity->org_id, array_column($organization, 'org_id'))))
              );
              
              if ($is_officer_viewable):
                $count++;
                if ($count > 2) break;
                $hasUpcomingActivities = true;
            ?>
                <div class="d-flex btn-reveal-trigger mb-3">
                  <div class="calendar text-center me-3">
                    <?php
                      $start_date = strtotime($activity->start_date);
                      echo '<span class="calendar-month d-block">' . date('M', $start_date) . '</span>';
                      echo '<span class="calendar-day d-block">' . date('j', $start_date) . '</span>';
                    ?>
                  </div>
                  <div class="flex-1 position-relative">
                    <h6 class="fs-9 mb-1">
                      <a href="<?= site_url('admin/activity-details/' . htmlspecialchars($activity->activity_id)) ?>">
                        <?= htmlspecialchars($activity->activity_title) ?>
                        <?php if ($activity->registration_fee == '0'): ?>
                          <span class="badge badge-subtle-success rounded-pill">Free</span>
                        <?php endif; ?>
                      </a>
                    </h6>
                    <p class="mb-1">Organized by 
                      <?php
                        if ($activity->dept_id == '0' && $activity->org_id == '0') {
                          echo htmlspecialchars("Institution");
                        } elseif (empty($activity->dept_id)) {
                          echo htmlspecialchars($activity->org_name);
                        } elseif (empty($activity->org_id)) {
                          echo htmlspecialchars($activity->dept_name);
                        }
                      ?>
                    </p>
                    <p class="text-1000 mb-0">Date: 
                      <?= htmlspecialchars(date('M j, Y', strtotime($activity->start_date))) ?>
                    </p>
                    <div class="border-bottom border-dashed my-2"></div>
                  </div>
                </div>
            <?php endif; endforeach; ?>

            <?php if (!$hasUpcomingActivities): ?>
              <h6 class="mb-0 text-center">No Upcoming Activity</h6>
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

<?php endif; ?>

