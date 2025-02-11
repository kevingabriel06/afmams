<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<div class="card mb-3 mb-lg-0">
    <div class="card-header bg-body-tertiary d-flex justify-content-between">
        <h5 class="mb-0"><?php echo $activities['activity_title']; ?> - Fines List of
            <?php foreach ($department as $dept): ?>
                <?php if ($dept_id == $dept->dept_id): ?>
                    <?php echo $dept->dept_name; ?>
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
                                            type="search" placeholder="Search by Activity" aria-label="search" />
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
                                <th class="text-900 px-6 py-2">Student ID</th>
                                <th class="text-900 px-6 py-2">Name</th>
                                <th class="text-900 px-7 py-2">Status</th>
                                <th class="text-900 px-7 py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody class="list" id="table-ticket-body">
                            <?php foreach ($fines as $fine) : ?>
                                <?php if ($role = 'Officer' && !empty($organization->org_id) && empty($departments->dept_id)) : ?>
                                    <?php if (
                                        isset($activities['activity_id']) &&
                                        $activities['activity_id'] == $fine->activity_id &&
                                        $fine->dept_id == $dept_id &&
                                        $fine->org_id == $organization->org_id
                                    ): ?>
                                        <tr class="fines-row"
                                            data-student-id="<?php echo htmlspecialchars($fine->student_id); ?>"
                                            data-name="<?php echo htmlspecialchars($fine->first_name . ' ' . $fine->last_name); ?>"
                                            data-status="<?php echo htmlspecialchars($fine->is_paid); ?>"
                                            data-am-in="<?php echo htmlspecialchars($fine->am_in ?? 'N/A'); ?>"
                                            data-am-out="<?php echo htmlspecialchars($fine->am_out ?? 'N/A'); ?>"
                                            data-pm-in="<?php echo htmlspecialchars($fine->pm_in ?? 'N/A'); ?>"
                                            data-pm-out="<?php echo htmlspecialchars($fine->pm_out ?? 'N/A'); ?>"
                                            data-total-amount="<?php echo htmlspecialchars($fine->total_amount ?? 'N/A'); ?>">

                                            <!-- Modal Trigger ONLY on Student ID -->
                                            <td class="student_id align-middle text-nowrap px-6 py-2">
                                                <h6 class="mb-0">
                                                    <a href="#" class="modal-trigger" data-bs-toggle="modal"
                                                        data-bs-target="#attendanceModal"
                                                        data-student-id="<?php echo htmlspecialchars($fine->student_id); ?>"
                                                        data-name="<?php echo htmlspecialchars($fine->first_name . ' ' . $fine->last_name); ?>"
                                                        data-status="<?php echo htmlspecialchars($fine->is_paid); ?>"
                                                        data-am-in="<?php echo htmlspecialchars($fine->am_in ?? 'N/A'); ?>"
                                                        data-am-out="<?php echo htmlspecialchars($fine->am_out ?? 'N/A'); ?>"
                                                        data-pm-in="<?php echo htmlspecialchars($fine->pm_in ?? 'N/A'); ?>"
                                                        data-pm-out="<?php echo htmlspecialchars($fine->pm_out ?? 'N/A'); ?>"
                                                        data-total-amount="<?php echo htmlspecialchars($fine->total_amount ?? 'N/A'); ?>">
                                                        <?php echo htmlspecialchars($fine->student_id); ?>
                                                    </a>
                                                </h6>
                                            </td>

                                            <!-- Modal Trigger ONLY on Name -->
                                            <td class="name px-6 py-2">
                                                <h6 class="mb-0">
                                                    <a href="#" class="modal-trigger" data-bs-toggle="modal"
                                                        data-bs-target="#attendanceModal"
                                                        data-student-id="<?php echo htmlspecialchars($fine->student_id); ?>"
                                                        data-name="<?php echo htmlspecialchars($fine->first_name . ' ' . $fine->last_name); ?>"
                                                        data-status="<?php echo htmlspecialchars($fine->is_paid); ?>"
                                                        data-am-in="<?php echo htmlspecialchars($fine->am_in ?? 'N/A'); ?>"
                                                        data-am-out="<?php echo htmlspecialchars($fine->am_out ?? 'N/A'); ?>"
                                                        data-pm-in="<?php echo htmlspecialchars($fine->pm_in ?? 'N/A'); ?>"
                                                        data-pm-out="<?php echo htmlspecialchars($fine->pm_out ?? 'N/A'); ?>"
                                                        data-total-amount="<?php echo htmlspecialchars($fine->total_amount ?? 'N/A'); ?>">
                                                        <?php echo htmlspecialchars($fine->first_name . ' ' . $fine->last_name); ?>
                                                    </a>
                                                </h6>
                                            </td>

                                            <td class="status px-7 py-2">
                                                <?php if ($fine->is_paid === 'Yes'): ?>
                                                    <span class="badge badge rounded-pill d-block p-2 badge-subtle-success">Paid
                                                        <span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span>
                                                    </span>
                                                <?php elseif ($fine->is_paid === 'No'): ?>
                                                    <span class="badge badge rounded-pill d-block p-2 badge-subtle-danger">Unpaid
                                                        <span class="ms-1 fas fa-redo" data-fa-transform="shrink-2"></span>
                                                    </span>
                                                <?php elseif ($fine->is_paid === 'No Fines'): ?>
                                                    <span class="badge badge rounded-pill d-block p-2 badge-subtle-warning">No Fines
                                                        <span class="ms-1 fas fa-stream" data-fa-transform="shrink-2"></span>
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Action Dropdown -->
                                            <td class="text-center align-middle px-6 py-2 position-relative">
                                                <div class="dropdown font-sans-serif btn-reveal-trigger">
                                                    <button class="btn btn-link text-600 btn-sm dropdown-toggle dropdown-caret-none btn-reveal"
                                                        type="button"
                                                        id="actionDropdown<?php echo $fine->student_id; ?>"
                                                        data-bs-toggle="dropdown"
                                                        aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <span class="fas fa-ellipsis-h fs-10"></span>
                                                    </button>

                                                    <!-- Dropdown Menu -->
                                                    <div class="dropdown-menu dropdown-menu-end py-3"
                                                        aria-labelledby="actionDropdown<?php echo $fine->student_id; ?>"
                                                        style="z-index: 1055; position: absolute;">

                                                        <?php if ($fine->is_paid == 'No Fines'): ?>
                                                            <a class="dropdown-item text-success">
                                                                <i class="fas fa-check-circle"></i> No Fines
                                                            </a>
                                                        <?php else : ?>
                                                            <?php if ($fine->is_paid === 'No'): ?>
                                                                <a class="dropdown-item text-success mark-as-paid"
                                                                    href="#"
                                                                    data-student-id="<?php echo $fine->student_id; ?>"
                                                                    data-activity-id="<?php echo $fine->activity_id; ?>"
                                                                    data-status="Yes">
                                                                    <i class="fas fa-check-circle"></i> Mark as Paid
                                                                </a>
                                                            <?php elseif ($fine->is_paid === 'Yes'): ?>
                                                                <a class="dropdown-item text-warning mark-as-unpaid"
                                                                    href="#"
                                                                    data-student-id="<?php echo $fine->student_id; ?>"
                                                                    data-activity-id="<?php echo $fine->activity_id; ?>"
                                                                    data-status="No">
                                                                    <i class="fas fa-times-circle"></i> Mark as Unpaid
                                                                </a>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>




                                        </tr>
                                    <?php endif; ?>
                                <?php elseif ($role = 'Admin') : ?>
                                    <?php if (
                                        isset($activities['activity_id']) &&
                                        $activities['activity_id'] == $fine->activity_id &&
                                        $fine->dept_id == $dept_id
                                    ): ?>
                                        <tr class="fines-row"
                                            data-student-id="<?php echo htmlspecialchars($fine->student_id); ?>"
                                            data-name="<?php echo htmlspecialchars($fine->first_name . ' ' . $fine->last_name); ?>"
                                            data-status="<?php echo htmlspecialchars($fine->is_paid); ?>"
                                            data-am-in="<?php echo htmlspecialchars($fine->am_in ?? 'N/A'); ?>"
                                            data-am-out="<?php echo htmlspecialchars($fine->am_out ?? 'N/A'); ?>"
                                            data-pm-in="<?php echo htmlspecialchars($fine->pm_in ?? 'N/A'); ?>"
                                            data-pm-out="<?php echo htmlspecialchars($fine->pm_out ?? 'N/A'); ?>"
                                            data-total-amount="<?php echo htmlspecialchars($fine->total_amount ?? 'N/A'); ?>">

                                            <!-- Modal Trigger ONLY on Student ID -->
                                            <td class="student_id align-middle text-nowrap px-6 py-2">
                                                <h6 class="mb-0">
                                                    <a href="#" class="modal-trigger" data-bs-toggle="modal"
                                                        data-bs-target="#attendanceModal"
                                                        data-student-id="<?php echo htmlspecialchars($fine->student_id); ?>"
                                                        data-name="<?php echo htmlspecialchars($fine->first_name . ' ' . $fine->last_name); ?>"
                                                        data-status="<?php echo htmlspecialchars($fine->is_paid); ?>"
                                                        data-am-in="<?php echo htmlspecialchars($fine->am_in ?? 'N/A'); ?>"
                                                        data-am-out="<?php echo htmlspecialchars($fine->am_out ?? 'N/A'); ?>"
                                                        data-pm-in="<?php echo htmlspecialchars($fine->pm_in ?? 'N/A'); ?>"
                                                        data-pm-out="<?php echo htmlspecialchars($fine->pm_out ?? 'N/A'); ?>"
                                                        data-total-amount="<?php echo htmlspecialchars($fine->total_amount ?? 'N/A'); ?>">
                                                        <?php echo htmlspecialchars($fine->student_id); ?>
                                                    </a>
                                                </h6>
                                            </td>

                                            <!-- Modal Trigger ONLY on Name -->
                                            <td class="name px-6 py-2">
                                                <h6 class="mb-0">
                                                    <a href="#" class="modal-trigger" data-bs-toggle="modal"
                                                        data-bs-target="#attendanceModal"
                                                        data-student-id="<?php echo htmlspecialchars($fine->student_id); ?>"
                                                        data-name="<?php echo htmlspecialchars($fine->first_name . ' ' . $fine->last_name); ?>"
                                                        data-status="<?php echo htmlspecialchars($fine->is_paid); ?>"
                                                        data-am-in="<?php echo htmlspecialchars($fine->am_in ?? 'N/A'); ?>"
                                                        data-am-out="<?php echo htmlspecialchars($fine->am_out ?? 'N/A'); ?>"
                                                        data-pm-in="<?php echo htmlspecialchars($fine->pm_in ?? 'N/A'); ?>"
                                                        data-pm-out="<?php echo htmlspecialchars($fine->pm_out ?? 'N/A'); ?>"
                                                        data-total-amount="<?php echo htmlspecialchars($fine->total_amount ?? 'N/A'); ?>">
                                                        <?php echo htmlspecialchars($fine->first_name . ' ' . $fine->last_name); ?>
                                                    </a>
                                                </h6>
                                            </td>

                                            <td class="status px-7 py-2">
                                                <?php if ($fine->is_paid === 'Yes'): ?>
                                                    <span class="badge badge rounded-pill d-block p-2 badge-subtle-success">Paid
                                                        <span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span>
                                                    </span>
                                                <?php elseif ($fine->is_paid === 'No'): ?>
                                                    <span class="badge badge rounded-pill d-block p-2 badge-subtle-danger">Unpaid
                                                        <span class="ms-1 fas fa-redo" data-fa-transform="shrink-2"></span>
                                                    </span>
                                                <?php elseif ($fine->is_paid === 'No Fines'): ?>
                                                    <span class="badge badge rounded-pill d-block p-2 badge-subtle-warning">No Fines
                                                        <span class="ms-1 fas fa-stream" data-fa-transform="shrink-2"></span>
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Action Dropdown -->
                                            <td class="text-center align-middle px-6 py-2 position-relative">
                                                <div class="dropdown font-sans-serif btn-reveal-trigger">
                                                    <button class="btn btn-link text-600 btn-sm dropdown-toggle dropdown-caret-none btn-reveal"
                                                        type="button"
                                                        id="actionDropdown<?php echo $fine->student_id; ?>"
                                                        data-bs-toggle="dropdown"
                                                        aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <span class="fas fa-ellipsis-h fs-10"></span>
                                                    </button>

                                                    <!-- Dropdown Menu -->
                                                    <div class="dropdown-menu dropdown-menu-end py-3"
                                                        aria-labelledby="actionDropdown<?php echo $fine->student_id; ?>"
                                                        style="z-index: 1055; position: absolute;">

                                                        <?php if ($fine->is_paid == 'No Fines'): ?>
                                                            <a class="dropdown-item text-success">
                                                                <i class="fas fa-check-circle"></i> No Fines
                                                            </a>
                                                        <?php else : ?>
                                                            <?php if ($fine->is_paid === 'No'): ?>
                                                                <a class="dropdown-item text-success mark-as-paid"
                                                                    href="#"
                                                                    data-student-id="<?php echo $fine->student_id; ?>"
                                                                    data-activity-id="<?php echo $fine->activity_id; ?>"
                                                                    data-status="Yes">
                                                                    <i class="fas fa-check-circle"></i> Mark as Paid
                                                                </a>
                                                            <?php elseif ($fine->is_paid === 'Yes'): ?>
                                                                <a class="dropdown-item text-warning mark-as-unpaid"
                                                                    href="#"
                                                                    data-student-id="<?php echo $fine->student_id; ?>"
                                                                    data-activity-id="<?php echo $fine->activity_id; ?>"
                                                                    data-status="No">
                                                                    <i class="fas fa-times-circle"></i> Mark as Unpaid
                                                                </a>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if (
                                        isset($activities['activity_id']) &&
                                        $activities['activity_id'] == $fine->activity_id &&
                                        $fine->dept_id == $dept_id
                                    ): ?>
                                        <tr class="fines-row"
                                            data-student-id="<?php echo htmlspecialchars($fine->student_id); ?>"
                                            data-name="<?php echo htmlspecialchars($fine->first_name . ' ' . $fine->last_name); ?>"
                                            data-status="<?php echo htmlspecialchars($fine->is_paid); ?>"
                                            data-am-in="<?php echo htmlspecialchars($fine->am_in ?? 'N/A'); ?>"
                                            data-am-out="<?php echo htmlspecialchars($fine->am_out ?? 'N/A'); ?>"
                                            data-pm-in="<?php echo htmlspecialchars($fine->pm_in ?? 'N/A'); ?>"
                                            data-pm-out="<?php echo htmlspecialchars($fine->pm_out ?? 'N/A'); ?>"
                                            data-total-amount="<?php echo htmlspecialchars($fine->total_amount ?? 'N/A'); ?>">

                                            <!-- Modal Trigger ONLY on Student ID -->
                                            <td class="student_id align-middle text-nowrap px-6 py-2">
                                                <h6 class="mb-0">
                                                    <a href="#" class="modal-trigger" data-bs-toggle="modal"
                                                        data-bs-target="#attendanceModal"
                                                        data-student-id="<?php echo htmlspecialchars($fine->student_id); ?>"
                                                        data-name="<?php echo htmlspecialchars($fine->first_name . ' ' . $fine->last_name); ?>"
                                                        data-status="<?php echo htmlspecialchars($fine->is_paid); ?>"
                                                        data-am-in="<?php echo htmlspecialchars($fine->am_in ?? 'N/A'); ?>"
                                                        data-am-out="<?php echo htmlspecialchars($fine->am_out ?? 'N/A'); ?>"
                                                        data-pm-in="<?php echo htmlspecialchars($fine->pm_in ?? 'N/A'); ?>"
                                                        data-pm-out="<?php echo htmlspecialchars($fine->pm_out ?? 'N/A'); ?>"
                                                        data-total-amount="<?php echo htmlspecialchars($fine->total_amount ?? 'N/A'); ?>">
                                                        <?php echo htmlspecialchars($fine->student_id); ?>
                                                    </a>
                                                </h6>
                                            </td>

                                            <!-- Modal Trigger ONLY on Name -->
                                            <td class="name px-6 py-2">
                                                <h6 class="mb-0">
                                                    <a href="#" class="modal-trigger" data-bs-toggle="modal"
                                                        data-bs-target="#attendanceModal"
                                                        data-student-id="<?php echo htmlspecialchars($fine->student_id); ?>"
                                                        data-name="<?php echo htmlspecialchars($fine->first_name . ' ' . $fine->last_name); ?>"
                                                        data-status="<?php echo htmlspecialchars($fine->is_paid); ?>"
                                                        data-am-in="<?php echo htmlspecialchars($fine->am_in ?? 'N/A'); ?>"
                                                        data-am-out="<?php echo htmlspecialchars($fine->am_out ?? 'N/A'); ?>"
                                                        data-pm-in="<?php echo htmlspecialchars($fine->pm_in ?? 'N/A'); ?>"
                                                        data-pm-out="<?php echo htmlspecialchars($fine->pm_out ?? 'N/A'); ?>"
                                                        data-total-amount="<?php echo htmlspecialchars($fine->total_amount ?? 'N/A'); ?>">
                                                        <?php echo htmlspecialchars($fine->first_name . ' ' . $fine->last_name); ?>
                                                    </a>
                                                </h6>
                                            </td>

                                            <td class="status px-7 py-2">
                                                <?php if ($fine->is_paid === 'Yes'): ?>
                                                    <span class="badge badge rounded-pill d-block p-2 badge-subtle-success">Paid
                                                        <span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span>
                                                    </span>
                                                <?php elseif ($fine->is_paid === 'No'): ?>
                                                    <span class="badge badge rounded-pill d-block p-2 badge-subtle-danger">Unpaid
                                                        <span class="ms-1 fas fa-redo" data-fa-transform="shrink-2"></span>
                                                    </span>
                                                <?php elseif ($fine->is_paid === 'No Fines'): ?>
                                                    <span class="badge badge rounded-pill d-block p-2 badge-subtle-warning">No Fines
                                                        <span class="ms-1 fas fa-stream" data-fa-transform="shrink-2"></span>
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Action Dropdown -->
                                            <td class="text-center align-middle px-6 py-2 position-relative">
                                                <div class="dropdown font-sans-serif btn-reveal-trigger">
                                                    <button class="btn btn-link text-600 btn-sm dropdown-toggle dropdown-caret-none btn-reveal"
                                                        type="button"
                                                        id="actionDropdown<?php echo $fine->student_id; ?>"
                                                        data-bs-toggle="dropdown"
                                                        aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <span class="fas fa-ellipsis-h fs-10"></span>
                                                    </button>

                                                    <!-- Dropdown Menu -->
                                                    <div class="dropdown-menu dropdown-menu-end py-3"
                                                        aria-labelledby="actionDropdown<?php echo $fine->student_id; ?>"
                                                        style="z-index: 1055; position: absolute;">

                                                        <?php if ($fine->is_paid == 'No Fines'): ?>
                                                            <a class="dropdown-item text-success">
                                                                <i class="fas fa-check-circle"></i> No Fines
                                                            </a>
                                                        <?php else : ?>
                                                            <?php if ($fine->is_paid === 'No'): ?>
                                                                <a class="dropdown-item text-success mark-as-paid"
                                                                    href="#"
                                                                    data-student-id="<?php echo $fine->student_id; ?>"
                                                                    data-activity-id="<?php echo $fine->activity_id; ?>"
                                                                    data-status="Yes">
                                                                    <i class="fas fa-check-circle"></i> Mark as Paid
                                                                </a>
                                                            <?php elseif ($fine->is_paid === 'Yes'): ?>
                                                                <a class="dropdown-item text-warning mark-as-unpaid"
                                                                    href="#"
                                                                    data-student-id="<?php echo $fine->student_id; ?>"
                                                                    data-activity-id="<?php echo $fine->activity_id; ?>"
                                                                    data-status="No">
                                                                    <i class="fas fa-times-circle"></i> Mark as Unpaid
                                                                </a>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <!-- "No activities listed" Row -->
                            <tr id="no-attendance-row" style="display: none;">
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

    <!-- ATTENDANCE DETAILs -->
    <div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attendanceModalLabel">Fine Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Student ID:</strong> <span id="modal-student-id"></span></p>
                    <p><strong>Name:</strong> <span id="modal-name"></span></p>
                    <p><strong>Status:</strong> <span id="modal-status"></span></p>
                    <hr>
                    <div id="morning-fine">
                        <p class="fw-bold">Morning Fine</p>
                        <p><strong>AM In:</strong> <span id="modal-am-in"></span></p>
                        <p><strong>AM Out:</strong> <span id="modal-am-out"></span></p>
                    </div>
                    <div id="afternoon-fine">
                        <p class="fw-bold">Afternoon Fine</p>
                        <p><strong>PM In:</strong> <span id="modal-pm-in"></span></p>
                        <p><strong>PM Out:</strong> <span id="modal-pm-out"></span></p>
                    </div>
                    <p><strong>Total Amount:</strong> <span id="modal-total-amount"></span></p>
                    <p id="no-fines" class="text-center text-muted d-none">No fines recorded.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <!-- JavaScript to Populate and Display Fines -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var attendanceModal = document.getElementById("attendanceModal");

            attendanceModal.addEventListener("show.bs.modal", function(event) {
                var button = event.relatedTarget; // Button that triggered the modal

                // Check if the button exists and has data attributes
                if (!button) return;

                // Extract data attributes
                var studentId = button.getAttribute("data-student-id") || "N/A";
                var name = button.getAttribute("data-name") || "N/A";
                var status = button.getAttribute("data-status") === "1" ? "Paid" : "Unpaid";
                var amIn = button.getAttribute("data-am-in") || "N/A";
                var amOut = button.getAttribute("data-am-out") || "N/A";
                var pmIn = button.getAttribute("data-pm-in") || "N/A";
                var pmOut = button.getAttribute("data-pm-out") || "N/A";
                var totalAmount = button.getAttribute("data-total-amount") || "N/A";

                // Update modal content
                document.getElementById("modal-student-id").textContent = studentId;
                document.getElementById("modal-name").textContent = name;
                document.getElementById("modal-status").textContent = status;
                document.getElementById("modal-am-in").textContent = amIn;
                document.getElementById("modal-am-out").textContent = amOut;
                document.getElementById("modal-pm-in").textContent = pmIn;
                document.getElementById("modal-pm-out").textContent = pmOut;
                document.getElementById("modal-total-amount").textContent = totalAmount;

                // Hide both sections initially
                document.getElementById("morning-fine").style.display = "none";
                document.getElementById("afternoon-fine").style.display = "none";
                document.getElementById("no-fines").classList.add("d-none");

                // Determine which sections to show
                var isMorningEmpty = (amIn === "N/A" && amOut === "N/A");
                var isAfternoonEmpty = (pmIn === "N/A" && pmOut === "N/A");

                if (isMorningEmpty && isAfternoonEmpty) {
                    // No fines recorded
                    document.getElementById("attendanceModalLabel").textContent = "No Fines Recorded";
                    document.getElementById("no-fines").classList.remove("d-none");
                } else {
                    document.getElementById("attendanceModalLabel").textContent = "Fine Details";

                    if (!isMorningEmpty) {
                        document.getElementById("morning-fine").style.display = "block";
                    }
                    if (!isAfternoonEmpty) {
                        document.getElementById("afternoon-fine").style.display = "block";
                    }
                }
            });
        });
    </script>


    <!-- MODAL FILTER -->
    <!-- Fine Details Modal -->
    <div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attendanceModalLabel">Fine Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Student ID:</strong> <span id="modal-student-id"></span></p>
                    <p><strong>Name:</strong> <span id="modal-name"></span></p>
                    <p><strong>Status:</strong> <span id="modal-status"></span></p>

                    <hr>

                    <!-- Morning Fine Section -->
                    <div id="morning-fine">
                        <p class="fw-bold">Morning Fine</p>
                        <p><strong>AM In:</strong> <span id="modal-am-in"></span></p>
                        <p><strong>AM Out:</strong> <span id="modal-am-out"></span></p>
                    </div>

                    <!-- Afternoon Fine Section -->
                    <div id="afternoon-fine">
                        <p class="fw-bold">Afternoon Fine</p>
                        <p><strong>PM In:</strong> <span id="modal-pm-in"></span></p>
                        <p><strong>PM Out:</strong> <span id="modal-pm-out"></span></p>
                    </div>

                    <p id="no-fines" class="text-center text-muted d-none">No fines recorded.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
            document.querySelectorAll(".mark-as-paid, .mark-as-unpaid").forEach((btn) => {
                btn.addEventListener("click", function(event) {
                    event.preventDefault();

                    let studentId = this.getAttribute("data-student-id");
                    let activityId = this.getAttribute("data-activity-id"); // Fetch activity_id
                    let newStatus = this.getAttribute("data-status");

                    let confirmationMessage = newStatus === "Yes" ?
                        "Are you sure you want to mark this fine as PAID?" :
                        "Are you sure you want to mark this fine as UNPAID?";

                    alertify.confirm("Confirm Action", confirmationMessage,
                        function() {
                            fetch("<?php echo base_url('admin/fines/update_status'); ?>", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json"
                                    },
                                    body: JSON.stringify({
                                        student_id: studentId,
                                        activity_id: activityId, // Send activity_id
                                        is_paid: newStatus
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

        function applyFilters() {
            // Get selected values from the modal filters
            var status = document.getElementById("status-filter").value;

            // Get all activity rows
            var activityRows = document.querySelectorAll(".fines-row");
            var noActivityRow = document.getElementById("no-attendance-row");
            var filteredRows = 0;

            // Loop through each activity row
            activityRows.forEach(function(row) {
                var rowStatus = row.getAttribute("data-status"); // Add status attribute in PHP

                // Check if the row matches the selected filters
                if (
                    (status === "" || status === rowStatus)
                ) {
                    row.style.display = ""; // Show the row if it matches
                    filteredRows++;
                } else {
                    row.style.display = "none"; // Hide the row if it doesn't match
                }
            });

            // Show or hide the "No activities listed" row
            if (filteredRows === 0) {
                noActivityRow.style.display = ""; // Show the no activity row
            } else {
                noActivityRow.style.display = "none"; // Hide the no activity row
            }

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