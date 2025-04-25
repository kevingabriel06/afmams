<div class="row gx-3">
  <div class="col-xxl-10 col-xl-12">
    <div class="card" id="ticketsTable"
      data-list='{"valueNames":["student","subject","status"],"page":11,"pagination":true,"fallback":"tickets-table-fallback"}'>

      <div class="card-header border-bottom border-200 px-0">
        <div class="d-lg-flex justify-content-between">
          <div class="row flex-between-center gy-2 px-x1">
            <div class="col-auto pe-0">
              <h5 class="mb-0"><?php echo $activities['activity_title']; ?> | List of Excuse Application</h5>
            </div>
          </div>

          <!-- Search Input -->
          <div class="d-flex align-items-center justify-content-between justify-content-lg-end px-x1">
            <div class="d-flex align-items-center" id="table-ticket-replace-element">
              <div class="col-auto">
                <form>
                  <div class="input-group input-search-width">
                    <input id="searchInput" class="form-control form-control-sm shadow-none search"
                      type="search" placeholder="Search by Student" aria-label="search" />
                    <button class="btn btn-sm btn-outline-secondary border-300 hover-border-secondary" type="button">
                      <span class="fa fa-search fs-10"></span>
                    </button>
                  </div>
                </form>
              </div>
              <!-- Export Button -->
              <button class="btn btn-sm btn-falcon-default ms-2" type="button" onclick="">
                <span class="fas fa-download"></span>
              </button>
              <button class="btn btn-sm btn-falcon-default ms-2" type="button"
                data-bs-toggle="modal" data-bs-target="#statusFilterModal">
                <span class="fas fa-filter me-1"></span>
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
                <th scope="col">Student</th>
                <th scope="col">Department</th>
                <th scope="col">Subject</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody class="list" id="table-ticket-body">
              <?php foreach ($letters as $letter) : ?>
                <?php if ($letter->activity_id == $activities['activity_id']): ?>
                  <tr class="letter-row" data-status="<?php echo $letter->status; ?>">
                    <td class="align-middle text-nowrap px-6 py-2 student">
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-xl me-3">
                          <img class="rounded-circle" src="<?php echo base_url('assets/profile/' . (!empty($letter->profile_pic) ? $letter->profile_pic : 'default.jpg')); ?>" alt="" />
                        </div>
                        <h6 class="mb-0 fw-semibold text-dark"><?php echo $letter->first_name . " " . $letter->last_name; ?></h6>
                      </div>
                    </td>
                    <td class="align-middle subject px-6 py-2 subject">
                      <?php echo $letter->dept_name; ?>
                    </td>
                    <td class="align-middle subject px-6 py-2 subject">
                      <a class="fw-semi-bold" href="<?php echo site_url('officer/review-excuse-letter/' . $letter->excuse_id); ?>"><?php echo $letter->subject; ?></a>
                    </td>
                    <td class="px-7 py-2 status">
                      <?php if ($letter->status === 'Approved'): ?>
                        <span class="badge badge rounded-pill d-block p-2 badge-subtle-success">Approved<span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span></span>
                      <?php elseif ($letter->status === 'Disapproved'): ?>
                        <span class="badge badge rounded-pill d-block p-2 badge-subtle-danger">Disapproved<span class="ms-1 fas fa-ban" data-fa-transform="shrink-2"></span></span>
                      <?php elseif ($letter->status === 'Pending'): ?>
                        <span class="badge badge rounded-pill d-block p-2 badge-subtle-warning">Pending<span class="ms-1 fas fa-redo" data-fa-transform="shrink-2"></span></span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>

              <!-- "No student listed" Row -->
              <tr id="no-results" class="d-none">
                <td colspan="3" class="text-center text-muted fs-8 fw-bold py-2 bg-light">
                  <span class="fas fa-folder-open fa-2x text-muted"></span> <!-- Folder icon -->
                  <h5 class="mt-2 mb-1">No excuse application found.</h5>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="text-center d-none" id="tickets-table-fallback">
            <span class="fas fa-user-slash fa-2x text-muted"></span>
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

<!-- Filter Modal -->
<div class="modal fade" id="statusFilterModal" tabindex="-1" aria-labelledby="statusFilterModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="statusFilterModalLabel">Filter Applications</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="filterStatus" class="form-label fw-semibold">Select Status</label>
          <select id="filterStatus" class="form-select">
            <option value="all" selected>All</option>
            <option value="Approved">Approved</option>
            <option value="Disapproved">Disapproved</option>
            <option value="Pending">Pending</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="applyFilter">Apply Filter
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    $("#applyFilter").click(function() {
      var selectedStatus = $("#filterStatus").val().toLowerCase(); // Get selected status

      // Show all rows initially
      $(".letter-row").hide();

      if (selectedStatus === "all") {
        $(".letter-row").show(); // Show all if "All" is selected
      } else {
        $(".letter-row").each(function() {
          var rowStatus = $(this).attr("data-status").toLowerCase();
          if (rowStatus === selectedStatus) {
            $(this).show(); // Show matching rows
          }
        });
      }

      // Check if any rows are visible, if not show "No excuse letter listed"
      if ($(".letter-row:visible").length === 0) {
        $("#no-results").removeClass("d-none");
      } else {
        $("#no-results").addClass("d-none");
      }

      $("#statusFilterModal").modal("hide"); // Close modal after applying filter
    });
  });

  document.addEventListener("DOMContentLoaded", function() {
    var options = {
      valueNames: ["student", "subject", "status"], // Ensure these match the table classes
      page: 11,
      pagination: true
    };

    var excuseList = new List("ticketsTable", options);

    // Search Functionality
    document.getElementById("searchInput").addEventListener("keyup", function() {
      excuseList.search(this.value);

      // Show or hide the "No Results" message based on search results
      if ($(".list tr:visible").length === 0) {
        $("#no-results").removeClass("d-none");
      } else {
        $("#no-results").addClass("d-none");
      }
    });
  });
</script>