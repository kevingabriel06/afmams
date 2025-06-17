<!-- Updated Card with Button Triggers -->
<div class="card mb-3">
	<div class="card-header bg-light">
		<h5 class="mb-0">General Settings</h5>
	</div>
</div>

<!-- Importing Students -->
<div class="card mb-3">
	<div class="card-header bg-light">
		<h6 class="mb-0">Student Section</h6>
	</div>
	<div class="card-body">
		<div class="row g-3">
			<?php if (!empty($this->session->dept_id)) : ?>
				<!-- Importing of Department Officers -->
				<div class="col-md-6">
					<div class="border rounded p-3 h-100 d-flex flex-column justify-content-between">
						<div class="d-flex">
							<div class="me-3">
								<span class="fs-4 text-primary"><i class="fas fa-file-import"></i></span>
							</div>
							<div>
								<h6 class="mb-1 fw-bold">Importing of Students</h6>
								<p class="mb-0 text-muted small">Upload bulk data of students of department using CSV or Excel files.</p>
							</div>
						</div>
						<div class="d-flex justify-content-end align-items-center gap-2 mt-3">
							<a href="<?= base_url('assets/templates/Template-ImportingDeptOfficers.xlsx') ?>" class="btn btn-sm btn-outline-secondary" download>
								Download Template
							</a>
							<button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModalDept">
								Open
							</button>
						</div>
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
							<a href="<?= base_url('officer/verify-receipt-page') ?>" class="btn btn-sm btn-outline-success">Go</a>
						</div>
					</div>
				</div>

				<!-- Import Modal for Department Officers -->
				<div class="modal fade" id="importModalDept" tabindex="-1" aria-labelledby="importModalDeptLabel" aria-hidden="true">
					<div class="modal-dialog">
						<form class="modal-content" id="importFormDept">
							<div class="modal-header">
								<h5 class="modal-title" id="importModalDeptLabel">Import Students</h5>
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

			<?php elseif (!empty($this->session->userdata('org_id'))): ?>
				<!-- Importing of Organization Officers -->
				<div class="col-md-6">
					<div class="border rounded p-3 h-100 d-flex flex-column justify-content-between">
						<div class="d-flex">
							<div class="me-3">
								<span class="fs-4 text-primary"><i class="fas fa-file-import"></i></span>
							</div>
							<div>
								<h6 class="mb-1 fw-bold">Importing of Organization Members</h6>
								<p class="mb-0 text-muted small">Upload bulk data of organization members using CSV or Excel files.</p>
							</div>
						</div>
						<div class="d-flex justify-content-end align-items-center gap-2 mt-3">
							<a href="<?= base_url('assets/templates/Template-ImportingOrgOfficers.xlsx') ?>" class="btn btn-sm btn-outline-secondary" download>
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
								<h5 class="modal-title" id="importModalOrgLabel">Import Organization Members</h5>
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
			<?php endif; ?>

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
					$.post("<?= site_url('OfficerController/save_active_semester') ?>", {
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

			<!-- Active Semester End -->


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
			url: '<?= base_url('OfficerController/get_current_header_footer') ?>',
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
			url: "<?= base_url('OfficerController/save_header_footer') ?>",
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
							url: "<?= base_url('OfficerController/get_current_logo') ?>",
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
			url: "<?= base_url('OfficerController/upload_logo') ?>",
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
			url: "<?= base_url('OfficerController/upload_logo') ?>",
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