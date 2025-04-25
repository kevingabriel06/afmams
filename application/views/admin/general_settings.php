<!-- Updated Card with Button Triggers -->
<div class="card mb-3">
	<div class="card-header bg-light">
		<h5 class="mb-0">General Settings</h5>
	</div>
	<div class="card-body">
		<div class="row g-4">
			<!-- Importing of List -->
			<div class="col-md-6">
				<div class="border rounded p-3 h-100 d-flex flex-column justify-content-between">
					<div class="d-flex">
						<div class="me-3">
							<span class="fs-4 text-primary"><i class="fas fa-file-import"></i></span>
						</div>
						<div>
							<h6 class="mb-1">Importing of Students</h6>
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
					</div>
				</div>
			</div>

			<!-- Importing of Officers -->
			<div class="col-md-6">
				<div class="border rounded p-3 h-100 d-flex flex-column justify-content-between">
					<div class="d-flex">
						<div class="me-3">
							<span class="fs-4 text-primary"><i class="fas fa-file-import"></i></span>
						</div>
						<div>
							<h6 class="mb-1">Importing of Officer</h6>
							<p class="mb-0 text-muted small">Upload bulk data of officers using CSV or Excel files.</p>
						</div>
					</div>
					<div class="d-flex justify-content-end align-items-center gap-2 mt-3">
						<a href="<?= base_url('assets/templates/Template-ImportingStudents.xlsx') ?>" class="btn btn-sm btn-outline-secondary" download>
							Download Template
						</a>
						<button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
							Open
						</button>
					</div>
				</div>
			</div>

			<!-- QR Code Generator Section -->
			<div class="col-md-6">
				<div class="border rounded p-3 h-100 d-flex justify-content-between align-items-start">
					<div class="d-flex">
						<div class="me-3">
							<span class="fs-4 text-info"><i class="fas fa-qrcode"></i></span>
						</div>
						<div>
							<h6 class="mb-1">QR Code Generation</h6>
							<p class="mb-0 text-muted small">Automatically generate QR codes for students who don't have one yet.</p>
						</div>
					</div>
					<button class="btn btn-sm btn-outline-info" id="openQRModal" data-bs-toggle="modal" data-bs-target="#qrModal">Open</button>
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


			<!-- Record Cash Payment -->
			<div class="col-md-6">
				<div class="border rounded p-3 h-100 d-flex justify-content-between align-items-start">
					<div class="d-flex">
						<div class="me-3">
							<span class="fs-4 text-danger"><i class="fas fa-money-bill-wave"></i></span>
						</div>
						<div>
							<h6 class="mb-1">Record Cash Payment</h6>
							<p class="mb-0 text-muted small">Manually input registration details for students who paid via cash.</p>
						</div>
					</div>
					<a href="<?= base_url('admin/record-cash-payment') ?>" class="btn btn-sm btn-outline-danger">
						Open
					</a>

				</div>
			</div>



		</div>
	</div>
</div>

<!-- Add these modals at the bottom of your file -->

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

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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



<!-- QR Modal -->
<div class="modal fade" id="qrModal" tabindex="-1">
	<div class="modal-dialog">
		<form class="modal-content" method="post" action="<?= base_url('settings/generate_qr') ?>">
			<div class="modal-header">
				<h5 class="modal-title">Generate QR Code</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<input type="text" name="qr_data" class="form-control" placeholder="Enter data to encode" required>
			</div>
			<div class="modal-footer">
				<button class="btn btn-info" type="submit">Generate</button>
			</div>
		</form>
	</div>
</div>

<!-- Header/Footer Modal -->
<div class="modal fade" id="headerFooterModal" tabindex="-1">
	<div class="modal-dialog">
		<form class="modal-content" method="post" action="<?= base_url('settings/save_header_footer') ?>">
			<div class="modal-header">
				<h5 class="modal-title">Customize Header & Footer</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<label>Header:</label>
				<textarea name="report_header" class="form-control mb-2" rows="2" placeholder="Header text here..."></textarea>
				<label>Footer:</label>
				<textarea name="report_footer" class="form-control" rows="2" placeholder="Footer text here..."></textarea>
			</div>
			<div class="modal-footer">
				<button class="btn btn-success" type="submit">Save</button>
			</div>
		</form>
	</div>
</div>

<!-- Logo Upload Modal -->
<div class="modal fade" id="logoModal" tabindex="-1">
	<div class="modal-dialog">
		<form class="modal-content" method="post" enctype="multipart/form-data" action="<?= base_url('settings/upload_logo') ?>">
			<div class="modal-header">
				<h5 class="modal-title">Upload Logo</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<input type="file" name="logo_file" class="form-control" accept="image/*" required>
			</div>
			<div class="modal-footer">
				<button class="btn btn-warning" type="submit">Upload</button>
			</div>
		</form>
	</div>
</div>