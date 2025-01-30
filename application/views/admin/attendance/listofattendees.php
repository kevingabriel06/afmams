
<div class="container">
    <div class="row">
        <!-- Table -->
        <div class="col-lg-12" id="tableColumn" style="overflow-x: auto;">
            <div class="card" id="customersTable" data-list='{"valueNames":["name","email","phone","address","joined"],"page":10,"pagination":true}'>
                <div class="card-header">
                    <div class="row flex-between-center">
                        <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                            <h5 class="fs-9 mb-0 text-nowrap py-2 py-xl-0">Attendance</h5>
                        </div>
                        <div class="col-8 col-sm-auto text-end ps-2">
                            <div class="d-none" id="table-customers-actions"></div>
                            <div id="table-customers-replace-element">
                              <!-- Filter button -->
                              <button class="btn btn-falcon-default btn-sm mx-2" type="button" id="filterButton">
                                  <span class="fas fa-filter" data-fa-transform="shrink-3 down-2"></span>
                                  <span class="d-none d-sm-inline-block ms-1">Filter</span>
                              </button>
                                <button class="btn btn-falcon-default btn-sm " type="button">
                                    <span class="fas fa-external-link-alt" data-fa-transform="shrink-3 down-2"></span>
                                    <span class="d-none d-sm-inline-block ms-1">Export</span>
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
                                <th class="text-900 align-middle white-space-wrap" data-sort="id-number" style="min-width: 150px;">ID Number</th>
                                <th class="text-900 sort pe-1 align-middle white-space-wrap" data-sort="name">Name</th>
                                <th class="text-900 align-middle white-space-wrap" data-sort="date">Date</th>
                                <th class="text-900 align-middle white-space-wrap" data-sort="activity" style="min-width: 150px;">Activity</th>
                                <th class="text-900 align-middle white-space-wrap" data-sort="status">Status</th>
                                <th class="align-middle no-sort"></th>
                            </tr>
                        </thead>
                        <tbody class="list" id="table-customers-body">
                        
                        @foreach ($attendees as $attendee)  
                            <tr class="btn-reveal-trigger"> 
                                <td class="id-number align-middle white-space-wrap py-2">
                                    <a href="customer-details.html">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-1">
                                                @isset($attendee->user)
                                                <h5 class="mb-0 fs-10">{{ $attendee->user->student_id }}</h5>
                                                @endisset
                                            </div>
                                        </div>
                                    </a>
                                </td>
                                <td class="name align-middle py-2">
                                    {{-- Check if the user relationship exists --}}
                                    @isset($attendee->user)
                                    {{ $attendee->user->name }}
                                    @endisset
                                </td>
                                <td class="date align-middle py-2">
                                    {{-- Check if the activity relationship exists --}}
                                    @isset($attendee->activity)
                                    {{ $attendee->activity->date_start }}
                                    @endisset
                                </td>
                                <td class="activity align-middle white-space-wrap py-2">
                                    {{-- Check if the activity relationship exists --}}
                                    @isset($attendee->activity)
                                    {{ $attendee->activity->title }}
                                    @endisset
                                </td>
                                <td class="status align-middle white-space-wrap py-2">      
                                    @if($attendee->attendance_status === 'Present')
                                        <span class="badge badge rounded-pill d-block p-2 badge-subtle-success">Present<span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span></span>
                                    @else
                                        <span class="badge badge rounded-pill d-block p-2 badge-subtle-warning">Absent<span class="ms-1 fas fa-times" data-fa-transform="shrink-2"></span></span>
                                    @endif
                              </td>

                                <td class="align-middle white-space-nowrap py-2 text-end">
                                  <div class="dropdown font-sans-serif position-static"><button class="btn btn-link text-600 btn-sm dropdown-toggle btn-reveal" type="button" id="customer-dropdown-0" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs-10"></span></button>
                                    <div class="dropdown-menu dropdown-menu-end border py-0" aria-labelledby="customer-dropdown-0">
                                      <div class="py-2"><a class="dropdown-item" href="#!">Edit</a></div>
                                    </div>
                                  </div>
                                </td>
                            </tr>
                            @endforeach
                            
                            
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

        <!-- Filter Options -->
        <div class="col-lg-3 mb-3" id="filterOptions" style="display: none;">
            <div class="card">
                <div class="card-body">
                    <!-- Filter options (hidden by default) -->
                    <div>
                        <!-- Your filter options form goes here -->
                        <form>
                            <div class="mb-2 mt-n2">
                                <label class="mb-1">Contact Created</label>
                                <select class="form-select form-select-sm">
                                    <option>None</option>
                                    <option>Today</option>
                                    <option selected="selected">Last Day</option>
                                    <option>Last 7 days</option>
                                    <option>Last 30 days</option>
                                    <option>Choose a time period</option>
                                </select>
                            </div>
                            <!-- Include other filter options here -->
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterButton = document.getElementById('filterButton');
        const filterOptions = document.getElementById('filterOptions');
        const tableColumn = document.getElementById('tableColumn');

        
        // Add click event listener to the filter button
        filterButton.addEventListener('click', function () {
            // Toggle the visibility of filter options
            filterOptions.style.display = filterOptions.style.display === 'none' ? 'block' : 'none';

            // Adjust the table column size based on the visibility of the filter options
            if (filterOptions.style.display === 'block') {
              tableColumn.style.overflowX = 'auto';
                tableColumn.classList.remove('col-lg-12');
                tableColumn.classList.add('col-lg-9');
            } else {
              tableColumn.style.overflowX = 'initial';
                tableColumn.classList.remove('col-lg-9');
                tableColumn.classList.add('col-lg-12');
            }
        });
    });
</script>
