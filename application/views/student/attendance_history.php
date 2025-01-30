
<div class="container">
    <div class="row">
        <!-- Table -->
        <div class="col-lg-12" id="tableColumn" style="overflow-x: auto;">
            <div class="card" id="customersTable" data-list='{"valueNames":["name","email","phone","address","joined"],"page":10,"pagination":true}'>
                <div class="card-header">
                    <div class="row flex-between-center">
                        <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                            <h5 class="fs-9 mb-0 text-nowrap py-2 py-xl-0">Attendance History</h5>
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
                            <th style="border: 1px solid #ddd;">Activity</th>
                            <th style="border: 1px solid #ddd;">Organizer</th>
                            <th style="border: 1px solid #ddd;">Date</th>
                            <th style="border: 1px solid #ddd;">Status</th>
                        </tr>
                        </thead>
                            <tbody class="list" id="table-customers-body">
                                <?php foreach ($attendances as $attendance): ?>
                                    <tr data-attendance-id="<?= isset($attendance->id) ? $attendance->id : 'N/A' ?>"
                                        data-am-in="<?= $attendance->AM_IN ?>"
                                        data-am-out="<?= $attendance->AM_OUT ?>"
                                        data-pm-in="<?= $attendance->PM_IN ?>"
                                        data-pm-out="<?= $attendance->PM_OUT ?>"
                                        data-bs-toggle="modal" data-bs-target="#attendanceModal">
                                        <!-- Display the activity -->
                                        <td style="border: 1px solid #ddd; word-wrap: break-word;"><?= $attendance->Activity ?></td>

                                        <!-- Display the organizer -->
                                        <td style="border: 1px solid #ddd; word-wrap: break-word;"><?= $attendance->organizer ?></td>

                                        <!-- Display the date -->
                                        <td style="border: 1px solid #ddd;"><?= $attendance->Date ?></td>

                                        <!-- Display the attendance status -->
                                        <td style="border: 1px solid #ddd;"><?= $attendance->Status ?></td>
                                    </tr>
                                <?php endforeach; ?>
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

<!-- Floating Form Modal (For Viewing Only) -->
<div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attendanceModalLabel">Attendance Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Table inside Modal -->
                <table class="table-container" style="width: 100%; table-layout: fixed; border: 1px solid #ddd;">
                    <thead class="bg-200">
                        <tr>
                            <th style="border: 1px solid #ddd;">AM IN</th>
                            <th style="border: 1px solid #ddd;">AM OUT</th>
                            <th style="border: 1px solid #ddd;">PM IN</th>
                            <th style="border: 1px solid #ddd;">PM OUT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border: 1px solid #ddd;" id="modalAMIN"></td>
                            <td style="border: 1px solid #ddd;" id="modalAMOUT"></td>
                            <td style="border: 1px solid #ddd;" id="modalPMIN"></td>
                            <td style="border: 1px solid #ddd;" id="modalPMOUT"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Event listener for row click to populate modal
const tableRows = document.querySelectorAll('#attendanceTable tr');
const modalAMIN = document.getElementById('modalAMIN');
const modalAMOUT = document.getElementById('modalAMOUT');
const modalPMIN = document.getElementById('modalPMIN');
const modalPMOUT = document.getElementById('modalPMOUT');

tableRows.forEach(row => {
    row.addEventListener('click', function() {
        // Get the data attributes
        const amIn = this.getAttribute('data-am-in');
        const amOut = this.getAttribute('data-am-out');
        const pmIn = this.getAttribute('data-pm-in');
        const pmOut = this.getAttribute('data-pm-out');
        
        // Set the values in the modal
        modalAMIN.textContent = amIn;
        modalAMOUT.textContent = amOut;
        modalPMIN.textContent = pmIn;
        modalPMOUT.textContent = pmOut;
    });
});
</script>

<script>
    const student_id = '<?= $this->uri->segment(3); ?>'; // Get student_id from URL
    const searchBar = document.getElementById('searchBar');
    const filterStatus = document.getElementById('filterStatus');
    const attendanceTable = document.getElementById('attendanceTable');

    // Fetch filtered attendance data
    function fetchFilteredData() {
        const search = searchBar.value;
        const status = filterStatus.value;

        fetch(`<?= base_url('attendance/get_filtered_attendance') ?>?student_id=${student_id}&search=${search}&status=${status}`)
            .then(response => response.json())
            .then(data => {
                attendanceTable.innerHTML = '';
                data.forEach(attendance => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td style="border: 1px solid #ddd; word-wrap: break-word;">${attendance.activity}</td>
                        <td style="border: 1px solid #ddd; word-wrap: break-word;">${attendance.category}</td>
                        <td style="border: 1px solid #ddd;">${attendance.date}</td>
                        <td style="border: 1px solid #ddd;">${attendance.am_in}</td>
                        <td style="border: 1px solid #ddd;">${attendance.am_out}</td>
                        <td style="border: 1px solid #ddd;">${attendance.pm_in}</td>
                        <td style="border: 1px solid #ddd;">${attendance.pm_out}</td>
                        <td style="border: 1px solid #ddd;">${attendance.attendance_status}</td>
                    `;
                    attendanceTable.appendChild(row);
                });
            });
    }

    // Event listeners for search and filter
    searchBar.addEventListener('input', fetchFilteredData);
    filterStatus.addEventListener('change', fetchFilteredData);

    // Initial fetch
    fetchFilteredData();
</script>