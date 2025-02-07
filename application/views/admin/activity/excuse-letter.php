<!-- AlertifyJS CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
<!-- AlertifyJS Theme CSS (Optional, for better styling) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css"/>

<!-- AlertifyJS JS -->
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

    <div class="row g-3">
            <div class="col-xxl-9 col-xl-8">
              <div class="card">
                <div class="card-header d-flex flex-between-center"><a class="btn btn-falcon-default btn-sm" href="<?php echo site_url('admin/list-of-excuse-letter/'.$excuse['activity_id']); ?>">
                    <span class="fas fa-arrow-left"></span>
                  </a>
                  <div class="d-flex">
                    <?php if ($excuse['status'] == 'Pending'): ?> 
                    <!-- Approved Button -->
                    <button class="btn btn-falcon-success btn-sm mx-2" type="button" onclick="showApprovalModal('Approved')">
                      <span class="fas fa-check" data-fa-transform="shrink-2 down-2"></span><span class="d-none d-md-inline-block ms-1">Approved</span>
                    </button>

                    <!-- Disapproved Button -->
                    <button class="btn btn-falcon-danger btn-sm" type="button" onclick="showApprovalModal('Disapproved')">
                      <span class="fas fa-ban" data-fa-transform="shrink-2 down-1"></span><span class="d-none d-md-inline-block ms-1">Disapproved</span>
                    </button`>
                    <?php endif ;?>
                  </div>
                </div>
              </div>

              <!-- Modal for Approval -->
              <div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="approvalModalLabel">Approval Status</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <form id="approvalForm">
                        <!-- Hidden field for excuse_id, dynamically populated -->
                        <input type="hidden" id="excuse_id" name="excuse_id" value="<?php echo $excuse['excuse_id']; ?>">

                        <!-- Remarks textarea -->
                        <div class="mb-3">
                          <label for="remarks" class="form-label">Remarks</label>
                          <textarea class="form-control" id="remarks" rows="3" placeholder="Enter remarks..."></textarea>
                        </div>

                        <!-- Hidden field for approval status -->
                        <input type="hidden" id="approvalStatus" name="approvalStatus">

                        <!-- Submit button -->
                        <button type="submit" class="btn btn-primary">Send</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>

              <script>
                function showApprovalModal(status) {
                  document.getElementById('approvalStatus').value = status; // Set the approval status (approved or disapproved)
                  var modal = new bootstrap.Modal(document.getElementById('approvalModal'));
                  modal.show();
                }

                // Handle form submission
                document.getElementById('approvalForm').addEventListener('submit', function(event) {
                    event.preventDefault(); // Prevent the default form submission

                    // Get the values from the form
                    var remarks = document.getElementById('remarks').value;
                    var approvalStatus = document.getElementById('approvalStatus').value; // Set this value dynamically based on button clicked
                    var excuseId = document.getElementById('excuse_id').value;

                    // Align Alertify toast notifications to the right
                    alertify.set('notifier', 'position', 'top-right');

                    // Validate that the remarks are not empty
                    if (remarks.trim() === "") {
                        alertify.error('Remarks cannot be empty.');
                        return;
                    }

                    // Prepare the data to send
                    var formData = new FormData();
                    formData.append('remarks', remarks);
                    formData.append('approvalStatus', approvalStatus);
                    formData.append('excuse_id', excuseId);

                    // Send the data via AJAX to the backend
                    fetch('<?php echo site_url('admin/review-excuse-letter/update'); ?>', {  // Replace with your correct endpoint URL
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alertify.success('Approval status updated successfully');
                            // Close the modal after submission
                            var modal = bootstrap.Modal.getInstance(document.getElementById('approvalModal'));
                            modal.hide();
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000); // Reloads the page after 1 second (1000 milliseconds)

                        } else {
                            alertify.error('Failed to update approval status: ' + data.message);
                        }
                    })
                });
              </script>

              <div class="card mt-3">
                <div class="card-header bg-body-tertiary">
                  <h5><span class="fas fa-envelope me-2"></span><span><?php echo $excuse['subject'] ;?></span></h5>
                </div>
                <div class="card-body">
                  <div class="d-md-flex d-xl-inline-block d-xxl-flex align-items-center justify-content-between mb-x1">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-2xl">
                          <img class="rounded-circle" src="<?php echo base_url('assets/profile/' . (!empty($excuse['profile-pic']) ? $excuse['profile-pic'] : 'default.jpg')); ?>" alt="" />
                        </div>
                      <!-- details ng name at date -->
                      <p class="mb-0"><a class="fw-semi-bold mb-0 text-800"><?php echo $excuse['first_name']." ".$excuse['last_name']; ?></a>
                        <span class="mb-0 fs-10 d-block text-500 fw-semi-bold">
                          <?php echo date('d F, Y', strtotime($excuse['created_at'])); ?>
                          <span class="mx-1">|</span>
                          <span class="fst-italic">
                              <?php echo date('h:i A', strtotime($excuse['created_at'])); ?>
                          </span>
                        </span>
                      </p>
                    </div>
                  </div>
                  <div>
                    <!-- dito ang body ng message -->
                    <p><?php echo $excuse['content']; ?></p>

                    
                    <!-- Modal for Viewing Image -->
                      <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <h5 class="modal-title" id="imageModalLabel">View Attachment</h5>
                                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body text-center">
                                      <img id="modalImage" src="" class="img-fluid rounded" alt="Attached Image">
                                  </div>
                              </div>
                          </div>
                      </div>

                      <!-- File Attachment Section -->
                      <div class="d-inline-flex flex-column">
                          <?php 
                          // Allowed image file extensions
                          $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                          
                          // Get file extension
                          $file_extension = pathinfo($excuse['document'], PATHINFO_EXTENSION);
                          
                          // Check if document exists and is a valid image
                          if (!empty($excuse['document']) && in_array(strtolower($file_extension), $allowed_extensions)): 
                          ?>
                              <div class="border p-2 rounded-3 d-flex bg-white dark__bg-1000 fs-10 mb-2">
                                  <span class="fs-8 far fa-image"></span>
                                  <!-- Clickable text to open modal -->
                                  <span class="ms-2 me-3 text-decoration-none text-dark fw-bold"
                                        style="cursor: pointer;" 
                                        onclick="showImageModal('<?php echo base_url('assets/excuseFiles/' . $excuse['document']); ?>')">
                                      <?php echo $excuse['document']; ?>
                                  </span>
                                  <!-- Download button -->
                                  <a class="text-300 ms-auto" href="<?php echo base_url('assets/excuseFiles/' . $excuse['document']); ?>" 
                                    download="<?php echo $excuse['document']; ?>" 
                                    data-bs-toggle="tooltip" data-bs-placement="right" title="Download">
                                      <span class="fas fa-arrow-down"></span>
                                  </a>
                              </div>
                          <?php else: ?>
                              <p class="text-danger">No valid image attached.</p>
                          <?php endif; ?>
                      </div>

                      <!-- JavaScript to Show Modal -->
                      <script>
                      function showImageModal(imageSrc) {
                          document.getElementById('modalImage').src = imageSrc;
                          var imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
                          imageModal.show();
                      }
                      </script>
                  </div>
                </div>
              </div>
            </div>

            
            <div class="col-xxl-3 col-xl-4">
              <!-- Contact Details -->
              <div class="row g-3 g-xl-0 sticky-sidebar top-navbar-height mb-4"> <!-- Added mb-4 to create space below -->
                <div class="col-12">
                  <div class="card">
                    <div class="card-header d-flex flex-between-center py-2 bg-body-tertiary">
                      <h6 class="mb-0 fs-8">Contact Information</h6>
                    </div>
                    <div class="card-body">
                      <div class="row g-0 border-bottom pb-x1 mb-x1 align-items-sm-center align-items-xl-start">
                        <div class="col-12 col-sm-auto col-xl-12 me-sm-3 me-xl-0">
                          <div class="avatar avatar-3xl">
                            <img class="rounded-circle" src="<?php echo base_url('assets/profile/' . (!empty($excuse['profile-pic']) ? $excuse['profile-pic'] : 'default.jpg')); ?>" alt="Profile Picture" />
                          </div>
                        </div>
                        <div class="col-12 col-sm-auto col-xl-12">
                          <p class="fw-semi-bold text-800 mb-0"><?php echo $excuse['first_name']." ".$excuse['last_name'] ;?></p>
                        </div>
                      </div>
                      <div class="row g-0 justify-content-lg-between">
                        <div class="col-auto col-md-6 col-lg-auto">
                          <div class="row">
                            <div class="col-md-auto mb-4 mb-md-0 mb-xl-4">
                              <h6 class="mb-1">Email</h6><a class="fs-10" href="mailto:<?php echo $excuse['email'] ;?>"><?php echo $excuse['email'] ;?></a>
                            </div>
                            <div class="col-md-auto mb-4 mb-md-0 mb-xl-4">
                              <h6 class="mb-1">Department</h6><p class="fs-10"><?php echo $excuse['dept_name'] ;?></p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Remarks -->
              <div class="row g-3 g-xl-0 sticky-sidebar top-navbar-height mb-4"> <!-- Added mb-4 to create space below -->
                <div class="col-12">
                  <div class="card">
                    <div class="card-header d-flex flex-between-center py-2 bg-body-tertiary">
                      <h6 class="mb-0 fs-8">Remarks</h6>
                    </div>
                    <div class="card-body">
                      <div class="row g-0 justify-content-lg-between">
                        <div class="col-auto col-md-6 col-lg-auto">
                          <div class="row">
                              <h6 class="mb-1">Status: 
                                  <?php 
                                  // Dynamically set badge color based on status
                                  if ($excuse['status'] == 'Approved') {
                                      echo '<span class="badge bg-success">Approved</span>';
                                  } elseif ($excuse['status'] == 'Pending') {
                                      echo '<span class="badge bg-warning">Pending</span>';
                                  } elseif ($excuse['status'] == 'Disapproved') {
                                      echo '<span class="badge bg-danger">Disapproved</span>';
                                  } 
                                  ?>
                              </h6>
                              <p class="mb-1 fs-10"><b>Remarks: </b><?php echo $excuse['remarks']; ?></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>



        </div>