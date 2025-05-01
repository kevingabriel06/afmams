<script>
  $(document).ready(function() {
    // Attach the click event handler to the button using its id
    $('#share').click(function() {
      var activityId = <?php echo htmlspecialchars($activity['activity_id']); ?>;
      confirmShare(activityId);
    });
  });

  function confirmShare(activityId) {
    Swal.fire({
      title: 'Confirm Share',
      text: 'Are you sure you want to share this activity?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, share it!',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        // AJAX request to share the activity
        $.ajax({
          url: '<?php echo site_url('officer/activity-details/activity-share'); ?>', // Ensure PHP outputs the correct URL
          type: 'POST',
          contentType: 'application/json', // Ensure JSON format is sent
          data: JSON.stringify({
            activity_id: activityId
          }), // Send JSON data
          dataType: 'json', // Expect JSON response
          success: function(response) {
            if (response.success) {
              Swal.fire({
                icon: 'success',
                title: 'Shared!',
                text: 'Activity shared successfully!',
                timer: 2000,
                showConfirmButton: false
              });

              // Redirect after success alert
              setTimeout(function() {
                window.location.href = "<?= site_url('officer/community'); ?>";
              }, 2000); // Redirect after 2 seconds
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: response.message || 'Failed to share the activity.'
              });
            }
          },
          error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'An error occurred. Please try again.'
            });
          }
        });
      } else {
        Swal.fire({
          icon: 'info',
          title: 'Cancelled',
          text: 'The activity was not shared.',
          timer: 2000,
          showConfirmButton: false
        });
      }
    });
  }
</script>


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
    src="<?php echo base_url('assets/coverEvent/') . (!empty($activity['activity_image']) ? htmlspecialchars($activity['activity_image']) : 'default.jpg'); ?>"
    alt="Event Cover" />
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
        <?php if ($activity['registration_fee'] == '0') : ?>
          <button class="btn btn-falcon-default btn-sm me-2" type="button">
            <span class="fas fa-users text-danger me-1"></span>
            <?php echo $attendees_count; ?> Interested
          </button>
        <?php else : ?>
          <button class="btn btn-falcon-default btn-sm me-2" type="button" data-bs-toggle="modal" data-bs-target="#registeredModal">
            <span class="fas fa-users text-danger me-1"></span>
            <?php echo $verified_count; ?> Registered
          </button>
        <?php endif; ?>

        <!-- Registered Participants Modal -->
        <div class="modal fade" id="registeredModal" tabindex="-1" aria-labelledby="registeredModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="registeredModalLabel">Registered Participants</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <div class="modal-body">
                <div class="d-flex justify-content-end mb-3">
                  <button class="btn btn-primary" id="openRecordCashPaymentModal">
                    🧾 Record Cash Payment
                  </button>
                </div>

                <div class="card" id="registeredTable"
                  data-list='{"valueNames":["id", "name", "status"], "page": 11, "pagination": true, "fallback": "attendance-table-fallback"}'>

                  <div class="card-header border-bottom border-200 px-0">
                    <div class="d-lg-flex justify-content-between">
                      <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                          <!-- Optional Left Content -->
                        </div>
                      </div>

                      <div class="d-flex align-items-center justify-content-between justify-content-lg-end px-x1">
                        <div class="d-flex align-items-center" id="table-ticket-replace-element">
                          <div class="col-auto">
                            <form>
                              <div class="input-group input-search-width">
                                <input id="searchInput" class="form-control form-control-sm shadow-none search"
                                  type="search" placeholder="Search" aria-label="search" />
                                <button class="btn btn-sm btn-outline-secondary border-300 hover-border-secondary" type="button">
                                  <span class="fa fa-search fs-10"></span>
                                </button>
                              </div>
                            </form>
                          </div>
                          <button class="btn btn-sm btn-falcon-default ms-2" type="button">
                            <span class="fas fa-download"></span>
                          </button>
                          <button class="btn btn-sm btn-falcon-default ms-2" type="button" id="openFilterModal">
                            <span class="fas fa-filter"></span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="card-body p-0">
                    <div class="table-responsive scrollbar">
                      <table class="table table-hover table-striped overflow-hidden">
                        <thead class="bg-200">
                          <tr>
                            <th class="text-nowrap">Student ID</th>
                            <th class="text-nowrap">Name</th>
                            <th class="text-nowrap">Department</th>
                            <th class="text-nowrap">Amount</th>
                            <th class="text-nowrap">Reference Number</th>
                            <th class="text-nowrap">Status</th>
                            <th class="text-nowrap">Action</th>
                          </tr>
                        </thead>
                        <tbody class="list">
                          <?php foreach ($registrations as $registration) : ?>
                            <tr class="attendance-row">
                              <td class="text-nowrap id"><?php echo $registration->student_id; ?></td>
                              <td class="text-nowrap name"><?php echo $registration->first_name . " " . $registration->last_name; ?></td>
                              <td class="text-nowrap department"><?php echo $registration->dept_name; ?></td>
                              <td class="text-nowrap"><?php echo $registration->amount_paid; ?></td>
                              <td class="text-nowrap"><?php echo $registration->reference_number; ?></td>
                              <td class="status">
                                <?php if ($registration->registration_status == 'Verified'): ?>
                                  <span class="badge rounded-pill d-block p-2 bg-success text-white">
                                    <?php echo $registration->registration_status; ?>
                                    <span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span>
                                  </span>

                                <?php elseif ($registration->registration_status == 'Rejected'): ?>
                                  <span class="badge rounded-pill d-block p-2 bg-danger text-white">
                                    <?php echo $registration->registration_status; ?>
                                    <span class="ms-1 fas fa-times" data-fa-transform="shrink-2"></span>
                                  </span>

                                <?php elseif ($registration->registration_status == 'Pending'): ?>
                                  <span class="badge rounded-pill d-block p-2 bg-warning text-white">
                                    <?php echo $registration->registration_status; ?>
                                    <span class="ms-1 fas fa-clock" data-fa-transform="shrink-2"></span>
                                  </span>
                                <?php endif; ?>
                              </td>

                              <td class="text-nowrap">
                                <div class="dropdown font-sans-serif position-static">
                                  <button class="btn btn-link text-600 btn-sm dropdown-toggle btn-reveal" type="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="fas fa-ellipsis-h fs-10"></span>
                                  </button>
                                  <div class="dropdown-menu dropdown-menu-end border py-0">
                                    <div class="py-2">
                                      <?php if ($registration->registration_status == 'Verified') : ?>
                                        <a class="dropdown-item view-registration"
                                          href="#"
                                          data-bs-toggle="modal"
                                          data-bs-target="#viewRegistrationModal"
                                          data-student-name="<?php echo $registration->first_name . " " . $registration->last_name; ?>"
                                          data-department="<?php echo $registration->dept_name; ?>"
                                          data-reference-number="<?php echo $registration->reference_number; ?>"
                                          data-status="<?php echo $registration->registration_status; ?>"
                                          data-payment="<?php echo $registration->payment_type; ?>"
                                          data-remarks="<?php echo $registration->remark; ?>"
                                          data-receipt="<?php echo $registration->receipt; ?>">
                                          View Registration
                                        </a>
                                      <?php else: ?>
                                        <a class="dropdown-item text-danger validate-registration" href="#" data-student-id="<?php echo $registration->student_id; ?>" data-activity-id="<?php echo $registration->activity_id; ?>" data-bs-toggle="modal" data-bs-target="#validateModal">Validate Registration</a>
                                      <?php endif; ?>
                                    </div>
                                  </div>
                                </div>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>

                      <div class="text-center d-none" id="attendance-table-fallback">
                        <span class="fa fa-user-slash fa-2x text-muted"></span>
                        <p class="fw-bold fs-8 mt-3">No Student Found</p>
                      </div>
                    </div>
                  </div>

                  <div class="card-footer">
                    <div class="d-flex justify-content-center">
                      <button class="btn btn-sm btn-falcon-default me-1" type="button" title="Previous" data-list-pagination="prev">
                        <span class="fas fa-chevron-left"></span>
                      </button>
                      <ul class="pagination mb-0"></ul>
                      <button class="btn btn-sm btn-falcon-default ms-1" type="button" title="Next" data-list-pagination="next">
                        <span class="fas fa-chevron-right"></span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- VIEW REGISTRATION MODAL -->
        <div class="modal fade" id="viewRegistrationModal" tabindex="-1" aria-labelledby="viewRegistrationModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="viewRegistrationModalLabel">Registration Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <div class="modal-body">
                <p><strong>Student Name:</strong> <span id="modalStudentName"></span></p>
                <p><strong>Department:</strong> <span id="modalDepartment"></span></p>
                <p><strong>Reference Number:</strong> <span id="modalReferenceNumber"></span></p>
                <p><strong>Status:</strong> <span id="modalStatus" class="badge rounded-pill"></span></p>
                <p><strong>Remarks:</strong> <span id="modalRemarks"></span></p>

                <div class="mb-3">
                  <label class="form-label"><strong>Payment Receipt:</strong></label>
                  <div id="receiptContainer" class="border rounded p-2 text-center"></div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>

        <script>
          $(document).ready(function() {
            $('.view-registration').on('click', function() {
              const studentName = $(this).data('student-name');
              const department = $(this).data('department');
              const referenceNumber = $(this).data('reference-number');
              const remarks = $(this).data('remarks');
              const status = $(this).data('status');
              const paymentType = $(this).data('payment'); // 'Cash' or something else
              const receipt = $(this).data('receipt'); // File name or null

              $('#modalStudentName').text(studentName);
              $('#modalDepartment').text(department);
              $('#modalReferenceNumber').text(referenceNumber);
              $('#modalRemarks').text(remarks);

              $('#modalStatus')
                .text(status)
                .removeClass()
                .addClass('badge rounded-pill')
                .addClass(getStatusBadgeClass(status));

              // Receipt display logic
              if (paymentType === 'Cash') {
                $('#receiptContainer').html('<p class="mb-0 fw-semibold">Cash Payment – No Receipt Image</p>');
              } else {
                const imagePath = '<?= base_url("uploads/receipts/") ?>' + receipt;
                $('#receiptContainer').html(
                  `<img id="modalReceiptImage" src="${imagePath}" alt="Payment Receipt" class="img-fluid rounded" style="max-height: 300px;">`
                );
              }
            });

            function getStatusBadgeClass(status) {
              switch (status) {
                case 'Verified':
                  return 'bg-success';
                case 'Rejected':
                  return 'bg-danger';
                case 'Pending':
                  return 'bg-warning text-dark';
                default:
                  return 'bg-secondary';
              }
            }
          });
        </script>


        <!-- VALIDATE REGISTRATION MODAL -->
        <div class="modal fade" id="validateModal" tabindex="-1" aria-labelledby="validateModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
              <form id="validateForm">
                <div class="modal-header">
                  <h5 class="modal-title" id="validateModalLabel">Validate Registration</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                  <input type="hidden" id="student_id" name="student_id">
                  <input type="hidden" id="activity_id" name="activity_id">

                  <div class="mb-3">
                    <label for="referenceNumber" class="form-label">Verified Reference Number</label>
                    <input type="text" class="form-control form-control-sm" id="referenceNumber" name="reference_number" required>
                  </div>

                  <!-- Payment Receipt Section -->
                  <div class="mb-3">
                    <label class="form-label">Payment Receipt</label>
                    <div id="viewReceiptImageContainer" class="mt-3 d-none">
                      <img id="viewReceiptImage" src="" alt="Receipt" class="img-fluid rounded border" style="max-height: 400px;">
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="actionSelect" class="form-label">Select Action</label>
                    <select class="form-select form-select-sm" id="actionSelect" name="action" required>
                      <option value="">-- Select --</option>
                      <option value="Verified">Mark as Verified</option>
                      <option value="Rejected">Reject Registration</option>
                    </select>
                  </div>

                  <div class="mb-3">
                    <label for="remarks" class="form-label">Remarks</label>
                    <textarea class="form-control form-control-sm" id="remarks" name="remarks" rows="3"></textarea>
                  </div>
                </div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary btn-sm">Submit Validation</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <script>
          $(document).ready(function() {
            $('.validate-registration').on('click', function() {
              const studentId = $(this).data('student-id');
              const activityId = $(this).data('activity-id');

              $('#student_id').val(studentId);
              $('#activity_id').val(activityId);
            });

            $('#validateForm').on('submit', function(e) {
              e.preventDefault();

              $.ajax({
                url: '<?php echo site_url('officer/activity/registration'); ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                  if (res.status === 'success') {
                    Swal.fire({
                      icon: 'success',
                      title: 'Success',
                      text: 'Registration validated successfully!',
                      timer: 2000,
                      showConfirmButton: false
                    }).then(() => {
                      $('#validateModal').modal('hide');
                      location.reload();
                    });

                  } else if (res.status === 'warning') {
                    Swal.fire({
                      icon: 'warning',
                      title: 'Warning',
                      text: res.message
                    });

                  } else {
                    Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: res.message
                    });
                  }
                },
                error: function() {
                  Swal.fire({
                    icon: 'error',
                    title: 'Request Failed',
                    text: 'Something went wrong. Please try again later.'
                  });
                }
              });
            });
          });
        </script>

        <!-- FOR CASH REGISTRATION -->
        <div class="modal fade" id="recordCashPaymentModal" tabindex="-1" aria-labelledby="recordCashPaymentLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">

              <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="recordCashPaymentLabel">🧾 Record Cash Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <div class="modal-body px-4 py-3">
                <form id="cashPaymentForm">
                  <div class="row g-3">
                    <input type="text" name="activity_id" value="<?php echo $activity['activity_id']; ?>">

                    <!-- Student ID -->
                    <div class="col-md-6">
                      <label class="form-label fw-semibold">Student ID</label>
                      <input type="text" class="form-control" name="student_id" required>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-semibold">Receipt Number</label>
                      <input type="text" class="form-control" name="receipt_number" required>
                    </div>

                    <!-- Amount Paid -->
                    <div class="col-md-6">
                      <label class="form-label fw-semibold">Amount Paid</label>
                      <input type="number" step="0.01" class="form-control" name="amount_paid" value="<?php echo $activity['registration_fee']; ?>">
                    </div>

                    <!-- Remark -->
                    <div class="col-md-12">
                      <label class="form-label fw-semibold">Remark</label>
                      <textarea class="form-control" name="remark" rows="3" placeholder="Optional notes about the payment..."></textarea>
                    </div>
                  </div>

                  <!-- Submit Button -->
                  <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4">
                      <i class="fas fa-check-circle me-1"></i> Submit Payment
                    </button>
                  </div>
                </form>
              </div>

            </div>
          </div>
        </div>

        <script>
          $('#cashPaymentForm').submit(function(e) {
            e.preventDefault(); // Prevent the default form submission

            // Collect form data
            var formData = $(this).serialize();

            // Send the AJAX request
            $.ajax({
              url: '<?php echo site_url('officer/cash-payment/submit'); ?>', // Adjust the URL accordingly
              type: 'POST',
              data: formData,
              dataType: 'json', // Expecting a JSON response
              success: function(response) {
                // Handle the success response
                if (response.status === 'success') {
                  // Show success alert using SweetAlert
                  Swal.fire({
                    icon: 'success',
                    title: 'Payment Recorded!',
                    text: response.message,
                    confirmButtonText: 'OK'
                  }).then(function() {
                    // Optionally close the modal and reset the form
                    $('#recordCashPaymentModal').modal('hide');
                    $('#cashPaymentForm')[0].reset();
                  });
                } else {
                  // Show error alert using SweetAlert
                  Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: response.message,
                    confirmButtonText: 'Try Again'
                  });
                }
              },
              error: function(xhr, status, error) {
                // Handle AJAX errors
                Swal.fire({
                  icon: 'error',
                  title: 'Request Failed!',
                  text: 'There was an error processing your request. Please try again.',
                  confirmButtonText: 'Close'
                });
              }
            });
          });
        </script>

        <script>
          document.getElementById('openRecordCashPaymentModal').addEventListener('click', function() {
            var modal = new bootstrap.Modal(document.getElementById('recordCashPaymentModal'));
            modal.show();
          });
        </script>







        <!-- Filter Modal -->
        <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-md">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Registered Participants</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <form id="filterForm">
                <div class="modal-body">
                  <div class="mb-3">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select class="form-select" id="statusFilter" name="status">
                      <option value="">All</option>
                      <option value="Verified">Verified</option>
                      <option value="Rejected">Rejected</option>
                      <option value="Pending">Pending</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label for="departmentFilter" class="form-label">Department</label>
                    <select class="form-select" id="departmentFilter" name="department">
                      <option value="">All</option>
                      <?php foreach ($departments as $department): ?>
                        <option value="<?php echo $department->dept_name; ?>"><?php echo $department->dept_name; ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary btn-sm">Apply Filters</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Filter Script -->
        <script>
          document.addEventListener("DOMContentLoaded", function() {
            const filterForm = document.getElementById("filterForm");
            const filterModal = new bootstrap.Modal(document.getElementById("filterModal"));

            // Open modal
            document.getElementById("openFilterModal").addEventListener("click", () => {
              filterModal.show();
            });

            // Apply filter
            filterForm.addEventListener("submit", function(e) {
              e.preventDefault();

              const statusValue = document.getElementById("statusFilter").value.toLowerCase();
              const departmentValue = document.getElementById("departmentFilter").value.toLowerCase();

              const rows = document.querySelectorAll("#registeredTable tbody tr.attendance-row");

              let anyVisible = false;

              rows.forEach(row => {
                const status = row.querySelector(".status").innerText.trim().toLowerCase();
                const department = row.querySelector(".department").innerText.trim().toLowerCase();

                const matchStatus = !statusValue || status.includes(statusValue);
                const matchDepartment = !departmentValue || department.includes(departmentValue);

                if (matchStatus && matchDepartment) {
                  row.style.display = "";
                  anyVisible = true;
                } else {
                  row.style.display = "none";
                }
              });

              document.getElementById("attendance-table-fallback").classList.toggle("d-none", anyVisible);
              filterModal.hide();
            });

            // Reset filters on close
            document.getElementById("filterModal").addEventListener("hidden.bs.modal", () => {
              filterForm.reset();
            });
          });
        </script>

        <?php if ($activity['organizer'] == 'Bachelor of Science in Information Systems') : ?>
          <?php if ($activity['status'] == 'Upcoming' || $activity['status'] == 'Ongoing') : ?>
            <?php if ($activity['is_shared'] == 'No') : ?>
              <button id="share" class="btn btn-falcon-default btn-sm me-2" type="button">
                <span class="fas fa-share-alt me-1"></span> Share
              </button>
            <?php endif; ?>
            <a href="<?php echo site_url('officer/edit-activity/' . $activity['activity_id']); ?>" class="btn btn-falcon-default btn-sm me-2">
              <span class="fas fa-edit me-1"></span> Edit
            </a>
          <?php endif; ?>
        <?php endif; ?>
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

    <?php if ($activity['status'] == 'Ongoing') : ?>
      <div class="card mb-4">
        <div class="card-body">
          <h5 class="fs-9 mb-3">Scans</h5>
          <div class="row mb-3 gap-2">
            <?php foreach ($schedules as $schedule) : ?>
              <?php
              date_default_timezone_set('Asia/Manila');
              $currentDateTime = new DateTime();

              $dateTimeIn = !empty($schedule['date_time_in']) ? new DateTime($schedule['date_time_in']) : null;
              $dateCutIn = !empty($schedule['date_cut_in']) ? new DateTime($schedule['date_cut_in']) : null;
              $dateTimeOut = !empty($schedule['date_time_out']) ? new DateTime($schedule['date_time_out']) : null;
              $dateCutOut = !empty($schedule['date_cut_out']) ? new DateTime($schedule['date_cut_out']) : null;

              $isTimeInValid = ($dateTimeIn && $dateCutIn && $currentDateTime >= $dateTimeIn && $currentDateTime <= $dateCutIn);
              $isTimeOutValid = ($dateTimeOut && $dateCutOut && $currentDateTime >= $dateTimeOut && $currentDateTime <= $dateCutOut);
              ?>

              <div class="col-12">
                <div class="border-bottom border-dashed my-3"></div>
                <label class="fw-bold">Scan Options: <?= htmlspecialchars($schedule['slot_name']); ?></label>
                <div class="d-flex flex-row gap-4 flex-wrap mt-2">

                  <!-- Time In Section -->
                  <div class="d-flex flex-column align-items-start gap-1">
                    <div class="d-flex align-items-center gap-3">
                      <label class="small fw-semibold mb-0">Time In:</label>
                      <h6 class="fw-bold fst-italic text-muted mb-0">
                        <?= $dateTimeIn ? $dateTimeIn->format('Y-m-d h:i A') : 'N/A' ?>
                      </h6>
                    </div>
                    <a href="#" class="btn btn-falcon-success btn-sm px-4 px-sm-7 scan-btn"
                      data-url="<?= site_url('officer/activity/scan-qr/time-in/' . $schedule['activity_id']); ?>"
                      data-valid="<?= $isTimeInValid ? 'true' : 'false' ?>"
                      data-type="Time In">
                      Scan QR
                    </a>
                  </div>

                  <!-- Time Out Section -->
                  <div class="d-flex flex-column align-items-start gap-1">
                    <div class="d-flex align-items-center gap-3">
                      <label class="small fw-semibold mb-0">Time Out:</label>
                      <h6 class="fw-bold fst-italic text-muted mb-0">
                        <?= $dateTimeOut ? $dateTimeOut->format('Y-m-d h:i A') : 'N/A' ?>
                      </h6>
                    </div>
                    <a href="#" class="btn btn-falcon-danger btn-sm px-4 px-sm-7 scan-btn"
                      data-url="<?= site_url('officer/activity/scan-qr/time-out/' . $schedule['activity_id']); ?>"
                      data-valid="<?= $isTimeOutValid ? 'true' : 'false' ?>"
                      data-type="Time Out">
                      Scan QR
                    </a>
                  </div>

                </div>
                <div class="border-bottom border-dashed my-3"></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <script>
        document.addEventListener("DOMContentLoaded", function() {
          document.querySelectorAll(".scan-btn").forEach(function(btn) {
            btn.addEventListener("click", function(e) {
              e.preventDefault();

              const isValid = this.getAttribute("data-valid") === "true";
              const scanUrl = this.getAttribute("data-url");
              const scanType = this.getAttribute("data-type");

              if (isValid) {
                // Proceed
                window.location.href = scanUrl;
              } else {
                // Block and alert
                Swal.fire({
                  icon: 'error',
                  title: `${scanType} Not Allowed`,
                  text: 'You are outside the allowed scanning time!',
                  confirmButtonColor: '#d33'
                });
              }
            });
          });
        });
      </script>
    <?php endif; ?>
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
          <?php if (!empty($activities)) {
            shuffle($activities); // Shuffle only if activities exist 
          }

          $hasUpcomingActivities = false; // Default to false

          foreach ($activities as $act) {
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
                  <a href="<?= site_url('officer/activity-details/' . htmlspecialchars($act->activity_id)) ?>">
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
          <a class="btn btn-link d-block w-100 text-center" href="<?= site_url('officer/list-of-activity') ?>">
            All Events <span class="fas fa-chevron-right ms-1 fs-11"></span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>