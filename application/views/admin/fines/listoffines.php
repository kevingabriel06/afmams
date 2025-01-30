
<!-- division -->
<div class="card mb-3" id="ordersTable" data-list='{"valueNames":["order","date","address","status","amount"],"page":10,"pagination":true}'>
<div class="card-header">
    <div class="row flex-between-center">
    <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
        <h4 class="fs-9 mb-0 text-nowrap py-2 py-xl-0">Fines</h4>
    </div>
    <div class="col-8 col-sm-auto ms-auto text-end ps-0">
        <div id="orders-actions"><button class="btn btn-falcon-default btn-sm mx-2" type="button"><span class="fas fa-filter" data-fa-transform="shrink-3 down-2"></span><span class="d-none d-sm-inline-block ms-1">Filter</span></button><button class="btn btn-falcon-default btn-sm" type="button"><span class="fas fa-external-link-alt" data-fa-transform="shrink-3 down-2"></span><span class="d-none d-sm-inline-block ms-1">Export</span></button></div>
    </div>
    </div>
</div>
<div class="card-body p-0">
    <div class="table-responsive scrollbar">
    <table class="table table-sm table-striped fs-10 mb-0 overflow-hidden">
        <thead class="bg-200">
        <tr>
            <th class="text-900 align-middle white-space-wrap" data-sort="id-number" style="min-width: 150px;">ID Number</th>
            <th class="text-900 sort pe-1 align-middle white-space-wrap" data-sort="name">Name</th>
            <th class="text-900 align-middle white-space-wrap text-center" data-sort="status">Status</th>
            <th class="text-900 align-middle white-space-wrap text-center" data-sort="amount">Amount</th>
            <th class="align-middle no-sort"></th>
        </tr>
        </thead>
        <tbody class="list" id="table-customers-body">
        <tr class="btn-reveal-trigger">
            <td class="id-number align-middle white-space-nowrap py-2"><a href="customer-details.html">
                <div class="d-flex d-flex align-items-center">
                <div class="flex-1">
                    <h5 class="mb-0 fs-10"><strong> 11-00001 </strong></h5>
                </div>
                </div>
            </a></td>
            <td class="name align-middle py-2"><a href="#">Juan Dela Cruz</a></td>
            <td class="status py-2 align-middle text-center fs-9 white-space-wrap"><span class="badge badge rounded-pill d-block badge-subtle-success">Paid<span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span></span></td>
            <td class="amount py-2 align-middle text-center fs-9 fw-medium">₱ 99</td>
            <td class="align-middle white-space-nowrap py-2 text-end">
            <div class="dropdown font-sans-serif position-static"><button class="btn btn-link text-600 btn-sm dropdown-toggle btn-reveal" type="button" id="customer-dropdown-0" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs-10"></span></button>
                <div class="dropdown-menu dropdown-menu-end border py-0" aria-labelledby="customer-dropdown-0">
                <div class="py-2">
                    <a class="dropdown-item" href="#!">Paid</a>
                    <a class="dropdown-item" href="#!">Unpaid</a>
                </div>
                </div>
            </div>
            </td>
        </tr>
        
        <tr class="btn-reveal-trigger">
            <td class="id-number align-middle white-space-nowrap py-2"><a href="customer-details.html">
                <div class="d-flex d-flex align-items-center">
                <div class="flex-1">
                    <h5 class="mb-0 fs-10"><strong> 12-70001 </strong> </h5>
                </div>
                </div>
            </a></td>
            <td class="name align-middle py-2"><a href="#">Pedro Gomez</a></td>
            <td class="status py-2 align-middle text-center fs-9 white-space-wrap"><span class="badge badge rounded-pill d-block badge-subtle-warning">Unpaid<span class="ms-1 fas fa-times" data-fa-transform="shrink-2"></span></span></td>
            <td class="amount py-2 align-middle text-center fs-9 fw-medium">₱ 99</td>
            <td class="align-middle white-space-nowrap py-2 text-end">
            <div class="dropdown font-sans-serif position-static"><button class="btn btn-link text-600 btn-sm dropdown-toggle btn-reveal" type="button" id="customer-dropdown-0" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs-10"></span></button>
                <div class="dropdown-menu dropdown-menu-end border py-0" aria-labelledby="customer-dropdown-0">
                <div class="py-2">
                    <a class="dropdown-item" href="#!">Paid</a>
                    <a class="dropdown-item" href="#!">Unpaid</a>
                </div>
                </div>
            </div>
            </td>
        </tr>
        <!-- dito ifefetch ang data from data base -->
        </tbody>
    </table>

    </div>
</div>
<div class="card-footer">
    <div class="d-flex align-items-center justify-content-center"><button class="btn btn-sm btn-falcon-default me-1" type="button" title="Previous" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
    <ul class="pagination mb-0"></ul><button class="btn btn-sm btn-falcon-default ms-1" type="button" title="Next" data-list-pagination="next"><span class="fas fa-chevron-right"> </span></button>
    </div>
</div>
</div>
