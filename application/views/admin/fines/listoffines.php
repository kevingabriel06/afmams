<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<div class="card mb-3 mb-lg-0">
	<div class="card-header bg-body-tertiary d-flex justify-content-between align-items-center">
		<h5 class="mb-0">Summary of Fines</h5>
		<!-- Record Cash Payment Button -->
		<button
			class="btn btn-sm btn-outline-primary"
			data-bs-toggle="modal"
			data-bs-target="#cashPaymentModal">
			<i class="fas fa-plus"></i> Record Cash Payment
		</button>
	</div>
</div>


<!-- Record Cash Payment Modal -->
<div class="modal fade" id="cashPaymentModal" tabindex="-1" aria-labelledby="cashPaymentModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<form id="cashPaymentForm" method="POST" action="<?= base_url('AdminController/record_cash_payment') ?>">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Record Cash Payment</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<!-- Student ID Input -->
					<div class="mb-3">
						<label for="student_id" class="form-label">Student ID</label>
						<input type="text" name="student_id" id="student_id" class="form-control" placeholder="Type the student_id here..." required>
					</div>

					<!-- Add this hidden input to your modal form -->
					<input type="hidden" id="summary_id" name="summary_id">


					<!-- Student Info Preview -->
					<div class="mb-3 d-flex align-items-start" id="studentInfo" style="display: none;">
						<!-- Left side: Image only -->
						<div style="min-width: 90px;">
							<img
								id="studentProfilePic"
								src="<?= base_url('assets/profile/default.jpg') ?>"
								alt="Profile"
								class="rounded"
								style="width: 90px; height: 90px; object-fit: cover; display: block;" />
						</div>

						<!-- Right side: Inputs stacked vertically -->
						<div style="flex-grow: 1; margin-left: 8px;">
							<!-- Student Name input with placeholder -->
							<div class="mb-2" style="margin-bottom: 8px;">
								<input
									type="text"
									id="studentFullNameInput"
									class="form-control"
									readonly
									placeholder="Student Name"
									style="padding: 4px 8px; font-size: 0.9rem; width: 100%;" />
							</div>

							<!-- Year Level input with placeholder -->
							<div class="mb-2" style="margin-bottom: 8px;">
								<input
									type="text"
									id="studentYearLevelInput"
									class="form-control"
									readonly
									placeholder="Year Level"
									style="padding: 4px 8px; font-size: 0.9rem; width: 100%;" />
							</div>
						</div>
					</div>

					<!-- Department below the student info container, full width with label -->
					<div class="mb-3">
						<label for="studentDeptNameInput" class="form-label" style="font-size: 0.9rem;">Department</label>
						<input
							type="text"
							id="studentDeptNameInput"
							class="form-control"
							readonly
							placeholder="Department"
							style="padding: 4px 8px; font-size: 0.9rem; width: 100%;" />
					</div>







					<!-- Amount (readonly) -->
					<div class="mb-3">
						<label for="total_fines" class="form-label">Total Fines</label>
						<input type="text" name="total_fines" id="total_fines" class="form-control" readonly placeholder="Click to view student fines after typing the student_id">
					</div>


					<!-- Payment Mode -->
					<div class="mb-3">
						<label for="mode_payment" class="form-label">Mode of Payment</label>
						<input type="text" name="mode_payment" id="mode_payment" class="form-control" value="Cash" readonly>
					</div>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Submit Payment</button>
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
				</div>
			</div>
		</form>
	</div>
</div>



<script>
	document.getElementById('cashPaymentForm').addEventListener('submit', function(e) {
		e.preventDefault(); // prevent form submission

		Swal.fire({
			title: 'Confirm Payment',
			text: "Are you sure you want to record this cash payment?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, submit it!'
		}).then((result) => {
			if (result.isConfirmed) {
				this.submit(); // submit form if confirmed
			}
		});
	});
</script>

<!-- retrive total fines of the searched student -->
<!-- <script>
	document.getElementById('student_id').addEventListener('change', function() {
		const studentId = this.value;
		if (!studentId) return;

		fetch('<?= base_url('AdminController/get_student_total_fines') ?>', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-Requested-With': 'XMLHttpRequest'
				},
				body: JSON.stringify({
					student_id: studentId
				})
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					document.getElementById('total_fines').value = data.total_fines;
					document.getElementById('summary_id').value = data.summary_id;

					Swal.fire({
						icon: 'success',
						title: 'Fines Found',
						text: `Total unpaid fines: ₱${parseFloat(data.total_fines).toFixed(2)}`,
						timer: 2500,
						showConfirmButton: false
					});
				} else {
					document.getElementById('total_fines').value = '';
					document.getElementById('summary_id').value = '';

					Swal.fire({
						icon: 'warning',
						title: 'Notice',
						text: data.message || 'Unable to retrieve fines.',
						timer: 3000,
						showConfirmButton: false
					});
				}
			})

			.catch(error => {
				console.error('Error fetching total fines:', error);
				document.getElementById('total_fines').value = '';
				document.getElementById('summary_id').value = '';

				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'Something went wrong while retrieving fines.',
					confirmButtonText: 'OK'
				});
			});
	});
</script> -->


<script>
	document.getElementById('student_id').addEventListener('change', function() {
		const studentId = this.value;
		if (!studentId) return;

		fetch('<?= base_url('AdminController/get_student_total_fines') ?>', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-Requested-With': 'XMLHttpRequest'
				},
				body: JSON.stringify({
					student_id: studentId
				})
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					document.getElementById('total_fines').value = data.total_fines;
					document.getElementById('summary_id').value = data.summary_id;

					// Show student details
					const fullName = `${data.last_name}, ${data.first_name} ${data.middle_name ?? ''}`.trim();
					document.getElementById('studentFullNameInput').value = fullName;
					document.getElementById('studentDeptNameInput').value = data.dept_name ?? 'No department';

					document.getElementById('studentYearLevelInput').value = data.year_level ?? 'N/A'; // <-- here

					const profilePic = data.profile_pic ?
						`<?= base_url('assets/profile/') ?>${data.profile_pic}` :
						`<?= base_url('assets/profile/default.jpg') ?>`;

					document.getElementById('studentProfilePic').src = profilePic;

					document.getElementById('studentInfo').style.display = 'flex';

					Swal.fire({
						icon: 'success',
						title: 'Fines Found',
						text: `Total unpaid fines: ₱${parseFloat(data.total_fines).toFixed(2)}`,
						timer: 2500,
						showConfirmButton: false
					});
				} else {
					// Clear and hide
					document.getElementById('total_fines').value = '';
					document.getElementById('summary_id').value = '';
					document.getElementById('studentInfo').style.display = 'none';

					Swal.fire({
						icon: 'warning',
						title: 'Notice',
						text: data.message || 'Unable to retrieve fines.',
						timer: 3000,
						showConfirmButton: false
					});
				}
			})

			.catch(error => {
				console.error('Error fetching total fines:', error);
				document.getElementById('total_fines').value = '';
				document.getElementById('summary_id').value = '';
				document.getElementById('studentInfo').style.display = 'none';

				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'Something went wrong while retrieving fines.',
					confirmButtonText: 'OK'
				});
			});
	});
</script>




<?php if ($this->session->flashdata('swal_success')): ?>
	<script>
		Swal.fire({
			icon: 'success',
			title: 'Success!',
			text: '<?= $this->session->flashdata('swal_success') ?>',
			timer: 3000,
			showConfirmButton: false
		});
	</script>
<?php endif; ?>

<!-- Space Between Sections -->
<div class="space" style="height: 20px;"></div> <!-- Adds spacing between sections -->


<div class="row gx-3">
	<div class="col-xxl-10 col-xl-12">
		<div class="card" id="ticketsTable"
			data-list='{
				"valueNames":["name","status"],
				"page":11,
				"pagination":true,
				"fallback":"tickets-table-fallback"
			}'>

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
							<button class="btn btn-sm btn-falcon-default ms-2" type="button" onclick="exportFinesPDF()">
								<span class="fas fa-download"></span>
							</button>


							<script>
								function exportFinesPDF() {
									const status = document.getElementById('status-filter').value.trim();
									const department = document.getElementById('department-filter').value.trim();
									const year = document.getElementById('year-filter').value.trim();

									// Prepare data to send
									const data = {
										status: status,
										department: department,
										year_level: year
									};

									// AJAX POST to check data availability first
									$.post('<?= base_url("AdminController/check_fines_data") ?>', data, function(response) {
										if (response.hasData) {
											// If data exists, open PDF in new tab with filters
											const url = new URL("<?= base_url('AdminController/export_fines_pdf') ?>");
											if (department) url.searchParams.append('department', department);
											if (year) url.searchParams.append('year_level', year);
											if (status) url.searchParams.append('status', status); // ✅ This line is essential

											window.open(url.toString(), '_blank');
										} else {
											// Show SweetAlert if no data
											Swal.fire({
												icon: 'info',
												title: 'No data found',
												text: 'No fine data available to export.',
												confirmButtonText: 'OK'
											});
										}
									}, 'json');
								}
							</script>




							<!-- FILTER BUTTON -->
							<button class="btn btn-sm btn-falcon-default ms-2" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
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
								<th scope="col" class="text-nowrap">Year Level</th>
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
</div>


<!-- MODAL FILTER -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="filterModalLabel">Filter Students</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<!-- Status Filter -->
				<div class="mb-3">
					<label for="status-filter" class="form-label">Status</label>
					<select id="status-filter" class="form-select">
						<option value="">All</option>
						<option value="paid">Paid</option>
						<option value="unpaid">Unpaid</option>
						<option value="pending">Pending</option>
					</select>
				</div>

				<!-- Department Filter -->
				<div class="mb-3">
					<label for="department-filter" class="form-label">Department</label>
					<select id="department-filter" class="form-select">
						<option value="">All</option>
						<?php foreach ($departments as $department): ?>
							<option value="<?= $department->dept_name ?>"><?= $department->dept_name ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<!-- Status Filter -->
				<div class="mb-3">
					<label for="year-filter" class="form-label">Year Level</label>
					<select id="year-filter" class="form-select">
						<option value="">All</option>
						<option value="1st year">First Year</option>
						<option value="2nd year">Second Year</option>
						<option value="3rd year">Third Year</option>
						<option value="4th year">Fourth Year</option>
					</select>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="applyFilters()">Apply Filters</button>
			</div>
		</div>
	</div>
</div>

<!-- FILTER SCRIPT -->
<script>
	function applyFilters() {
		console.log('Filters Applied');
		var status = document.getElementById("status-filter").value.trim().toLowerCase();
		var department = document.getElementById("department-filter").value.trim().toLowerCase();
		var year = document.getElementById("year-filter").value.trim().toLowerCase();

		// Get all the student rows in the table
		const rows = document.querySelectorAll('#table-ticket-body tr');

		rows.forEach(row => {
			// Get the year (3rd column), department (4th), and status (6th span)
			const studentYear = row.querySelector('td:nth-child(3)')?.textContent.trim().toLowerCase() || '';
			const studentDepartment = row.querySelector('td:nth-child(4)')?.textContent.trim().toLowerCase() || '';
			const studentStatus = row.querySelector('td:nth-child(6) span')?.textContent.trim().toLowerCase() || '';



			// Check if row matches all selected filters
			const yearMatches = (year === "" || studentYear === year);
			const departmentMatches = (department === "" || studentDepartment === department);
			const statusMatches = (status === "" || studentStatus === status);

			// Show or hide the row based on filters
			row.style.display = (yearMatches && departmentMatches && statusMatches) ? "" : "none";
		});



		// Manually hide the modal by toggling the class or with jQuery
		$('#filterModal').modal('hide');
	}
</script>

<!-- EDIT FINES MODAL -->
<div class="modal fade" id="editFinesModal" tabindex="-1" aria-labelledby="editFinesModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="editFinesModalLabel">Edit Fines</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="editFinesForm">
				<div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
					<input type="hidden" id="editStudentId" name="student_id">

					<!-- Horizontal scrollable container -->
					<div style="overflow-x: auto; width: 100%;">
						<table class="table table-bordered table-sm" style="table-layout: auto; min-width: 1000px; white-space: nowrap;">
							<thead class="table-light">
								<tr>
									<th>#</th>
									<th>Event</th>
									<th>Slot</th>
									<th>Time In</th>
									<th>Time Out</th>
									<th>Fines Reason</th>
									<th>Amount (₱)</th>
									<th>Reason for Change</th>
								</tr>
							</thead>
							<tbody id="editFinesTableBody">
								<!-- JS fills this -->
							</tbody>
							<tfoot>
								<tr>
									<td colspan="2" class="fw-bold text-start text-primary bg-light border-top" id="totalFinesDisplay">
										<!-- JS fills this -->
									</td>
									<td colspan="6"></td>
								</tr>
							</tfoot>
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



<script>
	// Edit Fines Modal
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
  <td>${fine.slot_name}</td>
  <td><input type="text" name="time_in[]" class="form-control time-in" value="${fine.time_in || ''}"></td>
  <td><input type="text" name="time_out[]" class="form-control time-out" value="${fine.time_out || ''}"></td>
  <td>
    <select name="reason[]" class="form-control reason-select">
      <option value="Absent" ${fine.reason === 'Absent' ? 'selected' : ''}>Absent</option>
      <option value="Present" ${fine.reason === 'Present' ? 'selected' : ''}>Present</option>
      <option value="Incomplete" ${fine.reason === 'Incomplete' ? 'selected' : ''}>Incomplete</option>
    </select>
  </td>
  <td>
    <input type="number" step="0.01" name="amount[]" class="form-control" value="${fine.fine}">
  </td>
  <td>
    <input type="text" name="changes[]" class="form-control" value="${fine.changes || ''}">
  </td>
  <input type="hidden" name="fines_id[]" value="${fine.fines_id}">
  <input type="hidden" name="attendance_id[]" value="${fine.attendance_id}">
  <input type="hidden" name="time_status[]" class="time-status-field" value="${fine.time_status || ''}">
`;


						editFinesTableBody.appendChild(row);
					});

					// Attach 'change' event listeners to each reason select
					editFinesTableBody.querySelectorAll('.reason-select').forEach(select => {
						select.addEventListener('change', function() {
							const row = this.closest('tr');
							updateTimeFields(row, this.value);
						});
					});

					// Set initial readonly states of time inputs
					updateTimeOutFields();

					// Your existing setup function call
					setupLiveTotalCalculation();

					// Display total fines
					document.getElementById('totalFinesDisplay').innerText = `Total Fines: ₱${parseFloat(student.total_fines).toFixed(2)}`;
				}
			});
		}

		if (editFinesForm) {
			editFinesForm.addEventListener('submit', function(e) {
				e.preventDefault();

				Swal.fire({
					title: 'Are you sure?',
					text: "Do you want to update the fines?",
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes, update it!',
					cancelButtonText: 'Cancel'
				}).then((result) => {
					if (result.isConfirmed) {
						const formData = new FormData(this);

						fetch('<?= base_url("index.php/AdminController/edit_fines") ?>', {
								method: 'POST',
								body: formData
							})
							.then(response => response.json())
							.then(data => {
								if (data.status === 'success') {
									Swal.fire({
										icon: 'success',
										title: 'Success',
										text: 'Fines successfully updated.',
										timer: 1500,
										showConfirmButton: false
									}).then(() => {
										const modalInstance = bootstrap.Modal.getInstance(editFinesModal);
										if (modalInstance) modalInstance.hide();

										location.reload();
									});
								} else {
									Swal.fire({
										icon: 'error',
										title: 'Update Failed',
										text: data.message || 'Failed to update fines.'
									});
								}
							})
							.catch(error => {
								console.error('Error updating fines:', error);
								Swal.fire({
									icon: 'error',
									title: 'Error',
									text: 'An error occurred while updating.'
								});
							});
					}
					// else user cancelled, do nothing
				});
			});
		}
	});
</script>


<script>
	function updateTimeFields(row, reason) {
		const timeInInput = row.querySelector('.time-in');
		const timeOutInput = row.querySelector('.time-out');
		const timeStatusInput = row.querySelector('.time-status-field'); // <- get hidden input

		if (reason === 'Incomplete') {
			timeInInput.readOnly = false;
			timeOutInput.value = '';
			timeOutInput.readOnly = true;
			timeStatusInput.value = 'Incomplete'; // update hidden input
		} else if (reason === 'Absent') {
			timeInInput.value = '';
			timeOutInput.value = '';
			timeInInput.readOnly = true;
			timeOutInput.readOnly = true;
			timeStatusInput.value = 'Absent';
		} else {
			timeInInput.readOnly = false;
			timeOutInput.readOnly = false;
			timeStatusInput.value = 'Present';
		}
	}



	// Modify your existing updateTimeOutFields to handle time_in similarly:
	function updateTimeOutFields() {
		const rows = editFinesTableBody.querySelectorAll('tr');
		rows.forEach(row => {
			const reasonSelect = row.querySelector('.reason-select');
			if (reasonSelect) {
				updateTimeFields(row, reasonSelect.value);
			}
		});
	}
</script>

<!-- SCRIPT FOR LIVE CALCULATION -->
<script>
	function updateTotalFines() {
		const amountInputs = document.querySelectorAll('#editFinesTableBody input[name="amount[]"]');
		let total = 0;
		amountInputs.forEach(input => {
			const value = parseFloat(input.value);
			if (!isNaN(value)) total += value;
		});
		document.getElementById('totalFinesDisplay').innerText = `Total Fines: ₱${total.toFixed(2)}`;
	}

	function setupLiveTotalCalculation() {
		const amountInputs = document.querySelectorAll('#editFinesTableBody input[name="amount[]"]');
		amountInputs.forEach(input => {
			input.addEventListener('input', updateTotalFines);
		});
		updateTotalFines(); // Initialize the display
	}
</script>

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
					<img id="viewReceiptImage" src="" alt="Receipt" class="img-fluid rounded border" style="max-height: 500px;">
				</div>

				<hr>
				<h6>Fines Breakdown</h6>
				<table class="table table-bordered table-sm">
					<thead>
						<tr>
						<tr>
							<th>#</th>
							<th>Remarks</th>
							<th>Amount</th> <!-- this will now show the IN/OUT breakdown -->
							<th>Slot</th>
							<th>Activity</th>
							<th>Date</th>
						</tr>

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

<!-- script for view details -->
<script>
	// // View Details Modal
	document.addEventListener('DOMContentLoaded', () => {
		const viewDetailsModal = document.getElementById('viewDetailsModal');
		const viewBreakdownTable = document.querySelector("#viewFinesTableBody");
		const viewReceiptContainer = viewDetailsModal.querySelector('#viewReceiptImageContainer');
		const viewReceiptImage = viewDetailsModal.querySelector('#viewReceiptImage');
		const baseImageUrl = "<?= base_url('uploads/fine_receipts/') ?>"; // adjust path as needed

		viewDetailsModal.addEventListener('show.bs.modal', event => {
			const studentId = event.relatedTarget.getAttribute('data-student-id');
			const student = studentsData.find(s => s.id == studentId); // replace with finesData if needed

			if (student) {
				// Populate basic details
				viewDetailsModal.querySelector('#viewName').textContent = student.name;
				viewDetailsModal.querySelector('#viewDepartment').textContent = student.department;
				viewDetailsModal.querySelector('#viewTotalFines').textContent = `${parseFloat(student.total_fines).toFixed(2)}`;
				viewDetailsModal.querySelector('#viewReferenceNumber').textContent = student.reference || 'N/A';


				// Receipt
				if (student.receipt) {
					viewReceiptImage.src = baseImageUrl + student.receipt;
					viewReceiptContainer.classList.remove('d-none');
				} else {
					viewReceiptImage.src = '';
					viewReceiptContainer.classList.add('d-none');
				}

				// Fines table
				// Fines table
				viewBreakdownTable.innerHTML = '';
				student.fines.forEach((fine, i) => {
					const fineIn = fine.time_in === null ? parseFloat(fine.fines_scan || 0).toFixed(2) : '0.00';
					const fineOut = fine.time_out === null ? parseFloat(fine.fines_scan || 0).toFixed(2) : '0.00';
					const timeDisplay = `IN: ₱${fineIn} | OUT: ₱${fineOut}`;

					viewBreakdownTable.innerHTML += `
        <tr>
            <td>${i + 1}</td>
            <td>${fine.reason}</td>
            <td>${timeDisplay}</td>  <!-- Show both IN and OUT fines -->
            <td>${fine.slot_name}</td> <!-- Add slot name -->
            <td>${fine.title}</td>
            <td>${fine.event_date}</td>
        </tr>
    `;
				});

			}
		});
	});
</script>

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
				<input type="hidden" name="academic_year" id="modalAcademicYear" value="">
				<input type="hidden" name="semester" id="modalSemester" value="">


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
									<th>Amount</th> <!-- this will now show the IN/OUT breakdown -->
									<th>Slot</th>
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
						<option value="" id="modalModePayment">Select Mode of Payment</option>
						<option value="Online Payment" selected>Online Payment</option>
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
						<img id="receiptImage" src="" alt="Receipt" class="img-fluid mb-3" style="max-height: 500px; width: auto;">
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
	document.addEventListener('DOMContentLoaded', () => {
		const paymentModal = document.getElementById('paymentModal');
		const breakdownTable = document.querySelector("#finesBreakdownTable tbody");
		const receiptSection = paymentModal.querySelector('#receiptSection');
		const receiptImage = paymentModal.querySelector('#receiptImage');
		const baseImageUrl = "<?= base_url('uploads/fine_receipts/') ?>";

		paymentModal.addEventListener('show.bs.modal', event => {
			const studentId = event.relatedTarget.getAttribute('data-student-id');
			const student = studentsData.find(s => s.id == studentId);

			if (student) {
				paymentModal.querySelector('#modalStudentId').value = student.id;
				paymentModal.querySelector('#modalTotalFines').value = `₱${student.total_fines}`;
				paymentModal.querySelector('#modalModePayment').value = student.mode_payment || '';
				paymentModal.querySelector('#modeOfPayment').value = student.mode_payment || '';

				// Add these two lines:
				paymentModal.querySelector('#modalAcademicYear').value = student.academic_year || '';
				paymentModal.querySelector('#modalSemester').value = student.semester || '';

				if (student.receipt) {
					receiptImage.src = baseImageUrl + student.receipt;
					receiptSection.classList.remove('d-none');
				} else {
					receiptImage.src = "";
					receiptSection.classList.add('d-none');
				}

				breakdownTable.innerHTML = '';
				student.fines.forEach((fine, i) => {
					const fineIn = fine.time_in === null ? parseFloat(fine.fines_scan || 0).toFixed(2) : '0.00';
					const fineOut = fine.time_out === null ? parseFloat(fine.fines_scan || 0).toFixed(2) : '0.00';
					const timeDisplay = `IN: ₱${fineIn} | OUT: ₱${fineOut}`;

					breakdownTable.innerHTML += `
	<tr>
		<td>${i + 1}</td>
		<td>${fine.reason}</td>
		<td>${timeDisplay}</td>
		<td>${fine.slot_name}</td> <!-- Added slot here -->
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
	});

	$(document).ready(function() {
		$('#paymentModal form').submit(function(e) {
			e.preventDefault();

			Swal.fire({
				title: 'Are you sure?',
				text: "Do you want to confirm this payment?",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, confirm it!',
				cancelButtonText: 'Cancel'
			}).then((result) => {
				if (result.isConfirmed) {
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
				}
			});
		});
	});
</script>

<!-- supplying fines -->
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
				name: `${fine.last_name} ${fine.first_name}`,
				department: fine.dept_name,
				year: fine.year_level,
				fines: [],
				status: fine.fines_status,
				mode_payment: fine.mode_payment,
				total_fines: fine.total_fines,
				reference: fine.reference_number_students,
				receipt: fine.receipt,
				changes: fine.remarks,
				fines_scan: fine.fines_scan,

				// Add these:
				academic_year: fine.academic_year,
				semester: fine.semester
			});
		}

		studentsMap.get(fine.student_id).fines.push({
			eventId: fine.activity_id,
			fines_id: fine.fines_id, // ✅ For fines update
			attendance_id: fine.attendance_id, // ✅ For attendance update
			fine: fine.fines_amount,
			reason: fine.fines_reason,
			event_date: fine.start_date,
			title: fine.activity_title,
			reference: fine.reference_number_students,

			// Add these:
			fines_scan: fine.fines_scan,
			slot_name: fine.slot_name, // || 'No Slot Name'
			time_in: fine.time_in, // || 'N/A' ✅ ACTUAL TIME FROM ATTENDANCE
			time_out: fine.time_out, // || 'N/A' ✅ ACTUAL TIME FROM ATTENDANCE
			time_status: fine.attendance_status, // || 'N/A' ✅ Add this line
			attendance_id: fine.attendance_id || null,
			timeslot_id: fine.timeslot_id || null
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

	// Function to render rows based on pagination
	const renderRows = (students, page = 1, itemsPerPage = 10) => {
		const tableBody = document.getElementById('table-ticket-body');
		tableBody.innerHTML = ''; // Clear current rows

		// Paginate the data
		const start = (page - 1) * itemsPerPage;
		const end = page * itemsPerPage;
		const paginatedStudents = students.slice(start, end);

		// Populate student rows
		paginatedStudents.forEach(student => {
			const row = document.createElement('tr');

			row.innerHTML = `
            <td class="text-nowrap">${student.id}</td>
            <td class="text-nowrap">${student.name}</td>
			<td class="text-nowrap">${student.year}</td>
            <td class="text-nowrap">${student.department}</td>
        `;

			// Fill in fines per event
			events.forEach(event => {
				const totalFine = student.fines
					.filter(f => f.eventId === event.id)
					.reduce((sum, f) => sum + parseFloat(f.fine || 0), 0);

				const cell = document.createElement('td');
				cell.className = 'text-nowrap';
				cell.innerHTML = `₱${Math.round(totalFine)}`;
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

			let dropdownItems = '';

			if (student.status === 'Unpaid') {
				dropdownItems += `
				<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#paymentModal" data-student-id="${student.id}">Confirm Payment</a>
				<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewDetailsModal" data-student-id="${student.id}">View Details</a>

				<a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#editFinesModal" data-student-id="${student.id}">Edit Fines</a>
			`;
			} else if (student.status === 'Pending') {
				dropdownItems += `
				<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#paymentModal" data-student-id="${student.id}">Confirm Payment</a>
				<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewDetailsModal" data-student-id="${student.id}">View Details</a>
			`;
			} else if (student.status === 'Paid') {
				dropdownItems += `
				<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewDetailsModal" data-student-id="${student.id}">View Details</a>
			`;
			} else if (student.status === 'No Fines') {
				dropdownItems += `
				<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewDetailsModal" data-student-id="${student.id}">View Details</a>
			`;
			}

			actionCell.innerHTML = `
			<div class="dropdown font-sans-serif position-static">
				<button class="btn btn-link text-600 btn-sm dropdown-toggle btn-reveal" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span class="fas fa-ellipsis-h fs-10"></span>
				</button>
				<div class="dropdown-menu dropdown-menu-end border py-0">
					<div class="py-2">
						${dropdownItems}
					</div>
				</div>
			</div>
			`;
			row.appendChild(actionCell);
			tableBody.appendChild(row);
		});

		renderPaginationNumbers(students.length, page, itemsPerPage);
	};

	// Pagination function
	const renderPaginationNumbers = (totalItems, currentPage, itemsPerPage) => {
		const totalPages = Math.ceil(totalItems / itemsPerPage);
		const paginationList = document.querySelector('.pagination');
		paginationList.innerHTML = ''; // Clear existing pagination numbers

		for (let i = 1; i <= totalPages; i++) {
			const li = document.createElement('li');
			li.className = `page-item ${i === currentPage ? 'active' : ''}`;
			li.innerHTML = `
			<button class="page-link" type="button">${i}</button>
		`;

			li.querySelector('button').addEventListener('click', () => {
				renderRows(studentsData, i, itemsPerPage);
			});

			paginationList.appendChild(li);
		}
	};

	// Initialize pagination and search
	let currentPage = 1;
	const itemsPerPage = 10;

	// Render the initial rows
	renderRows(studentsData, currentPage, itemsPerPage);

	// Pagination click handlers
	document.querySelector('[data-list-pagination="prev"]').addEventListener('click', () => {
		if (currentPage > 1) {
			currentPage--;
			renderRows(studentsData, currentPage, itemsPerPage);
		}
	});

	document.querySelector('[data-list-pagination="next"]').addEventListener('click', () => {
		const totalPages = Math.ceil(studentsData.length / itemsPerPage);
		if (currentPage < totalPages) {
			currentPage++;
			renderRows(studentsData, currentPage, itemsPerPage);
		}
	});

	// Search functionality
	document.getElementById('searchInput').addEventListener('input', (e) => {
		const searchTerm = e.target.value.toLowerCase();
		const filteredStudents = studentsData.filter(student => student.name.toLowerCase().includes(searchTerm));
		renderRows(filteredStudents, 1, itemsPerPage); // Reset to page 1 on search
	});
</script>





<!-- <script>
	

	

	// Payment Modal
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
                        </tr>
                    `;
				});
			}
		});
	});


	// View Details Modal
	document.addEventListener('DOMContentLoaded', () => {
		const viewDetailsModal = document.getElementById('viewDetailsModal');
		const viewBreakdownTable = document.querySelector("#viewFinesTableBody");
		const viewReceiptContainer = viewDetailsModal.querySelector('#viewReceiptImageContainer');
		const viewReceiptImage = viewDetailsModal.querySelector('#viewReceiptImage');
		const baseImageUrl = "<?= base_url('assets/receipt/') ?>"; // adjust path as needed

		viewDetailsModal.addEventListener('show.bs.modal', event => {
			const studentId = event.relatedTarget.getAttribute('data-student-id');
			const student = studentsData.find(s => s.id == studentId); // replace with finesData if needed

			if (student) {
				// Populate basic details
				viewDetailsModal.querySelector('#viewName').textContent = student.name;
				viewDetailsModal.querySelector('#viewDepartment').textContent = student.department;
				viewDetailsModal.querySelector('#viewTotalFines').textContent = `₱${parseFloat(student.total_fines).toFixed(2)}`;
				viewDetailsModal.querySelector('#viewReferenceNumber').textContent = student.reference || 'N/A';


				// Receipt
				if (student.receipt) {
					viewReceiptImage.src = baseImageUrl + student.receipt;
					viewReceiptContainer.classList.remove('d-none');
				} else {
					viewReceiptImage.src = '';
					viewReceiptContainer.classList.add('d-none');
				}

				// Fines table
				viewBreakdownTable.innerHTML = '';
				student.fines.forEach((fine, i) => {
					viewBreakdownTable.innerHTML += `
					<tr>
						<td>${i + 1}</td>
						<td>${fine.reason}</td>
						<td>₱${parseFloat(fine.fine).toFixed(2)}</td>
						<td>${fine.title}</td>
						<td>${fine.event_date}</td>
					</tr>
				`;
				});
			}
		});
	});

	// Edit Fines Modal
	const editFinesModal = document.getElementById('editFinesModal');
	const editFinesTableBody = document.getElementById('editFinesTableBody');
	const editStudentIdInput = document.getElementById('editStudentId');

	editFinesModal.addEventListener('show.bs.modal', event => {
		const studentId = event.relatedTarget.getAttribute('data-student-id');
		const student = studentsData.find(s => s.id == studentId);

		if (!student) return;

		// Set the hidden input for student_id
		editStudentIdInput.value = student.id;

		// Clear existing rows
		editFinesTableBody.innerHTML = '';

		// Loop and add fines
		student.fines.forEach((fine, index) => {
			editFinesTableBody.innerHTML += `
		<tr>
			<td>${index + 1}</td>
			<td>${fine.title || 'N/A'}</td>
			<td>
				<input type="text" name="reasons[]" class="form-control form-control-sm" 
					value="${fine.reason || ''}" placeholder="Reason">
			</td>
			<td>
				<input type="number" name="amounts[]" class="form-control form-control-sm" 
					value="${fine.fine || 0}" min="0" step="0.01">
			</td>
			<td>
				<input type="text" name="change_reasons[]" class="form-control form-control-sm" 
					placeholder="Reason for change">
			</td>
		</tr>
	`;
		});

	});
</script>


<script>
	document.addEventListener('DOMContentLoaded', function() {
		document.body.addEventListener('click', function(e) {
			if (e.target.classList.contains('view-details-btn')) {
				e.preventDefault();
				const studentId = e.target.getAttribute('data-student-id');
				openViewDetailsModal(studentId);
			}
		});
	});
</script> -->


<!-- 
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

	// Function to render rows based on pagination
	const renderRows = (students, page = 1, itemsPerPage = 10) => {
		const tableBody = document.getElementById('table-ticket-body');
		tableBody.innerHTML = ''; // Clear current rows

		// Paginate the data
		const start = (page - 1) * itemsPerPage;
		const end = page * itemsPerPage;
		const paginatedStudents = students.slice(start, end);

		// Populate student rows
		paginatedStudents.forEach(student => {
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

		renderPaginationNumbers(students.length, page, itemsPerPage);
	};

</script> -->
<!-- 

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
				changes: fine.remarks,

				// Add these:
				academic_year: fine.academic_year,
				semester: fine.semester
			});
		}

		studentsMap.get(fine.student_id).fines.push({
			eventId: fine.attendance_id, //use this to know which table to update
			fines_id: fine.fines_id, // ✅ For fines update
			attendance_id: fine.attendance_id, // ✅ For attendance update
			fine: fine.fines_amount,
			reason: fine.fines_reason,
			event_date: fine.start_date,
			title: fine.activity_title,
			changes: fine.remarks,


			// Add these:
			slot_name: fine.slot_name || 'No Slot Name',
			time_in: fine.date_time_in || 'N/A',
			time_out: fine.date_time_out || 'N/A',
			attendance_id: fine.attendance_id || null,
			timeslot_id: fine.timeslot_id || null
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

		let dropdownItems = '';

		if (student.status === 'Unpaid') {
			dropdownItems += `
			<a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#editFinesModal" data-student-id="${student.id}">Edit Fines</a>
			`;
		} else if (student.status === 'Pending') {
			dropdownItems += `
			<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#paymentModal" data-student-id="${student.id}">Confirm Payment</a>
			<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewDetailsModal" data-student-id="${student.id}">View Details</a>
			`;
		} else if (student.status === 'Paid') {
			dropdownItems += `
			<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewDetailsModal" data-student-id="${student.id}">View Details</a>
			`;
		}

		actionCell.innerHTML = `
			<div class="dropdown font-sans-serif position-static">
			<button class="btn btn-link text-600 btn-sm dropdown-toggle btn-reveal" type="button"
			data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<span class="fas fa-ellipsis-h fs-10"></span>
			</button>
			<div class="dropdown-menu dropdown-menu-end border py-0">
			<div class="py-2">
			${dropdownItems}
			</div>
			</div>
			</div>
		`;

		row.appendChild(actionCell);
		tableBody.appendChild(row);
	});


	

	// Edit Fines Modal
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


						// Determine whether the slot is for IN or OUT based on the slot name
						const slotName = fine.slot_name || 'No Slot Name';
						const timeIn = fine.time_in || 'N/A';
						const timeOut = fine.time_out || 'N/A';

						row.innerHTML = `
								<td>${i + 1}</td>
								<td>
									<strong>${fine.title}</strong><br>
									<small class="text-muted">
										${slotName}<br>
										In: ${timeIn}<br>
										Out: ${timeOut}
									</small>
								</td>
								<td>
									<select name="reason[]" class="form-control">
										<option value="Absent" ${fine.reason === 'Absent' ? 'selected' : ''}>Absent</option>
										<option value="Present" ${fine.reason === 'Present' ? 'selected' : ''}>Present</option>
									</select>
								</td>
								<td><input type="number" step="0.01" name="amount[]" class="form-control" value="${fine.fine}"></td>
								<td><input type="text" name="changes[]" class="form-control" value="${fine.changes}"></td>
								
								<input type="hidden" name="fines_id[]" value="${fine.fines_id}">
							<input type="hidden" name="attendance_id[]" value="${fine.attendance_id}">

							`;

						editFinesTableBody.appendChild(row);
					});
				}
			});
		}

		if (editFinesForm) {
			editFinesForm.addEventListener('submit', function(e) {
				e.preventDefault();

				Swal.fire({
					title: 'Are you sure?',
					text: "Do you want to update the fines?",
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes, update it!',
					cancelButtonText: 'Cancel'
				}).then((result) => {
					if (result.isConfirmed) {
						const formData = new FormData(this);

						fetch('<?= base_url("index.php/AdminController/edit_fines") ?>', {
								method: 'POST',
								body: formData
							})
							.then(response => response.json())
							.then(data => {
								if (data.status === 'success') {
									Swal.fire({
										icon: 'success',
										title: 'Success',
										text: 'Fines successfully updated.',
										timer: 1500,
										showConfirmButton: false
									}).then(() => {
										const modalInstance = bootstrap.Modal.getInstance(editFinesModal);
										if (modalInstance) modalInstance.hide();

										location.reload();
									});
								} else {
									Swal.fire({
										icon: 'error',
										title: 'Update Failed',
										text: data.message || 'Failed to update fines.'
									});
								}
							})
							.catch(error => {
								console.error('Error updating fines:', error);
								Swal.fire({
									icon: 'error',
									title: 'Error',
									text: 'An error occurred while updating.'
								});
							});
					}
					// else user cancelled, do nothing
				});
			});
		}



	});
</script> -->

<!-- <option value="Incomplete" ${fine.reason === 'Incomplete' ? 'selected' : ''}>Incomplete</option> -->
<!-- <input type="hidden" name="event_id[]" value="${fine.eventId}"> -->

<!-- <script>
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
</script> -->



<!-- pagination fines -->
<!-- <script>
	// Pagination function
	const renderPaginationNumbers = (totalItems, currentPage, itemsPerPage) => {
		const totalPages = Math.ceil(totalItems / itemsPerPage);
		const paginationList = document.querySelector('.pagination');
		paginationList.innerHTML = ''; // Clear existing pagination numbers

		for (let i = 1; i <= totalPages; i++) {
			const li = document.createElement('li');
			li.className = `page-item ${i === currentPage ? 'active' : ''}`;
			li.innerHTML = `
			<button class="page-link" type="button">${i}</button>
		`;

			li.querySelector('button').addEventListener('click', () => {
				renderRows(studentsData, i, itemsPerPage);
			});

			paginationList.appendChild(li);
		}
	};

	// Initialize pagination and search
	let currentPage = 1;
	const itemsPerPage = 10;

	// Render the initial rows
	renderRows(studentsData, currentPage, itemsPerPage);

	// Pagination click handlers
	document.querySelector('[data-list-pagination="prev"]').addEventListener('click', () => {
		if (currentPage > 1) {
			currentPage--;
			renderRows(studentsData, currentPage, itemsPerPage);
		}
	});

	document.querySelector('[data-list-pagination="next"]').addEventListener('click', () => {
		const totalPages = Math.ceil(studentsData.length / itemsPerPage);
		if (currentPage < totalPages) {
			currentPage++;
			renderRows(studentsData, currentPage, itemsPerPage);
		}
	});

	// Search functionality
	document.getElementById('searchInput').addEventListener('input', (e) => {
		const searchTerm = e.target.value.toLowerCase();
		const filteredStudents = studentsData.filter(student => student.name.toLowerCase().includes(searchTerm));
		renderRows(filteredStudents, 1, itemsPerPage); // Reset to page 1 on search
	});
</script> -->