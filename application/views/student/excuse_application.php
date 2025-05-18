<div class="container">
	<div class="card mb-3">
		<div class="card-header">
			<div class="d-flex align-items-center">
				<h5 class="mb-0 fw-bold">Excuse Application Form</h5>
			</div>
		</div>

		<!-- Excuse Application Form -->
		<div class="card">
			<div class="card-body">
				<form id="excuseForm" enctype="multipart/form-data">
					<!-- Name Fields -->
					<input type="hidden" name="student_id" value="<?php echo $this->session->userdata('student_id'); ?>">

					<div class="row mb-3">
						<div class="col-md-12">
							<label for="activitySelect" class="form-label">Activity</label>
							<select class="form-select" id="activitySelect" name="activity_id">
								<option value="" selected disabled>Select Activity</option>
								<?php foreach ($activities as $activity) : ?>
									<option value="<?php echo $activity->activity_id; ?>"><?php echo $activity->activity_title; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<!-- Subject & Message -->
					<div class="mb-3">
						<label for="emailSubject" class="form-label">Subject</label>
						<input type="text" class="form-control" id="emailSubject" name="emailSubject" placeholder="Subject">
					</div>
					<div class="mb-3">
						<label for="emailBody" class="form-label">Message</label>
						<textarea class="form-control" id="emailBody" name="emailBody" rows="5" placeholder="Type your message here..."></textarea>
					</div>

					<!-- File Upload Section -->
					<div class="mb-3">
						<label class="form-label">Upload Image only</i></label>
						<div class="d-flex align-items-center">
							<!-- Hidden file input -->
							<input type="file" class="d-none" id="fileUpload" name="fileUpload" accept="image/*" multiple onchange="updateFilePreview()">

							<!-- File upload button -->
							<label for="fileUpload" class="btn btn-secondary d-flex align-items-center">
								<i class="fas fa-paperclip me-2"></i> Attach Files
							</label>

							<!-- Display selected file names -->
							<span id="fileName" class="ms-2 text-muted">No files selected</span>
						</div>
						<!-- File Preview Section -->
						<div id="filePreview" class="d-inline-flex flex-column mt-2">
							<!-- This will be dynamically filled with file previews -->
						</div>
					</div>

					<!-- Modal for Image/PDF Preview -->
					<div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true">
						<div class="modal-dialog modal-lg">
							<!-- Use modal-xl for larger width -->
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="filePreviewModalLabel">File Preview</h5>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body">
									<div id="modalContent"></div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
								</div>
							</div>
						</div>
					</div>

					<script>
						function updateFilePreview() {
							var files = document.getElementById('fileUpload').files;
							var fileNameText = [];
							var previewContainer = document.getElementById('filePreview');
							previewContainer.innerHTML = ''; // Clear any previous previews

							// Loop through the selected files
							for (var i = 0; i < files.length; i++) {
								var file = files[i];
								fileNameText.push(file.name);

								var fileReader = new FileReader();
								fileReader.onload = function(e) {
									var fileType = file.type;
									var filePreviewElement = document.createElement('div');
									filePreviewElement.classList.add('border', 'p-2', 'rounded-3', 'd-flex', 'bg-white', 'dark__bg-1000', 'fs-10', 'mb-2');

									var iconElement = document.createElement('span');
									var fileLinkElement = document.createElement('a');
									var downloadButton = document.createElement('a');

									iconElement.classList.add('fs-8', 'far', 'fa-image');
									fileLinkElement.textContent = file.name;
									fileLinkElement.style.cursor = 'pointer';

									// On click, show the image in the modal
									fileLinkElement.onclick = function() {
										var modalContent = document.getElementById('modalContent');
										modalContent.innerHTML = '<img src="' + e.target.result + '" class="img-fluid" alt="Preview">';
										var modal = new bootstrap.Modal(document.getElementById('filePreviewModal'));
										modal.show();
									};

									// Create a download button for images
									downloadButton.classList.add('text-300', 'ms-auto');
									downloadButton.href = e.target.result;
									downloadButton.download = file.name;
									downloadButton.setAttribute('data-bs-toggle', 'tooltip');
									downloadButton.setAttribute('data-bs-placement', 'right');
									downloadButton.setAttribute('title', 'Download');
									downloadButton.innerHTML = '<span class="fas fa-arrow-down"></span>';

									// Add spacing between elements
									filePreviewElement.appendChild(iconElement);
									filePreviewElement.appendChild(document.createTextNode(' ')); // Space between icon and filename
									filePreviewElement.appendChild(fileLinkElement);
									filePreviewElement.appendChild(document.createTextNode('  ')); // Space between filename and download button
									filePreviewElement.appendChild(downloadButton);

									// Append the preview element to the container
									previewContainer.appendChild(filePreviewElement);
								};
								fileReader.readAsDataURL(file);
							}

							// Update the file name text
							document.getElementById('fileName').textContent = fileNameText.join(', ');
						}
					</script>

					<!-- Submit Button -->
					<div class="d-flex justify-content-end gap-2">
						<button type="submit" class="btn btn-primary">Submit</button>
						<button type="button" class="btn btn-danger" onclick="history.back()">Back</button>
					</div>

				</form>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('#excuseForm').on('submit', function(e) {
			e.preventDefault(); // prevent default form submission

			var form = $('#excuseForm')[0];
			var formData = new FormData(form);

			// Validate before AJAX
			var activity = $('#activitySelect').val();
			var subject = $('#emailSubject').val().trim();
			var message = $('#emailBody').val().trim();
			var file = $('#fileUpload')[0].files.length;

			// Validation checks
			if (!activity) {
				Swal.fire({
					title: 'Activity Required',
					text: 'Please select an activity.',
					icon: 'warning',
					confirmButtonText: 'OK'
				});
				return;
			}

			if (!subject) {
				Swal.fire({
					title: 'Subject Required',
					text: 'Please enter the subject of your excuse.',
					icon: 'warning',
					confirmButtonText: 'OK'
				});
				return;
			}

			if (!message) {
				Swal.fire({
					title: 'Message Required',
					text: 'Please provide a message or reason for your excuse.',
					icon: 'warning',
					confirmButtonText: 'OK'
				});
				return;
			}

			if (file === 0) {
				Swal.fire({
					title: 'File Required',
					text: 'Please upload an image or PDF file as your supporting document.',
					icon: 'warning',
					confirmButtonText: 'OK'
				});
				return;
			}

			// Confirmation before AJAX
			Swal.fire({
				title: 'Confirm Submission',
				text: 'Are you sure you want to submit this excuse application?',
				icon: 'question',
				showCancelButton: true,
				confirmButtonText: 'Yes, submit it!',
				cancelButtonText: 'Cancel'
			}).then((result) => {
				if (result.isConfirmed) {
					// Proceed with AJAX
					$.ajax({
						url: '<?php echo site_url("student/excuse-application/submit"); ?>', // your controller method
						type: 'POST',
						data: formData,
						contentType: false,
						processData: false,
						beforeSend: function() {
							console.log("Submitting...");
						},
						success: function(response) {
							console.log(response);
							Swal.fire({
								title: 'Success!',
								text: 'Excuse application submitted successfully!',
								icon: 'success',
								confirmButtonText: 'OK'
							}).then((result) => {
								if (result.isConfirmed) {
									window.location.href = '<?php echo site_url("student/excuse-application/list"); ?>';
								}
							});
							$('#excuseForm')[0].reset();
							$('#fileName').text('No files selected');
							$('#filePreview').html('');
						},
						error: function(xhr, status, error) {
							console.error(xhr.responseText);
							Swal.fire({
								title: 'Error!',
								text: 'There was an error submitting your application.',
								icon: 'error',
								confirmButtonText: 'OK'
							});
						}
					});
				}
			});
		});

		// Show selected file names
		$('#fileUpload').on('change', function() {
			let files = $(this)[0].files;
			let fileNames = [];
			for (let i = 0; i < files.length; i++) {
				fileNames.push(files[i].name);
			}
			$('#fileName').text(fileNames.join(', '));
		});
	});
</script>