<?php foreach ($feed as $item): ?>
	<div class="feed-item">
		<?php if (isset($item->post_id)): ?>
			<!-- THIS IS THE TEMPLATE FOR POSTING BOTH TEXT AND IMAGE -->
			<div class="card mb-3">
				<div class="card-header bg-body-tertiary">
					<div class="row justify-content-between">
						<div class="col">
							<div class="d-flex">
								<div class="avatar avatar-2xl status-online">
									<!-- Display the profile picture based on the post -->
									<img class="rounded-circle" src="<?php echo base_url('assets/profile/') . (!empty($item->profile_pic) ? $item->profile_pic : 'default.jpg'); ?>" />
								</div>
								<div class="flex-1 align-self-center ms-2">
									<p class="mb-1 lh-1"> <!-- Display the post creator's name -->
										<a class="fw-semi-bold" href="#"><?php echo htmlspecialchars($item->first_name) . " " . ($item->last_name); ?></a>
									</p>
									<p class="mb-0 fs-10">
										<span class="time-ago" data-timestamp="<?= $item->created_at ?>">
											<?= time_elapsed_string($item->created_at) ?>
										</span>
										&bull;
										<?php if ($item->privacy == 'Public'): ?>
											<span class="fas fa-globe-americas"></span>
										<?php elseif ($item->privacy == 'Private'): ?>
											<span class="fas fa-users"></span>
										<?php endif; ?>
										&bull;
										<?php if (empty($item->org_id) && empty($item->dept_id)): ?>
											<?= htmlspecialchars("Student Parliament") ?>
										<?php elseif (empty($item->org_id)) : ?>
											<?= htmlspecialchars($item->dept_name) ?>
										<?php elseif (empty($item->dept_id)) : ?>
											<?= htmlspecialchars($item->org_name) ?>
										<?php endif; ?>
									</p>

								</div>
								<script>
									function updateTimeAgo() {
										document.querySelectorAll(".time-ago").forEach(function(element) {
											let timestamp = new Date(element.getAttribute("data-timestamp"));
											element.innerText = timeAgo(timestamp);
										});
									}

									function timeAgo(date) {
										let seconds = Math.floor((new Date() - date) / 1000);
										let minutes = Math.floor(seconds / 60);
										let hours = Math.floor(minutes / 60);
										let days = Math.floor(hours / 24);

										if (seconds < 60) return "Just now";
										if (minutes < 60) return minutes === 1 ? "1 minute ago" : minutes + " minutes ago";
										if (hours < 24) return hours === 1 ? "1 hour ago" : hours + " hours ago";
										if (days < 7) return days === 1 ? "1 day ago" : days + " days ago";

										return date.toLocaleString('en-US', {
											month: 'long',
											day: 'numeric',
											year: 'numeric',
											hour: 'numeric',
											minute: '2-digit',
											hour12: true
										});
									}
									// Auto-update every minute
									setInterval(updateTimeAgo, 60000);
									updateTimeAgo();
								</script>
							</div>
						</div>
					</div>
				</div>
				<div class="card-body overflow-hidden">
					<?php
					$content = htmlspecialchars($item->content);
					$maxLength = 200;

					if (strlen($content) > $maxLength):
						$shortContent = substr($content, 0, $maxLength);
					?>
						<p class="post-preview">
							<?php echo $shortContent; ?>...
							<a href="javascript:void(0);" class="view-more" data-full-content="<?php echo $content; ?>"> View More </a>
						</p>
						<p class="full-content d-none">
							<?php echo $content; ?>
							<a href="javascript:void(0);" class="view-less">See Fewer</a>
						</p>
					<?php else: ?>
						<p><?php echo $content; ?></p>
					<?php endif; ?>
				</div>
				<?php if ($item->media): ?>
					<div class="card-body overflow-hidden">
						<img class="img-fluid rounded" src="<?php echo base_url('assets/post/') . $item->media; ?>" alt="" />
					</div>
				<?php endif; ?>
				<div class="card-footer bg-body-tertiary pt-0">
					<div class="border-bottom border-200 fs-10 py-3">
						<span class="like-count" id="like-count-<?= $item->post_id; ?>">
							<?php echo $item->like_count; ?>
							<a href="javascript:void(0);" onclick="showLikesModal(<?= $item->post_id; ?>)">
								Likes
							</a>
						</span>
						&bull;
						<a class="text-700 comment-counter" id="comment-counter-<?= $item->post_id; ?>" href="#!">
							<?= htmlspecialchars($item->comments_count); ?> Comments
						</a>
					</div>
					<!-- Modal to Display Likes (Facebook Style) -->
					<div class="modal fade" id="likesModal-<?= $item->post_id; ?>" tabindex="-1" role="dialog" aria-labelledby="likesModalLabel-<?= $item->post_id; ?>" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered" role="document">
							<div class="modal-content">
								<!-- Modal Header -->
								<div class="modal-header">
									<h5 class="modal-title" id="likesModalLabel-<?= $item->post_id; ?>" style="font-weight: bold;">People Who Liked This</h5>
									<!-- Close Button -->
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<!-- Modal Body -->
								<div class="modal-body">
									<div class="container">
										<div class="row">
											<div class="col-12">
												<ul id="likesList-<?= $item->post_id; ?>" class="list-unstyled" style="max-height: 300px; overflow-y: auto;">
													<!-- List of users will be injected here via AJAX -->
												</ul>
											</div>
										</div>
									</div>
								</div>

							</div>
						</div>
					</div>


					<div class="row g-0 fw-semi-bold text-center py-2 fs-10">
						<div class="col-auto">
							<?php if ($item->user_has_liked_post): ?>
								<button class="rounded-2 d-flex align-items-center me-3" id="btn-like" data-post-id="<?= $item->post_id; ?>" style="background: transparent; border: none; padding: 0;">
									<img src="<?= base_url(); ?>assets/img/icons/spot-illustrations/like-active.png" width="20" alt="Like Icon" />
									<span class="ms-1">Liked</span>
								</button>
							<?php else: ?>
								<button class="rounded-2 d-flex align-items-center me-3" id="btn-like" data-post-id="<?= $item->post_id; ?>" style="background: transparent; border: none; padding: 0;">
									<img src="<?= base_url(); ?>assets/img/icons/spot-illustrations/like-inactive.png" width="20" alt="Like Icon" />
									<span class="ms-1">Like</span>
								</button>
							<?php endif; ?>
						</div>
						<div class="col-auto">
							<button class="rounded-2 d-flex align-items-center me-3" href="#!" style="background: transparent; border: none; padding: 0;">
								<img src="<?= base_url(); ?>assets/img/icons/spot-illustrations/comment-active.png" width="20" alt="" />
								<span class="ms-1">Comment</span>
							</button>
						</div>
					</div>
					<!-- COMMENT SECTION -->
					<form id="commentForm-<?= $item->post_id; ?>" class="d-flex align-items-center border-top border-200 pt-3">
						<div class="avatar avatar-xl">
							<img class="rounded-circle" src="<?php $profile_pic = !empty($authors->profile_pic) ? base_url('assets/profile/') . $authors->profile_pic : base_url('assets/profile/default.jpg');
																echo $profile_pic; ?>" alt="" />
						</div>
						<input class="form-control rounded-pill ms-2 fs-10" type="text" name="comment" placeholder="Write a comment..." required />
						<input type="hidden" name="post_id" value="<?= $item->post_id; ?>" />
						<button type="submit" class="btn btn-link ms-auto">
							<i class="fas fa-paper-plane"></i>
						</button>
					</form>
					<!-- DISPLAYING COMMENTS -->
					<div id="comment-section-<?= $item->post_id; ?>">
						<?php if (!empty($item->comments)): ?>
							<?php $comment_count = 0; ?>
							<?php foreach ($item->comments as $comment): ?>
								<?php if ($comment->post_id == $item->post_id): ?>
									<div class="d-flex mt-3 comment-item <?= $comment_count >= 2 ? 'd-none extra-comment' : ''; ?>">
										<div class="avatar avatar-xl">
											<img class="rounded-circle" src="<?= base_url('assets/profile/') . ($comment->profile_pic ?: 'default.jpg'); ?>" alt="Profile Picture" />
										</div>
										<div class="flex-1 ms-2 fs-10">
											<p class="mb-1 bg-200 rounded-3 p-2">
												<a class="fw-semi-bold" href="#!"><?= htmlspecialchars($comment->first_name . " " . $comment->last_name); ?></a>
												<?= htmlspecialchars($comment->content); ?>
											</p>
										</div>
									</div>
									<?php $comment_count++; ?>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php else: ?>
							<div class="mt-3 no-comments">
								<p class="text-muted fs-20">No comments yet. Be the first to comment!</p>
							</div>
						<?php endif; ?>
					</div>
					<!-- Load More Comments Button -->
					<?php if (count($item->comments) > 2): ?>
						<div class="mt-2">
							<a href="#!" class="fs-10 text-700 d-inline-block mt-2 view-more-comments" data-post-id="<?= $item->post_id; ?>"> View all comments </a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php elseif (isset($item->activity_id)): ?>
			<!-- THIS IS THE EVENT TEMPLATE -->
			<div class="card mb-3">
				<img id="coverPhoto" class="card-img-top" src="<?php echo !empty($item->activity_image)
																	? base_url('assets/coverEvent/' . $item->activity_image)
																	: base_url('assets/image/OIP.jpg'); ?>" alt="Event Cover" />
				<div class="card-body overflow-hidden">
					<div class="row justify-content-between align-items-center">
						<div class="col">
							<div class="d-flex">
								<!-- Calendar Section -->
								<div class="calendar me-2">
									<?php
									$start_date = strtotime($item->start_date);
									$month = date('M', $start_date);
									$day = date('j', $start_date);
									$year = date('Y', $start_date);
									?>
									<span class="calendar-month"><?php echo $month; ?></span>
									<span class="calendar-day"><?php echo $day; ?></span>
									<span class="calendar-year" hidden><?php echo $year; ?></span>
								</div>
								<!-- Event Details -->
								<div class="flex-1 position-relative ps-3">
									<p class="mb-1" hidden><?php echo $item->activity_id; ?></p>
									<h6 class="fs-9 mb-0">
										<a href="<?php echo site_url('student/activity-details/' . $item->activity_id); ?>">
											<?php echo $item->activity_title; ?>
										</a>
									</h6>
									<p class="mb-1"> Organized by
										<?php echo $item->organizer; ?> </p>
									<span class="fs-9 text-warning fw-semi-bold">
										<?php echo ($item->registration_fee > 0) ? 'Php ' . $item->registration_fee : 'Free Event'; ?>
									</span>
								</div>
							</div>
						</div>
						<!-- Action Button -->
						<div class="col-md-auto d-none d-md-block">
							<?php if ($item->registration_fee == '0'): ?>
								<?php if ($item->attendees_status == 'Attending'): ?>
									<button class="btn btn-falcon-default btn-sm px-4 cancel-button"
										data-activity-id="<?= $item->activity_id ?>"
										data-student-id="<?= $this->session->userdata('student_id') ?>"
										data-status="<?= $item->attendees_status; ?>">
										Cancel
									</button>
								<?php else: ?>
									<button class="btn btn-falcon-default btn-sm px-4 attend-button"
										data-activity-id="<?= $item->activity_id ?>"
										data-student-id="<?= $this->session->userdata('student_id') ?>"
										data-status="<?= $item->attendees_status; ?>">
										Attend
									</button>
								<?php endif; ?>
							<?php else: ?>
								<button
									class="btn btn-falcon-default btn-sm px-4 open-registration-modal"
									type="button"
									data-bs-toggle="modal"
									data-bs-target="#registrationModal"
									data-activity-id="<?= $item->activity_id; ?>"
									data-registration-fee="<?= $item->registration_fee; ?>"
									data-status="<?= $item->registration_status; ?>"
									data-qr-code="<?= $item->qr_code; ?>"
									data-student-id="<?= $this->session->userdata('student_id') ?>">
									Register
								</button>
							<?php endif; ?>
						</div>

					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
<?php endforeach; ?>

<script>
	$('.attend-button').on('click', function() {
		const button = $(this);
		const activityId = button.data('activity-id');
		const studentId = button.data('student-id');
		const currentStatus = button.data('status'); // Either "Attending" or null/empty

		let confirmText = currentStatus === "Attending" ?
			"Do you want to cancel your attendance?" :
			"Do you want to mark yourself as attending this event?";

		Swal.fire({
			title: 'Are you sure?',
			text: confirmText,
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "<?= site_url('student/attend'); ?>", // Ensure your URL is correct here
					type: "POST",
					data: {
						activity_id: activityId,
						student_id: studentId,
						status: currentStatus === "Attending" ? "Cancelled" : "Attending"
					},
					dataType: "json",
					success: function(response) {
						if (response.status === "success") {
							Swal.fire({
								icon: 'success',
								title: 'Success',
								text: response.message
							}).then(() => {
								// Optionally, update button text or hide the button
								location.reload(); // Reload the page to reflect changes
							});
						} else {
							Swal.fire({
								icon: 'info',
								title: 'Notice',
								text: response.message
							});
						}
					},
					error: function() {
						Swal.fire({
							icon: 'error',
							title: 'Error',
							text: 'Something went wrong while updating your attendance.'
						});
					}
				});
			}
		});
	});

	$('.cancel-button').on('click', function() {
		const activityId = $(this).data('activity-id');
		const studentId = $(this).data('student-id');

		// Confirm before canceling
		Swal.fire({
			title: 'Are you sure?',
			text: "Do you want to cancel your attendance for this event?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Yes, Cancel Attendance',
			cancelButtonText: 'No, Keep Attendance',
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "<?= site_url('student/cancel'); ?>",
					type: "POST",
					data: {
						activity_id: activityId,
						student_id: studentId
					},
					dataType: "json",
					success: function(response) {
						if (response.status === "success") {
							Swal.fire({
								icon: 'success',
								title: 'Attendance Cancelled',
								text: response.message
							}).then(() => {
								// Optionally, update button text or hide the button
								location.reload(); // Reload the page to reflect changes
							});
						} else {
							Swal.fire({
								icon: 'error',
								title: 'Error',
								text: response.message
							});
						}
					},
					error: function() {
						Swal.fire({
							icon: 'error',
							title: 'Error',
							text: 'Failed to cancel attendance.'
						});
					}
				});
			}
		});
	});
</script>


<!-- <script>
	// SCRIPT FOR THE RECEIPT REGISTRATION
	document.addEventListener("DOMContentLoaded", function() {
		const uploadContainer = document.getElementById("receipt-upload-container");
		const uploadInput = document.getElementById("receipt-upload");
		const receiptPreview = document.getElementById("receipt-preview");
		const receiptPlaceholder = document.getElementById("receipt-placeholder");

		// Trigger file upload when clicking on the preview area
		uploadContainer.addEventListener("click", function() {
			uploadInput.click();
		});

		// Preview the uploaded receipt image
		uploadInput.addEventListener("change", function(event) {
			const file = event.target.files[0];
			if (file) {
				const reader = new FileReader();
				reader.onload = function(e) {
					receiptPreview.src = e.target.result;
					receiptPreview.classList.remove("d-none");
					receiptPlaceholder.classList.add("d-none");
				};
				reader.readAsDataURL(file);
			}
		});

		const registrationModal = document.getElementById("registrationModal");
		const modalActivityIdInput = document.getElementById("modal_activity_id");
		const modalRegistrationInput = document.getElementById("modal_amount");
		const modalStatusInput = document.getElementById("modal_status");
		const registrationForm = document.getElementById("registrationForm");
		const statusMessage = document.getElementById("status-message");

		const modalBody = registrationModal.querySelector(".modal-body");

		// === Register Button Click Handler ===
		$(document).on("click", ".open-registration-modal", function() {
			const activityId = $(this).data("activity-id");
			const registration = $(this).data("registration-fee");
			const status = $(this).data("status");
			const actstatus = $(this).data("act-status");
			const qrCode = $(this).data("qr-code");

			const qrCodeImagePath = "<?= base_url('assets/qrcodeRegistration/'); ?>" + qrCode;

			// Populate modal fields
			$("#modal_activity_id").val(activityId);
			$("#modal_amount").val(registration);
			$("#modal_status").val(status);

			const qrCodeImage = document.getElementById("qrCodeImage");

			// Show proper content based on status
			if (status === "Pending") {
				$("#registrationForm").addClass("d-none");
				$("#status-message")
					.removeClass("d-none alert-success")
					.addClass("alert alert-warning")
					.text("Your registration is pending. Please wait for the admin to verify.");
				qrCodeImage.classList.add("d-none"); // Hide QR code
			} else if (status === "Verified") {
				$("#registrationForm").addClass("d-none");
				$("#status-message")
					.removeClass("d-none alert-warning")
					.addClass("alert alert-success")
					.text("You are registered to this activity.");
				qrCodeImage.classList.add("d-none"); // Hide QR code
			} else if (actstatus === "Completed" || actstatus === "Ongoing") {
				$("#registrationForm").addClass("d-none");
				$("#status-message")
					.removeClass("d-none alert-success")
					.addClass("alert alert-danger")
					.text("This activity is not accepting registration. Contact the organizer.");
				qrCodeImage.classList.add("d-none"); // Hide QR code
			} else {
				// Show registration form
				$("#registrationForm").removeClass("d-none");
				$("#status-message").addClass("d-none").text("");
				qrCodeImage.src = qrCodeImagePath;
				qrCodeImage.classList.remove("d-none"); // Show QR code
			}
		});


		registrationModal.addEventListener('hidden.bs.modal', function() {
			registrationForm.reset();
			receiptPreview.src = "";
			receiptPreview.classList.add("d-none");
			receiptPlaceholder.classList.remove("d-none");
			registrationForm.classList.remove('d-none');
			statusMessage.classList.add('d-none');
			statusMessage.innerText = "";

			const qrCodeImage = document.getElementById("qrCodeImage");
			qrCodeImage.src = "";
			qrCodeImage.classList.add("d-none");
		});
	});
</script> -->


<!-- // SCRIPT FOR THE RECEIPT REGISTRATION
// document.addEventListener("DOMContentLoaded", function() {
// const uploadContainer = document.getElementById("receipt-upload-container");
// const uploadInput = document.getElementById("receipt-upload");

// // Trigger file upload when clicking on the preview area
// uploadContainer.addEventListener("click", function() {
// uploadInput.click();
// });

// // Preview the uploaded receipt image
// uploadInput.addEventListener("change", function(event) {
// const file = event.target.files[0];
// if (file) {
// const reader = new FileReader();
// reader.onload = function(e) {
// document.getElementById("receipt-preview").src = e.target.result;
// document.getElementById("receipt-preview").classList.remove("d-none");
// document.getElementById("receipt-placeholder").classList.add("d-none");
// };
// reader.readAsDataURL(file);
// }
// });

// const registrationModal = document.getElementById('registrationModal');
// const modalActivityIdInput = document.getElementById('modal_activity_id');
// const modalRegistrationInput = document.getElementById('modal_amount');
// const modalStatusInput = document.getElementById('modal_status');
// const registrationForm = document.getElementById('registrationForm');
// const statusMessage = document.getElementById('status-message');

// // === Register Button Click Handler ===
// $(document).on('click', '.open-registration-modal', function() {
// const activityId = $(this).data('activity-id');
// const registration = $(this).data('registration-fee');
// const status = $(this).data('status');

// $('#modal_activity_id').val(activityId);
// $('#modal_amount').val(registration);
// $('#modal_status').val(status);

// if (status === "Pending") {
// $('#registrationForm').addClass("d-none");
// $('#status-message').removeClass("d-none alert-success").addClass("alert-warning").text("Your registration is pending. Please wait for the admin to verify.");
// } else if (status === "Verified") {
// $('#registrationForm').addClass("d-none");
// $('#status-message').removeClass("d-none alert-info").addClass("alert-success").text("You are registered to this activity.");
// } else {
// $('#registrationForm').removeClass("d-none");
// $('#status-message').addClass("d-none").text("");
// }
// });

// // === Reset modal on close ===
// registrationModal.addEventListener('hidden.bs.modal', function() {
// registrationForm.reset();
// receiptPreview.src = "";
// receiptPreview.classList.add("d-none");
// receiptPlaceholder.classList.remove("d-none");
// registrationForm.classList.remove('d-none');
// statusMessage.classList.add('d-none');
// statusMessage.innerText = "";
// });
// });

// // REGISTRATION INSERT
// $(document).ready(function() {

// // Handle form submission via AJAX with SweetAlert
// $('#registrationForm').on('submit', function(e) {
// e.preventDefault();

// const form = $(this);
// const formData = new FormData(this);
// const submitButton = form.find('button[type="submit"]');
// submitButton.prop('disabled', true);

// $.ajax({
// url: "<?= site_url('student/register'); ?>",
// type: "POST",
// data: formData,
// contentType: false,
// processData: false,
// dataType: "json",
// success: function(response) {
// if (response.status === "success") {
// Swal.fire({
// icon: 'success',
// title: 'Registration Successful!',
// text: response.message,
// confirmButtonColor: '#3085d6',
// confirmButtonText: 'OK'
// }).then(() => {
// location.reload(); // Reload after the user clicks OK
// });
// } else {
// Swal.fire({
// icon: 'error',
// title: 'Oops!',
// text: response.message
// });
// }
// },
// error: function() {
// Swal.fire({
// icon: 'error',
// title: 'Submission Failed',
// text: 'An error occurred while submitting the registration.'
// });
// },
// complete: function() {
// submitButton.prop('disabled', false);
// }
// });
// });

// // Handle modal open with data passed
// $('.attend-button').on('click', function() {
// const activityId = $(this).data('activity-id');
// const studentId = $(this).data('student-id');

// $('#modal_activity_id').val(activityId);
// $('#modal_student_id').val(studentId);
// });

// // Reset modal when closed
// $('#registrationModal').on('hidden.bs.modal', function() {
// $('#registrationForm')[0].reset();
// $('#receipt-preview').addClass('d-none').attr('src', '');
// $('#receipt-placeholder').removeClass('d-none');
// });

// });
// </script> -->