<!-- Updated Card with Button Triggers -->
<div class="card mb-3">
	<div class="card-header bg-light">
		<h5 class="mb-0">General Settings</h5>
	</div>
</div>

<!-- Importing Students -->
<div class="card mb-3">
	<div class="card-header bg-light">
		<h6 class="mb-0">Student and Officer Section</h6>
	</div>
	<div class="card-body">
		<div class="row g-3">
			<!-- Importing of List -->
			<div class="col-md-6">
				<div class="border rounded p-3 h-100 d-flex flex-column justify-content-between">
					<div class="d-flex">
						<div class="me-3">
							<span class="fs-4 text-primary"><i class="fas fa-file-import"></i></span>
						</div>
						<div>
							<h6 class="mb-1 fw-bold">Importing of Students</h6>
							<p class="mb-0 text-muted small">Upload bulk data of students using CSV or Excel files.</p>
						</div>
					</div>
					<div class="d-flex justify-content-end align-items-center gap-2 mt-3">
						<a href="<?= base_url('assets/templates/Template-ImportingStudents.xlsx') ?>" class="btn btn-sm btn-outline-secondary" download>
							Download Template
						</a>
						<button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
							Open
						</button>
						<!-- View Students Button -->
						<button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#mainModal">
							View
						</button>


						<!-- Registered Participants Modal -->
						<div class="modal fade" id="mainModal" tabindex="-1" aria-labelledby="mainModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">

							<div class="modal-dialog modal-xl modal-dialog-scrollable">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="registeredModalLabel">List of Students</h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
									</div>

									<div class="modal-body">
										<div class="card" id="registeredTable"
											data-list='{"valueNames":["id", "name"], "page": 11, "pagination": true, "fallback": "attendance-table-fallback"}'>

											<div class="card-header border-bottom border-200 px-0">
												<div class="d-flex justify-content-end px-3">
													<div class="d-flex align-items-center" id="table-ticket-replace-element">
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
												</div>
											</div>


											<div class="card-body p-0">
												<div class="table-responsive scrollbar">
													<table class="table table-hover table-striped overflow-hidden">
														<thead class="bg-200">
															<tr>
																<th class="text-nowrap">Student ID</th>
																<th class="text-nowrap">Name</th>
																<th class="text-nowrap">Department</th>
																<th class="text-nowrap">Year Level</th>
																<th class="text-nowrap">Sex</th>
																<th class="text-nowrap">Email Address</th>
																<th class="text-nowrap">Action</th>
															</tr>
														</thead>
														<tbody class="list">
															<?php foreach ($students as $student) : ?>
																<tr class="attendance-row">
																	<td class="text-nowrap id"><?php echo $student->student_id; ?></td>
																	<td class="text-nowrap name"><?php echo $student->first_name . " " . $student->middle_name . " " . $student->last_name; ?></td>
																	<td class="text-nowrap department"><?php echo $student->dept_name; ?></td>
																	<td class="text-nowrap"><?php echo $student->year_level; ?></td>
																	<td class="text-nowrap"><?php echo $student->sex; ?></td>
																	<td class="text-nowrap"><?php echo $student->email; ?></td>

																	<!-- Optional Dropdown Actions -->
																	<td class="text-nowrap">
																		<div class="dropdown font-sans-serif position-static">
																			<button class="btn btn-link text-600 btn-sm dropdown-toggle btn-reveal" type="button"
																				data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																				<span class="fas fa-ellipsis-h fs-10"></span>
																			</button>
																			<div class="dropdown-menu dropdown-menu-end border py-0">
																				<div class="py-2">
																					<a class="dropdown-item text-danger validate-registration" href="#"
																						data-userid="<?= $student->user_id ?>"
																						data-id="<?= $student->student_id ?>"
																						data-firstname="<?= $student->first_name ?>"
																						data-middlename="<?= $student->middle_name ?>"
																						data-lastname="<?= $student->last_name ?>"
																						data-dept-id="<?= $student->department_id ?>"
																						data-year="<?= $student->year_level ?>"
																						data-sex="<?= $student->sex ?>"
																						data-email="<?= $student->email ?>">
																						Edit Student
																					</a>
																				</div>
																			</div>
																		</div>
																	</td>
																</tr>
															<?php endforeach; ?>
														</tbody>
													</table>
													<!-- Edit Student Modal -->
													<div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
														<div class="modal-dialog modal-dialog-centered modal-lg"> <!-- make modal wider -->
															<div class="modal-content">
																<div class="modal-header">
																	<h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
																	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
																</div>
																<div class="modal-body">
																	<form id="editStudentForm">
																		<input type="hidden" name="user_id" id="edit-user-id">

																		<div class="row">
																			<div class="col-md-6">
																				<div class="mb-3">
																					<label for="edit-student-id" class="form-label">Student ID</label>
																					<input type="text" class="form-control" name="student_id" id="edit-student-id" required>
																				</div>
																				<div class="mb-3">
																					<label for="edit-first-name" class="form-label">First Name</label>
																					<input type="text" class="form-control" id="edit-first-name" name="first_name" required>
																				</div>
																				<div class="mb-3">
																					<label for="edit-middle-name" class="form-label">Middle Name</label>
																					<input type="text" class="form-control" id="edit-middle-name" name="middle_name" required>
																				</div>
																				<div class="mb-3">
																					<label for="edit-last-name" class="form-label">Last Name</label>
																					<input type="text" class="form-control" id="edit-last-name" name="last_name" required>
																				</div>
																			</div>

																			<div class="col-md-6">
																				<div class="mb-3">
																					<label for="edit-department" class="form-label">Department</label>
																					<select class="form-control" id="edit-department" name="department" required>
																						<option value="" disabled>Select Department</option>
																						<?php foreach ($departments as $dept): ?>
																							<option value="<?= htmlspecialchars($dept->dept_id) ?>">
																								<?= htmlspecialchars($dept->dept_name) ?>
																							</option>
																						<?php endforeach; ?>
																					</select>
																				</div>
																				<div class="mb-3">
																					<label for="edit-year" class="form-label">Year Level</label>
																					<select class="form-control" id="edit-year" name="year_level" required>
																						<option value="" disabled selected>Select Year Level</option>
																						<option value="1st Year">1st Year</option>
																						<option value="2nd Year">2nd Year</option>
																						<option value="3rd Year">3rd Year</option>
																						<option value="4th Year">4th Year</option>
																					</select>
																				</div>
																				<div class="mb-3">
																					<label for="edit-sex" class="form-label">Sex</label>
																					<input type="text" class="form-control" id="edit-sex" name="sex" required>
																				</div>
																				<div class="mb-3">
																					<label for="edit-email" class="form-label">Email</label>
																					<input type="email" class="form-control" id="edit-email" name="email" required>
																				</div>
																			</div>
																		</div>

																		<button type="submit" class="btn btn-primary w-100">Update Student</button>
																	</form>
																</div>
															</div>
														</div>
													</div>

													<script>
														document.getElementById('editStudentForm').addEventListener('submit', function(e) {
															e.preventDefault();

															Swal.fire({
																title: 'Confirm update?',
																icon: 'question',
																showCancelButton: true,
																confirmButtonText: 'Yes, update it!',
																cancelButtonText: 'Cancel'
															}).then((result) => {
																if (result.isConfirmed) {
																	const form = e.target;
																	const formData = new FormData(form);

																	fetch('<?php echo site_url("admin/update-student-data"); ?>', {
																			method: 'POST',
																			body: formData,
																			headers: {
																				'X-Requested-With': 'XMLHttpRequest' // for CI ajax detection if needed
																			}
																		})
																		.then(response => response.json())
																		.then(data => {
																			if (data.success) {
																				Swal.fire('Updated!', 'Student info has been updated.', 'success');
																				// Optionally close modal or refresh page/list here:
																				const modalEl = document.getElementById('editStudentModal');
																				const modal = bootstrap.Modal.getInstance(modalEl);
																				modal.hide();

																				// Reload the page
																				location.reload();
																			} else {
																				Swal.fire('Error!', data.message || 'Failed to update.', 'error');
																			}
																		})
																		.catch(() => {
																			Swal.fire('Error!', 'Something went wrong.', 'error');
																		});
																}
															});
														});
													</script>

													<script>
														document.querySelectorAll('.validate-registration').forEach(link => {
															link.addEventListener('click', function(e) {
																e.preventDefault();

																// Populate modal fields from data attributes
																document.getElementById('edit-user-id').value = this.dataset.userid;
																document.getElementById('edit-student-id').value = this.dataset.id;
																document.getElementById('edit-first-name').value = this.dataset.firstname;
																document.getElementById('edit-middle-name').value = this.dataset.middlename;
																document.getElementById('edit-last-name').value = this.dataset.lastname;
																document.getElementById('edit-department').value = this.dataset.deptId;
																document.getElementById('edit-year').value = this.dataset.year;
																document.getElementById('edit-sex').value = this.dataset.sex;
																document.getElementById('edit-email').value = this.dataset.email;

																// Show the modal
																const editModal = new bootstrap.Modal(document.getElementById('editStudentModal'));
																editModal.show();
															});
														});
													</script>


													<script>
														document.querySelectorAll('.validate-registration').forEach(button => {
															button.addEventListener('click', function(e) {
																e.preventDefault();

																// Get modal element
																const modalEl = document.getElementById('editStudentModal');

																// Populate fields here as you need
																// ...

																// Create modal instance (default options, including backdrop)
																const editModal = new bootstrap.Modal(modalEl);

																// Show the modal
																editModal.show();

																// Optional: When modal closes, dispose to clean up properly
																modalEl.addEventListener('hidden.bs.modal', () => {
																	editModal.dispose();
																}, {
																	once: true
																});
															});
														});
													</script>

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

										</div> <!-- end card -->
									</div> <!-- end modal-body -->
								</div> <!-- end modal-content -->
							</div> <!-- end modal-dialog -->
						</div> <!-- end modal -->
					</div>
				</div>
			</div>

			<!-- Import Modal -->
			<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<form class="modal-content" id="importForm">
						<div class="modal-header">
							<h5 class="modal-title" id="importModalLabel">Import List</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<div class="mb-3">
								<label for="importFile" class="form-label">Choose a file</label>
								<input type="file" name="import_file" class="form-control" id="importFile" accept=".csv, .xlsx" required>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-primary">Upload</button>
						</div>
					</form>
				</div>
			</div>

			<script>
				$(document).ready(function() {
					$('#importForm').submit(function(e) {
						e.preventDefault();

						// Show confirmation prompt
						Swal.fire({
							title: 'Are you sure?',
							text: "This action is not reversible. Make sure all details are correct before proceeding.",
							icon: 'warning',
							showCancelButton: true,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Yes, upload it!',
							cancelButtonText: 'Cancel'
						}).then((result) => {
							if (result.isConfirmed) {
								var formData = new FormData(this);

								// Show loading state
								Swal.fire({
									title: 'Uploading...',
									text: 'Please wait while we import your file.',
									allowOutsideClick: false,
									didOpen: () => {
										Swal.showLoading();
									}
								});

								$.ajax({
									url: "<?= site_url('admin/import-students'); ?>",
									type: "POST",
									data: formData,
									contentType: false,
									processData: false,
									dataType: 'json',
									success: function(response) {
										Swal.close();

										if (response.success) {
											Swal.fire({
												icon: 'success',
												title: 'Success!',
												text: response.message
											}).then(() => {
												$('#importModal').modal('hide');
												setTimeout(function() {
													window.location.reload();
												}, 2000);
											});
										} else {
											Swal.fire({
												icon: 'error',
												title: 'Import Failed',
												text: response.message
											});
										}
									},
									error: function() {
										Swal.close();
										Swal.fire({
											icon: 'error',
											title: 'Error',
											text: 'An unexpected error occurred. Please try again.'
										});
									}
								});
							}
						});
					});
				});
			</script>

			<!-- Importing of Department Officers -->
			<div class="col-md-6">
				<div class="border rounded p-3 h-100 d-flex flex-column justify-content-between">
					<div class="d-flex">
						<div class="me-3">
							<span class="fs-4 text-primary"><i class="fas fa-file-import"></i></span>
						</div>
						<div>
							<h6 class="mb-1 fw-bold">Importing of Department Officers</h6>
							<p class="mb-0 text-muted small">Upload bulk data of department officers using CSV or Excel files.</p>
						</div>
					</div>
					<div class="d-flex justify-content-end align-items-center gap-2 mt-3">
						<a href="<?= base_url('assets/templates/Template-ImportingDepartment.xlsx') ?>" class="btn btn-sm btn-outline-secondary" download>
							Download Template
						</a>
						<button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModalDept">
							Open
						</button>
					</div>
				</div>
			</div>

			<!-- Import Modal for Department Officers -->
			<div class="modal fade" id="importModalDept" tabindex="-1" aria-labelledby="importModalDeptLabel" aria-hidden="true">
				<div class="modal-dialog">
					<form class="modal-content" id="importFormDept">
						<div class="modal-header">
							<h5 class="modal-title" id="importModalDeptLabel">Import Department Officers</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<div class="mb-3">
								<label for="importFileDept" class="form-label">Choose a file</label>
								<input type="file" name="import_file" class="form-control" id="importFileDept" accept=".csv, .xlsx" required>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-primary">Upload</button>
						</div>
					</form>
				</div>
			</div>

			<!-- Script for Importing Department Officers -->
			<script>
				$(document).ready(function() {
					$('#importFormDept').submit(function(e) {
						e.preventDefault();

						Swal.fire({
							title: 'Are you sure?',
							text: "This action is not reversible. Make sure all details are correct before proceeding.",
							icon: 'warning',
							showCancelButton: true,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Yes, upload it!',
							cancelButtonText: 'Cancel'
						}).then((result) => {
							if (result.isConfirmed) {
								var formData = new FormData(this);

								Swal.fire({
									title: 'Uploading...',
									text: 'Please wait while we import your file.',
									allowOutsideClick: false,
									didOpen: () => {
										Swal.showLoading();
									}
								});

								$.ajax({
									url: "<?= site_url('admin/import-department-officers'); ?>",
									type: "POST",
									data: formData,
									contentType: false,
									processData: false,
									dataType: 'json',
									success: function(response) {
										Swal.close();

										if (response.success) {
											Swal.fire({
												icon: 'success',
												title: 'Success!',
												text: response.message
											}).then(() => {
												$('#importModalDept').modal('hide');
												setTimeout(function() {
													window.location.reload();
												}, 2000);
											});
										} else {
											Swal.fire({
												icon: 'error',
												title: 'Import Failed',
												text: response.message
											});
										}
									},
									error: function() {
										Swal.close();
										Swal.fire({
											icon: 'error',
											title: 'Error',
											text: 'An unexpected error occurred. Please try again.'
										});
									}
								});
							}
						});
					});
				});
			</script>

			<!-- Importing of Organization Officers -->
			<div class="col-md-6">
				<div class="border rounded p-3 h-100 d-flex flex-column justify-content-between">
					<div class="d-flex">
						<div class="me-3">
							<span class="fs-4 text-primary"><i class="fas fa-file-import"></i></span>
						</div>
						<div>
							<h6 class="mb-1 fw-bold">Importing of Organization Officers</h6>
							<p class="mb-0 text-muted small">Upload bulk data of organization officers using CSV or Excel files.</p>
						</div>
					</div>
					<div class="d-flex justify-content-end align-items-center gap-2 mt-3">
						<a href="<?= base_url('assets/templates/Template-ImportingOrganzation.xlsx') ?>" class="btn btn-sm btn-outline-secondary" download>
							Download Template
						</a>
						<button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModalOrg">
							Open
						</button>
					</div>
				</div>
			</div>

			<!-- Import Modal for Organization Officers -->
			<div class="modal fade" id="importModalOrg" tabindex="-1" aria-labelledby="importModalOrgLabel" aria-hidden="true">
				<div class="modal-dialog">
					<form class="modal-content" id="importFormOrg">
						<div class="modal-header">
							<h5 class="modal-title" id="importModalOrgLabel">Import Organization Officers</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<div class="mb-3">
								<label for="importFileOrg" class="form-label">Choose a file</label>
								<input type="file" name="import_file" class="form-control" id="importFileOrg" accept=".csv, .xlsx" required>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-primary">Upload</button>
						</div>
					</form>
				</div>
			</div>

			<!-- Script for Importing Organization Officers -->
			<script>
				$(document).ready(function() {
					$('#importFormOrg').submit(function(e) {
						e.preventDefault();

						Swal.fire({
							title: 'Are you sure?',
							text: "This action is not reversible. Make sure all details are correct before proceeding.",
							icon: 'warning',
							showCancelButton: true,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Yes, upload it!',
							cancelButtonText: 'Cancel'
						}).then((result) => {
							if (result.isConfirmed) {
								var formData = new FormData(this);

								Swal.fire({
									title: 'Uploading...',
									text: 'Please wait while we import your file.',
									allowOutsideClick: false,
									didOpen: () => {
										Swal.showLoading();
									}
								});

								$.ajax({
									url: "<?= site_url('admin/import-organization-officers'); ?>",
									type: "POST",
									data: formData,
									contentType: false,
									processData: false,
									dataType: 'json',
									success: function(response) {
										Swal.close();

										if (response.success) {
											Swal.fire({
												icon: 'success',
												title: 'Success!',
												text: response.message
											}).then(() => {
												$('#importModalOrg').modal('hide');
												setTimeout(function() {
													window.location.reload();
												}, 2000);
											});
										} else {
											Swal.fire({
												icon: 'error',
												title: 'Import Failed',
												text: response.message
											});
										}
									},
									error: function() {
										Swal.close();
										Swal.fire({
											icon: 'error',
											title: 'Error',
											text: 'An unexpected error occurred. Please try again.'
										});
									}
								});
							}
						});
					});
				});
			</script>

			<!-- Importing of Exempted Students -->
			<div class="col-md-6">
				<div class="border rounded p-3 h-100 d-flex flex-column justify-content-between">
					<div class="d-flex">
						<div class="me-3">
							<span class="fs-4 text-primary"><i class="fas fa-file-import"></i></span>
						</div>
						<div>
							<h6 class="mb-1 fw-bold">Importing of Exempted Students</h6>
							<p class="mb-0 text-muted small">Upload bulk data of exempted students (C.O.E) using CSV or Excel files.</p>
						</div>
					</div>
					<div class="d-flex justify-content-end align-items-center gap-2 mt-3">
						<a href="<?= base_url('assets/templates/Template-ImportingStudents.xlsx') ?>" class="btn btn-sm btn-outline-secondary" download>
							Download Template
						</a>
						<button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModalExempted">
							Open
						</button>
						<button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#studentExempted">
							View
						</button>

					</div>
				</div>
			</div>

			<!-- Modal -->
			<div class="modal fade" id="studentExempted" tabindex="-1" aria-labelledby="studentExemptedLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="studentExemptedLabel">Exempted Students</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<table class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>#</th>
										<th>Student ID</th>
										<th>Name</th>
										<th>Department</th>
									</tr>
								</thead>
								<tbody>
									<?php if (!empty($exempted)) : ?>
										<?php foreach ($exempted as $index => $student) : ?>
											<tr>
												<td><?= $index + 1 ?></td>
												<td><?= htmlspecialchars($student->student_id) ?></td>
												<td><?= htmlspecialchars($student->first_name . " " . $student->last_name) ?></td>
												<td><?= htmlspecialchars($student->dept_name) ?></td>
											</tr>
										<?php endforeach; ?>
									<?php else : ?>
										<tr>
											<td colspan="2" class="text-center">No exempted students found.</td>
										</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>

			<!-- Import Modal for Exempted Students -->
			<div class="modal fade" id="importModalExempted" tabindex="-1" aria-labelledby="importModalExemptedLabel" aria-hidden="true">
				<div class="modal-dialog">
					<form class="modal-content" id="importFormExempted">
						<div class="modal-header">
							<h5 class="modal-title" id="importModalExemptedLabel">Import Exempted Students</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<div class="mb-3">
								<label for="importFileExempted" class="form-label">Choose a file</label>
								<input type="file" name="import_file" class="form-control" id="importFileExempted" accept=".csv, .xlsx" required>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-primary">Upload</button>
						</div>
					</form>
				</div>
			</div>

			<!-- Script for Importing Exempted Students -->
			<script>
				$(document).ready(function() {
					$('#importFormExempted').submit(function(e) {
						e.preventDefault();

						Swal.fire({
							title: 'Are you sure?',
							text: "This action is not reversible. Make sure all details are correct before proceeding.",
							icon: 'warning',
							showCancelButton: true,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Yes, upload it!',
							cancelButtonText: 'Cancel'
						}).then((result) => {
							if (result.isConfirmed) {
								var formData = new FormData(this);

								Swal.fire({
									title: 'Uploading...',
									text: 'Please wait while we import your file.',
									allowOutsideClick: false,
									didOpen: () => {
										Swal.showLoading();
									}
								});

								$.ajax({
									url: "<?= site_url('admin/import-exempted-students'); ?>",
									type: "POST",
									data: formData,
									contentType: false,
									processData: false,
									dataType: 'json',
									success: function(response) {
										Swal.close();

										if (response.success) {
											Swal.fire({
												icon: 'success',
												title: 'Success!',
												text: response.message
											}).then(() => {
												$('#importModalExempted').modal('hide');
											});
										} else {
											Swal.fire({
												icon: 'error',
												title: 'Import Failed',
												text: response.message
											});
										}
									},
									error: function() {
										Swal.close();
										Swal.fire({
											icon: 'error',
											title: 'Error',
											text: 'An unexpected error occurred. Please try again.'
										});
									}
								});
							}
						});
					});
				});
			</script>


			<!-- QR Code Generator Section -->
			<!-- QR Code Generator Section -->
			<div class="col-md-6">
				<div class="border rounded p-3 h-100 d-flex flex-column">
					<div class="d-flex">
						<div class="me-3">
							<span class="fs-4 text-info"><i class="fas fa-qrcode"></i></span>
						</div>
						<div>
							<h6 class="mb-1 fw-bold">QR Code Generation</h6>
							<p class="mb-0 text-muted small">Automatically generate QR codes for students who don't have one yet.</p>
						</div>
					</div>
					<!-- Spacer pushes button to bottom -->
					<div class="mt-auto align-self-end">
						<button class="btn btn-sm btn-outline-info mt-3"
							id="openQRModal" data-bs-toggle="modal" data-bs-target="#qrModal">
							Open
						</button>
					</div>
				</div>
			</div>

			<!-- QR Modal -->
			<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="qrModalLabel">Generate QR Codes</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<p>This will generate and assign QR codes for all students without one.</p>
							<div class="text-end">
								<button class="btn btn-info" id="generateAllQrBtn">Generate All</button>
							</div>
							<div class="mt-3" id="qrStatus" style="display:none;">
								<p class="text-success mb-0">QR codes generated: <span id="qrCount"></span></p>
							</div>
							<!-- Container for displaying generated QR codes -->
							<div id="qrPreview" class="d-flex flex-wrap justify-content-center mt-3">
								<!-- QR Code images will be inserted here -->
							</div>
						</div>
					</div>
				</div>
			</div>

			<script>
				document.getElementById('generateAllQrBtn').addEventListener('click', function() {
					// Use SweetAlert for confirmation instead of the native confirm
					Swal.fire({
						title: 'Are you sure?',
						text: "Generate QR codes for all students who don't have one yet?",
						icon: 'warning',
						showCancelButton: true,
						confirmButtonText: 'Yes, generate!',
						cancelButtonText: 'Cancel'
					}).then((result) => {
						// If the user confirmed the action
						if (result.isConfirmed) {
							// Show the SweetAlert loading spinner
							Swal.fire({
								title: 'Generating QR Codes...',
								html: 'Please wait while the QR codes are being generated.',
								timerProgressBar: true,
								didOpen: () => {
									Swal.showLoading();
								},
								allowOutsideClick: false, // Prevent closing the modal by clicking outside
								allowEscapeKey: false // Prevent closing the modal by pressing ESC
							});

							// Send the request to generate the QR codes
							fetch('<?php echo site_url("admin/generate_bulk_qr"); ?>', {
									method: 'POST'
								})
								.then(res => res.json())
								.then(data => {
									// Close the SweetAlert modal once the request is completed
									Swal.close();

									// Handle the success response
									if (data.status === 'success') {
										document.getElementById('qrStatus').style.display = 'block';
										document.getElementById('qrCount').textContent = data.count;
									} else {
										Swal.fire({
											icon: 'error',
											title: 'Failed to generate QR codes',
											text: 'Something went wrong while generating QR codes.'
										});
									}
								})
								.catch(err => {
									// Close the SweetAlert modal if an error occurs
									Swal.close();

									console.error(err);
									Swal.fire({
										icon: 'error',
										title: 'An error occurred',
										text: 'Please try again later.'
									});
								});
						}
					});
				});
			</script>

			<!-- Manage Organization -->
			<div class="col-md-6">
				<div class="border rounded p-3 h-100 d-flex flex-column justify-content-between position-relative">
					<div class="d-flex">
						<div class="me-3">
							<span class="fs-4 text-success"><i class="fas fa-building"></i></span>
						</div>
						<div>
							<h6 class="mb-1 fw-bold">Manage Organization</h6>
							<p class="mb-0 text-muted small">
								Add new organizations, view existing ones, and update organization.
							</p>
						</div>
					</div>
					<div class="d-flex justify-content-end gap-2 mt-3">
						<button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewOrganizationsModal">View</button>
						<button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addOrganizationModal">Add</button>
					</div>
				</div>
			</div>

			<!-- Add Organization Modal -->
			<div class="modal fade" id="addOrganizationModal" tabindex="-1" role="dialog" aria-labelledby="addOrganizationModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<form id="addOrganizationForm" enctype="multipart/form-data">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">Add Organization</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<div class="mb-3">
									<label for="org_name" class="form-label">Organization Name</label>
									<input type="text" class="form-control" id="org_name" name="org_name" required>
								</div>
								<div class="mb-3">
									<label for="org_logo" class="form-label">Organization Logo</label>
									<input type="file" class="form-control" id="org_logo" name="org_logo" accept="image/*">
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-primary">Save</button>
							</div>
						</div>
					</form>
				</div>
			</div>


			<!-- Verify Receipt -->
			<div class="col-md-6">
				<div class="border rounded p-3 h-100 d-flex flex-column justify-content-between position-relative">
					<div class="d-flex">
						<div class="me-3">
							<span class="fs-4 text-info"><i class="fas fa-receipt"></i></span>
						</div>
						<div>
							<h6 class="mb-1 fw-bold">Verify Receipt</h6>
							<p class="mb-0 text-muted small">
								Search for and validate a receipt by reference number.
							</p>
						</div>
					</div>
					<div class="d-flex justify-content-end gap-2 mt-3">
						<!-- Example trigger to open a modal or redirect to a verify receipt page -->
						<a href="<?= base_url('admin/verify-receipt-page') ?>" class="btn btn-sm btn-outline-success">Go</a>
					</div>
				</div>
			</div>


			<script>
				$('#addOrganizationForm').on('submit', function(e) {
					e.preventDefault();

					const form = this;
					const formData = new FormData(form);

					Swal.fire({
						title: 'Are you sure?',
						text: "Do you want to add this organization?",
						icon: 'question',
						showCancelButton: true,
						confirmButtonText: 'Yes, add it!',
						cancelButtonText: 'Cancel'
					}).then((result) => {
						if (result.isConfirmed) {
							$.ajax({
								url: "<?php echo site_url('admin/save-organization'); ?>",
								type: 'POST',
								data: formData,
								contentType: false,
								processData: false,
								dataType: 'json',
								success: function(response) {
									if (response.success) {
										Swal.fire({
											icon: 'success',
											title: 'Success',
											text: 'Organization added successfully!',
											confirmButtonColor: '#3085d6',
											timer: 2000,
											showConfirmButton: false
										});

										$('#addOrganizationModal').modal('hide');
										$('#addOrganizationForm')[0].reset();
										// Optionally refresh a table or list here
									} else {
										Swal.fire({
											icon: 'error',
											title: 'Error',
											text: response.message || 'An error occurred while saving.'
										});
									}
								},
								error: function() {
									Swal.fire({
										icon: 'error',
										title: 'Request Failed',
										text: 'Something went wrong while saving the organization.'
									});
								}
							});
						}
					});
				});
			</script>

			<!-- View Organizations Modal -->
			<div class="modal fade" id="viewOrganizationsModal" tabindex="-1" aria-labelledby="viewOrganizationsModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header bg-light">
							<h5 class="modal-title" id="viewOrganizationsModalLabel">Manage Organizations</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<div class="table-responsive">
								<table class="table table-hover align-middle" id="orgTable">
									<thead class="table-light">
										<tr>
											<th>#</th>
											<th>Logo</th>
											<th>Organization Name</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
										<!-- Dynamic rows via AJAX -->
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Edit Organization Modal -->
			<div class="modal fade" id="editOrganizationModal" tabindex="-1" aria-labelledby="editOrganizationModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<form id="editOrganizationForm" enctype="multipart/form-data">
						<div class="modal-content">
							<div class="modal-header bg-light">
								<h5 class="modal-title">Edit Organization</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
							</div>
							<div class="modal-body">
								<input type="hidden" name="id" id="editOrgId">
								<div class="mb-3">
									<label for="editOrgName" class="form-label">Organization Name</label>
									<input type="text" name="org_name" id="editOrgName" class="form-control" required>
								</div>
								<div class="mb-3">
									<label class="form-label">New Logo (optional)</label>
									<input type="file" name="logo" class="form-control">
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-primary">Update</button>
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
							</div>
						</div>
					</form>
				</div>
			</div>

			<script>
				// Fetch and display organizations in modal
				function loadOrganizations() {
					$.ajax({
						url: "<?php echo site_url('admin/get_organizations'); ?>",
						method: 'GET',
						dataType: 'json',
						success: function(data) {
							let html = '';
							const baseUrl = "<?php echo base_url('assets/imageOrg/'); ?>";

							data.forEach((org, index) => {
								html += `
					<tr>
						<td>${index + 1}</td>
						<td><img src="${baseUrl}${org.logo}" class="rounded" width="40" height="40" /></td>
						<td>${org.org_name}</td>
						<td>
							<button class="btn btn-sm btn-primary edit-org" data-id="${org.org_id}" data-name="${org.org_name}" data-logo="${org.logo}">
								<i class="fas fa-edit"></i>
							</button>
						</td>
					</tr>`;
							});

							$('#orgTable tbody').html(html);
						},
						error: function() {
							Swal.fire('Error', 'Failed to load organizations.', 'error');
						}
					});
				}

				// Trigger the function when the modal is shown
				$('#viewOrganizationsModal').on('shown.bs.modal', loadOrganizations);

				// Edit button opens modal pre-filled
				$(document).on('click', '.edit-org', function() {
					const id = $(this).data('id');
					const name = $(this).data('name');
					const logo = $(this).data('logo');

					$('#editOrgId').val(id);
					$('#editOrgName').val(name);
					$('#currentOrgLogo').attr('src', "<?php echo base_url('assets/imageOrg/'); ?>" + logo);
					$('#editOrganizationModal').modal('show');
				});

				// Submit update with confirmation
				$('#editOrganizationForm').submit(function(e) {
					e.preventDefault();
					const form = this;
					const formData = new FormData(form);

					Swal.fire({
						title: 'Are you sure?',
						text: "Do you want to update this organization?",
						icon: 'question',
						showCancelButton: true,
						confirmButtonText: 'Yes, update it!',
						cancelButtonText: 'Cancel'
					}).then((result) => {
						if (result.isConfirmed) {
							$.ajax({
								url: "<?php echo site_url('admin/update-organization'); ?>",
								type: 'POST',
								data: formData,
								contentType: false,
								processData: false,
								success: function(response) {
									Swal.fire('Updated!', 'Organization updated successfully.', 'success');
									$('#editOrganizationModal').modal('hide');
									loadOrganizations();
								},
								error: function() {
									Swal.fire('Error!', 'Update failed.', 'error');
								}
							});
						}
					});
				});
			</script>
		</div>
	</div>
</div>


<!-- Other Section -->
<div class="card mb-3">
	<div class="card-header bg-light">
		<h6 class="mb-0">Customize</h6>
	</div>
	<div class="card-body">
		<div class="row g-3">
			<!-- Header and Footer -->
			<div class="col-md-6">
				<div class="border rounded p-3 h-100 d-flex justify-content-between align-items-start">
					<div class="d-flex">
						<div class="me-3">
							<span class="fs-4 text-success"><i class="fas fa-file-alt"></i></span>
						</div>
						<div>
							<h6 class="mb-1">Report Header & Footer</h6>
							<p class="mb-0 text-muted small">Customize report documents with institutional headers and footers.</p>
						</div>
					</div>
					<button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#headerFooterModal">Open</button>
				</div>
			</div>

			<!-- Logo Upload -->
			<div class="col-md-6">
				<div class="border rounded p-3 h-100 d-flex justify-content-between align-items-start">
					<div class="d-flex">
						<div class="me-3">
							<span class="fs-4 text-warning"><i class="fas fa-image"></i></span>
						</div>
						<div>
							<h6 class="mb-1">Logo Upload</h6>
							<p class="mb-0 text-muted small">Upload your organization’s logo for use in receipts, reports, and more.</p>
						</div>
					</div>
					<button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#logoModal">Open</button>
				</div>
			</div>

			<!-- Active Semester -->
			<div class="col-md-6">
				<div class="border rounded p-3 h-100 d-flex justify-content-between align-items-start">
					<div class="d-flex">
						<div class="me-3">
							<span class="fs-4 text-warning"><i class="fas fa-calendar-alt"></i></span>
						</div>
						<div>
							<h6 class="mb-1">Active Semester</h6>
							<p class="mb-0 text-muted small">Set your active semester and academic year for filtering and reporting.</p>
						</div>
					</div>
					<button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#semesterModal">Open</button>
				</div>
			</div>

			<!-- Modal for Setting Active Semester -->
			<div class="modal fade" id="semesterModal" tabindex="-1" aria-labelledby="semesterModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">

						<div class="modal-header">
							<h5 class="modal-title" id="semesterModalLabel">Set Active Semester</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>

						<div class="modal-body">
							<!-- Semester Select -->
							<div class="mb-3">
								<label for="active-semester" class="form-label">Semester</label>
								<select id="active-semester" class="form-select">
									<option value="">Select Semester</option>
									<option value="1st Semester">1st Semester</option>
									<option value="2nd Semester">2nd Semester</option>
								</select>
							</div>

							<!-- Academic Year Select -->
							<div class="mb-3">
								<label for="academic-year" class="form-label">Academic Year</label>
								<div class="input-group">
									<select id="active-start-year" class="form-select">
										<option value="">Start Year</option>
									</select>
									<span class="input-group-text">-</span>
									<select id="active-end-year" class="form-select">
										<option value="">End Year</option>
									</select>
								</div>
								<div id="year-feedback" class="invalid-feedback d-none">
									Please select a valid year range with a 1-year difference.
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
							<button type="button" class="btn btn-primary" onclick="saveActiveSemester()">Set</button>
						</div>

					</div>
				</div>
			</div>

			<script>
				document.addEventListener("DOMContentLoaded", function() {
					const currentYear = new Date().getFullYear();
					const startYear = $('#active-start-year');
					const endYear = $('#active-end-year');

					// Populate start year dropdown
					for (let y = currentYear; y >= 2000; y--) {
						startYear.append(new Option(y, y));
					}

					// On change of start year, populate end year
					startYear.on('change', function() {
						const start = parseInt(this.value);
						endYear.empty().append(new Option("End Year", "", true, true));

						if (start) {
							endYear.append(new Option(start + 1, start + 1));
						}
					});
				});

				function saveActiveSemester() {
					const semester = $('#active-semester').val();
					const startYear = $('#active-start-year').val();
					const endYear = $('#active-end-year').val();

					if (!semester || !startYear || !endYear || parseInt(endYear) - parseInt(startYear) !== 1) {
						$('#year-feedback').removeClass('d-none');
						return;
					}

					$('#year-feedback').addClass('d-none');

					const activeLabel = `${semester} AY ${startYear}-${endYear}`;

					// ✅ AJAX request to insert into database
					$.post("<?= site_url('admin/active-semester') ?>", {
						semester: semester,
						start_year: startYear,
						end_year: endYear
					}, function(response) {
						if (response.status === 'success') {
							alert(response.message);
							$('#activeSemesterLabel').text(response.value); // update the label (optional)
							$('#semesterModal').modal('hide'); // hide modal
						} else {
							alert(response.message);
						}
					}, 'json');
				}
			</script>





		</div>
	</div>
</div>

<!-- Header/Footer Modal -->
<div class="modal fade" id="headerFooterModal" tabindex="-1">
	<div class="modal-dialog">
		<form class="modal-content" id="headerFooterForm" method="post" enctype="multipart/form-data" action="<?= base_url('AdminController/save_header_footer') ?>">
			<div class="modal-header">
				<h5 class="modal-title">Customize Header & Footer</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">

				<!-- Dropdown -->
				<div class="mb-3">
					<label for="hfFor" class="form-label">Apply To:</label>
					<select class="form-select" name="hf_for" id="hfFor" required>
						<option value="" disabled selected>Select type</option>
						<?php foreach ($logo_targets as $target): ?>
							<option value="<?= $target['value'] ?>"><?= $target['label'] ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<!-- Show Current Header -->
				<?php if (!empty($current_header) && file_exists($current_header)): ?>
					<div class="mb-3">
						<label class="form-label">Current Header:</label>
						<div>
							<img src="<?= base_url($current_header) ?>" alt="Current Header" style="max-width: 100%; height: auto;">
						</div>
					</div>
				<?php endif; ?>

				<!-- Header Upload -->
				<div class="mb-3">
					<label for="headerFile" class="form-label">Upload Header Image:</label>
					<input type="file" name="header_file" class="form-control" id="headerFile" accept="image/*">
				</div>

				<!-- Image Preview -->
				<div class="mb-3" id="headerPreviewContainer" style="display: none;">
					<label class="form-label">Header Preview:</label>
					<div>
						<img id="headerPreview" src="" alt="Header Preview" style="max-width: 100%; height: auto;">
					</div>
				</div>


				<!-- Show Current Footer -->
				<?php if (!empty($current_footer) && file_exists($current_footer)): ?>
					<div class="mb-3">
						<label class="form-label">Current Footer:</label>
						<div>
							<img src="<?= base_url($current_footer) ?>" alt="Current Footer" style="max-width: 100%; height: auto;">
						</div>
					</div>
				<?php endif; ?>

				<!-- Footer Upload -->
				<div class="mb-3">
					<label for="footerFile" class="form-label">Upload Footer Image:</label>
					<input type="file" name="footer_file" class="form-control" id="footerFile" accept="image/*">
				</div>

				<!-- Image Preview -->
				<div class="mb-3" id="footerPreviewContainer" style="display: none;">
					<label class="form-label">Footer Preview:</label>
					<div>
						<img id="footerPreview" src="" alt="Footer Preview" style="max-width: 100%; height: auto;">
					</div>
				</div>

				<!-- Paper Preview (combined header and footer) -->
				<div class="mb-3" id="paperPreviewContainer" style="display: none;">
					<label class="form-label">Preview on Paper:</label>
					<div style="border: 1px solid #ccc; padding: 20px; max-width: 100%; width: auto; background-color: #fff; overflow: hidden;">
						<div id="paperHeader" style="margin-bottom: 20px;"></div>
						<div id="paperContent" style="min-height: 300px; background-color: #f9f9f9;"></div>
						<div id="paperFooter" style="margin-top: 20px;"></div>
					</div>
				</div>


			</div>
			<div class="modal-footer">
				<button class="btn btn-success" type="submit">Save</button>
			</div>
		</form>
	</div>
</div>


<script>
	// Header input preview
	document.getElementById('headerFile').addEventListener('change', function(e) {
		const file = e.target.files[0];
		if (file) {
			const reader = new FileReader();
			reader.onload = function(event) {
				document.getElementById('headerPreview').src = event.target.result;
				document.getElementById('headerPreviewContainer').style.display = 'block';
				document.getElementById('paperHeader').innerHTML = `<img src="${event.target.result}" style="width: 100%; height: auto;">`;
				document.getElementById('paperPreviewContainer').style.display = 'block';
			};
			reader.readAsDataURL(file);
		}
	});

	// Footer input preview
	document.getElementById('footerFile').addEventListener('change', function(e) {
		const file = e.target.files[0];
		if (file) {
			const reader = new FileReader();
			reader.onload = function(event) {
				document.getElementById('footerPreview').src = event.target.result;
				document.getElementById('footerPreviewContainer').style.display = 'block';
				document.getElementById('paperFooter').innerHTML = `<img src="${event.target.result}" style="width: 100%; height: auto;">`;
				document.getElementById('paperPreviewContainer').style.display = 'block';
			};
			reader.readAsDataURL(file);
		}
	});
</script>


<!-- FOR PAPER PREVIEW -->
<script>
	// Header preview logic
	document.getElementById('headerFile').addEventListener('change', function(e) {
		const file = e.target.files[0];
		if (file) {
			const reader = new FileReader();
			reader.onload = function(event) {
				// Set header preview image
				document.getElementById('headerPreview').src = event.target.result;
				document.getElementById('headerPreviewContainer').style.display = 'block';

				// Update paper preview
				document.getElementById('paperHeader').innerHTML = `<img src="${event.target.result}" alt="Header Image" style="width: 100%; height: auto;">`;
				document.getElementById('paperPreviewContainer').style.display = 'block';
			};
			reader.readAsDataURL(file);
		}
	});

	// Footer preview logic
	document.getElementById('footerFile').addEventListener('change', function(e) {
		const file = e.target.files[0];
		if (file) {
			const reader = new FileReader();
			reader.onload = function(event) {
				// Set footer preview image
				document.getElementById('footerPreview').src = event.target.result;
				document.getElementById('footerPreviewContainer').style.display = 'block';

				// Update paper preview
				document.getElementById('paperFooter').innerHTML = `<img src="${event.target.result}" alt="Footer Image" style="width: 100%; height: auto;">`;
				document.getElementById('paperPreviewContainer').style.display = 'block';
			};
			reader.readAsDataURL(file);
		}
	});
</script>

<!-- CURRENT HEADER and FOOTER -->

<script>
	function fetchCurrentHeaderFooter() {
		$.ajax({
			url: '<?= base_url('AdminController/get_current_header_footer') ?>',
			method: 'GET',
			dataType: 'json',
			success: function(response) {
				let hasContent = false;

				if (response.header) {
					$('#paperHeader').html(`<img src="${response.header}" style="width: 100%; height: auto;">`);
					hasContent = true;
				} else {
					$('#paperHeader').html('<p class="text-muted">No current header uploaded yet.</p>');
				}

				if (response.footer) {
					$('#paperFooter').html(`<img src="${response.footer}" style="width: 100%; height: auto;">`);
					hasContent = true;
				} else {
					$('#paperFooter').html('<p class="text-muted">No current footer uploaded yet.</p>');
				}

				$('#paperPreviewContainer').toggle(true); // Always show the preview
			},
			error: function() {
				$('#paperHeader').html('<p class="text-danger">Failed to load header.</p>');
				$('#paperFooter').html('<p class="text-danger">Failed to load footer.</p>');
				$('#paperPreviewContainer').show();
			}
		});
	}

	// Call fetch when modal opens
	$('#headerFooterModal').on('show.bs.modal', function() {
		fetchCurrentHeaderFooter();
	});
</script>


<script>
	$('#headerFooterForm').on('submit', function(e) {
		e.preventDefault();

		const formData = new FormData(this);

		Swal.fire({
			title: 'Uploading...',
			text: 'Please wait while the header and footer are being uploaded.',
			didOpen: () => Swal.showLoading(),
			allowOutsideClick: false
		});

		$.ajax({
			url: "<?= base_url('AdminController/save_header_footer') ?>",
			type: "POST",
			data: formData,
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function(response) {
				Swal.close();
				if (response.success) {
					Swal.fire({
						icon: 'success',
						title: 'Success!',
						text: response.message
					}).then(() => {
						$('#headerFooterModal').modal('hide');
						location.reload(); // Refresh to show new preview if needed
					});
				} else {
					Swal.fire({
						icon: 'error',
						title: 'Upload Failed',
						text: response.message
					});
				}
			},
			error: function() {
				Swal.close();
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'An unexpected error occurred. Please try again.'
				});
			}
		});
	});
</script>





<!-- Logo Upload Modal -->
<div class="modal fade" id="logoModal" tabindex="-1">
	<div class="modal-dialog">
		<form class="modal-content" id="logoUploadForm" method="post" enctype="multipart/form-data" action="<?= base_url('AdminController/upload_logo') ?>">
			<div class="modal-header">
				<h5 class="modal-title">Upload Logo</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">

				<!-- Dropdown for selecting logo type -->
				<div class="mb-3">
					<label for="logoFor" class="form-label">Upload Logo For:</label>
					<select class="form-select" name="logo_for" id="logoFor" required>
						<option value="" disabled selected>Select type</option>
						<?php foreach ($logo_targets as $target): ?>
							<option value="<?= $target['value'] ?>"><?= $target['label'] ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<!-- File input -->
				<div class="mb-3">
					<label for="logoFile" class="form-label">Choose Logo:</label>
					<input type="file" name="logo_file" class="form-control" id="logoFile" accept="image/*" required>
				</div>


				<!-- Current Logo Preview -->
				<div class="mb-3" id="currentLogoContainer">
					<label class="form-label">Current Logo:</label>
					<div id="currentLogoWrapper">
						<p class="text-muted">No current logo yet.</p>
					</div>
				</div>


				<script>
					function loadCurrentLogo() {
						$.ajax({
							url: "<?= base_url('AdminController/get_current_logo') ?>",
							method: 'GET',
							dataType: 'json',
							success: function(res) {
								if (res.success) {
									$('#currentLogoWrapper').html(
										'<img src="' + res.logo + '" alt="Current Logo" style="max-width: 100%; height: auto;">'
									);
								} else {
									$('#currentLogoWrapper').html(
										'<p class="text-muted">No current logo yet.</p>'
									);
								}
							},
							error: function() {
								console.error("Could not fetch current logo.");
							}
						});
					}

					$(document).ready(function() {
						$('#logoModal').on('shown.bs.modal', function() {
							loadCurrentLogo();
						});

						$('#logoFor').on('change', function() {
							loadCurrentLogo(); // Just call the function directly
						});
					});
				</script>


				<script>
					$(document).ready(function() {
						// Reload current logo preview when dropdown changes
						$('#logoFor').on('change', function() {
							$('#logoModal').trigger('shown.bs.modal'); // Simulate modal show to refresh the current logo
						});
					});
				</script>


				<!-- Image Preview -->
				<div class="mb-3" id="logoPreviewContainer" style="display: none;">
					<label class="form-label">Logo Preview:</label>
					<div>
						<img id="logoPreview" src="" alt="Logo Preview" style="max-width: 100%; height: auto;">
					</div>
				</div>

			</div>
			<div class="modal-footer">
				<button class="btn btn-warning" type="submit">Upload</button>
			</div>
		</form>
	</div>
</div>


<!-- FOR IMAGE PREVIEW -->
<script>
	// Show image preview when a file is selected
	$('#logoFile').on('change', function(event) {
		const file = event.target.files[0];

		if (file) {
			const reader = new FileReader();

			reader.onload = function(e) {
				$('#logoPreview').attr('src', e.target.result); // Set the preview image
				$('#logoPreviewContainer').show(); // Show the preview container
			};

			reader.readAsDataURL(file); // Read the selected file as a Data URL
		} else {
			$('#logoPreviewContainer').hide(); // Hide the preview if no file is selected
		}
	});
</script>

<script>
	$('#logoUploadForm').on('submit', function(e) {
		e.preventDefault();

		const formData = new FormData(this);

		Swal.fire({
			title: 'Uploading...',
			text: 'Please wait while the logo is being uploaded.',
			didOpen: () => Swal.showLoading(),
			allowOutsideClick: false
		});

		$.ajax({
			url: "<?= base_url('AdminController/upload_logo') ?>",
			type: "POST",
			data: formData,
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function(response) {
				Swal.close();
				if (response.success) {
					Swal.fire({
						icon: 'success',
						title: 'Success!',
						text: response.message
					}).then(() => {
						$('#logoModal').modal('hide');
						location.reload(); // Optional: reload to show the new logo
					});
				} else {
					Swal.fire({
						icon: 'error',
						title: 'Upload Failed',
						text: response.message
					});
				}
			},
			error: function() {
				Swal.close();
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'An unexpected error occurred. Please try again.'
				});
			}
		});
	});
</script>