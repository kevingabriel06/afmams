<div class="container mb-3">
    <div class="row">
        <!-- FETCHING ALL THE AVAILABLE AND OPEN FORMS -->
        <div class="col-md-6">
            <div class="card mb-4" onclick="toggleScroll(this)">
                <div class="card-header">
                    <div class="row flex-between-center">
                        <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                            <h5 class="fs-7 mb-0 text-nowrap py-2 py-xl-0">Evaluation Form</h5>
                        </div>
                    </div>
                </div>
                <div class="border"></div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <i class="fas fa-calendar-check me-2"></i>
                        <strong class="text-muted">Form Title:</strong>
                        <span>Event Feedback</span>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-users me-2"></i>
                        <strong class="text-muted">From:</strong>
                        <span>JPCS</span>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong class="text-muted">Description:</strong>
                        <p>This evaluation is for the "Event Feedback" form. Please provide your feedback to help us improve future events. Thank you!</p>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-clock me-2"></i>
                        <strong class="text-muted">Duration:</strong>
                        <span>October 22, 2015, 8:00 PM - November 5, 2015, 5:00 PM</span>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        <button class="btn btn-primary w-100" onclick="alert('Form opened for evaluation!')">Open Form</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- fetching all the evaluation form answered in talble form -->
        <div class="container">
            <div class="row">
                <!-- Table -->
                <div class="col-lg-12" id="tableColumn" style="overflow-x: auto;">
                    <div class="card" id="customersTable" data-list='{"valueNames":["name","email","phone","address","joined"],"page":10,"pagination":true}'>
                        <div class="card-header">
                            <div class="row flex-between-center">
                                <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                                    <h5 class="fs-9 mb-0 text-nowrap py-2 py-xl-0">Evaluation Form</h5>
                                </div>
                                <div class="col-8 col-sm-auto text-end ps-2">
                                    <div class="d-none" id="table-customers-actions"></div>
                                    <div id="table-customers-replace-element">
                                    <!-- Filter button -->
                                    <button class="btn btn-falcon-default btn-sm mx-2" type="button" id="filterButton">
                                        <span class="fas fa-filter" data-fa-transform="shrink-3 down-2"></span>
                                        <span class="d-none d-sm-inline-block ms-1">Filter</span>
                                    </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" id="attendanceTable">
                                <table class="table table-sm table-striped fs-10 mb-0 overflow-hidden">
                                <thead class="bg-200">
                                    <tr>
                                        <th class="text-900 align-middle white-space-wrap" data-sort="id-number" style="min-width: 150px;">Activity</th>
                                        <th class="text-900 sort pe-1 align-middle white-space-wrap" data-sort="name">Organizer</th>
                                        <th class="text-900 align-middle white-space-wrap" data-sort="date">Date</th>
                                        <th class="text-900 align-middle white-space-wrap" data-sort="status">Status</th>
                                        <th class="align-middle no-sort"></th>
                                    </tr>
                                </thead>
                                <tbody class="list" id="table-customers-body">
                                <tr class="btn-reveal-trigger"> 
                                    <td class="id-number align-middle white-space-wrap py-2">
                                        <a href="#">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-1">
                                                    <h5 class="mb-0 fs-10">TAGISLAKASAN</h5>
                                                </div>
                                            </div>
                                        </a>
                                    </td>
                                    <td class="name align-middle py-2">
                                        STUDENT PARLIAMENT
                                    </td>
                                    <td class="date align-middle py-2">
                                        September 1, 2025
                                    </td>
                                    <td class="status align-middle white-space-wrap py-2">      
                                        <span class="badge badge rounded-pill d-block p-2 badge-subtle-success">Answered<span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span></span>
                                </td>

                                    <td class="align-middle white-space-nowrap py-2 text-end">
                                    <div class="dropdown font-sans-serif position-static"><button class="btn btn-link text-600 btn-sm dropdown-toggle btn-reveal" type="button" id="customer-dropdown-0" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs-10"></span></button>
                                        <div class="dropdown-menu dropdown-menu-end border py-0" aria-labelledby="customer-dropdown-0">
                                        <div class="py-2"><a class="dropdown-item" href="#!">View Form</a></div>
                                        </div>
                                    </div>
                                    </td>
                                </tr>
                                    
                                    
                                </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-center">
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
