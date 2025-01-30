<div class="row gx-3">
            <div class="col-xxl-10 col-xl-12">
              <div class="card" id="ticketsTable" data-list='{"valueNames":["client","subject","status","priority","agent"],"page":11,"pagination":true,"fallback":"tickets-table-fallback"}'>
                <div class="card-header border-bottom border-200 px-0">
                  <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                      <div class="col-auto pe-0">
                        <h6 class="mb-0">$Activity Name Excuse Form</h6>
                      </div>
                    </div>

                    <!-- bulk actions options -->
                    <div class="border-bottom border-200 my-3"></div>
                    <div class="d-flex align-items-center justify-content-between justify-content-lg-end px-x1"><button class="btn btn-sm btn-falcon-default d-xl-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#ticketOffcanvas" aria-controls="ticketOffcanvas"><span class="fas fa-filter" data-fa-transform="shrink-4 down-1"></span><span class="ms-1 d-none d-sm-inline-block">Filter</span></button>
                      <div class="bg-300 mx-3 d-none d-lg-block d-xl-none" style="width:1px; height:29px"></div>
                      <div class="d-none" id="table-ticket-actions">
                        <div class="d-flex"><select class="form-select form-select-sm" aria-label="Bulk actions">
                            <option selected="">Bulk actions</option>
                            <option value="Approved">Approved</option>
                            <option value="Declined">Declined</option>
                          </select><button class="btn btn-falcon-default btn-sm ms-2" type="button">Apply</button></div>
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
                            <div class="form-check d-flex align-items-center"><input class="form-check-input" id="checkbox-bulk-table-tickets-select" type="checkbox" data-bulk-select='{"body":"table-ticket-body","actions":"table-ticket-actions","replacedElement":"table-ticket-replace-element"}' /></div>
                          </th>
                          <th class="text-800 sort align-middle ps-2" data-sort="client">Student</th>
                          <th class="text-800 sort align-middle" data-sort="subject" style="min-width:15.625rem">Subject</th>
                          <th class="text-800 sort align-middle" data-sort="status">Status</th>
                          <th class="text-800 sort align-middle" data-sort="event">Event</th>
                        </tr>
                      </thead>
                      <tbody class="list" id="table-ticket-body">
                        <!-- approved excuse letter -->
                        <tr>
                          <td class="align-middle fs-9 py-3">
                            <div class="form-check mb-0"><input class="form-check-input" type="checkbox" id="table-view-tickets-0" data-bulk-select-row="data-bulk-select-row" /></div>
                          </td>
                          <td class="align-middle client white-space-nowrap pe-3 pe-xxl-4 ps-2">
                            <div class="d-flex align-items-center gap-2 position-relative">
                              <div class="avatar avatar-xl">
                                <div class="avatar-name rounded-circle"><span>EW</span></div>
                              </div>
                              <h6 class="mb-0"><a class="stretched-link text-900" href="contact-details.html">Emma Watson</a></h6>
                            </div>
                          </td>
                          <td class="align-middle subject py-2 pe-4"><a class="fw-semi-bold" href="<?php echo site_url('admin/review-excuse-letter');?>">Synapse Design #1125</a></td>
                          <td class="align-middle status fs-9 pe-4"><small class="badge rounded badge-subtle-success false">Approved</small></td>
                          <td class="align-middle client white-space-nowrap pe-3 pe-xxl-4 ps-2">
                            <div class="d-flex align-items-center gap-2 position-relative">
                              <h6 class="mb-0"><a class="stretched-link text-900" href="contact-details.html">Emma Watson</a></h6>
                            </div>
                          </td>
                        </tr>
                        <!-- dissapproved excuse letter -->
                        <tr>
                          <td class="align-middle fs-9 py-3">
                            <div class="form-check mb-0"><input class="form-check-input" type="checkbox" id="table-view-tickets-1" data-bulk-select-row="data-bulk-select-row" /></div>
                          </td>
                          <td class="align-middle client white-space-nowrap pe-3 pe-xxl-4 ps-2">
                            <div class="d-flex align-items-center gap-2 position-relative">
                              <div class="avatar avatar-xl">
                                <div class="avatar-name rounded-circle"><span>L</span></div>
                              </div>
                              <h6 class="mb-0"><a class="stretched-link text-900" href="contact-details.html">Luke</a></h6>
                            </div>
                          </td>
                          <td class="align-middle subject py-2 pe-4"><a class="fw-semi-bold" href="tickets-preview.html">Change of refund my last buy | Order #125631</a></td>
                          <td class="align-middle status fs-9 pe-4"><small class="badge rounded badge-subtle-danger false">Disapproved</small></td>
                          <td class="align-middle client white-space-nowrap pe-3 pe-xxl-4 ps-2">
                            <div class="d-flex align-items-center gap-2 position-relative">
                              <h6 class="mb-0"><a class="stretched-link text-900" href="contact-details.html">Emma Watson</a></h6>
                            </div>
                          </td>
                        </tr>
                        <!-- pending excuse letter -->
                        <tr>
                          <td class="align-middle fs-9 py-3">
                            <div class="form-check mb-0"><input class="form-check-input" type="checkbox" id="table-view-tickets-3" data-bulk-select-row="data-bulk-select-row" /></div>
                          </td>
                          <td class="align-middle client white-space-nowrap pe-3 pe-xxl-4 ps-2">
                            <div class="d-flex align-items-center gap-2 position-relative">
                              <div class="avatar avatar-xl">
                                <div class="avatar-name rounded-circle"><span>PG</span></div>
                              </div>
                              <h6 class="mb-0"><a class="stretched-link text-900" href="contact-details.html">Peter Gill</a></h6>
                            </div>
                          </td>
                          <td class="align-middle subject py-2 pe-4"><a class="fw-semi-bold" href="tickets-preview.html">I need your help #2256</a></td>
                          <td class="align-middle status fs-9 pe-4"><small class="badge rounded badge-subtle-info false">Pending</small></td>
                          <td class="align-middle client white-space-nowrap pe-3 pe-xxl-4 ps-2">
                            <div class="d-flex align-items-center gap-2 position-relative">
                              <h6 class="mb-0"><a class="stretched-link text-900" href="contact-details.html">Emma Watson</a></h6>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="text-center d-none" id="tickets-table-fallback">
                      <p class="fw-bold fs-8 mt-3">No ticket found</p>
                    </div>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="d-flex justify-content-center"><button class="btn btn-sm btn-falcon-default me-1" type="button" title="Previous" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                    <ul class="pagination mb-0"></ul><button class="btn btn-sm btn-falcon-default ms-1" type="button" title="Next" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="ticketModalLabel">Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form>
                      <div class="mb-2 mt-n2">
                        <label class="mb-1">Status</label>
                        <select class="form-select form-select-sm">
                          <option>None</option>
                          <option>Approved</option>
                          <option>Disapproved</option>
                          <option>Pending</option>
                        </select>
                      </div>
                      <div class="mb-2">
                        <label class="mb-1 mt-2">Event</label>
                        <select class="form-select form-select-sm">
                          <option>None</option>
                          <option>Email</option>
                          <option>Phone</option>
                          <option>Forum</option>
                          <option selected="selected">Facebook</option>
                          <option>Twitter</option>
                          <option>Chat</option>
                          <option>Whatsapp</option>
                          <option>Portal</option>
                          <option>Bots</option>
                          <option>External Email</option>
                          <option>Ecommerce</option>
                          <option>Feedback Widget</option>
                        </select>
                      </div>
                    </form>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Update</button>
                  </div>
                </div>
              </div>
            </div>


          </div>