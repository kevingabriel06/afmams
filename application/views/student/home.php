<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="row g-3">
  <div class="col-lg-8">
      <!-- ito ang template kapag may post with pic -->
      <?php foreach($posts as $post): ?>
        <div class="card mb-3">
            <div class="card-header bg-body-tertiary">
                <div class="row justify-content-between">
                    <div class="col">
                        <div class="d-flex">
                            <div class="avatar avatar-2xl status-online">
                                <!-- Display the profile picture based on the post -->
                                <img class="rounded-circle" src="<?php echo base_url('assets/profile/') . (!empty($post->profile_pic) ? $post->profile_pic : 'default-pic.jpg'); ?>" />
                            </div>
                            <div class="flex-1 align-self-center ms-2">
                                <p class="mb-1 lh-1">
                                    <!-- Display the post creator's name -->
                                    <a class="fw-semi-bold" href="#"><?php echo htmlspecialchars($post->name); ?></a>
                                </p>
                                <p class="mb-0 fs-10">
                                    <?php
                                    // Convert the created_at timestamp to a DateTime object
                                    $date = new DateTime($post->created_at);
                                    // Format the date to the desired format: "January 25, 2024 • 8:00 am"
                                    echo $date->format('F j, Y') . ' at ' . $date->format('g:i a');
                                    ?>
                                    &bull;
                                    <?php if ($post->privacy == 'Public'): ?>
                                        <span class="fas fa-globe-americas"></span>
                                    <?php else: ?>
                                        <span class="fas fa-users"></span>
                                    <?php endif; ?>
                                    &bull;
                                    <?php echo htmlspecialchars($org_name); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="dropdown font-sans-serif btn-reveal-trigger">
                            <button class="btn btn-link text-600 btn-sm dropdown-toggle dropdown-caret-none btn-reveal" type="button" id="post-album-action" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="fas fa-ellipsis-h fs-10"></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end py-3" aria-labelledby="post-album-action">
                                <a class="dropdown-item" href="#!">View</a>
                                <a class="dropdown-item" href="#!">Edit</a>
                                <a class="dropdown-item" href="#!">Report</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-warning" href="#!">Archive</a>
                                <a class="dropdown-item text-danger" href="#!">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body overflow-hidden">
                <p><?php echo htmlspecialchars($post->content); ?></p>
            </div>
            <?php if($post->media): ?>
            <div class="card-body overflow-hidden"><a href="../../assets/img/generic/11.jpg" data-gallery="gallery-2">
              <img class="img-fluid rounded" src="<?php echo base_url('assets/post/'). $post->media ; ?>" alt="" /></a>
            </div>
            <?php endif; ?>
            <div class="card-footer bg-body-tertiary pt-0">
                <div class="border-bottom border-200 fs-10 py-3">
                    <span class="like-count" id="like-count-<?= $post->post_id; ?>"><?php echo $post->like_count; ?> Likes</span> &bull; 
                    <a class="text-700 comment-counter" id="comment-counter-<?= $post->post_id; ?>" href="#!">
                        <?= htmlspecialchars($post->comments_count); ?> Comments
                    </a>
                </div>
                <div class="row g-0 fw-semi-bold text-center py-2 fs-10">
                  <div class="col-auto">
                      <?php if ($post->user_has_liked_post): ?>
                        <button class="rounded-2 d-flex align-items-center me-3" id="btn-like" data-post-id="<?= $post->post_id; ?>" style="background: transparent; border: none; padding: 0;">
                          <img src="<?= base_url(); ?>assets/img/icons/spot-illustrations/like-active.png" width="20" alt="Like Icon" />
                          <span class="ms-1">Liked</span>
                        </button>
                      <?php else: ?>
                        <button class="rounded-2 d-flex align-items-center me-3" id="btn-like" data-post-id="<?= $post->post_id; ?>" style="background: transparent; border: none; padding: 0;">
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
                <form id="commentForm-<?= $post->post_id; ?>" class="d-flex align-items-center border-top border-200 pt-3">
                  <div class="avatar avatar-xl">
                    <img class="rounded-circle" src="<?= base_url('assets/profile/') . ($authors ? $authors->profile_pic : 'default-pic.jpg'); ?>" alt="" />
                  </div>
                  <input class="form-control rounded-pill ms-2 fs-10" type="text" name="comment" placeholder="Write a comment..." required />
                  <input type="hidden" name="post_id" value="<?= $post->post_id; ?>" />
                  <button type="submit" class="btn btn-link ms-auto">
                    <i class="fas fa-paper-plane"></i>
                  </button>
                </form>
                <!-- Display comments -->
                <?php if (!empty($post->comments)): ?>
                    <?php foreach ($post->comments as $comment): ?>
                        <div class="d-flex mt-3">
                            <div class="avatar avatar-xl">
                                <img class="rounded-circle" src="<?= base_url('assets/profile/') . ($comment->profile_pic ?: 'default-pic.jpg'); ?>" alt="Profile Picture" />
                            </div>
                            <div class="flex-1 ms-2 fs-10">
                                <p class="mb-1 bg-200 rounded-3 p-2">
                                    <a class="fw-semi-bold" href="#!"><?= htmlspecialchars($comment->name); ?></a>
                                    <?= htmlspecialchars($comment->content); ?>
                                </p>
                                <div class="px-2">
                                  <a href="#!" class="flex items-center">
                                    <i class="fas fa-reply mr-2"></i> Reply
                                  </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                  <div class="mt-3">
                    <p class="text-muted fs-20">No comments yet. Be the first to comment!</p>
                  </div>
                <?php endif; ?>
                <a class="fs-10 text-700 d-inline-block mt-2" href="#!">
                    Load more comments (<?= count($post->comments); ?> of <?= $post->comments_count; ?>)
                </a>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- ito ang template kapag nagshare ng event -->
    <?php foreach ($posted_activity as $activity): ?>
      <div class="card mb-3">
        <img id="coverPhoto" class="card-img-top" src="<?php echo base_url('assets/coverEvent/') . $activity->activity_image; ?>" alt="Event Cover" />
        <div class="card-body overflow-hidden">
          <div class="row justify-content-between align-items-center">
            <div class="col">
              <div class="d-flex">
                <!-- Calendar Section -->
                <div class="calendar me-2">
                  <?php
                  // Format the start date to display month, day, and year
                  $start_date = strtotime($activity->start_date);
                  $month = date('M', $start_date); // Abbreviated month (e.g., Mar)
                  $day = date('j', $start_date);   // Day of the month (e.g., 26)
                  $year = date('Y', $start_date);  // Full year (e.g., 2025)
                  ?>
                  <span class="calendar-month"><?php echo $month; ?></span>
                  <span class="calendar-day"><?php echo $day; ?></span>
                  <span class="calendar-year" hidden><?php echo $year; ?></span>
                </div>

                <!-- Event Details -->
                <div class="flex-1 position-relative ps-3">
                  <p class="mb-1" hidden><?php echo $activity->activity_id; ?></p>
                  <h6 class="fs-9 mb-0">
                    <a href="<?php echo site_url('admin/activity-details/' . $activity->activity_id); ?>">
                      <?php echo $activity->activity_title; ?>
                    </a>
                  </h6>
                  <p class="mb-1">
                    Organized by <?php echo $activity->org_name; ?>
                  </p>
                  <span class="fs-9 text-warning fw-semi-bold">
                        <?php echo ($activity->registration_fee > 0) ? 'Php ' . $activity->registration_fee : 'Free Event'; ?>
                      </span>
                </div>
              </div>
            </div>

            <!-- Action Button -->
            <div class="col-md-auto d-none d-md-block">
              <?php if ($activity->registration_fee == '0'): ?>
                <button class="btn btn-falcon-default btn-sm px-4" type="button">Attend</button>
              <?php else: ?>
                <button class="btn btn-falcon-default btn-sm px-4" type="button">Register</button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>


  <div class="col-lg-4">
    <!-- flashing of Event -->
    <div class="card mb-3 mb-lg-0">
      <div class="card-header bg-body-tertiary">
        <h5 class="mb-0">Upcoming Activities</h5>
      </div>
      <div class="card-body fs-10">
        <?php foreach ($activities as $activity): ?>
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
              <p class="mb-1">Organized by <a href="#!" class="text-700"><?php echo $activity->org_name ?></a></p>
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
        <?php endforeach ;?>
      </div>
      <div class="card-footer bg-body-tertiary p-0 border-top"><a class="btn btn-link d-block w-100" href="<?php echo site_url('list-of-activity');?>">All Events<span class="fas fa-chevron-right ms-1 fs-11"></span></a></div>
    </div>
  </div>
</div>

<!-- =========LIKES======== -->

<script>
$(document).ready(function() {
    // Event listener for the Like button
    $('[id^=btn-like]').click(function() {
        var post_id = $(this).data('post-id');  // Get the post ID
        var button = $(this);  // Reference to the button

        // Send AJAX request to like/unlike the post
        $.ajax({
            url: '<?= site_url("admin/community/like-post/") ?>' + post_id,  // Send to controller's like_post method
            type: 'POST',
            success: function(response) {
                var data = JSON.parse(response);  // Parse the response data

                // Update the button's icon and text based on the response
                button.find('img').attr('src', data.like_img);  // Change image (active/inactive)
                button.find('span').text(data.like_text);  // Change text (Liked/Like)

                // Update the like count dynamically
                $('#like-count-' + post_id).text(data.new_like_count + ' Likes');  // Update like count text

            },
            error: function() {
                alert('Something went wrong, please try again later.');
            }
        });
    });
});
$(document).on('submit', 'form[id^="commentForm"]', function (e) {
    e.preventDefault();

    var form = $(this);
    var formData = form.serialize();
    var postId = form.find('input[name="post_id"]').val(); // Get the post ID

    $.ajax({
        url: '<?= site_url("admin/community/add-comment"); ?>', // Adjust your URL
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                // Update the comment counter dynamically
                $('#comment-counter-' + postId).text(response.comments_count + ' Comments');

                // Optionally clear the input field
                form.find('input[name="comment"]').val('');

                alert('Comment added successfully!');
            } else {
                alert('Error: ' + response.errors);
            }
        },
        error: function () {
            alert('Something went wrong. Please try again.');
        },
    });
});

    $(document).ready(function(){
        $('#postForm').on('submit', function(e){
        e.preventDefault();

        var formData = new FormData(this);
        $.ajax({
            url: '<?php echo site_url("admin/community/add-post"); ?>',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response){
                if (response.status == 'error'){
                $('#messages').html('<div class="alert alert-danger">' + response.errors+ '</div>');}
                else if (response.status == 'success'){
                    $('#messages').html('<div class="alert alert-success">' + response.message+ '</div>');
                    setTimeout(function(){
                        window.location.href = response.redirect;
                        },1000);}
                }
            });
        });
    });

</script>

<!-- Modal for displaying activity list -->
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
          <?php foreach ($activities_admin as $activity): ?>
            <div class="col-md-6 mb-4">
              <!-- Activity Item -->
              <div class="d-flex btn-reveal-trigger activity-item p-3 border rounded shadow-sm hover-shadow" data-id="<?php echo $activity->activity_id; ?>" data-title="<?php echo $activity->activity_title; ?>" data-org="<?php echo $activity->org_name; ?>" data-start="<?php echo $activity->start_date; ?>" data-end="<?php echo $activity->end_date; ?>" data-fee="<?php echo $activity->registration_fee; ?>" data-am-in="<?php echo $activity->am_in; ?>" data-pm-in="<?php echo $activity->pm_in; ?>">
                
                <!-- Calendar Info -->
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

                <!-- Activity Details -->
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
                  <p class="text-1000 mb-0">Date: 
                    <?php
                      // Format the start and end dates to Month Day, Year
                      $start_date = date('M j, Y', strtotime($activity->start_date));
                      echo $start_date ;
                    ?>
                  </p>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Share Button (Initially hidden) -->
        <div class="mt-4 text-center" id="shareButtonDiv" style="display:none;">
          <button class="btn btn-primary px-4 py-2" id="shareButton">Share Selected Activity</button>
        </div>
      </div>
    </div>
  </div>
</div>



<script>
  // JavaScript to handle activity selection and sharing
  document.addEventListener('DOMContentLoaded', function () {
    const activityItems = document.querySelectorAll('.activity-item');
    const shareButtonDiv = document.getElementById('shareButtonDiv');
    const shareButton = document.getElementById('shareButton');
    let selectedActivity = null;

    // Handle activity selection
    activityItems.forEach(item => {
      item.addEventListener('click', function () {
        // Deselect previously selected activity
        if (selectedActivity) {
          selectedActivity.classList.remove('selected');
        }

        // Select the current activity
        selectedActivity = this;
        selectedActivity.classList.add('selected');

        // Show the share button
        shareButtonDiv.style.display = 'block';
      });
    });

    // Handle sharing when the button is clicked
    shareButton.addEventListener('click', function () {
      if (!selectedActivity) {
        alert('Please select an activity first.');
        return;
      }

      const activityId = selectedActivity.getAttribute('data-id');
      const activityTitle = selectedActivity.getAttribute('data-title');

      if (confirm(`Are you sure you want to share this activity: "${activityTitle}"?`)) {
        // Send AJAX request to update `is_shared`
        fetch("<?= site_url('admin/community/share-activity') ?>", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest", // For detecting AJAX request in CodeIgniter
          },
          body: JSON.stringify({ activity_id: activityId }),
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert("Activity successfully shared!");
              location.reload(); // Refresh the page
            } else {
              alert("Failed to share the activity. Please try again.");
            }
          })
          .catch(error => {
            console.error("Error:", error);
            alert("An error occurred. Please try again.");
          });
      }
    });
  });
</script>


<style>
  /* Highlight selected activity */
.activity-item.selected {
    background-color: #f0f0f0;  /* Change background to show selection */
    border: 1px solid #007bff;  /* Add a blue border for visual feedback */
}

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