<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<div class="row g-3">
	<!-- FEED -->
	<div class="col-lg-8">
		<div class="card mb-3">
			<div class="card-header bg-body-tertiary overflow-hidden">
				<div class="d-flex align-items-center">
					<div class="avatar avatar-m">
						<!-- PROFILE PIC BASED ON THE USER IN ADMIN AND OFFICER SIDE -->
						<img class="rounded-circle" src="
                            <?php
							$profile_pic = !empty($authors->profile_pic) ? base_url('assets/profile/') . $authors->profile_pic : base_url('assets/profile/default.jpg');
							echo $profile_pic;
							?>" alt="" />
					</div>
					<div class="flex-1 ms-2">
						<h5 class="mb-0 fs-9">Create post</h5>
					</div>
				</div>
			</div>

			<div class="card-body p-0">
				<form id="postForm" enctype="multipart/form-data">
					<!-- Textarea for content -->
					<textarea class="shadow-none form-control rounded-0 resize-none px-x1 border-y-0 border-200" placeholder="What do you want to talk about?" rows="4" name="content" id="postContent"></textarea>
					<div class="border-bottom border-dashed my-3"></div>
					<!-- Image Preview Section -->
					<div id="imagePreviewContainer" class="d-none d-flex align-items-center border p-2 rounded">
						<!-- Image -->
						<img id="imagePreview" src="" alt="Selected Image" style="width: 60px; height: 60px; border-radius: 8px; border: 1px solid #ccc; margin-right: 10px;" />
						<!-- File Info & Remove Option -->
						<div class="flex-1 d-flex justify-content-between w-100">
							<div>
								<h6 id="imageName" class="mb-1 text-truncate" style="max-width: 150px;">Image Name</h6>
								<div class="d-flex align-items-center">
									<p id="imageSize" class="mb-0 fs-10 text-muted">0 MB</p>
								</div>
							</div>
							<!-- Dropdown for Remove -->
							<div class="dropdown font-sans-serif">
								<button class="btn btn-link text-600 btn-sm dropdown-toggle btn-reveal dropdown-caret-none" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<span class="fas fa-ellipsis-h"></span>
								</button>
								<div class="dropdown-menu dropdown-menu-end border">
									<a class="dropdown-item remove-image" href="#">Remove Image</a>
								</div>
							</div>
						</div>
					</div>
					<!-- Button section -->
					<div class="row g-0 justify-content-between mt-3 px-x1 pb-3">
						<div class="col">
							<!-- Image Upload Button -->
							<button class="btn btn-tertiary btn-sm rounded-pill shadow-none d-inline-flex align-items-center fs-10 mb-0 me-1" type="button" id="imageBtn">
								<img class="cursor-pointer" src="<?php echo base_url(); ?>assets/img/icons/spot-illustrations/image.svg" width="17" alt="" />
								<span class="ms-2 d-none d-md-inline-block">Image</span>
							</button>
							<!-- Hidden File Input -->
							<input type="file" name="image" id="imageInput" accept="image/*" class="d-none" />
							<!-- Activity Button -->
							<button class="btn btn-tertiary btn-sm rounded-pill shadow-none d-inline-flex align-items-center fs-10 me-1" type="button" data-bs-toggle="modal" data-bs-target="#activityModal">
								<img class="cursor-pointer" src="<?php echo base_url(); ?>assets/img/icons/spot-illustrations/calendar.svg" width="17" alt="" />
								<span class="ms-2 d-none d-md-inline-block">Activity</span>
							</button>
						</div>
						<div class="col-auto">
							<!-- Privacy Dropdown -->
							<div class="dropdown d-inline-block me-1">
								<button class="btn btn-sm dropdown-toggle px-1" id="dropdownMenuButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<span id="privacy-icon" class="fas fa-globe-americas"></span>
								</button>
								<div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
									<a class="dropdown-item" href="#" data-privacy="Public">Public</a>
									<a class="dropdown-item" href="#" data-privacy="Private">Private</a>
								</div>
							</div>
							<!-- Hidden Input to Store Selected Privacy -->
							<input type="hidden" id="privacyStatus" name="privacyStatus" value="Public" />
							<!-- Submit Button -->
							<button class="btn btn-primary btn-sm px-4 px-sm-5" type="submit">Share</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div id="feed-container">
			<?php foreach ($feed as $item): ?>
				<div class="feed-item">
					<?php if ($item->type == "post"): ?>
						<!-- THIS IS THE TEMPLATE FOR POSTING BOTH TEXT AND IMAGE -->
						<div class="card mb-3">
							<div class="card-header bg-body-tertiary">
								<div class="row justify-content-between">
									<div class="col">
										<div class="d-flex">
											<div class="avatar avatar-2xl status-online">
												<!-- Display the profile picture based on the post -->
												<img class="rounded-circle" src="<?php echo base_url('assets/profile/') . (!empty($item->profile_pic) ? $item->profile_pic : 'default-pic.jpg'); ?>" />
											</div>
											<div class="flex-1 align-self-center ms-2">
												<p class="mb-1 lh-1">
													<!-- Display the post creator's name -->
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
									<div class="col-auto">
										<?php if ($item->student_id == $this->session->userdata('student_id')): ?>
											<div class="dropdown font-sans-serif btn-reveal-trigger">
												<!-- Post Actions -->
												<button class="btn btn-link text-600 btn-sm dropdown-toggle dropdown-caret-none btn-reveal" type="button" id="post-album-action" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<span class="fas fa-ellipsis-h fs-10"></span>
												</button>
												<!-- Dropdown Menu -->
												<div class="dropdown-menu dropdown-menu-end py-3" aria-labelledby="post-album-action">
													<a class="dropdown-item text-danger" id="delete-post" data-post-id="<?php echo $item->post_id; ?>" data-url="<?php echo site_url('admin/community/delete-post'); ?>">Delete</a>
												</div>
											</div>
										<?php endif; ?>
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
					<?php elseif ($item->type == "activity"): ?>
						<!-- THIS IS THE EVENT TEMPLATE -->
						<div class="card mb-3">
							<div class="position-absolute top-0 end-0 p-2 z-1">
								<div class="dropdown">
									<button class="btn p-0 border-0 bg-transparent" type="button" data-bs-toggle="dropdown" aria-expanded="false">
										<i class="fas fa-ellipsis-h"></i> <!-- horizontal ellipsis -->
									</button>
									<ul class="dropdown-menu dropdown-menu-end">
										<li>
											<a href="#" class="dropdown-item text-danger unshare-activity" data-id="<?= $item->activity_id ?>">
												<i class="fas fa-share-slash me-1"></i> Unshare Activity
											</a>
										</li>
									</ul>
								</div>
							</div>
							<!-- SCRIPT FOR UNSHARING -->
							<script>
								$(document).on('click', '.unshare-activity', function(e) {
									e.preventDefault();
									var activityId = $(this).data('id');

									Swal.fire({
										title: 'Are you sure?',
										text: 'Do you want to unshare this activity?',
										icon: 'warning',
										showCancelButton: true,
										confirmButtonText: 'Yes, unshare it!',
										cancelButtonText: 'Cancel',
										confirmButtonColor: '#d33'
									}).then((result) => {
										if (result.isConfirmed) {
											$.ajax({
												url: "<?php echo site_url('admin/unshare-activity'); ?>",
												type: "POST",
												data: {
													activity_id: activityId
												},
												success: function(response) {
													Swal.fire('Unshared!', 'The activity is no longer shared.', 'success').then(() => {
														location.reload(); // Or dynamically remove/update the card
													});
												},
												error: function() {
													Swal.fire('Oops!', 'Something went wrong.', 'error');
												}
											});
										}
									});
								});
							</script>


							<img id="coverPhoto" class="card-img-top" src="<?php echo base_url('assets/coverEvent/') . $item->activity_image; ?>" alt="Event Cover" />
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
													<a href="<?php echo site_url('admin/activity-details/' . $item->activity_id); ?>">
														<?php echo $item->activity_title; ?>
													</a>
												</h6>
												<p class="mb-1"> Organized by
													<?php
													if (empty($item->dept_id) && empty($item->org_id)) {
														echo htmlspecialchars("Institution");
													} elseif (empty($item->dept_id)) {
														echo htmlspecialchars($item->org_name);
													} elseif (empty($item->org_id)) {
														echo htmlspecialchars($item->dept_name);
													}
													?> </p>
												<span class="fs-9 text-warning fw-semi-bold">
													<?php echo ($item->registration_fee > 0) ? 'Php ' . $item->registration_fee : 'Free Event'; ?>
												</span>
											</div>
										</div>
									</div>
									<!-- Action Button -->
									<div class="col-md-auto d-none d-md-block">
										<?php if ($item->registration_fee == '0'): ?>
											<button class="btn btn-falcon-default btn-sm px-4" type="button">Attend</button>
										<?php else: ?>
											<button class="btn btn-falcon-default btn-sm px-4" type="button">Register</button>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<!-- FETCHING UPCOMING ACTIVITY RANDOMLY -->
	<div class="col-lg-4">
		<div class="card mb-3 mb-lg-0 sticky-sidebar">
			<div class="card-header bg-body-tertiary">
				<h5 class="mb-0">Upcoming Activities</h5>
			</div>
			<div class="card-body fs-10"> <?php shuffle($activities_upcoming); ?>
				<?php
				$count = 0;
				$hasUpcomingActivities = false;

				foreach ($activities_upcoming as $activity):
					$count++;
					if ($count > 4) break;
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
								<a href="<?php echo site_url('admin/activity-details/' . $activity->activity_id); ?>">
									<?= htmlspecialchars($activity->activity_title) ?>
									<?php if ($activity->registration_fee == '0'): ?>
										<span class="badge badge-subtle-success rounded-pill">Free</span>
									<?php endif; ?>
								</a>
							</h6>
							<p class="mb-1">Organized by <?php echo htmlspecialchars($activity->organizer); ?></p>
							<p class="text-1000 mb-0">Date: <?= htmlspecialchars(date('M j, Y', strtotime($activity->start_date))) ?></p>
							<div class="border-bottom border-dashed my-2"></div>
						</div>
					</div>
				<?php endforeach; ?>
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

<style>
	.sticky-sidebar {
		position: sticky;
		top: 8%;
		/* Adjust as needed */
		z-index: 1000;
	}
</style>


<!-- MODAL OF ACTIVITY LIST -->
<div class="modal fade" id="activityModal" tabindex="-1" aria-labelledby="activityModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header">
				<h5 class="modal-title" id="activityModalLabel">Shared an Activity</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<!-- Modal Body -->
			<div class="modal-body">
				<div class="row">
					<?php
					$has_activity = false; // Flag to track if at least one valid activity exists

					foreach ($activities_upcoming as $activity):
						if ($activity->is_shared == 'No' && $activity->organizer == 'Student Parliament'):
							$has_activity = true; // Set flag to true if a valid activity is found
					?>
							<div class="col-md-6 mb-4">
								<!-- Activity Item -->
								<div class="d-flex btn-reveal-trigger activity-item p-3 border rounded shadow-sm hover-shadow"
									data-id="<?php echo $activity->activity_id; ?>"
									data-title="<?php echo $activity->activity_title; ?>"
									data-start="<?php echo $activity->start_date; ?>"
									data-end="<?php echo $activity->end_date; ?>"
									data-fee="<?php echo $activity->registration_fee; ?>">

									<!-- Calendar Info -->
									<div class="calendar">
										<?php
										$start_date_timestamp = strtotime($activity->start_date);
										$month = date('M', $start_date_timestamp);
										$day = date('j', $start_date_timestamp);
										$year = date('y', $start_date_timestamp);
										?>
										<span class="calendar-month"><?php echo $month; ?></span>
										<span class="calendar-day"><?php echo $day; ?></span>
										<span class="calendar-year" hidden><?php echo $year; ?></span>
									</div>

									<!-- Activity Details -->
									<div class="flex-1 position-relative ps-3">
										<p class="mb-1" hidden><?php echo $activity->activity_id; ?></p>
										<h6 class="fs-9 mb-0">
											<p>
												<?php echo $activity->activity_title; ?>
												<?php if ($activity->registration_fee == '0'): ?>
													<span class="badge badge-subtle-success rounded-pill">Free</span>
												<?php endif; ?>
											</p>
										</h6>
										<p class="text-1000 mb-0">Date: <?php echo date('M j, Y', strtotime($activity->start_date)); ?> </p>
									</div>
								</div>
							</div>
					<?php endif;
					endforeach; ?>

					<!-- Display "No activities listed" message if no valid activity is found -->
					<?php if (!$has_activity): ?>
						<div class="text-center py-4">
							<h5 class="text-muted">No activities listed</h5>
							<p class="text-secondary">Looks like there are no activities yet!</p>
						</div>
					<?php endif; ?>
				</div>


				<!-- Share Button (Initially hidden) -->
				<div class="mt-4 text-center" id="shareButtonDiv" style="display: none;">
					<button class="btn btn-primary px-4 py-2" id="shareButton">Share Selected Activity</button>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		let offset = 5; // Start after the initial 5 posts
		const limit = 5; // Number of posts per batch
		let loading = false; // Prevent multiple requests
		let allPostsLoaded = false; // Stop when no more posts

		function loadMorePosts() {
			if (loading || allPostsLoaded) return;
			loading = true;
			$('#loading').show();

			$.ajax({
				url: '<?= site_url('admin/community') ?>',
				type: 'POST',
				data: {
					offset: offset,
					limit: limit
				},
				success: function(response) {
					if ($.trim(response) === '') {
						allPostsLoaded = true;
						$('#loading').text('No more posts.');
					} else {
						$('#feed-container').append(response); // Adds new posts at the bottom
						offset += limit; // Increase offset for next batch
					}
				},
				complete: function() {
					$('#loading').hide();
					loading = false;
				}
			});
		}

		// Detect scroll to bottom
		$(window).scroll(function() {
			if (!loading && !allPostsLoaded && $(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
				loadMorePosts();
			}
		});

		// Prevent scroll position restoration on reload
		if ('scrollRestoration' in history) {
			history.scrollRestoration = 'manual';
		}

		// Force scroll to top on reload
		window.onload = function() {
			window.scrollTo(0, 0);
		};
	});
</script>

<script>
	$(document).ready(function() {
		// Event listener for the Like button
		$('[id^=btn-like]').click(function() {
			var postId = $(this).data('post-id'); // Get the post ID
			var button = $(this); // Reference to the button

			// Send AJAX request to like/unlike the post
			$.ajax({
				url: '<?= site_url("admin/community/like-post/") ?>' + postId, // Send to controller's like_post method
				type: 'POST',
				success: function(response) {
					var data = JSON.parse(response); // Parse the response data

					// Update the button's icon and text based on the response
					button.find('img').attr('src', data.like_img); // Change image (active/inactive)
					button.find('span').text(data.like_text); // Change text (Liked/Like)

					// Update the like count dynamically
					$('#like-count-' + postId).html(`
                    ${data.new_like_count} 
                        <a href="javascript:void(0);" onclick="showLikesModal(${postId})">
                           Likes
                        </a>
                    `);

					// Auto-update the like list in the modal
					updateLikeList(postId);
				},
				error: function() {
					alert('Something went wrong, please try again later.');
				}
			});
		});
	});

	// Function to update the like list in real-time
	function updateLikeList(postId) {
		$.ajax({
			url: '<?= site_url('admin/view_likes/'); ?>' + postId,
			method: 'GET',
			success: function(response) {
				$('#likesList-' + postId).html(response); // Update modal content
			},
			error: function() {
				alert('Error fetching likes. Please try again.');
			}
		});
	}

	// Function to show the modal and update the like list
	function showLikesModal(postId) {
		updateLikeList(postId); // Refresh likes before showing modal
		$('#likesModal-' + postId).modal('show');
	}
</script>


<!-- ========= JAVASCRIPT  ======== -->
<script>
	// FOR COMMENT SECTION
	$(document).on('submit', 'form[id^="commentForm"]', function(e) {
		e.preventDefault();
		var form = $(this);
		var formData = form.serialize();
		var postId = form.find('input[name="post_id"]').val();
		var commentSection = $('#comment-section-' + postId); // Where comments are displayed
		var commentCounter = $('#comment-counter-' + postId); // Comment counter
		var noCommentsMsg = commentSection.find('.no-comments'); // No comments message

		$.ajax({
			url: '<?= site_url("admin/community/add-comment"); ?>',
			type: 'POST',
			data: formData,
			dataType: 'json',
			success: function(response) {
				if (response.status === 'success') {
					// Update the comment counter
					commentCounter.text(response.comments_count + ' Comments');

					// Hide "No comments yet" message
					if (noCommentsMsg.length) {
						noCommentsMsg.remove();
					}

					// Check if the new_comment object is properly returned
					if (response.new_comment) {
						let newCommentHTML = `
                        <div class="d-flex mt-3 comment-item">
                            <div class="avatar avatar-xl">
                                <img class="rounded-circle" src="${response.new_comment.profile_pic}" alt="Profile Picture" />
                            </div>
                            <div class="flex-1 ms-2 fs-10">
                                <p class="mb-1 bg-200 rounded-3 p-2">
                                    <a class="fw-semi-bold" href="#">${response.new_comment.first_name} ${response.new_comment.last_name}</a>
                                    ${response.new_comment.content}
                                </p>
                            </div>
                        </div>`;

						// Prepend the latest comment at the top
						commentSection.prepend(newCommentHTML);
					}

					// Clear input field
					form.find('input[name="comment"]').val('');

					// Show success notification with SweetAlert2
					Swal.fire({
						icon: 'success',
						title: 'Comment Added!',
						text: 'Your comment has been posted successfully.',
						showConfirmButton: false,
						timer: 1500
					});
				} else {
					Swal.fire({
						icon: 'error',
						title: 'Error',
						text: response.errors,
						confirmButtonText: 'OK'
					});
				}
			},
			error: function() {
				Swal.fire({
					icon: 'error',
					title: 'Oops!',
					text: 'Something went wrong. Please try again later.',
					confirmButtonText: 'OK'
				});
			},
		});
	});

	$(document).on('click', '.view-more-comments', function(e) {
		e.preventDefault();
		let postId = $(this).data('post-id');
		let commentSection = $('#comment-section-' + postId);
		let hiddenComments = commentSection.find('.extra-comment');
		if ($(this).text().trim() === 'View all comments') {
			hiddenComments.removeClass('d-none'); // Show all comments
			$(this).text('See fewer comments');
		} else {
			hiddenComments.addClass('d-none'); // Hide extra comments
			$(this).text('View all comments');
		}
	});

	// FUNCTION FOR INSERTING POST
	$(document).ready(function() {
		$('#postForm').on('submit', function(e) {
			e.preventDefault();
			var formData = new FormData(this);

			Swal.fire({
				title: 'Confirm Sharing',
				text: 'Are you sure you want to post this?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes!',
				cancelButtonText: 'Cancel',
				confirmButtonClass: 'btn btn-primary', // Primary button style
				cancelButtonClass: 'btn btn-danger', // Danger button style
				reverseButtons: true,
				customClass: {
					confirmButton: 'btn btn-primary', // Primary button style
					cancelButton: 'btn btn-danger', // Danger button style
				}
			}).then((result) => {
				if (result.isConfirmed) {
					// Proceed with the AJAX request if confirmed
					$.ajax({
						url: '<?php echo site_url("admin/community/add-post"); ?>',
						type: 'POST',
						data: formData,
						contentType: false,
						processData: false,
						dataType: 'json',
						success: function(response) {
							if (response.status === 'error') {
								Swal.fire({
									icon: 'error',
									title: 'Error',
									text: response.errors // Show error message
								});
							} else if (response.status === 'success') {
								Swal.fire({
									icon: 'success',
									title: 'Success',
									text: response.message, // Show success message
									showConfirmButton: false,
									timer: 2000
								});
								setTimeout(function() {
									window.location.href = response.redirect; // Redirect after 1 second
								}, 1000);
							}
						}
					});
				} else {
					// If cancelled, show a message
					Swal.fire({
						icon: 'info',
						title: 'Cancelled',
						text: 'Post cancelled.',
						showConfirmButton: false,
						timer: 2000
					});
				}
			});
		});
	});

	// DELETE POST
	$(document).ready(function() {
		$(document).on("click", "#delete-post", function(event) {
			event.preventDefault();
			var postId = $(this).data("post-id"); // Get post ID
			var deleteUrl = $(this).data("url"); // Get delete URL
			console.log("Delete clicked, post ID:", postId); // Debugging
			console.log("Delete URL:", deleteUrl); // Debugging

			if (!postId || !deleteUrl) {
				Swal.fire({
					icon: 'error',
					title: 'Invalid Data',
					text: 'Invalid Post ID or URL'
				});
				return;
			}

			// SweetAlert2 confirmation
			Swal.fire({
				title: 'Confirm Deletion',
				text: 'Are you sure you want to delete this post?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes, delete it!',
				cancelButtonText: 'Cancel',
				confirmButtonClass: 'btn btn-primary', // Primary button style
				cancelButtonClass: 'btn btn-danger', // Danger button style
				reverseButtons: true,
				customClass: {
					confirmButton: 'btn btn-primary', // Primary button style
					cancelButton: 'btn btn-danger', // Danger button style
				}
			}).then((result) => {
				if (result.isConfirmed) {
					// Perform AJAX request to delete post
					$.ajax({
						url: deleteUrl, // Ensure this is correct
						type: "POST",
						data: {
							post_id: postId
						},
						dataType: "json", // Ensure response is treated as JSON
						success: function(response) {
							console.log("Delete response:", response); // Debugging
							if (response.status === "success") {
								Swal.fire({
									icon: 'success',
									title: 'Deleted!',
									text: response.message
								});
								setTimeout(function() {
									location.reload(); // Reload the page after 1 second
								}, 1000);
							} else {
								Swal.fire({
									icon: 'error',
									title: 'Error',
									text: response.message
								});
							}
						},
						error: function(xhr) {
							Swal.fire({
								icon: 'error',
								title: 'Error',
								text: 'Error deleting post'
							});
							console.log("AJAX Error:", xhr.responseText);
						}
					});
				} else {
					Swal.fire({
						icon: 'info',
						title: 'Canceled',
						text: 'Deletion canceled',
						showConfirmButton: false,
						timer: 2000
					});

				}
			});
		});
	});

	// SHARING OF ACTIVITY
	document.addEventListener('DOMContentLoaded', function() {
		const activityItems = document.querySelectorAll('.activity-item');
		const shareButtonDiv = document.getElementById('shareButtonDiv');
		const shareButton = document.getElementById('shareButton');
		let selectedActivity = null;

		// Handle activity selection
		activityItems.forEach(item => {
			item.addEventListener('click', function() {
				if (selectedActivity) {
					selectedActivity.classList.remove('selected');
				}
				selectedActivity = this;
				selectedActivity.classList.add('selected');
				shareButtonDiv.style.display = 'block';
			});
		});

		// Handle sharing when the button is clicked
		shareButton.addEventListener('click', function() {
			if (!selectedActivity) {
				// Show SweetAlert2 error message if no activity is selected
				Swal.fire({
					icon: 'error',
					title: 'Selection Required',
					text: 'Please select an activity first.'
				});
				return;
			}

			const activityId = selectedActivity.getAttribute('data-id');
			const activityTitle = selectedActivity.getAttribute('data-title');

			// Confirm the sharing action
			Swal.fire({
				title: 'Confirm Sharing',
				html: `Are you sure you want to share this activity: "<b>${activityTitle}</b>"?`,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes, share it!',
				cancelButtonText: 'Cancel',
				confirmButtonClass: 'btn btn-primary', // Primary button style
				cancelButtonClass: 'btn btn-danger', // Danger button style
				reverseButtons: true,
				customClass: {
					confirmButton: 'btn btn-primary', // Primary button style
					cancelButton: 'btn btn-danger', // Danger button style
				}
			}).then((result) => {
				if (result.isConfirmed) {
					// User confirmed, proceed with sharing
					fetch("<?= site_url('admin/community/share-activity') ?>", {
						method: "POST",
						headers: {
							"Content-Type": "application/json",
							"X-Requested-With": "XMLHttpRequest",
						},
						body: JSON.stringify({
							activity_id: activityId
						}),
					}).then(response => response.json()).then(data => {
						if (data.success) {
							// Show success message
							Swal.fire({
								icon: 'success',
								title: 'Shared!',
								html: `"<b>${activityTitle}</b>" has been shared successfully.`
							});

							// Reload the page after 1 second
							setTimeout(() => {
								location.reload();
							}, 1000);
						} else {
							// Show error message
							Swal.fire({
								icon: 'error',
								title: 'Error',
								text: "Failed to share the activity. Please try again."
							});
						}
					}).catch(error => {
						console.error("Error:", error);
						Swal.fire({
							icon: 'error',
							title: 'Error',
							text: "An error occurred. Please try again.",
							showConfirmButton: false,
							timer: 2000
						});
					});
				} else {
					// User canceled
					Swal.fire({
						icon: 'info',
						title: 'Canceled',
						text: "Sharing canceled.",
						showConfirmButton: false,
						timer: 2000
					});
				}
			});
		});
	});


	// DISPLAYING EXCERPT AND VIEWING OF POST
	$(document).on('click', '.view-more', function() {
		var container = $(this).closest('.card-body');
		// Hide preview and show full content
		container.find('.post-preview').hide();
		container.find('.full-content').removeClass('d-none');
	});
	$(document).on('click', '.view-less', function() {
		var container = $(this).closest('.card-body');
		// Hide full content and show preview
		container.find('.full-content').addClass('d-none');
		container.find('.post-preview').show();
	});


	$(document).ready(function() {
		// Handle Privacy Selection
		$(document).on('click', '.dropdown-menu .dropdown-item', function(e) {
			e.preventDefault();
			const selectedPrivacy = $(this).data('privacy');
			const privacyIcon = $('#privacy-icon');
			// Change icon based on selected privacy
			if (selectedPrivacy === 'Public') {
				privacyIcon.removeClass('fa-users').addClass('fa-globe-americas');
			} else if (selectedPrivacy === 'Private') {
				privacyIcon.removeClass('fa-globe-americas').addClass('fa-users');
			}
			// Store selection in hidden input
			$('#privacyStatus').val(selectedPrivacy);
		});
	});

	// IMAGE HANDLING - PREVIEW AND REMOVE
	$(document).ready(function() {
		// Trigger file input when button is clicked
		$("#imageBtn").click(function() {
			$("#imageInput").click();
		});
		// Show image preview
		$("#imageInput").change(function(event) {
			var file = event.target.files[0];
			if (file) {
				var reader = new FileReader();
				reader.onload = function(e) {
					$("#imagePreview").attr("src", e.target.result);
					$("#imagePreviewContainer").removeClass("d-none");
				};
				reader.readAsDataURL(file);
			}
		});
	});

	document.getElementById("imageInput").addEventListener("change", function() {
		var file = this.files[0];
		if (file) {
			var reader = new FileReader();
			reader.onload = function(e) {
				document.getElementById("imagePreview").src = e.target.result;
				document.getElementById("imageName").textContent = file.name;
				document.getElementById("imageSize").textContent = (file.size / (1024 * 1024)).toFixed(2) + " MB";
				document.getElementById("imagePreviewContainer").classList.remove("d-none");
			};
			reader.readAsDataURL(file);
		}
	});
	// Remove Image Functionality
	document.querySelector(".remove-image").addEventListener("click", function(e) {
		e.preventDefault();
		document.getElementById("imagePreviewContainer").classList.add("d-none");
		document.getElementById("imageInput").value = ""; // Clear file input
	});
</script>


<style>
	/* Highlight selected activity */
	.activity-item.selected {
		background-color: #f0f0f0;
		/* Change background to show selection */
		border: 1px solid #007bff;
		/* Add a blue border for visual feedback */
	}

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