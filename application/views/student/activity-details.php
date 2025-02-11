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
  
<div class="card mb-3">
<?php if (!empty($activity->activity_image) && file_exists(FCPATH . 'assets/coverEvent/' . $activity->activity_image)): ?>
    <img src="<?= base_url('assets/coverEvent/' . $activity->activity_image); ?>" alt="Activity Cover" style="height: 300px; object-fit: cover;" />
<?php else: ?>
    <p>No image available</p>
<?php endif; ?>



    <div class="card-body">
        <div class="row justify-content-between align-items-center">
            <div class="col">
                <div class="d-flex">
                    <div class="calendar me-2">
					 <!-- Change the calendar icon day and month -->
					<span class="calendar-month"><?= date('M', strtotime($activity->start_date)); ?></span>
					<span class="calendar-day"><?= date('d', strtotime($activity->start_date)); ?></span>
                        <span class="calendar-year" hidden>25</span>
                    </div>
                    <div class="flex-1 fs-10">
					<h5 class="fs-9"><?= $activity->activity_title; ?></h5>
					<p class="mb-0">
                            <!-- Determine if the dept_name or org_name is available -->
                            by <a href="#!">
                                <?php
                                if ($activity->dept_id) {
                                    // Fetch department name if dept_id is not null
                                    $department = $this->db->get_where('department', ['dept_id' => $activity->dept_id])->row();
                                    echo $department->dept_name;
                                } elseif ($activity->org_id) {
                                    // Fetch organization name if org_id is not null
                                    $organization = $this->db->get_where('organization', ['org_id' => $activity->org_id])->row();
                                    echo $organization->org_name;
                                } else {
                                    echo "Student Parliament";
                                }
                                ?>
                            </a>
                        </p>
                        <span class="fs-9 text-warning fw-semi-bold">
                           <!-- Dynamic registration fee -->
						   <?= $activity->registration_fee ? 'Php ' . number_format($activity->registration_fee, 2) : 'Free Event'; ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-auto mt-4 mt-md-0">
			<button class="btn btn-falcon-default btn-sm me-2" type="button">
				<span class="fas fa-users text-danger me-1"></span>
				<?= isset($attendee_count) ? $attendee_count : '0' ?> Registered Attendees
			</button>

                <!-- <button class="btn btn-falcon-default btn-sm me-2" type="button"><span class="fas fa-share-alt me-1"></span>Share</button> -->
                <!-- <a class="btn btn-falcon-primary btn-sm px-4 px-sm-5" type="button" href="scan-qr">Scan QR</a> -->
            </div>
        </div>
    </div>
</div>

<div class="row g-0">
    <div class="col-lg-8 pe-lg-2">
        <div class="card mb-3 mb-lg-0">
            <div class="card-body">
                 <!-- Use dynamic activity title -->
				 <h5 class="fs-9 mb-3"><?= $activity->activity_title; ?></h5>
                <!-- Use dynamic description -->
                <p><?= $activity->description; ?></p>
                <!-- <ul>
                    <li>Christopher Columbus Park, North End</li>
                    <li>Fan Pier, Seaport District</li>
                    <li>East Boston Harborwalk</li>
                </ul>
                <p>The show will begin promptly at midnight.</p>
                <p>Register here for a reminder and updates about the harbor fireworks and other waterfront public programs as they become available. Be the first to be notified for popular waterfront New Year's Eve public activities.</p> -->
            </div>
        </div>
    </div>
    <div class="col-lg-4 ps-lg-2">
        <div class="sticky-sidebar">
            <div class="card mb-3 fs-10">
                <div class="card-body">
                    <h6>Date And Time</h6>
                    <p class="mb-1">
                        <!-- Use dynamic start_date and end_date -->
                        Start Date: <?= date('M d, Y, h:i A', strtotime($activity->start_date)); ?> <br />
                        End Date: <?= date('M d, Y, h:i A', strtotime($activity->end_date)); ?>
                    </p>
                    <h6 class="mt-4">Registration Details</h6>
                    <p class="fs-10 mb-0">Remarks: <?= $activity->registration_fee ? 'Paid Event' : 'Free Event'; ?></p>
                    <p class="fs-10 mb-0">Registration Deadline: <?= date('M d, Y', strtotime($activity->registration_deadline)); ?></p>
                    <p class="fs-10 mb-0">
                        Registration Fee: <?= $activity->registration_fee ? 'Php ' . number_format($activity->registration_fee, 2) : 'Free'; ?>
                    </p>
                </div>
            </div>
            <div class="card mb-3 mb-lg-0">
                <div class="card-header bg-body-tertiary">
                    <h5 class="mb-0">Upcoming Activities</h5>
                </div>
                <div class="card-body fs-10">
				<?php foreach ($upcoming_activities as $activity): ?>
    <div class="d-flex btn-reveal-trigger">
        <div class="calendar">
            <span class="calendar-month"><?= date('M', strtotime($activity['start_date'])); ?></span>
            <span class="calendar-day"><?= date('d', strtotime($activity['start_date'])); ?></span>
        </div>
        <div class="flex-1 position-relative ps-3">

            <h6 class="fs-9 mb-0">
                <a href="<?= base_url('student/activity-details/' . $activity['activity_id']); ?>">
                    <?= $activity['activity_title']; ?>
                    <?php if ($activity['registration_fee'] == 0 || is_null($activity['registration_fee'])): ?>
                        <span class="badge badge-subtle-success rounded-pill">
                            Free
                        </span>
                    <?php endif; ?>
                </a>
            </h6>

            <!-- Organized by section -->
            <p class="mb-1">
                Organized by 
                <?php 
                if (is_null($activity['dept_id']) && is_null($activity['org_id'])) {
                    echo 'Student Parliament';
                } elseif (!is_null($activity['org_id'])) {
                    echo $activity['org_name'];
                } elseif (!is_null($activity['dept_id'])) {
                    echo $activity['dept_name'];
                }
                ?>
            </p>

            <!-- Time Section: Updated to show time range -->
            <p class="text-1000 mb-0">
                Time: <?= date('h:i a', strtotime($activity['start_date'])) ?> - <?= date('h:i a', strtotime($activity['end_date'])) ?>
            </p>

            <!-- Date Section: Display Start Date in the required format -->
            <p class="text-1000 mb-0">
                Date: <?= date('F d, Y', strtotime($activity['start_date'])) ?>
            </p>

            <div class="border-bottom border-dashed my-3"></div>
        </div>
    </div>
<?php endforeach; ?>

           
                </div>
                <div class="card-footer bg-body-tertiary p-0 border-top">
                    <a class="btn btn-link d-block w-100" href="<?php echo site_url('student/list-activity/'. $this->session->userdata('student_id')); ?>">All Events<span class="fas fa-chevron-right ms-1 fs-11"></span></a>
                </div>
            </div>
        </div>
    </div>
</div>

