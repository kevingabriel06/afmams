<div class="row gx-3">
  <div class="col-xxl-10 col-xl-12">
    <div class="card" id="ticketsTable" data-list='{"valueNames":["client","date","formResponse","priority","agent"],"page":11,"pagination":true,"fallback":"tickets-table-fallback"}'>
      <div class="card-header border-bottom border-200 px-0">
        <div class="d-lg-flex justify-content-between">
          <div class="row flex-between-center gy-2 px-x1">
            <div class="col-auto pe-0">
						
						<?php foreach ($activities as $activity): ?>
								<h6><?php echo $activity->activity_title; ?> FORM RESPONSES </h6>
						<?php endforeach; ?>

            </div>
          </div>
          
          <!-- bulk actions options -->
          <div class="border-bottom border-200 my-3"></div>
          <div class="d-flex align-items-center justify-content-between justify-content-lg-end px-x1">
            <button class="btn btn-sm btn-falcon-default d-xl-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#ticketOffcanvas" aria-controls="ticketOffcanvas">
              <span class="fas fa-filter" data-fa-transform="shrink-4 down-1"></span><span class="ms-1 d-none d-sm-inline-block">Filter</span>
            </button>
            <div class="bg-300 mx-3 d-none d-lg-block d-xl-none" style="width:1px; height:29px"></div>
            <div class="d-none" id="table-ticket-actions">
              <div class="d-flex">
                <select class="form-select form-select-sm" aria-label="Bulk actions">
                  <option selected="">Bulk actions</option>
                  <option value="Approved">Approved</option>
                  <option value="Declined">Declined</option>
                </select>
                <button class="btn btn-falcon-default btn-sm ms-2" type="button">Apply</button>
              </div>
            </div>
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
                <span class="d-none d-sm-inline-block d-xl-none d-xxl-inline-block ms-1">Export</span>
              </button>
              <!-- Dropdown Menu -->
              <div class="dropdown font-sans-serif ms-2">
                <button class="btn btn-falcon-default text-600 btn-sm dropdown-toggle dropdown-caret-none" type="button" id="preview-dropdown" data-bs-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false">
                  <span class="fas fa-ellipsis-h fs-11"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-end border py-2" aria-labelledby="preview-dropdown">
                  <a class="dropdown-item" href="" data-bs-toggle="modal" data-bs-target="#ticketModal">Filter</a>
                  <a class="dropdown-item" href="#!">Export</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item text-danger" href="#!">Remove</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive scrollbar">
				<table class="table table-sm mb-0 fs-10 table-view-tickets">
					<thead class="bg-body-tertiary">
							<tr>
									<th class="py-2 fs-9 pe-2" style="width: 28px;">
											<div class="form-check d-flex align-items-center">
													<input class="form-check-input" id="checkbox-bulk-table-tickets-select" type="checkbox" data-bulk-select='{"body":"table-ticket-body","actions":"table-ticket-actions","replacedElement":"table-ticket-replace-element"}' />
											</div>
									</th>
									<th class="text-800 sort align-middle ps-2" data-sort="client">Student</th>
									<th class="text-800 sort align-middle" data-sort="date" style="min-width:15.625rem">Date Answered</th>
									<th class="text-800 sort align-middle" data-sort="formResponse">Form Response</th>
							</tr>
					</thead>
					<tbody class="list" id="table-ticket-body">
							<?php if (!empty($form_responses)): ?>
									<?php foreach ($form_responses as $response): ?>
											<tr>
													<td class="align-middle fs-9 py-3">
															<div class="form-check mb-0">
																	<input class="form-check-input" type="checkbox" id="table-view-tickets-<?php echo $response['form_id']; ?>" data-bulk-select-row="data-bulk-select-row" />
															</div>
													</td>
													<td class="align-middle client white-space-nowrap pe-3 pe-xxl-4 ps-2">
															<div class="d-flex align-items-center gap-2 position-relative">
																	<div class="avatar avatar-xl">
																			<div class="avatar-name rounded-circle"><span><?php echo substr($response['student_name'], 0, 1); ?></span></div>
																	</div>
																	<h6 class="mb-0"><a class="stretched-link text-900" href="contact-details.html"><?php echo $response['student_name']; ?></a></h6>
															</div>
													</td>
													<td class="align-middle date py-2 pe-4"><a class="fw-semi-bold" href="tickets-preview.html"><?php echo date('M d, Y', strtotime($response['submitted_at'])); ?></a></td>
													<td class="align-middle formResponse fs-9 pe-4">
													<a href="<?= base_url('admin/list-evaluation-answers/view_response/' . $response['evaluation_response_id']); ?>" 
   class="btn btn-sm btn-outline-primary">View Response</a>

													</td>

											</tr>
									<?php endforeach; ?>
							<?php else: ?>
									<tr><td colspan="4">No evaluation responses found for this activity.</td></tr>
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
          <button class="btn btn-sm btn-falcon-default me-1" type="button" title="Previous" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
          <ul class="pagination mb-0"></ul>
          <button class="btn btn-sm btn-falcon-default ms-1" type="button" title="Next" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
        </div>
      </div>
    </div>
  </div>
</div>
