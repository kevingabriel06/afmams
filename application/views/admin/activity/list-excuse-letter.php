<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="row gx-3">
  <div class="col-xxl-10 col-xl-12">
    <div class="card" id="ticketsTable" data-list='{"valueNames":["client","subject","status","priority","agent"],"page":11,"pagination":true,"fallback":"tickets-table-fallback"}'>
      <div class="card-header border-bottom border-200 px-0">
        <div class="d-lg-flex justify-content-between">
          <div class="row flex-between-center gy-2 px-x1">
            <div class="col-auto pe-0">
              <h6 class="mb-0"><?php echo $activities['activity_title']; ?> - List of Excuse Application</h6>
            </div>
          </div>

          <!-- bulk actions options -->
          <div class="border-bottom border-200 my-3"></div>
          <div class="d-flex align-items-center justify-content-between justify-content-lg-end px-x1">
            <div class="bg-300 mx-3 d-none d-lg-block d-xl-none" style="width:1px; height:29px"></div>
            <div class="d-flex align-items-center" id="table-ticket-replace-element">
              <div class="col-auto">
                <form>
                  <div class="input-group input-search-width">
                    <input class="form-control form-control-sm shadow-none search" type="search" placeholder="Search by event" aria-label="search" />
                    <button class="btn btn-sm btn-outline-secondary border-300 hover-border-secondary" type="button">
                      <span class="fa fa-search fs-10"></span>
                    </button>
                  </div>
                </form>
              </div>
              <!-- Export Button -->
              <button class="btn btn-falcon-default btn-sm ms-2" type="button">
                <span class="fas fa-external-link-alt" data-fa-transform="shrink-3"></span>
              </button>
              <button class="btn btn-falcon-default btn-sm ms-2" type="button" data-bs-toggle="modal" data-bs-target="#statusFilterModal">
                <span class="fas fa-filter" data-fa-transform="shrink-3"></span> Filter
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive scrollbar">
          <!-- Table -->
          <table class="table table-sm mb-0 fs-10 table-view-tickets">
            <thead class="bg-body-tertiary">
              <tr>
                <th class="py-2 fs-9 pe-2" style="width: 28px;">
                  <div class="form-check d-flex align-items-center"></div>
                </th>
                <th class="text-800 sort align-middle ps-2">Student</th>
                <th class="text-800 sort align-middle" style="min-width:15.625rem">Subject</th>
                <th class="text-800 sort align-middle">Status</th>
              </tr>
            </thead>
            <tbody id="table-ticket-body">
              <?php foreach($letters as $letter) :?>
                <?php if($letter->activity_id == $activities['activity_id']):?>
                <tr class="excuse-row" data-status="<?php echo $letter->status; ?>">
                  <td class="align-middle fs-9 py-3">
                    <div class="form-check mb-0"></div>
                  </td>
                  <td class="align-middle client white-space-nowrap pe-3 pe-xxl-4 ps-2">
                    <div class="d-flex align-items-center gap-2 position-relative">
                      <div class="avatar avatar-xl">
                        <img class="avatar-name rounded-circle" src="<?php echo base_url('assets/profile/' . (!empty($letter->profile_pic) ? $letter->profile_pic : 'default.jpg')); ?>" alt="Profile Picture" />
                      </div>
                      <h6 class="mb-0"> <?php echo $letter->first_name." ".$letter->last_name ;?></h6>
                    </div>
                  </td>
                  <td class="align-middle subject py-2 pe-4">
                    <a class="fw-semi-bold" href="<?php echo site_url('admin/review-excuse-letter/'.$letter->excuse_id);?>"><?php echo $letter->subject ;?></a>
                  </td>
                  <td class="align-middle status fs-9 pe-4">
                    <?php 
                        if ($letter->status == 'Approved') {
                            $badgeClass = 'badge-subtle-success';
                        } elseif ($letter->status == 'Disapproved') {
                            $badgeClass = 'badge-subtle-danger';
                        } elseif ($letter->status == 'Pending') {
                            $badgeClass = 'badge-subtle-warning';
                        }
                    ?>
                    <small class="badge rounded <?php echo $badgeClass; ?>"><?php echo $letter->status; ?></small>
                  </td>
                </tr>
                <tr id="no-results" class="d-none">
                  <td colspan="5" class="fw-bold text-center fs-8">
                    No excuse letter listed
                  </td>
                </tr>
                <?php endif;?>
              <?php endforeach;?>
            </tbody>
          </table>
          <div class="text-center d-none" id="tickets-table-fallback">
            <p class="fw-bold fs-8 mt-3">No ticket found</p>
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
        <h5 class="modal-title" id="statusFilterModalLabel">Filter by Status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <label for="filterStatus" class="form-label">Select Status:</label>
        <select id="filterStatus" class="form-select">
          <option value="all">All</option>
          <option value="Approved">Approved</option>
          <option value="Disapproved">Disapproved</option>
          <option value="Pending">Pending</option>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="applyFilter">Apply Filter</button>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function () {
    $("#applyFilter").click(function () {
      var selectedStatus = $("#filterStatus").val();
      
      // Show all rows initially
      $(".excuse-row").hide();

      if (selectedStatus === "all") {
        $(".excuse-row").show();
      } else {
        $(".excuse-row").each(function () {
          if ($(this).attr("data-status") === selectedStatus) {
            $(this).show();
          }
        });
      }

      // Check if any rows are visible, if not show the "No excuse letter listed" message
      if ($(".excuse-row:visible").length === 0) {
        $("#no-results").removeClass("d-none");
      } else {
        $("#no-results").addClass("d-none");
      }

      $("#statusFilterModal").modal("hide"); // Close the modal after filtering
    });
  });
</script>

