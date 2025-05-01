<div class="card mb-3 mb-lg-0">
	<div class="card-header bg-body-tertiary d-flex justify-content-between">
		<h5 class="mb-0">Summary of Fines</h5>
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
								<th scope="col" class="text-nowrap">Student ID</th>
								<th scope="col">Name</th>
								<th scope="col">Department</th>
								<!-- Dynamic Event Columns will be added here -->
								<th scope="col" class="text-nowrap">Total Fines</th>
								<th scope="col">Status</th>
								<th scope="col">Action</th>
							</tr>
						</thead>
						<tbody class="list" id="table-ticket-body">
							<!-- Dynamic Rows will be generated here -->
						</tbody>

					</table>
					<div class="text-center d-none" id="tickets-table-fallback">
						<span class="fa fa-user-slash fa-2x text-muted"></span>
						<p class="fw-bold fs-8 mt-3">No Student Found</p>
					</div>
				</div>
			</div>

			<script>
				// Convert PHP fines data to JSON
				const finesData = <?= json_encode($fines, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

				// Extract unique events
				const events = [...new Map(finesData.map(fine => [fine.activity_id, {
					id: fine.activity_id,
					name: fine.activity_title
				}])).values()];

				// Group fines by student
				const studentsMap = new Map();

				finesData.forEach(fine => {
					if (!studentsMap.has(fine.student_id)) {
						studentsMap.set(fine.student_id, {
							id: fine.student_id,
							name: `${fine.first_name} ${fine.last_name}`,
							department: fine.dept_name,
							fines: [],
							status: fine.fines_status,
							mode_payment: fine.mode_payment,
							total_fines: fine.total_fines,
							reference: fine.reference_number_admin,
							receipt: fine.receipt,
							changes: fine.remarks
						});
					}

					studentsMap.get(fine.student_id).fines.push({
						eventId: fine.activity_id,
						fine: fine.fines_amount,
						reason: fine.fines_reason,
						event_date: fine.start_date,
						title: fine.activity_title
					});
				});

				const studentsData = Array.from(studentsMap.values());

				// Populate dynamic event headers
				const eventsHeader = document.querySelector('thead tr');
				events.forEach(event => {
					const th = document.createElement('th');
					th.className = 'text-nowrap';
					th.scope = 'col';
					th.innerHTML = event.name;
					eventsHeader.insertBefore(th, eventsHeader.children[eventsHeader.children.length - 3]);
				});

				// Populate student rows
				const tableBody = document.getElementById('table-ticket-body');
				studentsData.forEach(student => {
					const row = document.createElement('tr');

					row.innerHTML = `
            <td class="text-nowrap">${student.id}</td>
            <td class="text-nowrap">${student.name}</td>
            <td class="text-nowrap">${student.department}</td>
        `;

					// Fill in fines per event
					events.forEach(event => {
						const fine = student.fines.find(f => f.eventId === event.id);
						const cell = document.createElement('td');
						cell.className = 'text-nowrap';
						cell.innerHTML = fine ? `₱${fine.fine}` : "₱0";
						row.appendChild(cell);
					});

					// Total Fines
					const totalCell = document.createElement('td');
					totalCell.className = 'text-nowrap';
					totalCell.innerHTML = `₱${student.total_fines}`;
					row.appendChild(totalCell);

					// Status Badge
					const statusCell = document.createElement('td');
					statusCell.className = 'text-nowrap';

					let badgeClass = 'badge rounded-pill badge-subtle-secondary';
					let iconClass = 'fas fa-question-circle';

					switch ((student.status || '').toLowerCase()) {
						case 'paid':
							badgeClass = 'badge rounded-pill badge-subtle-success';
							iconClass = 'fas fa-check-circle'; // ✅ Green check
							break;
						case 'unpaid':
							badgeClass = 'badge rounded-pill badge-subtle-danger';
							iconClass = 'fas fa-times-circle'; // ❌ Red cross
							break;
						case 'pending':
							badgeClass = 'badge rounded-pill badge-subtle-warning';
							iconClass = 'fas fa-clock'; // 🕒 Yellow clock
							break;
						default:
							badgeClass = 'badge rounded-pill badge-subtle-secondary';
							iconClass = 'fas fa-exclamation-circle'; // ⚠️ Default fallback
							break;
					}

					statusCell.innerHTML = `
            <span class="${badgeClass}">
                ${student.status} <i class="${iconClass}"></i>
            </span>
        `;
					row.appendChild(statusCell);

					// Action Dropdown
					const actionCell = document.createElement('td');
					actionCell.innerHTML = `
            <div class="dropdown font-sans-serif position-static">
                <button class="btn btn-link text-600 btn-sm dropdown-toggle btn-reveal" type="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="fas fa-ellipsis-h fs-10"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-end border py-0">
                    <div class="py-2">
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#paymentModal" data-student-id="${student.id}">Confirm Payment</a>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewDetailsModal" data-student-id="${student.id}">View Details</a>
                        <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#editFinesModal" data-student-id="${student.id}">Edit Fines</a>
                    </div>
                </div>
            </div>
        `;
					row.appendChild(actionCell);
					tableBody.appendChild(row);
				});

				document.addEventListener('DOMContentLoaded', () => {
					const paymentModal = document.getElementById('paymentModal');
					const breakdownTable = document.querySelector("#finesBreakdownTable tbody");
					const receiptSection = paymentModal.querySelector('#receiptSection');
					const receiptImage = paymentModal.querySelector('#receiptImage');
					const baseImageUrl = "<?= base_url('assets/receipt/') ?>";

					paymentModal.addEventListener('show.bs.modal', event => {
						const studentId = event.relatedTarget.getAttribute('data-student-id');
						const student = studentsData.find(s => s.id == studentId);

						if (student) {
							paymentModal.querySelector('#modalStudentId').value = student.id;
							paymentModal.querySelector('#modalTotalFines').value = `₱${student.total_fines}`;
							paymentModal.querySelector('#modalModePayment').value = student.mode_payment || '';
							paymentModal.querySelector('#modeOfPayment').value = student.mode_payment || '';

							if (student.receipt) {
								receiptImage.src = baseImageUrl + student.receipt;
								receiptSection.classList.remove('d-none');
							} else {
								receiptImage.src = "";
								receiptSection.classList.add('d-none');
							}

							breakdownTable.innerHTML = '';
							student.fines.forEach((fine, i) => {
								breakdownTable.innerHTML += `
                        <tr>
                            <td>${i + 1}</td>
                            <td>${fine.reason}</td>
                            <td>₱${fine.fine}</td>
                            <td>${fine.title}</td>
                            <td>${fine.event_date}</td>
                        </tr>`;
							});
						}
					});

					paymentModal.addEventListener('hidden.bs.modal', () => {
						['#modalStudentId', '#modalTotalFines', '#modalModePayment', '#modeOfPayment', '#referenceNumber'].forEach(id => {
							paymentModal.querySelector(id).value = '';
						});
						receiptImage.src = "";
						receiptSection.classList.add('d-none');
					});

					const viewDetailsModal = document.getElementById('viewDetailsModal');
					const baseReceiptUrl = "<?= base_url('assets/receipt/') ?>";

					viewDetailsModal.addEventListener('show.bs.modal', event => {
						const studentId = event.relatedTarget.getAttribute('data-student-id');
						const student = studentsData.find(s => s.id == studentId);

						if (student) {
							document.getElementById('viewName').textContent = student.name;
							document.getElementById('viewDepartment').textContent = student.department;
							document.getElementById('viewTotalFines').textContent = `${student.total_fines}`;
							document.getElementById('viewReferenceNumber').textContent = student.reference;

							const receiptImage = document.getElementById('viewReceiptImage');
							const receiptContainer = document.getElementById('viewReceiptImageContainer');

							if (student.receipt) {
								receiptImage.src = baseReceiptUrl + student.receipt;
								receiptImage.alt = `Receipt of ${student.name}`;
								receiptImage.classList.remove('d-none');
								receiptContainer.classList.remove('d-none');
							} else {
								receiptImage.src = "";
								receiptImage.alt = "";
								receiptImage.classList.add('d-none');
								receiptContainer.classList.add('d-none');
							}

							const finesBody = document.getElementById('viewFinesTableBody');
							finesBody.innerHTML = '';
							student.fines.forEach((fine, i) => {
								finesBody.innerHTML += `
                        <tr>
                            <td>${i + 1}</td>
                            <td>${fine.reason}</td>
                            <td>₱${fine.fine}</td>
                            <td>${fine.title}</td>
                            <td>${fine.event_date}</td>
                        </tr>`;
							});
						}
					});
				});

				document.addEventListener('DOMContentLoaded', () => {
					const editFinesModal = document.getElementById('editFinesModal');
					const editFinesTableBody = document.getElementById('editFinesTableBody');
					const editFinesForm = document.getElementById('editFinesForm');

					if (editFinesModal) {
						editFinesModal.addEventListener('show.bs.modal', function(event) {
							const trigger = event.relatedTarget;
							if (!trigger) return;

							const studentId = trigger.getAttribute('data-student-id');
							const student = studentsData.find(s => s.id == studentId);

							if (student) {
								document.getElementById('editStudentId').value = student.id;
								editFinesTableBody.innerHTML = '';

								student.fines.forEach((fine, i) => {
									const row = document.createElement('tr');
									row.innerHTML = `
                                    <td>${i + 1}</td>
<td>${fine.title}</td>
<td>
    <select name="reason[]" class="form-control">
        <option value="Absent" ${fine.reason === 'Absent' ? 'selected' : ''}>Absent</option>
        <option value="Incomplete" ${fine.reason === 'Incomplete' ? 'selected' : ''}>Incomplete</option>
        <option value="Present" ${fine.reason === 'Present' ? 'selected' : ''}>Present</option>
    </select>
</td>
<td><input type="number" step="0.01" name="amount[]" class="form-control" value="${fine.fine}"></td>
<td><input type="text" name="changes[]" class="form-control" value="${fine.changes}"></td>
<input type="hidden" name="event_id[]" value="${fine.eventId}">
                    `;
									editFinesTableBody.appendChild(row);
								});
							}
						});
					}

					if (editFinesForm) {
						editFinesForm.addEventListener('submit', function(e) {
							e.preventDefault();

							const formData = new FormData(this);
							const studentId = formData.get("student_id");
							const reasons = formData.getAll("reason[]");
							const amounts = formData.getAll("amount[]");
							const eventIds = formData.getAll("event_id[]");
							const changes = formData.getAll("changes[]");

							console.log('Edited Fines:', {
								studentId,
								reasons,
								amounts,
								eventIds
							});

							alert('Fines updated (dummy success). You can hook this to a real backend call.');

							const modalInstance = bootstrap.Modal.getInstance(editFinesModal);
							if (modalInstance) {
								modalInstance.hide();
							}
						});
					}
				});
			</script>


			<div class="modal fade" id="editFinesModal" tabindex="-1" aria-labelledby="editFinesModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="editFinesModalLabel">Edit Fines</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<form id="editFinesForm">
							<div class="modal-body">
								<input type="hidden" id="editStudentId" name="student_id">
								<div class="table-responsive">
									<table class="table table-bordered table-sm">
										<thead class="table-light">
											<tr>
												<th class="text-nowrap">#</th>
												<th class="text-nowrap">Event</th>
												<th class="text-nowrap">Reason</th>
												<th class="text-nowrap">Amount (₱)</th>
												<th class="text-nowrap">Reason for Change</th>
											</tr>
										</thead>
										<tbody id="editFinesTableBody">
											<!-- JS fills this -->
										</tbody>
									</table>

								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-primary">Save Changes</button>
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
							</div>
						</form>
					</div>
				</div>
			</div>



			<!-- View Details -->
			<div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">

						<div class="modal-header">
							<h5 class="modal-title" id="viewDetailsModalLabel">View Details</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>

						<div class="modal-body">
							<p><strong>Name:</strong> <span id="viewName"></span></p>
							<p><strong>Department:</strong> <span id="viewDepartment"></span></p>
							<p><strong>Total Fines:</strong> ₱<span id="viewTotalFines"></span></p>
							<p><strong>Reference Number:</strong> <span id="viewReferenceNumber"></span></p>
							<div id="viewReceiptImageContainer" class="mt-3 d-none">
								<p><strong>Receipt:</strong></p>
								<img id="viewReceiptImage" src="" alt="Receipt" class="img-fluid rounded border" style="max-height: 400px;">
							</div>

							<hr>
							<h6>Fines Breakdown</h6>
							<table class="table table-bordered table-sm">
								<thead>
									<tr>
										<th>#</th>
										<th>Reason</th>
										<th>Amount</th>
										<th>Event</th>
										<th>Date</th>
									</tr>
								</thead>
								<tbody id="viewFinesTableBody"></tbody>
							</table>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>

					</div>
				</div>
			</div>





			<!-- Payment Modal -->
			<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<form action="your-payment-handler.php" method="POST" class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="paymentModalLabel">Confirm Payment</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>

						<div class="modal-body">
							<input type="hidden" name="student_id" id="modalStudentId">

							<!-- Total Fines -->
							<div class="mb-3">
								<label for="modalTotalFines" class="form-label">Total Fines</label>
								<input type="text" class="form-control" id="modalTotalFines" name="total_fines" readonly>
							</div>

							<!-- Fines Breakdown Table -->
							<div class="mb-3">
								<label class="form-label">Fines Breakdown</label>
								<div class="table-responsive">
									<table class="table table-bordered table-sm" id="finesBreakdownTable">
										<thead class="table-light">
											<tr>
												<th>#</th>
												<th>Remarks</th>
												<th>Amount</th>
												<th>Activity</th>
												<th>Date</th>
											</tr>
										</thead>
										<tbody>
											<!-- Rows will be inserted via JS -->
										</tbody>
									</table>
								</div>
							</div>

							<!-- Payment Input Fields -->
							<div class="mb-3">
								<label for="modeOfPayment" class="form-label">Mode of Payment</label>
								<select class="form-select" name="mode_of_payment" id="modeOfPayment" required>
									<option value="" id="modalModePayment" selected disabled>Select Mode of Payment</option>
									<option value="Online Payment">Online Payment</option>
									<option value="Cash">Cash</option>
								</select>
							</div>


							<div class="mb-3">
								<label for="referenceNumber" class="form-label">Reference Number</label>
								<input type="text" class="form-control" name="reference_number" id="referenceNumber" placeholder="Enter reference number" required>
							</div>
							<!-- Receipt Preview (Initially Hidden) -->
							<div class="mt-4" id="receiptSection">
								<h6>Payment Receipt</h6>
								<div class="mb-3">
									<img id="receiptImage" src="" alt="Receipt" class="img-fluid mb-3" style="max-height: 150px; width: auto;">
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="submit" class="btn btn-success">Confirm Payment</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						</div>
					</form>
				</div>
			</div>

			<script>
				$(document).ready(function() {
					$('#paymentModal form').submit(function(e) {
						e.preventDefault();
						let formData = $(this).serialize();

						$.ajax({
							url: "<?php echo site_url('admin/fines-payment/confirm'); ?>",
							method: "POST",
							data: formData,
							dataType: "json",
							success: function(response) {
								if (response.status === 'success') {
									Swal.fire({
										icon: 'success',
										title: 'Payment Confirmed',
										text: response.message,
										confirmButtonColor: '#3085d6',
										confirmButtonText: 'OK'
									}).then(() => {
										$('#paymentModal').modal('hide');
										location.reload();
									});
								} else if (response.status === 'processing') {
									Swal.fire({
										icon: 'info',
										title: 'Under Review',
										text: response.message,
										confirmButtonColor: '#3085d6',
										confirmButtonText: 'OK'
									}).then(() => {
										$('#paymentModal').modal('hide');
										location.reload();
									});
								} else {
									Swal.fire({
										icon: 'error',
										title: 'Error',
										text: response.message || 'Something went wrong!',
									});
								}
							},
							error: function() {
								Swal.fire({
									icon: 'error',
									title: 'Server Error',
									text: 'An error occurred while processing the payment.',
								});
							}
						});
					});
				});
			</script>


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