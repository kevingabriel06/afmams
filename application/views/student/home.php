<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<div class="row g-3">
    <!-- FEED -->
    <div class="col-lg-8">
        <div id="feed-container">
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
                                                <img class="rounded-circle" src="<?php echo base_url('assets/profile/') . (!empty($item->profile_pic) ? $item->profile_pic : 'default-pic.jpg'); ?>" />
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
                                <a href="<?php echo site_url('student/activity-details/' . $activity->activity_id); ?>">
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

    <style>
        .sticky-sidebar {
            position: sticky;
            top: 8%;
            /* Adjust as needed */
            z-index: 1000;
        }
    </style>
</div>

<!-- Registration Modal -->
<div class="modal fade" id="registrationModal" tabindex="-1" aria-labelledby="registrationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registrationModalLabel">Register for Activity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="registrationForm" enctype="multipart/form-data">
                    <input type="hidden" id="modal_activity_id" name="activity_id">
                    <input type="hidden" id="modal_student_id" name="student_id">

                    <div class="mb-3">
                        <label for="reference_number" class="form-label">GCash Reference Number</label>
                        <input type="text" class="form-control" id="reference_number" name="reference_number" required>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount Paid</label>
                        <input type="number" class="form-control" id="amount" name="amount" required>
                    </div>

                    <div class="mb-3">
                        <label for="payment_type" class="form-label">Payment Type</label>
                        <select class="form-control" id="payment_type" name="payment_type" required>
                            <option value="" selected disabled>Select Payment Type</option>
                            <option value="cash">Cash</option>
                            <option value="ecash">E-Cash (e.g.,GCash/PayMaya)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="gcash_receipt" class="form-label">Upload Payment Screenshot</label>
                        <input type="file" class="form-control" id="gcash_receipt" name="gcash_receipt" accept="image/*" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Registration</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- script for registration start -->
<script>
    $(document).ready(function() {
        // Function to toggle required fields based on payment type
        function toggleRequiredFields() {
            var paymentType = $('#payment_type').val();

            if (paymentType === "cash") {
                $('#reference_number').prop('required', false).val(''); // Clear field
                $('#gcash_receipt').prop('required', false);
            } else {
                $('#reference_number').prop('required', true);
                $('#gcash_receipt').prop('required', true);
            }
        }

        // Run function when payment type is changed
        $('#payment_type').change(function() {
            toggleRequiredFields();
        });

        // Handle form submission
        $('#registrationForm').submit(function(e) {
            e.preventDefault();
            toggleRequiredFields(); // Ensure correct validation before submitting

            var formData = new FormData(this);

            $.ajax({
                url: "<?= base_url('student/register'); ?>",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {
                        alert(response.message);
                        $('#registrationModal').modal('hide');
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert("An error occurred while submitting the registration.");
                }
            });
        });

        // Pass data to modal when clicking "Register"
        $('.attend-button').click(function() {
            var activityId = $(this).data('activity-id');
            var studentId = $(this).data('student-id');

            $('#modal_activity_id').val(activityId);
            $('#modal_student_id').val(studentId);
        });

        // Ensure correct validation when modal is opened
        toggleRequiredFields();
    });
</script>

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
                url: '<?= site_url('student/home') ?>',
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
                url: '<?= site_url("student/home/like-post/") ?>' + postId, // Send to controller's like_post method
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
            url: '<?= site_url('student/view_likes/'); ?>' + postId,
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
            url: '<?= site_url("student/home/add-comment"); ?>',
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
</script>

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