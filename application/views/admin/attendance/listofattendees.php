<div class="card mb-3 mb-lg-0">
	<div class="card-header bg-body-tertiary d-flex justify-content-between">
		<h5 class="mb-0"><?php echo $activities['activity_title']; ?> - Attendance List </h5>
	</div>
</div>

<!-- Space Between Sections -->
<div class="space" style="height: 20px;"></div> <!-- Adds spacing between sections -->

<div class="row gx-3">
	<div class="col-xxl-10 col-xl-12">
		<div class="card" id="attendanceTable"
			data-list='{"valueNames":["id", "name", "department", "status"],"page":11,"pagination":true,"fallback":"attendance-table-fallback"}'>

			<div class="card-header border-bottom border-200 px-0">
				<div class="d-lg-flex justify-content-between">
					<div class="row flex-between-center gy-2 px-x1">
						<div class="col-auto pe-0">

						</div>
					</div>

					<!-- Search Input -->
					<div class="d-flex align-items-center justify-content-between justify-content-lg-end px-x1">
						<div class="d-flex align-items-center" id="table-ticket-replace-element">
							<div class="col-auto">
								<form>
									<div class="input-group input-search-width">
										<input id="searchInput" class="form-control form-control-sm shadow-none search"
											type="search" placeholder="Search" aria-label="search" />
										<button class="btn btn-sm btn-outline-secondary border-300 hover-border-secondary" type="button">
											<span class="fa fa-search fs-10"></span>
										</button>
									</div>
								</form>
							</div>
							<?php if (!empty($activities)): ?>
								<a href="<?php echo base_url('AdminController/export_attendance_pdf/' . $activities['activity_id']); ?>"
									target="_blank" title="Export to PDF">
									<button class="btn btn-sm btn-falcon-default ms-2" type="button" id="exportPdfBtn" title="Export to PDF">
										<span class="fas fa-download"></span>
									</button>
								</a>
							<?php endif; ?>

							<!-- script for filtered tables -->

							<script>
								document.getElementById('exportPdfBtn').addEventListener('click', function() {
									const status = document.getElementById("status-filter").value;
									const department = document.getElementById("department-filter").value;
									const baseUrl = "<?php echo base_url('AdminController/export_attendance_pdf/' . $activities['activity_id']); ?>";

									const url = new URL(baseUrl);
									if (status) url.searchParams.append("status", status);
									if (department) url.searchParams.append("department", department);

									window.open(url.toString(), '_blank');
								});
							</script>


							<a href="<?= base_url('AdminController/view_attendance_reports/' . $activity_id) ?>"
								title="View Attendance Reports">
								<button class="btn btn-sm btn-falcon-default ms-2" type="button">
									<span class="fas fa-chart-pie"></span>
								</button>
							</a>



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
								<th scope="col" class=" text-nowrap">Name</th>
								<th scope="col" class=" text-nowrap">Department</th>
								<?php foreach ($timeslots as $slot): ?>
									<?php
									$period = '';
									if (strtolower($slot->slot_name) === 'morning') {
										$period = 'AM';
									} elseif (strtolower($slot->slot_name) === 'afternoon') {
										$period = 'PM';
									} else {
										$period = strtoupper($slot->slot_name); // fallback for custom names
									}
									?>
									<th scope="col" class="text-nowrap"><?php echo $period; ?> - Time In</th>
									<th scope="col" class="text-nowrap"><?php echo $period; ?> - Time Out</th>
								<?php endforeach; ?>
								<th scope="col" class="text-nowrap">Status</th>
							</tr>
						</thead>
						<tbody class="list" id="table-ticket-body">
							<?php foreach ($students as $student): ?>
								<tr class="attendance-row">
									<td class="text-nowrap id"><?php echo $student['student_id']; ?></td>
									<td class="text-nowrap name"><?php echo $student['name']; ?></td>
									<td class="text-nowrap department"><?php echo $student['dept_name']; ?></td>
									<!-- Loop through the timeslots to show the time in and time out for each -->
									<?php foreach ($timeslots as $slot): ?>
										<?php
										// Determine the period (AM or PM)
										$period = strtolower($slot->slot_name) === 'morning' ? 'am' : 'pm';
										$time_in_key = 'in_' . $period;  // For time_in (am or pm)
										$time_out_key = 'out_' . $period;  // For time_out (am or pm)
										?>
										<td class="text-nowrap">
											<!-- Display time_in or 'No Data' if not available -->
											<?php echo isset($student[$time_in_key]) ? $student[$time_in_key] : 'No Data'; ?>
										</td>
										<td class="text-nowrap">
											<!-- Display time_out or 'No Data' if not available -->
											<?php echo isset($student[$time_out_key]) ? $student[$time_out_key] : 'No Data'; ?>
										</td>
									<?php endforeach; ?>
									<td class="status">
										<?php if ($student['attendance_status'] == 'Present'): ?>
											<span class="badge badge rounded-pill d-block p-2 badge-subtle-success">
												<?php echo $student['attendance_status']; ?>
												<span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span>
											</span>
										<?php elseif ($student['attendance_status'] == 'Absent'): ?>
											<span class="badge badge rounded-pill d-block p-2 badge-subtle-danger">
												<?php echo $student['attendance_status']; ?>
												<span class="ms-1 fas fa-times" data-fa-transform="shrink-2"></span>
											</span>
										<?php elseif ($student['attendance_status'] == 'No Status'): ?>
											<span class="badge badge rounded-pill d-block p-2 badge-subtle-danger">
												No Status
												<span class="ms-1 fas fa-times" data-fa-transform="shrink-2"></span>
											</span>
										<?php elseif ($student['attendance_status'] == 'Incomplete'): ?>
											<span class="badge badge rounded-pill d-block p-2 badge-subtle-warning">
												<?php echo $student['attendance_status']; ?>
												<span class="ms-1 fas fa-exclamation" data-fa-transform="shrink-2"></span>
											</span>
										<?php elseif ($student['attendance_status'] == 'Excused'): ?>
											<span class="badge badge rounded-pill d-block p-2 badge-subtle-primary">
												Excused
											</span>
										<?php elseif ($student['attendance_status'] == 'Exempted'): ?>
											<span class="badge badge rounded-pill d-block p-2 badge-subtle-primary">
												Exempted
											</span>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
					</table>
					<div class="text-center d-none" id="attendance-table-fallback">
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

	<!-- MODAL FILTER -->
	<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="filterModalLabel">Filter Activities</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<!-- Status Filter -->
					<div class="mb-3">
						<label for="status-filter" class="form-label">Activity Status</label>
						<select id="status-filter" class="form-select">
							<option value="" selected>Select Status</option>
							<option value="Present">Present</option>
							<option value="Incomplete">Incomplete</option>
							<option value="Absent">Absent</option>
						</select>
					</div>

					<!-- Department Filter -->
					<div class="mb-3">
						<label for="department-filter" class="form-label">Department</label>
						<select id="department-filter" class="form-select">
							<option value="" selected>Select Department</option>
							<?php foreach ($departments as $department): ?>
								<option value="<?php echo $department->dept_name; ?>"><?php echo $department->dept_name; ?></option>
							<?php endforeach; ?>
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

	<script>
		function applyFilters() {
			// Get selected values from the modal filters
			var status = document.getElementById("status-filter").value;
			var department = document.getElementById("department-filter").value;

			// Get all activity rows
			var activityRows = document.querySelectorAll(".attendance-row");
			var filteredRows = 0;

			// Loop through each activity row
			activityRows.forEach(function(row) {
				// Get the status and department values from the row (add status and department attributes in PHP)
				var rowStatus = row.querySelector(".status") ? row.querySelector(".status").textContent.trim() : "";
				var rowDepartment = row.querySelector(".department") ? row.querySelector(".department").textContent.trim() : "";

				// Fallback: If status or department is empty, treat it as "No Status" or "No Department"
				rowStatus = rowStatus === "" ? "No Status" : rowStatus;
				rowDepartment = rowDepartment === "" ? "No Department" : rowDepartment;

				// Check if the row matches the selected filters
				if (
					(status === "" || rowStatus === status) &&
					(department === "" || rowDepartment === department)
				) {
					row.style.display = ""; // Show the row if it matches
					filteredRows++;
				} else {
					row.style.display = "none"; // Hide the row if it doesn't match
				}
			});

			// Close the modal properly
			var modalElement = document.getElementById("filterModal");
			var modal = bootstrap.Modal.getInstance(modalElement);
			if (modal) {
				modal.hide();
			}
		}
	</script>