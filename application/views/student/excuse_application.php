<!-- Main Container -->

<div class="container mt-4">
    <div class="card mb-3">
        <!-- Success message placeholder -->
        <div class="alert alert-success" style="display:none;" id="successMessage">
            Your message has been sent successfully!
        </div>

		<div class="card-header">
			<div class="d-flex align-items-center">
				<h5 class="mb-0 font-weight-bold">Excuse Application</h5>
			</div>
		</div>
		<div class="border"></div>

        <!-- Excuse Application Form -->
        <div class="card">
            <div class="card-body">
                <form id="myForm" action="#" method="POST" enctype="multipart/form-data">
                    <!-- Name Fields -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last name" required>
                        </div>
                    </div>

                    <!-- Department & Year Level -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="departmentSelect1" class="form-label">Department</label>
                            <select class="form-select" id="departmentSelect1" name="department_name1" >
                                <option value="" selected disabled>Select Department</option>
                                <option value="Department 1">Department 1</option>
                                <option value="Department 2">Department 2</option>
                                <option value="Department 3">Department 3</option>
                            </select>
                        </div>
                    </div>

                    <!-- Activity Selection -->
                    <h5 class="card-title font-weight-bold mb-3">Send a Message</h5>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="activitySelect" class="form-label">Activity</label>
                            <select class="form-select" id="activitySelect" name="activity_name" >
                                <option value="" selected disabled>Select Activity</option>
                                <option value="Tagislakasan">Tagislakasan</option>
                                <option value="Valentine's Day">Valentine's Day</option>
                                <option value="Sports Fest">Sports Fest</option>
                            </select>
                        </div>
                    </div>

                    <!-- Subject & Message -->
                    <div class="mb-3">
                        <label for="emailSubject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="emailSubject" name="emailSubject" placeholder="Subject" >
                    </div>
                    <div class="mb-3">
                        <label for="emailBody" class="form-label">Message</label>
                        <textarea class="form-control" id="emailBody" name="emailBody" rows="5" placeholder="Type your message here..." ></textarea>
                    </div>

                    <!-- File Upload Section -->
					<div class="mb-3">
						<label class="form-label">Upload Image or Document</label>
						<div class="d-flex align-items-center">
							<!-- Hidden File Input -->
							<input type="file" class="d-none" id="fileUpload" name="fileUpload" accept="image/*, .pdf, .docx, .txt" onchange="updateFileName()">
							
							<!-- Pin Icon as File Upload Button -->
							<label for="fileUpload" class="btn btn-secondary d-flex align-items-center">
								<i class="fas fa-paperclip me-2"></i> Attach File
							</label>
							
							<!-- File Name Display -->
							<span id="fileName" class="ms-2 text-muted">No file selected</span>
						</div>
					</div>
                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Display file name when a file is selected
    function updateFileName() {
        var fileInput = document.getElementById("fileUpload");
        var fileName = document.getElementById("fileName");
        fileName.textContent = fileInput.files[0] ? fileInput.files[0].name : "No file selected";
    }

    // Show success message
    document.getElementById("myForm").onsubmit = function (e) {
        e.preventDefault(); // Prevent form submission for demonstration
        document.getElementById("successMessage").style.display = "block";
    };

    function updateFileName() {
        var fileInput = document.getElementById("fileUpload");
        var fileName = document.getElementById("fileName");
        fileName.textContent = fileInput.files[0] ? fileInput.files[0].name : "No file selected";
    }
</script>
