<div class="row gx-3">
    <div class="col-xxl-10 col-xl-9">
        <div class="card" id="ticketsTable">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h6 class="mb-0">All Activities</h6>
                        </div>
                    </div>
                    <!-- bulk actions options -->
                    <div class="border-bottom border-200 my-3"></div>
                    <div class="d-flex align-items-center justify-content-between justify-content-lg-end px-x1">
                        <button class="btn btn-sm btn-falcon-default d-xl-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#ticketOffcanvas" aria-controls="ticketOffcanvas">
                            <span class="fas fa-filter" data-fa-transform="shrink-4 down-1"></span><span class="ms-1 d-none d-sm-inline-block">Filter</span>
                        </button>
                        <div class="bg-300 mx-3 d-none d-lg-block d-xl-none" style="width:1px; height:29px"></div>
                        <div class="d-flex align-items-center" id="table-ticket-replace-element">
                            <div class="col-auto">
                                <form>
                                    <div class="input-group input-search-width">
                                        <input class="form-control form-control-sm shadow-none search" type="search" placeholder="Search by name" aria-label="search" />
                                        <button class="btn btn-sm btn-outline-secondary border-300 hover-border-secondary" type="button">
                                            <span class="fa fa-search fs-10"></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <!-- Export Button -->
                            <button class="btn btn-falcon-default btn-sm ms-2" type="button">
                                <span class="fas fa-external-link-alt" data-fa-transform="shrink-3"></span>
                                <span class="d-none d-sm-inline-block d-xl-none d-xxl-inline-block ms-1">Export</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive scrollbar">
				<table class="table table-sm table-striped table-hover mb-0 fs-10">
					<thead class="bg-body-tertiary">
						<tr>
							<th class="text-800 sort align-middle ps-2 w-50">Event</th>
							<th class="text-800 sort align-middle w-50">Status</th>
						</tr>
					</thead>
					<tbody id="table-ticket-body">
						<?php if (!empty($activities)): ?>
							<?php foreach ($activities as $activity): ?>
								<tr>
									<td class="align-middle client white-space-nowrap pe-3 pe-xxl-4 ps-2">
										<div class="d-flex align-items-center gap-2 position-relative">
											<!-- <div class="avatar avatar-xl">
												<img class="rounded-circle" src="path_to_image/<?php echo $activity->activity_image; ?>" alt="<?php echo $activity->activity_title; ?>" />
											</div> -->
											<h6 class="mb-0">
											<a class="stretched-link text-900" href="<?php echo site_url('admin/list-evaluation-answers/'.$activity->activity_id); ?>">
													<?php echo $activity->activity_title; ?>
											</a>
											</h6>
										</div>
									</td>
									<td class="align-middle status fs-9 pe-4">
										<small class="badge rounded badge-subtle-<?php echo $activity->status_class; ?>"><?php echo $activity->status; ?></small>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else: ?>
							<tr><td colspan="2">No activities found.</td></tr>
						<?php endif; ?>
					</tbody>
				</table>

                    <div class="text-center d-none" id="tickets-table-fallback">
                        <p class="fw-bold fs-8 mt-3">No ticket found</p>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-center">
                    <button class="btn btn-sm btn-falcon-default me-1" type="button" title="Previous">
                        <span class="fas fa-chevron-left"></span>
                    </button>
                    <ul class="pagination mb-0"></ul>
                    <button class="btn btn-sm btn-falcon-default ms-1" type="button" title="Next">
                        <span class="fas fa-chevron-right"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-2 col-xl-3">
        <div class="offcanvas offcanvas-end offcanvas-filter-sidebar border-0 dark__bg-card-dark h-auto rounded-xl-3" tabindex="-1" id="ticketOffcanvas" aria-labelledby="ticketOffcanvasLabel">
            <div class="offcanvas-header d-flex flex-between-center d-xl-none bg-body-tertiary">
                <h6 class="fs-9 mb-0 fw-semi-bold">Filter</h6>
                <button class="btn-close text-reset d-xl-none shadow-none" id="ticketOffcanvasLabel" type="button" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="card scrollbar shadow-none shadow-show-xl">
                <div class="card-header bg-body-tertiary d-none d-xl-block">
                    <h6 class="mb-0">Filter</h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-2 mt-n2">
                            <label class="mb-1">Date</label>
                            <select class="form-select form-select-sm">
                                <option>None</option>
                                <option>Last Month</option>
                                <option>Last 3 Months</option>
                                <option>Last 6 Months</option>
                                <option>Last Year</option>
                                <option>All</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="card-footer border-top border-200 py-x1">
                    <button class="btn btn-primary w-100">Update</button>
                </div>
            </div>
        </div>
    </div>
</div>
