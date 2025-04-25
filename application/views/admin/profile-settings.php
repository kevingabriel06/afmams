<div class="row">
    <div class="col-12">
        <div class="card mb-3 btn-reveal-trigger">
            <div class="card-header position-relative min-vh-25 mb-8">
                <div class="cover-image">
                    <div class="bg-holder rounded-3 rounded-bottom-0" style="background-image: url('<?php echo base_url('assets/img/pictures/college.jpg'); ?>');"></div>
                </div>

                <form id="profilePicForm" enctype="multipart/form-data">
                    <div class="avatar avatar-5xl avatar-profile shadow-sm img-thumbnail rounded-circle">
                        <div class="h-100 w-100 rounded-circle overflow-hidden" style="width: 200px; height: 200px;">
                            <img id="profile-preview" src="<?php echo base_url('assets/profile/' . $student_details->profile_pic); ?>" alt="Profile Picture" />
                            <input class="d-none" id="profile-image" type="file" name="profile_pic" accept="image/*" />
                            <label class="mb-0 overlay-icon d-flex flex-center" for="profile-image">
                                <span class="bg-holder overlay overlay-0"></span>
                                <span class="z-1 text-white dark__text-white text-center fs-10">
                                    <span class="fas fa-camera"></span>
                                    <span class="d-block">Update</span>
                                </span>
                            </label>
                        </div>
                    </div>
                </form>
                <script>
                    document.getElementById('profile-image').addEventListener('change', function(event) {
                        if (event.target.files && event.target.files[0]) {
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                document.getElementById('profile-preview').src = e.target.result;
                            };
                            reader.readAsDataURL(event.target.files[0]);

                            // SweetAlert confirmation before uploading
                            Swal.fire({
                                title: 'Update Profile Picture?',
                                text: 'Do you want to save the selected image as your new profile picture?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, update it!',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    let formData = new FormData(document.getElementById('profilePicForm'));

                                    fetch("<?= base_url('admin/profile/update-profile-pic') ?>", {
                                            method: "POST",
                                            body: formData
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.status === 'success') {
                                                document.getElementById('profile-preview').src = "<?= base_url('assets/profile/') ?>" + data.file_name;
                                                Swal.fire('Success!', 'Profile picture updated successfully!', 'success')
                                                    .then(() => location.reload());
                                            } else {
                                                Swal.fire('Error', data.error || 'Something went wrong.', 'error');
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            Swal.fire('Error', 'An unexpected error occurred.', 'error');
                                        });
                                }
                            });
                        }
                    });
                </script>



            </div>
        </div>
    </div>
</div>

<div class="row g-0">
    <div class="col-lg-8 pe-lg-2">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Profile Settings</h5>
            </div>
            <div class="card-body bg-body-tertiary">
                <form class="row g-3">
                    <div class="col-lg-6">
                        <label class="form-label" for="first-name">First Name</label>
                        <input class="form-control" id="first-name" name="first_name" type="text" value="<?php echo $student_details->first_name; ?>" />
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label" for="middle-name">Middle Name</label>
                        <input class="form-control" id="middle-name" name="middle_name" type="text" value="<?php echo $student_details->middle_name; ?>" />
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label" for="last-name">Last Name</label>
                        <input class="form-control" id="last-name" name="last_name" type="text" value="<?php echo $student_details->last_name; ?>" />
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label" for="email1">Email</label>
                        <input class="form-control" id="email1" name="email" type="text" value="<?php echo $student_details->email; ?>" />
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label" for="sex">Sex</label>
                        <select class="form-control" id="sex" name="sex">
                            <?php
                            $sexs = ['Male', 'Female']; // Define available sex options
                            $current_sex = $student_details->sex ?? ''; // Get current sex from student details

                            // Show the current sex option as selected
                            foreach ($sexs as $sex) {
                                if ($sex === $current_sex) {
                                    echo '<option value="' . $sex . '" selected>' . $sex . '</option>';
                                } else {
                                    echo '<option value="' . $sex . '">' . $sex . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label" for="year_level">Year Level</label>
                        <select class="form-control" id="year_level" name="year_level">
                            <?php
                            $year_levels = ['1st Year', '2nd Year', '3rd Year', '4th Year'];
                            $current_level = $student_details->year_level ?? '';

                            foreach ($year_levels as $level) {
                                $selected = ($level === $current_level) ? 'selected' : '';
                                echo '<option value="' . $level . '" ' . $selected . '>' . $level . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-12 d-flex justify-content-end">
                        <button class="btn btn-primary" type="submit">Update</button>
                    </div>
                </form>

                <script>
                    $(document).ready(function() {
                        $('form').submit(function(e) {
                            e.preventDefault();

                            Swal.fire({
                                title: 'Are you sure?',
                                text: 'You are about to update your profile information.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, update it!',
                                cancelButtonText: 'No, cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    let formData = $(this).serialize();

                                    $.ajax({
                                        url: "<?php echo base_url('admin/profile/update-profile'); ?>",
                                        method: "POST",
                                        data: formData,
                                        success: function(response) {
                                            let res = JSON.parse(response);
                                            if (res.status === 'success') {
                                                Swal.fire('Updated!', res.message, 'success').then(() => {
                                                    location.reload();
                                                });
                                            } else {
                                                Swal.fire('Failed', res.message, 'error');
                                            }
                                        },
                                        error: function() {
                                            Swal.fire('Oops!', 'Something went wrong. Please try again.', 'error');
                                        }
                                    });
                                }
                            });
                        });
                    });
                </script>

            </div>
        </div>

        <!-- My Organizations -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">My Organizations</h5>
            </div>
            <div class="card-body bg-body-tertiary">
                <?php foreach ($student_details->organizations as $organization) : ?>
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-3xl" style="margin-top: -4px;">
                            <div class="avatar-name rounded-circle overflow-hidden">
                                <!-- Display organization logo if available, else fallback letter -->
                                <?php if (!empty($organization->logo)) : ?>
                                    <img src="<?= base_url('assets/imageOrg/' . $organization->logo); ?>" alt="<?= $organization->org_name ?>" style="width: 100%; height: 100%; object-fit: cover;" />
                                <?php else : ?>
                                    <span><?= strtoupper(substr($organization->org_name, 0, 1)); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex-1 position-relative ps-3">
                            <h6 class="fs-9 mb-0">
                                <!-- Remove anchor tag to avoid making the name a link -->
                                <?= $organization->org_name; ?>
                                <span data-bs-toggle="tooltip" data-bs-placement="top" title="Verified">
                                    <small class="fa fa-check-circle text-primary"></small>
                                </span>
                            </h6>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- My Department -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">My Department</h5>
            </div>
            <div class="card-body bg-body-tertiary">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-3xl" style="margin-top: -4px;">
                        <div class="avatar-name rounded-circle overflow-hidden d-flex align-items-center justify-content-center" style="width: 100%; height: 100%; background-color: #f0f0f0;">
                            <?php if (!empty($student_details->logo)) : ?>
                                <img src="<?= base_url('assets/imageDept/' . $student_details->logo); ?>" alt="<?= $student_details->dept_name ?>" style="width: 100%; height: 100%; object-fit: cover;" />
                            <?php else : ?>
                                <span><?= strtoupper(substr($student_details->dept_name, 0, 1)); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex-1 position-relative ps-3">
                        <h6 class="fs-9 mb-0">
                            <!-- Removed anchor tag to avoid making the name a link -->
                            <?= $student_details->dept_name; ?>
                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Verified">
                                <small class="fa fa-check-circle text-primary"></small>
                            </span>
                        </h6>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <!-- Sidebar -->
    <div class="col-lg-4 ps-lg-2">
        <div class="sticky-sidebar">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Change Password</h5>
                </div>
                <div class="card-body bg-body-tertiary">
                    <form id="update-password-form">
                        <div class="mb-3 position-relative">
                            <label class="form-label" for="old-password">Old Password</label>
                            <div class="position-relative">
                                <input class="form-control" id="old-password" type="password" name="old_password" required />
                            </div>
                        </div>
                        <div class="mb-3 position-relative">
                            <label class="form-label" for="new-password">New Password</label>
                            <div class="position-relative">
                                <input class="form-control" id="new-password" type="password" name="new_password" required />
                            </div>
                        </div>
                        <div class="mb-3 position-relative">
                            <label class="form-label" for="confirm-password">Confirm New Password</label>
                            <div class="position-relative">
                                <input class="form-control" id="confirm-password" type="password" name="confirm_password" required />
                            </div>
                        </div>
                        <button class="btn btn-primary d-block w-100" type="button" onclick="updatePassword()">Update Password</button>
                    </form>

                    <script>
                        function togglePassword(id) {
                            var passwordField = document.getElementById(id);
                            passwordField.type = passwordField.type === "password" ? "text" : "password";
                        }

                        function updatePassword() {
                            const oldPass = document.getElementById('old-password').value.trim();
                            const newPass = document.getElementById('new-password').value.trim();
                            const confirmPass = document.getElementById('confirm-password').value.trim();

                            // Basic client-side validation
                            if (!oldPass || !newPass || !confirmPass) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Missing Fields',
                                    text: 'Please fill in all the password fields.'
                                });
                                return;
                            }

                            // Password mismatch validation
                            if (newPass !== confirmPass) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops!',
                                    text: 'New password and confirmation do not match.'
                                });
                                return;
                            }

                            // Confirmation before updating the password
                            Swal.fire({
                                title: 'Are you sure?',
                                text: "Do you want to update your password?",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, update it!',
                                cancelButtonText: 'Cancel',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    const url = '<?= site_url("admin/profile/update_password") ?>';

                                    fetch(url, {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                            },
                                            body: JSON.stringify({
                                                old_password: oldPass,
                                                new_password: newPass,
                                                confirm_password: confirmPass
                                            })
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.status === 'success') {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Updated!',
                                                    text: data.message
                                                });
                                                document.getElementById('update-password-form').reset(); // Reset form
                                            } else {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Update Failed',
                                                    text: data.message
                                                });
                                            }
                                        })
                                        .catch(error => {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: 'Something went wrong while updating the password.'
                                            });
                                            console.error('Fetch error:', error);
                                        });
                                }
                            });
                        }
                    </script>

                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Your QR Code</h5>
                </div>
                <div class="card-body bg-light text-center" id="qrCodeContainer" style="width: 100%; overflow: hidden;">
                    <!-- QR Code will be inserted here dynamically -->
                    <!-- If QR Code is missing, display a placeholder -->
                    <p id="noQrMessage" class="text-muted">No QR code found for this student.</p>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    fetch('<?php echo site_url("admin/profile/get_qr_code_by_student"); ?>', {
                            method: 'GET'
                        })
                        .then(response => response.json())
                        .then(data => {
                            const qrCodeContainer = document.querySelector('#qrCodeContainer');
                            const noQrMessage = document.querySelector('#noQrMessage');

                            if (data.status === 'success' && data.qr_code) {
                                // QR code data exists, create image element
                                const img = document.createElement('img');
                                img.src = 'data:image/png;base64,' + data.qr_code;
                                img.alt = 'Your QR Code';
                                img.classList.add('qr-code-img'); // Custom class for larger QR code
                                img.style.width = '100%'; // Make sure the image takes up the full container width
                                img.style.height = 'auto'; // Maintain aspect ratio
                                img.style.maxHeight = '100%'; // Ensure the image doesn't exceed the container height
                                img.style.objectFit = 'contain'; // Ensure the image scales proportionally

                                qrCodeContainer.innerHTML = ''; // Clear the container
                                qrCodeContainer.appendChild(img); // Append the image
                                noQrMessage.style.display = 'none'; // Hide the "No QR code" message
                            } else {
                                // If no QR code, show fallback message
                                noQrMessage.style.display = 'block'; // Display "No QR code" message
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            alert('An error occurred while fetching the QR code.');
                        });
                });
            </script>

        </div>
    </div>
</div>