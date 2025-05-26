<?php foreach ($feed as $item): ?>
    <?php if ($item->type === "post"): ?>
        <!-- THIS IS THE TEMPLATE FOR POSTING BOTH TEXT AND IMAGE -->
        <? //php foreach ($posts as $post): 
        ?>
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

                <script>
                    $(document).ready(function() {
                        $('#likesModal-<?= $item->post_id; ?>').modal({
                            show: false
                        }); // Optional if modal isn't triggered
                    });

                    // Function to show the modal and load the list of users who liked the post
                    function showLikesModal(postId) {
                        // Send an AJAX request to get the list of users who liked the post
                        $.ajax({
                            url: '<?= site_url('admin/view_likes/'); ?>' + postId, // URL of the controller method
                            method: 'GET',
                            success: function(response) {
                                // Assuming the response contains the HTML for the list of likes
                                // Populate the modal with the list of users who liked the post
                                $('#likesList-' + postId).html(response);
                                // Show the modal
                                $('#likesModal-' + postId).modal('show');
                            },
                            error: function() {
                                alert('Error fetching data. Please try again.');
                            }
                        });
                    }
                </script>


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
<?php endforeach; ?>