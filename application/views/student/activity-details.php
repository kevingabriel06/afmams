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
		src="<?php echo !empty($item->activity_image)
					? base_url('assets/coverEvent/' . $item->activity_image)
					: base_url('assets/image/OIP.jpg'); ?>" alt="Event Cover" />
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

				<!-- Registered/Interested Button (Triggers Modal) -->
				<button class="btn btn-falcon-default btn-sm me-2" type="button" data-bs-toggle="modal" data-bs-target="#downloadReceiptModal">
					<span class="fas fa-users text-danger me-1"></span>
					<?php if ($activity['registration_fee'] == '0') : ?>
						<?= $attendees; ?> Interested
					<?php else : ?>
						<?= $registered; ?> Registered
					<?php endif; ?>
				</button>

				<!-- Modal Structure -->
				<div class="modal fade" id="downloadReceiptModal" tabindex="-1" aria-labelledby="downloadReceiptModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-sm modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="downloadReceiptModalLabel">Receipt</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body text-center">

								<?php if (isset($receipt) && !is_null($receipt)) : ?>
									<?php if ($receipt['registration_status'] == 'Verified') : ?>
										<?php if (!empty($receipt['generated_receipt'])) : ?>
											<p>You may download your receipt below.</p>
											<a href="<?= base_url('uploads/generated_receipts/' . $receipt['generated_receipt']) ?>"
												class="btn btn-success"
												download>
												<i class="fas fa-download me-1"></i> Download Receipt
											</a>
										<?php else : ?>
											<p class="text-warning">Receipt is still being generated. Please check back later.</p>
										<?php endif; ?>

									<?php elseif ($receipt['registration_status'] == 'Pending') : ?>
										<p class="text-muted">Your registration is still pending. Receipt will be available once approved.</p>

									<?php elseif ($receipt['registration_status'] == 'Rejected') : ?>
										<p class="text-danger">Your registration was rejected. No receipt available.</p>
									<?php endif; ?>
								<?php else : ?>
									<p>No receipt information available.</p>
								<?php endif; ?>


							</div>
						</div>
					</div>
				</div>



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
					<?php if (!empty($upcoming_activities)) {
						shuffle($upcoming_activities); // Shuffle only if activities exist 
					}

					$hasUpcomingActivities = false; // Default to false

					foreach ($upcoming_activities as $act) {
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
									<a href="<?= site_url('student/activity-details/' . htmlspecialchars($act->activity_id)) ?>">
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
					<a class="btn btn-link d-block w-100 text-center" href="<?= site_url('student/list-activity') ?>">
						All Events <span class="fas fa-chevron-right ms-1 fs-11"></span>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>