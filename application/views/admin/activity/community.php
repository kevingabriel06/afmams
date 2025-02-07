<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

      <div class="row g-3">
        <div class="col-lg-8">
          <div class="card mb-3">
            <div class="card-header bg-body-tertiary overflow-hidden">
              <div class="d-flex align-items-center">
                <div class="avatar avatar-m">
                  <!-- PROFILE PIC BASED ON THE USER IN ADMIN AND OFFICER SIDE -->
                  <img class="rounded-circle" src="<?php 
                      $profile_pic = !empty($authors->profile_pic) ? base_url('assets/profile/') . $authors->profile_pic : base_url('assets/profile/default.jpg'); 
                      echo $profile_pic;
                    ?>" alt="" />
                </div>
                <div id="messages"></div>
                <div class="flex-1 ms-2">
                  <h5 class="mb-0 fs-9">Create post</h5>
                </div>
              </div>
            </div>
            <div class="card-body p-0">
            <form id="postForm" enctype="multipart/form-data">
              <!-- Textarea for content -->
              <textarea
                  class="shadow-none form-control rounded-0 resize-none px-x1 border-y-0 border-200"
                  placeholder="What do you want to talk about?"
                  rows="4"
                  name="content"
                  id="postContent"
              ></textarea>
              <div class="border-bottom border-dashed my-3"></div>

              <!-- Image Preview Section -->
              <div id="imagePreviewContainer" class="mb-3" style="display: none;">
                  <img id="imagePreview" src="" alt="Selected Image" style="max-width: 100px; height: 100px; border-radius: 8px; border: 1px solid #ccc;" />
              </div>

              <!-- Button section -->
              <div class="row g-0 justify-content-between mt-3 px-x1 pb-3">
                  <div class="col">
                      <!-- Image Button -->
                      <button
                          class="btn btn-tertiary btn-sm rounded-pill shadow-none d-inline-flex align-items-center fs-10 mb-0 me-1"
                          type="button" id="imageBtn">
                          <img class="cursor-pointer" src="<?php echo base_url();?>assets/img/icons/spot-illustrations/image.svg" width="17" alt="" />
                          <span class="ms-2 d-none d-md-inline-block">Image</span>
                      </button>
                      <!-- File Input for Image Upload (hidden initially) -->
                      <input
                          type="file"
                          name="image"
                          id="imageInput"
                          style="display: none;"
                      />
                      <!-- Activity Button -->
                      <button class="btn btn-tertiary btn-sm rounded-pill shadow-none d-inline-flex align-items-center fs-10 me-1" type="button" data-bs-toggle="modal" data-bs-target="#activityModal">
                          <img class="cursor-pointer" src="<?php echo base_url();?>assets/img/icons/spot-illustrations/calendar.svg" width="17" alt="" />
                          <span class="ms-2 d-none d-md-inline-block">Activity</span>
                      </button>
                  </div>
                  <div class="col-auto">
                      <!-- Privacy Dropdown -->
                      <div class="dropdown d-inline-block me-1">
                          <button
                              class="btn btn-sm dropdown-toggle px-1"
                              id="dropdownMenuButton"
                              type="button"
                              data-bs-toggle="dropdown"
                              aria-haspopup="true"
                              aria-expanded="false"
                          >
                              <span id="privacy-icon" class="fas fa-globe-americas"></span>
                          </button>
                          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                              <a class="dropdown-item" href="#" data-privacy="Public">Public</a>
                              <a class="dropdown-item" href="#" data-privacy="Private">Private</a>
                          </div>
                      </div>

                      <!-- Hidden Input to store selected privacy status -->
                      <input type="hidden" id="privacyStatus" name="privacyStatus" value="Public" />

                      <script>
                        $(document).ready(function () {
                            // Handle Privacy Selection
                            $(document).on('click', '.dropdown-menu .dropdown-item', function (e) {
                                e.preventDefault(); // Prevent default action (e.g., navigating to a link)

                                const selectedPrivacy = $(this).data('privacy');  // Get the selected privacy status
                                const privacyIcon = $('#privacy-icon');  // Get the privacy icon element
                                
                                // Change the icon based on the selected privacy
                                if (selectedPrivacy === 'Public') {
                                    privacyIcon.removeClass('fa-users').addClass('fa-globe-americas');  // Update icon for 'Public'
                                } else if (selectedPrivacy === 'Private') {
                                    privacyIcon.removeClass('fa-globe-americas').addClass('fa-users');  // Update icon for 'Private'
                                }

                                // Store the selected privacy status in the hidden input field
                                $('#privacyStatus').val(selectedPrivacy);  // Update the hidden input value
                            });
                        });
                      </script>
                      <!-- Submit Button -->
                      <button class="btn btn-primary btn-sm px-4 px-sm-5" type="submit">Share</button>
                  </div>
              </div>
          </form>
            </div>
          </div>
            <!-- script for this form post -->
            <script>
              document.addEventListener('DOMContentLoaded', function () {
                  const imageBtn = document.getElementById('imageBtn');
                  const imageInput = document.getElementById('imageInput');
                  const imagePreviewContainer = document.getElementById('imagePreviewContainer');
                  const imagePreview = document.getElementById('imagePreview');

                  // Trigger file input when the image button is clicked
                  imageBtn.addEventListener('click', function () {
                      imageInput.click();
                  });

                  // Display the selected image as a preview
                  imageInput.addEventListener('change', function (event) {
                      const file = event.target.files[0];

                      if (file) {
                          const reader = new FileReader();

                          reader.onload = function (e) {
                              imagePreview.src = e.target.result;
                              imagePreviewContainer.style.display = 'block'; // Show the preview container
                          };

                          reader.readAsDataURL(file); // Read the image file
                      } else {
                          imagePreview.src = '';
                          imagePreviewContainer.style.display = 'none'; // Hide the preview container
                      }
                  });
              });
            </script>

          <!-- THIS IS THE TEMPLATE FOR POSTING BOTH TEXT AND IMAGE -->
          <?php foreach($posts as $post): ?>
            <?php if ($role == "Admin"):?>
              <div class="card mb-3">
                  <div class="card-header bg-body-tertiary">
                      <div class="row justify-content-between">
                          <div class="col">
                              <div class="d-flex">
                                  <div class="avatar avatar-2xl status-online">
                                      <!-- Display the profile picture based on the post -->
                                      <img class="rounded-circle" src="<?php echo base_url('assets/profile/') . (!empty($post->profile_pic) ? $post->profile_pic : 'default.jpg'); ?>" />
                                  </div>
                                  <div class="flex-1 align-self-center ms-2">
                                      <p class="mb-1 lh-1">
                                          <!-- Display the post creator's name -->
                                          <a class="fw-semi-bold" href="#"><?php echo htmlspecialchars($post->first_name)." ".($post->last_name); ?></a>
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
                                          <?php if ($post->org_id == '0' && $post->dept_id == '0'): ?>
                                              <?php echo htmlspecialchars("Student Parliament"); ?>
                                          <?php elseif ($post->org_id == '0') : ?>
                                              <?php echo htmlspecialchars($post->dept_name); ?>
                                          <?php elseif ($post->dept_id == '0') : ?>
                                              <?php echo htmlspecialchars($post->org_name); ?>
                                          <?php endif; ?>
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
                      <img class="img-fluid rounded" src="<?php echo base_url('assets/post/') . $post->media ; ?>" alt="" /></a>
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
                              <img class="rounded-circle" src="<?php 
                                $profile_pic = !empty($authors->profile_pic) ? base_url('assets/profile/') . $authors->profile_pic : base_url('assets/profile/default.jpg'); 
                                echo $profile_pic;
                              ?>" alt="" />
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
                                      <img class="rounded-circle" src="<?= base_url('assets/profile/') . ($comment->profile_pic ?: 'default.jpg'); ?>" alt="Profile Picture" />
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
            <?php elseif ($role === "Officer"):?>
              <?php 
                  // CHECK IF THE USER IS BELONG IN THE ORGANIZATION
                  $belongs_to_org = false;
                  if (!empty($org)) {
                      foreach ($org as $o) {
                          if ($post->org_id == $o->org_id) {
                              $belongs_to_org = true;
                              break;
                          }
                      }
                  } 
                  // CHECK THE POST IF IT IS PUBLIC, FOR ORGANIZATION AND DEPARTMENT
                  if ($post->privacy == 'Public' || (isset($dept) && $post->dept_id == $dept->dept_id) || $belongs_to_org) :
              ?>
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
                                          <a class="fw-semi-bold" href="#"><?php echo htmlspecialchars($post->first_name)." ".($post->last_name); ?></a>
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
                                          <?php if ($post->org_id == '0' && $post->dept_id == '0'): ?>
                                              <?php echo htmlspecialchars("Student Parliament"); ?>
                                          <?php elseif ($post->org_id == '0') : ?>
                                              <?php echo htmlspecialchars($post->dept_name); ?>
                                          <?php elseif ($post->dept_id == '0') : ?>
                                              <?php echo htmlspecialchars($post->org_name); ?>
                                          <?php endif; ?>
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
                      <img class="img-fluid rounded" src="<?php echo base_url('assets/post/') . $post->media ; ?>" alt="" /></a>
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
                              <img class="rounded-circle" src="<?php 
                                $profile_pic = !empty($authors->profile_pic) ? base_url('assets/profile/') . $authors->profile_pic : base_url('assets/profile/default.jpg'); 
                                echo $profile_pic;
                              ?>" alt="" />
                          </div>
                          <input class="form-control rounded-pill ms-2 fs-10" type="text" name="comment" placeholder="Write a comment..." required />
                          <input type="hidden" name="post_id" value="<?= $post->post_id; ?>" />
                          <button type="submit" class="btn btn-link ms-auto">
                              <i class="fas fa-paper-plane"></i>
                          </button>
                      </form>
                      <!-- Display comments -->
                      <div id="comment-section-<?= $post->post_id; ?>">
                          <?php if (!empty($post->comments)): ?>
                              <?php foreach ($post->comments as $comment): ?>
                                  <div class="d-flex mt-3 comment-item">
                                      <div class="avatar avatar-xl">
                                          <img class="rounded-circle" src="<?= base_url('assets/profile/') . ($comment->profile_pic ?: 'default.jpg'); ?>" alt="Profile Picture" />
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
                              <div class="mt-3 no-comments">
                                  <p class="text-muted fs-20">No comments yet. Be the first to comment!</p>
                              </div>
                          <?php endif; ?>
                      </div>

                      <!-- Comment Counter & Load More -->
                      <div class="mt-2">
                          <a id="load-more-comments-<?= $post->post_id; ?>" class="fs-10 text-700 d-inline-block mt-2" href="#!">
                              Load more comments (<?= count($post->comments); ?> of <?= $post->comments_count; ?>)
                          </a>
                      </div>

                  </div>
                </div>
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; ?>


          <!-- THIS IS THE EVENT TEMPLATE -->
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
            <!-- FETCHING UPCOMING ACTIVITY RANDOMLY -->
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

          <!-- MODAL OF  ACTIVITY LIST -->
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
                  <?php foreach ($activities as $activity): ?>
              <?php 
                  $is_admin_viewable = (
                      $role == 'Admin' && 
                      $activity->dept_id == '0' && 
                      $activity->org_id == '0' && 
                      strtotime($activity->start_date) > time()
                  );

                  $is_officer_viewable = (
                      $role == 'Officer' && 
                      strtotime($activity->start_date) > time() &&
                      (
                          (isset($department->dept_id) && $activity->dept_id == $dept_id) || 
                          (isset($department->org_id) && $activity->org_id == $org_id)
                      )
                  );
              ?>

              <?php if ($is_admin_viewable || $is_officer_viewable): ?>
                  <div class="col-md-6 mb-4">
                      <!-- Activity Item -->
                      <div class="d-flex btn-reveal-trigger activity-item p-3 border rounded shadow-sm hover-shadow"
                          data-id="<?php echo $activity->activity_id; ?>" 
                          data-title="<?php echo $activity->activity_title; ?>" 
                          data-org="<?php echo $activity->org_name; ?>" 
                          data-start="<?php echo $activity->start_date; ?>" 
                          data-end="<?php echo $activity->end_date; ?>" 
                          data-fee="<?php echo $activity->registration_fee; ?>" 
                          data-am-in="<?php echo $activity->am_in; ?>" 
                          data-pm-in="<?php echo $activity->pm_in; ?>">

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
                                  <a href="<?php echo site_url('admin/activity-details/' . $activity->activity_id); ?>">
                                      <?php echo $activity->activity_title; ?> 
                                      <?php if ($activity->registration_fee == '0'): ?>
                                          <span class="badge badge-subtle-success rounded-pill">Free</span>
                                      <?php endif; ?>
                                  </a>
                              </h6>

                              <p class="text-1000 mb-0">Time: 
                                  <?php
                                      $start_time = !empty($activity->am_in) 
                                          ? date('h:i A', strtotime($activity->am_in)) 
                                          : (!empty($activity->pm_in) ? date('h:i A', strtotime($activity->pm_in)) : 'N/A');

                                      echo $start_time;
                                  ?>
                              </p>

                              <p class="text-1000 mb-0">Date: 
                                  <?php echo date('M j, Y', strtotime($activity->start_date)); ?>
                              </p>
                          </div>
                      </div>
                  </div>
              <?php endif; ?>
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

      // FOR COMMENT SECTION
      $(document).on('submit', 'form[id^="commentForm"]', function (e) {
          e.preventDefault();

          var form = $(this);
          var formData = form.serialize();
          var postId = form.find('input[name="post_id"]').val();
          var commentSection = $('#comment-section-' + postId); // Where comments are displayed
          var commentCounter = $('#comment-counter-' + postId); // Comment counter
          var loadMoreButton = $('#load-more-comments-' + postId); // Load More button

          $.ajax({
              url: '<?= site_url("admin/community/add-comment"); ?>', 
              type: 'POST',
              data: formData,
              dataType: 'json',
              success: function (response) {
                  if (response.status === 'success') {
                      // Update the comment counter
                      commentCounter.text(response.comments_count + ' Comments');
                      loadMoreButton.text(`Load more comments (${response.comments_count})`);

                      // Append the latest comment(s) to the comment section
                      commentSection.prepend(formatComment(response.new_comments));

                      // Clear input field
                      form.find('input[name="comment"]').val('');

                      // Show success notification
                      alertify.success('Comment added successfully!');
                  } else {
                      alertify.error('Error: ' + response.errors);
                  }
              },
              error: function () {
                  alertify.error('Something went wrong. Please try again.');
              },
          });
      });

      // FUNCTION FORMAT FOR NEW COMMENTS
      function formatComment(comments) {
          let html = '';
          comments.forEach(comment => {
              html += `
              <div class="d-flex mt-3">
                  <div class="avatar avatar-xl">
                      <img class="rounded-circle" src="<?= base_url('assets/profile/') ?>${comment.profile_pic || 'default.jpg'}" alt="Profile Picture" />
                  </div>
                  <div class="flex-1 ms-2 fs-10">
                      <p class="mb-1 bg-200 rounded-3 p-2">
                          <a class="fw-semi-bold" href="#!">${comment.name}</a> ${comment.content}
                      </p>
                      <div class="px-2">
                          <a href="#!" class="flex items-center">
                              <i class="fas fa-reply mr-2"></i> Reply
                          </a>
                      </div>
                  </div>
              </div>`;
          });
          return html;
      }

      // FUNCTION FOR INSERTING POST
      $(document).ready(function () {
        $('#postForm').on('submit', function (e) {
            e.preventDefault();

            var formData = new FormData(this);

            alertify.confirm(
                'Confirm Sharing',
                'Are you sure you want to post this?',
                function () { // This function executes if the user clicks "OK"
                    $.ajax({
                        url: '<?php echo site_url("admin/community/add-post"); ?>',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function (response) {
                            if (response.status === 'error') {
                                alertify.error(response.errors); // Show error message
                            } else if (response.status === 'success') {
                                alertify.success(response.message); // Show success message

                                setTimeout(function () {
                                    window.location.href = response.redirect; // Redirect after 1 second
                                }, 1000);
                            }
                        }
                    });
                },
                function () { // This function executes if the user clicks "Cancel"
                    alertify.error('Post cancelled.');
                }
            );
        });
    });

</script>

<!-- SHARING OF ACTIVITY WITH ALERT -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const activityItems = document.querySelectorAll('.activity-item');
    const shareButtonDiv = document.getElementById('shareButtonDiv');
    const shareButton = document.getElementById('shareButton');
    let selectedActivity = null;

    // Align Alertify toast notifications to the right
    alertify.set('notifier', 'position', 'top-right');

    // Handle activity selection
    activityItems.forEach(item => {
      item.addEventListener('click', function () {
        if (selectedActivity) {
          selectedActivity.classList.remove('selected');
        }

        selectedActivity = this;
        selectedActivity.classList.add('selected');
        shareButtonDiv.style.display = 'block';
      });
    });

    // Handle sharing when the button is clicked
    shareButton.addEventListener('click', function () {
      if (!selectedActivity) {
        alertify.alert('Selection Required', 'Please select an activity first.');
        return;
      }

      const activityId = selectedActivity.getAttribute('data-id');
      const activityTitle = selectedActivity.getAttribute('data-title');

      alertify.confirm(
        'Confirm Sharing',
        `Are you sure you want to share this activity: "<b>${activityTitle}</b>"?`,
        function () {
          // User confirmed, proceed with sharing
          fetch("<?= site_url('admin/community/share-activity') ?>", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify({ activity_id: activityId }),
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alertify.success(`Activity "<b>${activityTitle}</b>" has been shared successfully.`);
              
              // Reload the page after 1.5 seconds
              setTimeout(() => {
                location.reload();
              }, 1000);
            } else {
              alertify.error("Failed to share the activity. Please try again.");
            }
          })
          .catch(error => {
            console.error("Error:", error);
            alertify.error("An error occurred. Please try again.");
          });
        },
        function () {
          // User canceled
          alertify.error("Sharing canceled.");
        }
      );
    });
  });
</script>

<!-- Include Alertify.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs/build/css/alertify.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs/build/css/themes/default.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs/build/alertify.min.js"></script>




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