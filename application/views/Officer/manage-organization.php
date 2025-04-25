<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<div class="card mb-3 mb-lg-0">
    <div class="card-header bg-body-tertiary d-flex justify-content-between">
        <h5 class="mb-0">
            <?php foreach ($organization as $org): ?>
                <?php if ($org_id == $org->org_id): ?>
                    List of Officers - <?php echo $org->org_name; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </h5>
    </div>
</div>

<!-- Space Between Sections -->
<div class="space" style="height: 20px;"></div> <!-- Adds spacing between sections -->


<div class="row gx-3">
    <div class="col-xxl-10 col-xl-12">
        <div class="card" id="ticketsTable"
            data-list='{"valueNames":["name","status"],"page":11,"pagination":true,"fallback":"tickets-table-fallback"}'>

            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">

                    </div>

                    <!-- Search Input -->
                    <div class="d-flex align-items-center justify-content-between justify-content-lg-end px-x1">
                        <div class="d-flex align-items-center" id="table-ticket-replace-element">
                            <div class="col-auto">
                                <form>
                                    <div class="input-group input-search-width">
                                        <input id="searchInput" class="form-control form-control-sm shadow-none search"
                                            type="search" placeholder="Search by Name" aria-label="search" />
                                        <button class="btn btn-sm btn-outline-secondary border-300 hover-border-secondary" type="button">
                                            <span class="fa fa-search fs-10"></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <button class="btn btn-sm btn-falcon-default ms-2" type="button">
                                <span class="fas fa-download"></span>
                            </button>
                            <button class="btn btn-sm btn-falcon-default ms-2" type="button"
                                data-bs-toggle="modal" data-bs-target="#filterModal">
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
                                <th class="text-900 px-5 py-2">Student ID</th>
                                <th class="text-900 px-5 py-2">Name</th>
                                <th class="text-900 px-5 py-2">Status</th>
                                <th class="text-900 px-5 py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody class="list" id="table-ticket-body">
                            <?php foreach ($officers as $officer) : ?>
                                <?php if ($officer->org_id == $org_id): ?>
                                    <tr class="users-row" data-is-admin="<?php echo $officer->is_admin; ?>">
                                        <td class="student_id align-middle text-nowrap px-5 py-2">
                                            <h6 class="mb-0">
                                                <?php echo htmlspecialchars($officer->student_id); ?>
                                            </h6>
                                        </td>
                                        <td class="name px-5 py-2">
                                            <h6 class="mb-0">
                                                <?php echo htmlspecialchars($officer->first_name . ' ' . $officer->last_name); ?>
                                            </h6>
                                        </td>

                                        <td class="status px-5 py-2">
                                            <?php if ($officer->is_admin === 'Yes'): ?>
                                                <span class="badge badge rounded-pill d-block p-2 badge-subtle-success">Organization Admin
                                                    <span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span>
                                                </span>
                                            <?php elseif ($officer->is_admin === 'No'): ?>
                                                <span class="badge badge rounded-pill d-block p-2 badge-subtle-danger">Organization Officer
                                                    <span class="ms-1 fas fa-redo" data-fa-transform="shrink-2"></span>
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Action Dropdown -->
                                        <td class="text-center align-middle px-5 py-2 position-relative">
                                            <div class="dropdown font-sans-serif btn-reveal-trigger">
                                                <button class="btn btn-link text-600 btn-sm dropdown-toggle dropdown-caret-none btn-reveal"
                                                    type="button"
                                                    id="actionDropdown<?php echo $officer->student_id; ?>"
                                                    data-bs-toggle="dropdown"
                                                    aria-haspopup="true"
                                                    aria-expanded="false">
                                                    <span class="fas fa-ellipsis-h fs-10"></span>
                                                </button>

                                                <!-- Dropdown Menu -->
                                                <div class="dropdown-menu dropdown-menu-end py-3"
                                                    aria-labelledby="actionDropdown<?php echo $officer->student_id; ?>"
                                                    style="z-index: 1055; position: absolute;">
                                                    <?php if ($officer->is_admin === 'No'): ?>
                                                        <a class="dropdown-item text-success make-a-admin"
                                                            href="#"
                                                            data-student-id="<?php echo $officer->student_id; ?>"
                                                            data-status="Yes">
                                                            <i class="fas fa-check-circle"></i> Make as Admin
                                                        </a>
                                                    <?php elseif ($officer->is_admin === 'Yes'): ?>
                                                        <a class="dropdown-item text-warning remove-a-admin"
                                                            href="#"
                                                            data-student-id="<?php echo $officer->student_id; ?>"
                                                            data-status="No">
                                                            <i class="fas fa-times-circle"></i> Remove as Admin
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <!-- "No activities listed" Row -->
                            <tr id="no-user-row" style="display: none;">
                                <td colspan="3" class="text-center text-muted fs-8 fw-bold py-2 bg-light">
                                    <span class="fa fa-user-slash fa-2x text-muted"></span>
                                    <h5 class="mt-2 mb-1">No student listed.</h5>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-center d-none" id="tickets-table-fallback">
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

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter by Admin Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <label for="is_admin">Select Status:</label>
                        <select id="is_admin" class="form-select">
                            <option value="">All</option>
                            <option value="Yes">Organization Admin</option>
                            <option value="No">Organization Officer</option>
                        </select>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="applyFilter()">Apply Filter</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs/build/css/alertify.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs/build/css/themes/default.min.css" />

    <!-- Alertify JS -->
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs/build/alertify.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".make-a-admin, .remove-a-admin").forEach((btn) => {
                btn.addEventListener("click", function(event) {
                    event.preventDefault();

                    let studentId = this.getAttribute("data-student-id");
                    let newStatus = this.getAttribute("data-status");

                    let confirmationMessage = newStatus === "Yes" ?
                        "Are you sure you want to make as an admin?" :
                        "Are you sure you want to remove as an admin";

                    alertify.confirm("Confirm Action", confirmationMessage,
                        function() {
                            fetch("<?php echo base_url('officer/manage-officers-organization/update_status'); ?>", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json"
                                    },
                                    body: JSON.stringify({
                                        student_id: studentId,
                                        is_admin: newStatus
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        alertify.set("notifier", "position", "top-right");
                                        alertify.success(`<i class="fas fa-check-circle"></i> ${data.message}`);

                                        setTimeout(() => {
                                            location.reload(); // Refresh to reflect changes
                                        }, 1500); // Short delay before reloading
                                    } else {
                                        alertify.set("notifier", "position", "top-right");
                                        alertify.error(`<i class="fas fa-times-circle"></i> ${data.message}`);
                                    }
                                })
                                .catch(error => {
                                    alertify.set("notifier", "position", "top-right");
                                    alertify.error(`<i class="fas fa-exclamation-triangle"></i> An error occurred.`);
                                    console.error("Error:", error);
                                });
                        },
                        function() {
                            alertify.set("notifier", "position", "top-right");
                            alertify.error("Action Cancelled");
                        }
                    );
                });
            });
        });

        function applyFilter() {
            // Get selected value from the dropdown
            var adminFilter = document.getElementById("is_admin").value;

            // Get all user rows
            var userRows = document.querySelectorAll(".users-row");
            var noUserRow = document.getElementById("no-user-row");
            var filteredRows = 0;

            // Loop through each user row
            userRows.forEach(function(row) {
                var rowAdminStatus = row.getAttribute("data-is-admin");

                // Check if the row matches the selected filter
                if (adminFilter === "" || adminFilter === rowAdminStatus) {
                    row.style.display = ""; // Show row if it matches
                    filteredRows++;
                } else {
                    row.style.display = "none"; // Hide row if it doesn't match
                }
            });

            // Show or hide the "No users found" row
            noUserRow.style.display = filteredRows === 0 ? "" : "none";

            // Close the modal properly
            var modalElement = document.getElementById("filterModal");
            var modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        }

        // FOR SEARCH
        document.addEventListener("DOMContentLoaded", function() {
            var options = {
                valueNames: ["name", "status"],
                page: 11,
                pagination: true
            };

            var excuseList = new List("ticketsTable", options);

            document.getElementById("searchInput").addEventListener("keyup", function() {
                excuseList.search(this.value);
            });
        });
    </script>