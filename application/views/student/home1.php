<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Alertify CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs/build/css/alertify.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs/build/css/themes/default.min.css" />
<!-- Alertify JS -->
<script src="https://cdn.jsdelivr.net/npm/alertifyjs/build/alertify.js"></script>


<div class="row g-3">
  <div class="col-lg-8">
    <!-- Static post with picture -->
    <?php foreach ($posts as $post): ?>
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
                    <a class="fw-semi-bold" href="#"><?php echo htmlspecialchars($post->first_name) . " " . ($post->last_name); ?></a>
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
                    <?php elseif ($post->privacy == 'Private'): ?>
                      <span class="fas fa-users"></span>
                    <?php endif; ?>
                    &bull;
                    <!-- <//?php if (empty($post->org_id) && empty($post->dept_id)): ?>
                      <//?php echo htmlspecialchars("Student Parliament"); ?>
                    <//?php elseif (empty($post->org_id)) : ?>
                      <//?php echo htmlspecialchars($post->dept_name); ?>
                    <//?php elseif (empty($post->dept_id)) : ?>
                      <//?php echo htmlspecialchars($post->org_name); ?>
                    <//?php endif; ?> -->
                  </p>
                </div>
              </div>
            </div>
            <div class="col-auto">
              <?php if ($post->student_id == $this->session->userdata('student_id')): ?>
                <div class="dropdown font-sans-serif btn-reveal-trigger">
                  <!-- Post Actions -->
                  <button class="btn btn-link text-600 btn-sm dropdown-toggle dropdown-caret-none btn-reveal" type="button" id="post-album-action" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="fas fa-ellipsis-h fs-10"></span>
                  </button>
                  <!-- Dropdown Menu -->
                  <div class="dropdown-menu dropdown-menu-end py-3" aria-labelledby="post-album-action">
                    <a class="dropdown-item text-danger" id="delete-post" data-post-id="<?php echo $post->post_id; ?>" data-url="<?php echo site_url('admin/community/delete-post'); ?>">Delete</a>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="card-body overflow-hidden">
          <?php
          $content = htmlspecialchars($post->content);
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
        <?php if ($post->media): ?>
          <div class="card-body overflow-hidden">
            <img class="img-fluid rounded" src="<?php echo base_url('assets/post/') . $post->media; ?>" alt="" />
          </div>
        <?php endif; ?>
        <div class="card-footer bg-body-tertiary pt-0">
          <div class="border-bottom border-200 fs-10 py-3">
            <span class="like-count" id="like-count-<?= $post->post_id; ?>"><?php echo $post->like_count; ?> Likes</span>
            &bull;
            <a class="text-700 comment-counter" id="comment-counter-<?= $post->post_id; ?>" href="#!">
              <?= htmlspecialchars($post->comments_count); ?> Comments
            </a>
          </div>
          <div class="row g-0 fw-semi-bold text-center py-2 fs-10">
            <div class="col-auto">
              <?php if ($post->user_has_liked_post): ?>
                <button class="btn-like rounded-2 d-flex align-items-center me-3" data-post-id="<?= $post->post_id; ?>" style="background: transparent; border: none; padding: 0;">

                  <img src="<?= base_url(); ?>assets/img/icons/spot-illustrations/like-active.png" width="20" alt="Like Icon" />
                  <span class="ms-1">Liked</span>
                </button>
              <?php else: ?>
                <button class="btn-like rounded-2 d-flex align-items-center me-3" data-post-id="<?= $post->post_id; ?>" style="background: transparent; border: none; padding: 0;">
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
          <!-- COMMENT SECTION -->
          <form id="commentForm-<?= $post->post_id; ?>" class="d-flex align-items-center border-top border-200 pt-3">
            <div class="avatar avatar-xl">
              <img class="rounded-circle" src="<?php $profile_pic = !empty($authors->profile_pic) ? base_url('assets/profile/') . $authors->profile_pic : base_url('assets/profile/default.jpg');
                                                echo $profile_pic; ?>" alt="" />
            </div>
            <input class="form-control rounded-pill ms-2 fs-10" type="text" name="comment" placeholder="Write a comment..." required />
            <input type="hidden" name="post_id" value="<?= $post->post_id; ?>" />
            <button type="submit" class="btn btn-link ms-auto">
              <i class="fas fa-paper-plane"></i>
            </button>
          </form>
          <!-- DISPLAYING COMMENTS -->
          <div id="comment-section-<?= $post->post_id; ?>">
            <?php if (!empty($post->comments)): ?>
              <?php $comment_count = 0; ?>
              <?php foreach ($post->comments as $comment): ?>
                <?php if ($comment->post_id == $post->post_id): ?>
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
          <?php if (count($post->comments) > 2): ?>
            <div class="mt-2">
              <a href="#!" class="fs-10 text-700 d-inline-block mt-2 view-more-comments" data-post-id="<?= $post->post_id; ?>"> View all comments </a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>


    <!-- ========= JAVASCRIPT FOR COMMENT AND LIKES ======== -->
    <script>
      $(document).ready(function() {
        // Event listener for the Like button
        $('[id^=btn-like]').click(function() {
          var post_id = $(this).data('post-id'); // Get the post ID
          var button = $(this); // Reference to the button
          // Send AJAX request to like/unlike the post
          $.ajax({
            url: '<?= site_url("student/like-post/") ?>' + post_id, // Send to controller's like_post method
            type: 'POST',
            success: function(response) {
              var data = JSON.parse(response); // Parse the response data
              // Update the button's icon and text based on the response
              button.find('img').attr('src', data.like_img); // Change image (active/inactive)
              button.find('span').text(data.like_text); // Change text (Liked/Like)
              // Update the like count dynamically
              $('#like-count-' + post_id).text(data.new_like_count + ' Likes'); // Update like count text
            },
            error: function() {
              alert('Something went wrong, please try again later.');
            }
          });
        });
      });


      // FOR COMMENT SECTION

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
              // Show success notification
              alertify.success('Comment added successfully!');
            } else {
              alertify.error('Error: ' + response.errors);
            }
          },
          error: function() {
            alertify.error('Something went wrong. Please try again.');
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
    </script>

    <!-- // DISPLAYING EXCERPT AND VIEWING OF POST -->
    <script>
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
    </script>



    <!-- Static event sharing -->
    <?php if (!empty($activities)): ?>
      <?php foreach ($activities as $activity): ?>
        <div class="card mb-3">
          <img src="<?= base_url('assets/coverEvent/' . $activity['activity_image']); ?>" alt="Activity Cover" />
          <div class="card-body overflow-hidden">
            <div class="row justify-content-between align-items-center">
              <div class="col">
                <div class="d-flex">
                  <div class="calendar me-2">
                    <span class="calendar-month"><?= date('M', strtotime($activity['start_date'])); ?></span>
                    <span class="calendar-day"><?= date('d', strtotime($activity['start_date'])); ?></span>
                  </div>
                  <div class="flex-1 position-relative ps-3">
                    <h6 class="fs-9 mb-0">
                      <a href="<?= base_url('student/activity-details/' . $activity['activity_id']); ?>">
                        <?= htmlspecialchars($activity['activity_title']); ?>
                      </a>
                    </h6>
                    <p class="mb-1">
                      Organized by
                      <?=
                      empty($activity['org_name']) && empty($activity['dept_name'])
                        ? "Student Parliament"
                        : (!empty($activity['org_name']) ? htmlspecialchars($activity['org_name']) : htmlspecialchars($activity['dept_name']))
                      ?>
                    </p>

                    <span class="fs-9 text-warning fw-semi-bold">
                      <?= $activity['registration_fee'] > 0 ? '₱' . number_format($activity['registration_fee'], 2) : 'Free Event'; ?>
                    </span>
                  </div>
                </div>
              </div>

              <div class="col-md-auto d-none d-md-block">
                <button class="btn btn-falcon-default btn-sm px-4 attend-button"
                  data-activity-id="<?= $activity['activity_id']; ?>"
                  data-student-id="<?= $student_id; ?>"
                  data-attendee-count="<?= $activity['attendee_count']; ?>"
                  <?= isset($activity['has_attended']) && $activity['has_attended'] ? '' : ''; ?>>

                  <?php
                  if (!empty($activity['registration_fee']) && $activity['registration_fee'] != 0) {
                    echo 'Register';  // If registration_fee exists and is not 0, show Register
                  } elseif (isset($activity['has_attended']) && $activity['has_attended']) {
                    echo 'View Activity';  // If already attended, show View Activity
                  } else {
                    echo 'Attend';  // Default state
                  }
                  ?>
                </button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No activities available.</p>
    <?php endif; ?>

  </div>

  <div class="col-lg-4">
    <!-- Upcoming Activities -->
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
                // if (is_null($activity['dept_id']) && is_null($activity['org_id'])) {
                //   echo 'Student Parliament';
                // } elseif (!is_null($activity['org_id'])) {
                //   echo $activity['org_name'];
                // } elseif (!is_null($activity['dept_id'])) {
                //   echo $activity['dept_name'];
                // }
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
        <a class="btn btn-link d-block w-100" href="<?php echo site_url('student/list-activity/' . $this->session->userdata('student_id')); ?>">All Events<span class="fas fa-chevron-right ms-1 fs-11"></span></a>
      </div>
    </div>

  </div>
</div>

<!-- =========LIKES======== -->

<script>
  $(document).ready(function() {
    // Event listener for the Like button
    $('.btn-like').click(function() {
      var post_id = $(this).data('post-id'); // Get the post ID
      var button = $(this); // Store reference to button

      // Send AJAX request to like/unlike the post
      $.ajax({
        url: '<?= site_url("student/like-post/") ?>' + post_id, // Send to controller
        type: 'POST',
        dataType: 'json', // Ensure response is JSON
        success: function(response) {
          if (response && response.like_img && response.like_text) {
            // Update the button icon and text based on response
            button.find('img').attr('src', response.like_img);
            button.find('span').text(response.like_text);
          } else {
            alert('Error: Unable to process your request.');
          }

          // Update the like count dynamically
          $('#like-count-' + post_id).text(response.new_like_count + ' Likes');
        },
        error: function(xhr, status, error) {
          console.error("AJAX Error:", status, error);
          console.error("Response:", xhr.responseText);
          alert('Something went wrong, please try again later.');
        }
      });
    });
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


<!-- SCRIPT FOR ATTEND ACTIVITY START -->

<script>
  $(document).ready(function() {
    $('.attend-button').click(function() {
      var $button = $(this);
      var activityId = $button.data('activity-id');
      var studentId = $button.data('student-id');

      if ($button.text().trim() === 'Attend') {
        $button.prop('disabled', true); // Disable button to prevent multiple clicks

        $.ajax({
          url: '<?= base_url('student/express-interest'); ?>',
          type: 'POST',
          data: {
            activity_id: activityId,
            student_id: studentId
          },
          dataType: 'json', // Ensure JSON response
          success: function(response) {
            console.log(response); // Debugging

            alertify.set('notifier', 'position', 'top-right'); // Set alert position

            if (response.status === 'success') {
              $button.text('View Activity'); // Change button text
              alertify.success('You have successfully marked attendance!');
            } else {
              alertify.error('An error occurred. Please try again.');
            }
          },
          error: function(xhr, status, error) {
            console.error(xhr.responseText); // Log full error response
            alertify.error('An error occurred. Please try again.');
          },
          complete: function() {
            $button.prop('disabled', false); // Re-enable button
          }
        });
      } else if ($button.text().trim() === 'View Activity') {
        var activityUrl = '<?= base_url('student/activity-details'); ?>/' + activityId;
        window.location.href = activityUrl;
      }
    });
  });
</script>





<!-- SCRIPT FOR ATTEND ACTIVITY END -->