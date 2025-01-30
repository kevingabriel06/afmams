    <div class="row g-3">
            <div class="col-xxl-9 col-xl-8">
              <div class="card">
                <div class="card-header d-flex flex-between-center"><a class="btn btn-falcon-default btn-sm" href="<?php echo site_url('list-excuse-letter'); ?>">
  <span class="fas fa-arrow-left"></span>
</a>
                  <div class="d-flex">
                    <!-- approved button -->
                    </button><button class="btn btn-falcon-success btn-sm mx-2" type="button"><span class="fas fa-check" data-fa-transform="shrink-2 down-2"></span><span class="d-none d-md-inline-block ms-1">Approved</span></button>
                    <!-- disapproved button -->
                    <button class="btn btn-falcon-danger btn-sm" type="button"><span class="fas fa-ban" data-fa-transform="shrink-2 down-1"></span><span class="d-none d-md-inline-block ms-1">Disapproved</span></button>
                  </div>
                </div>
              </div>
              <div class="card mt-3">
                <div class="card-header bg-body-tertiary">
                  <h5><span class="fas fa-envelope me-2"></span><span>Title dito ng email or message</span></h5>
                </div>
                <div class="card-body">
                  <div class="d-md-flex d-xl-inline-block d-xxl-flex align-items-center justify-content-between mb-x1">
                    <div class="d-flex align-items-center gap-2"><a href="contact-details.html">
                        <div class="avatar avatar-2xl">
                          <img class="rounded-circle" src="<?php echo base_url();?>assets/img/team/1-thumb.png" alt="" />
                        </div>
                      </a>
                      <!-- details ng name at date -->
                      <p class="mb-0"><a class="fw-semi-bold mb-0 text-800" href="contact-details.html">Pangalan ng sender</a>
                      <span class="mb-0 fs-10 d-block text-500 fw-semi-bold ">01 March, 2020<span class="mx-1">|</span><span class="fst-italic">8:30 AM (1 Day ago)</span></></p>
                    </div>
                  </div>
                  <div>
                    <!-- dito ang body ng message -->
                    <h6 class="mb-3 fw-semi-bold text-1000">Improve in A purposed Manner</h6>
                    <p>Hi</p>
                    <p>The television I ordered from your site was delivered with a cracked screen. I need some help with a refund or a replacement.</p>
                    <p>Here is the order number FD07062010</p>
                    <p class="mb-0">Thanks</p>
                    <p class="mb-0">Emma Watson</p>
                    <div class="p-x1 bg-body-tertiary rounded-3 mt-3">
                      <div class="d-inline-flex flex-column">
                        <div class="border p-2 rounded-3 d-flex bg-white dark__bg-1000 fs-10 mb-2"><span class="fs-8 far fa-image"></span><span class="ms-2 me-3">broken_tv_solve.jpg (873kb)</span><a class="text-300 ms-auto" href="#!" data-bs-toggle="tooltip" data-bs-placement="right" title="Download"><span class="fas fa-arrow-down"></span></a></div>
                        <div class="border p-2 rounded-3 d-flex bg-white dark__bg-1000 fs-10"><span class="fs-8 fas fa-file-archive"></span><span class="ms-2 me-3">broken_tv_solve.zip (342kb)</span><a class="text-300 ms-auto" href="#!" data-bs-toggle="tooltip" data-bs-placement="right" title="Download"><span class="fas fa-arrow-down"></span></a></div>
                      </div>
                      <hr class="my-x1" />
                      <div class="row flex-between-center gx-4 gy-2">
                        <div class="col-auto">
                          <p class="fs-10 text-1000 mb-sm-0 font-sans-serif fw-medium mb-0"><span class="fas fa-link me-2"></span>2 files attached</p>
                        </div>
                        <div class="col-auto"><button class="btn btn-falcon-default btn-sm"><span class="fas fa-file-download me-2"></span>Download all</button></div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- this part is for the reply -->
                <div class="collapse transition-none" id="previewMailForm">
                  <h5 class="mb-0 p-x1 bg-body-tertiary">Reply</h5>
                  <div class="border border-top-0 border-200"><input class="form-control border-0 rounded-0 outline-none px-x1" id="email-to" type="email" aria-describedby="email-from" placeholder="From" value="mike@support.com" /></div>
                  <div class="border border-top-0 border-200"><input class="form-control border-0 rounded-0 outline-none px-x1" id="email-from" type="email" aria-describedby="email-to" placeholder="To" value="emma@watson.com" /></div>
                  <div class="border border-y-0 border-200"><input class="form-control border-0 rounded-0 outline-none px-x1" id="email-subject" type="text" aria-describedby="email-subject" placeholder="Subject" /><textarea class="tinymce d-none" data-tinymce="data-tinymce" name="content"></textarea></div>
                  <div class="px-x1 py-3 bg-body-tertiary">
                    <div class="d-inline-flex flex-column">
                      <div class="border p-2 rounded-3 d-flex bg-white dark__bg-1000 fs-10 mb-2"><span class="fs-8 far fa-image"></span><span class="ms-2 me-3">broken_tv_solve.jpg (873kb)</span><a class="text-300 ms-auto" href="#!" data-bs-toggle="tooltip" data-bs-placement="right" title="Detach"><span class="fas fa-times"></span></a></div>
                      <div class="border p-2 rounded-3 d-flex bg-white dark__bg-1000 fs-10"><span class="fs-8 fas fa-file-archive"></span><span class="ms-2 me-3">broken_tv_solve.zip (342kb)</span><a class="text-300 ms-auto" href="#!" data-bs-toggle="tooltip" data-bs-placement="right" title="Detach"><span class="fas fa-times"></span></a></div>
                    </div>
                  </div>
                  <div class="d-flex align-items-center justify-content-between px-x1 py-3">
                    <div class="d-flex align-items-center"><button class="btn btn-primary btn-sm px-4 me-2" type="submit">Send</button><input class="d-none" id="email-attachment" type="file" /><label class="me-2 btn btn-tertiary btn-sm mb-0 cursor-pointer" for="email-attachment" data-bs-toggle="tooltip" data-bs-placement="top" title="Attach files"><span class="fas fa-paperclip fs-8" data-fa-transform="down-2"></span></label><input class="d-none" id="email-image" type="file" accept="image/*" /><label class="btn btn-tertiary btn-sm mb-0 cursor-pointer" for="email-image" data-bs-toggle="tooltip" data-bs-placement="top" title="Attach images"><span class="fas fa-image fs-8" data-fa-transform="down-2"></span></label></div>
                    <div class="d-flex align-items-center">
                      <div class="dropdown font-sans-serif me-2 btn-reveal-trigger"><button class="btn btn-link text-600 btn-sm dropdown-toggle btn-reveal dropdown-caret-none" id="email-options" type="button" data-bs-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-v" data-fa-transform="down-2"></span></button>
                        <div class="dropdown-menu dropdown-menu-end border py-2" aria-labelledby="email-options"><a class="dropdown-item" href="#!">Print</a><a class="dropdown-item" href="#!">Check spelling</a><a class="dropdown-item" href="#!">Plain text mode</a>
                          <div class="dropdown-divider"></div><a class="dropdown-item" href="#!">Archive</a>
                        </div>
                      </div><button class="btn btn-tertiary btn-sm" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" data-dismiss="collapse"><span class="fas fa-trash"></span></button>
                    </div>
                  </div>
                </div>

                <div class="card-footer bg-body-tertiary" id="preview-footer"><button class="btn btn-falcon-default btn-sm fs-10" type="button" data-bs-toggle="collapse" data-bs-target="#previewMailForm" aria-expanded="false" aria-controls="previewMailForm"><span class="fas fa-reply"></span><span class="d-none d-sm-inline-block ms-1">Reply</span></button><button class="btn btn-falcon-default btn-sm fs-10 mx-2" type="button" data-bs-toggle="collapse" data-bs-target="#previewMailForm" aria-expanded="false" aria-controls="previewMailForm"><span class="fas fa-location-arrow"></span><span class="d-none d-sm-inline-block ms-1">Forward</span></button><button class="btn btn-falcon-default btn-sm fs-10" type="button" data-bs-toggle="collapse" data-bs-target="#previewMailForm" aria-expanded="false" aria-controls="previewMailForm"><span class="fas fa-file-alt"></span><span class="d-none d-sm-inline-block ms-1">Add Note</span></button></div>
              </div>
            </div>

            <div class="col-xxl-3 col-xl-4">
              <div class="row g-3 g-xl-0 sticky-sidebar top-navbar-height">
                <div class="col-12">
                  <div class="card">
                    <div class="card-header d-flex flex-between-center py-2 bg-body-tertiary">
                      <h6 class="mb-0">Contact Information</h6>
                    </div>
                    <div class="card-body">
                      <div class="row g-0 border-bottom pb-x1 mb-x1 align-items-sm-center align-items-xl-start">
                        <div class="col-12 col-sm-auto col-xl-12 me-sm-3 me-xl-0">
                          <div class="avatar avatar-3xl">
                            <img class="rounded-circle" src="<?php echo base_url()?>assets/img/team/1.jpg" alt="" />
                          </div>
                        </div>
                        <div class="col-12 col-sm-auto col-xl-12">
                          <p class="fw-semi-bold text-800 mb-0">Emma Watson</p><a class="btn btn-link btn-sm p-0 fe-medium fs-10" href="contact-details.html">View more details</a>
                        </div>
                      </div>
                      <div class="row g-0 justify-content-lg-between">
                        <div class="col-auto col-md-6 col-lg-auto">
                          <div class="row">
                            <div class="col-md-auto mb-4 mb-md-0 mb-xl-4">
                              <h6 class="mb-1">Email</h6><a class="fs-10" href="mailto:mattrogers@gmail.com">mattrogers@gmail.com</a>
                            </div>
                            <div class="col-md-auto mb-4 mb-md-0 mb-xl-4">
                              <h6 class="mb-1">Phone Number</h6><a class="fs-10" href="tel:+6(855)747677">+6(855) 747 677</a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>