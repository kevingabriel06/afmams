
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
  
  <div class="card mb-3"><img id="coverPhoto" class="card-img-top" src="<?php echo base_url('assets/coverEvent/'). $activity['activity_image']; ?>" alt="" />
            <div class="card-body">
              <div class="row justify-content-between align-items-center">
                <div class="col">
                  <div class="d-flex">
                    <div class="calendar me-2">
                      <?php
                          // Format the start date to get the month and day
                          $start_date = strtotime($activity['start_date']);
                          $month = date('M', $start_date); // Get the abbreviated month (e.g., Mar)
                          $day = date('j', $start_date); // Get the day of the month (e.g., 26)
                          $year = date('y', $start_date);

                          // Output the formatted month and day
                          echo '<span class="calendar-month">' . $month .  '</span>';
                          echo '<span class="calendar-day">' . $day . '</span>';
                          echo '<span class="calendar-year" hidden>' . $year . '</span>';
                      ?></div>
                    <div class="flex-1 fs-10">
                      <h5 class="fs-9"><?php echo $activity['activity_title'] ; ?></h5>
                      <p class="mb-0">by <a href="#!">Boston Harbor Now</a></p>
                      <span class="fs-9 text-warning fw-semi-bold">
                          <?php echo ($activity['registration_fee'] > 0) ? 'Php ' . $activity['registration_fee'] : 'Free Event'; ?>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-md-auto mt-4 mt-md-0"><button class="btn btn-falcon-default btn-sm me-2" type="button"><span class="fas fa-users text-danger me-1"></span>235 Attendees</button><button class="btn btn-falcon-default btn-sm me-2" type="button"><span class="fas fa-share-alt me-1"></span>Share</button><a class="btn btn-falcon-primary btn-sm px-4 px-sm-5" type="button" href="<?php echo site_url('scan-qr'); ?>">Scan QR</a></div>
              </div>
            </div>
          </div>
          <div class="row g-0">
            <div class="col-lg-8 pe-lg-2">
              <div class="card mb-3 mb-lg-0">
                <div class="card-body">
                  <h5 class="fs-9 mb-3">New Year's Eve on the Waterfront</h5>
                  <p>Boston Harbor Now in partnership with the Friends of Christopher Columbus Park, the Wharf District Council and the City of Boston is proud to announce the New Year's Eve Midnight Harbor Fireworks! This beloved nearly 40-year old tradition is made possible by the generous support of local waterfront organizations and businesses and the support of the City of Boston and the Office of Mayor Marty Walsh.</p>
                  <p>Join us as we ring in the New Year with a dazzling display over Boston Harbor. Public viewing is free and available from the Harborwalk of these suggested viewing locations:</p>
                  <ul>
                    <li>Christopher Columbus Park, North End</li>
                    <li>Fan Pier, Seaport District</li>
                    <li>East Boston Harborwalk</li>
                  </ul>
                  <p>The show will begin promptly at midnight.</p>
                  <p>Register here for a reminder and updates about the harbor fireworks and other waterfront public programs as they become available. Be the first to be notified for popular waterfront New Year's Eve public activities.</p>
                </div>
              </div>
            </div>
            <div class="col-lg-4 ps-lg-2">
              <div class="sticky-sidebar">
                <div class="card mb-3 fs-10">
                  <div class="card-body">
                    <h6>Date And Time</h6>
                      <?php
                        // Format the dates to "Month day, year"
                        $start_date_formatted = date('M d, Y', strtotime($activity['start_date']));
                        $end_date_formatted = date('M d, Y', strtotime($activity['end_date']));

                        // Determine the start time and format it to 12-hour with AM/PM
                          if ($activity['am_in']) {
                            $start_time = date('h:i A', strtotime($activity['am_in']));
                          } else {
                            $start_time = date('h:i A', strtotime($activity['pm_in']));
                          }

                          // Determine the end time and format it to 12-hour with AM/PM
                          if ($activity['pm_out']) {
                            $end_time = date('h:i A', strtotime($activity['pm_out']));
                          } else {
                            $end_time = date('h:i A', strtotime($activity['am_out']));
                          }
                        ?>

                        <p class="mb-1">
                            Start Date:  <?= $start_date_formatted ?>, <?= $start_time ?> <br />
                            End Date: <?= $end_date_formatted ?>, <?= $end_time ?>
                        </p>
                    <h6 class="mt-4">Registration Details</h6>
                    <?php
                        // Format the dates to "Month day, year"
                        $registration_date_formatted = date('M d, Y', strtotime($activity['registration_deadline']));

                        // Determine the start time and format it to 12-hour with AM/PM
                          if ($activity['am_in']) {
                            $start_time = date('h:i A', strtotime($activity['am_in']));
                          } else {
                            $start_time = date('h:i A', strtotime($activity['pm_in']));
                          }

                          // Determine the end time and format it to 12-hour with AM/PM
                          if ($activity['pm_out']) {
                            $end_time = date('h:i A', strtotime($activity['pm_out']));
                          } else {
                            $end_time = date('h:i A', strtotime($activity['am_out']));
                          }
                        ?>  
                      <?php
                        // Check if the registration date and fee indicate a free event
                        if ($registration_date_formatted === '0000-00-00 00:00:00' || $activity['registration_fee'] == 0) {
                            echo '<p class="fs-10 mb-0">Remarks: Free Event</p>';
                        } else {
                            // Display the registration details
                            ?>
                            <p class="fs-10 mb-0">Registration Deadline: <?= $registration_date_formatted ?></p>
                            <p class="fs-10 mb-0">Registration Fee: <?= $activity['registration_fee'] ?></p>
                            <?php
                        }
                        ?>
                  </div>
                </div>
                <div class="card mb-3 mb-lg-0">
                  <div class="card-header bg-body-tertiary">
                    <h5 class="mb-0">Upcoming Acivities</h5>
                  </div>
                  <div class="card-body fs-10">
                  <?php 
                    $count = 0; // Initialize a counter to track displayed items

                    foreach ($activities as $activity): 
                        // Check if the activity's start date is in the future
                        if (strtotime($activity->start_date) > strtotime(date('Y-m-d'))) :
                            $count++; // Increment the counter

                            // Limit the output to the first 3 upcoming activities
                            if ($count > 3) break;
                            ?>
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
                                    <h6 class="fs-9 mb-0">
                                        <a href="<?php echo site_url('activity-details/' . $activity->activity_id); ?>">
                                            <?php echo htmlspecialchars($activity->activity_title); ?>
                                        </a>
                                    </h6>
                                    <p class="mb-1">Organized by <a href="#!" class="text-700">University of Oxford</a></p>
                                    <p class="text-1000 mb-0">Date and Time: 
                                      <?php
                                          // Format the start date
                                          $start_date = date('M j', strtotime($activity->start_date));

                                          // Determine the start time (12-hour format with AM/PM)
                                          if (!empty($activity->am_in)) {
                                              $start_time = date('h:i A', strtotime($activity->am_in));
                                          } elseif (!empty($activity->pm_in)) {
                                              $start_time = date('h:i A', strtotime($activity->pm_in));
                                          } else {
                                              $start_time = ''; // Fallback if no time is available
                                          }

                                          // Output the formatted date and time
                                          echo $start_date;
                                          if (!empty($start_time)) {
                                              echo ', ' . $start_time;
                                          }
                                      ?>
                                  </p>
                                    <div class="border-bottom border-dashed my-3"></div>
                                </div>
                            </div>
                            <?php 
                        endif; 
                    endforeach; 
                    ?>
                  </div>
                  <div class="card-footer bg-body-tertiary p-0 border-top"><a class="btn btn-link d-block w-100" href="<?php echo site_url('list-of-activity'); ?>">All Events<span class="fas fa-chevron-right ms-1 fs-11"></span></a></div>
                </div>
              </div>
            </div>
          </div>