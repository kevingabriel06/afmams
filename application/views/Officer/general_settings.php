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
	// Header preview logic
	document.getElementById('headerFile').addEventListener('change', function(e) {
		const file = e.target.files[0];
		if (file) {
			const reader = new FileReader();
			reader.onload = function(event) {
				document.getElementById('headerPreview').src = event.target.result;
				document.getElementById('headerPreviewContainer').style.display = 'block';
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
				document.getElementById('footerPreview').src = event.target.result;
				document.getElementById('footerPreviewContainer').style.display = 'block';
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