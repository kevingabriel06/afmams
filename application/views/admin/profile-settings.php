
<div class="row">
<div class="col-12">
    <div class="card mb-3 btn-reveal-trigger">
    <div class="card-header position-relative min-vh-25 mb-8">
        <div class="cover-image">
        <div class="bg-holder rounded-3 rounded-bottom-0" style="background-image:url(../../assets/img/generic/4.jpg);"></div><!--/.bg-holder-->
        <input class="d-none" id="upload-cover-image" type="file" /><label class="cover-image-file-input" for="upload-cover-image"><span class="fas fa-camera me-2"></span><span>Change cover photo</span></label>
        </div>

		<form id="profilePicForm" action="<?= base_url('admin/update_profile_pic') ?>" method="POST" enctype="multipart/form-data">
		<div class="avatar avatar-5xl avatar-profile shadow-sm img-thumbnail rounded-circle">
        
         <!-- Fixed size circle container -->
		 <div class="h-100 w-100 rounded-circle overflow-hidden" style="width: 200px; height: 200px;">
             <!-- Display current profile picture dynamically, or default if none is set -->
			 <img id="profile-preview" src="<?= base_url('assets/profile/' . ($profile_pic ? $profile_pic : 'default.jpg')) ?>" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover;" />
            <input class="d-none" id="profile-image" type="file" name="profile_pic" accept="image/*" />
            <label class="mb-0 overlay-icon d-flex flex-center" for="profile-image">
                <span class="bg-holder overlay overlay-0"></span>
                <span class="z-1 text-white dark__text-white text-center fs-10">
                    <span class="fas fa-camera"></span>
                    <span class="d-block">Update</span>
                </span>
            </label>
        </div>
    </form>

    <!-- PROFILE UPDATE SCRIPT -->
    <script>
document.getElementById('profile-image').addEventListener('change', function(event) {
    if (event.target.files && event.target.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profile-preview').src = e.target.result;
        };
        reader.readAsDataURL(event.target.files[0]);

        let formData = new FormData(document.getElementById('profilePicForm'));

        fetch("<?= base_url('admin/update_profile_pic') ?>", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('profile-preview').src = "<?= base_url('assets/profile/') ?>" + data.file_name;
                alert('Profile picture updated successfully!');
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => console.error('Error:', error));
    }
});
</script>
</div>



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
	
	<!-- Your form -->
<form class="row g-3" action="<?php echo site_url('admin/update-profile/'.$student_id); ?>" method="POST">
    <input type="hidden" name="student_id" value="<?php echo $student_id; ?>" />

    <!-- First Name -->
    <div class="col-lg-6"> 
        <label class="form-label" for="first-name">First Name</label>
        <input class="form-control" id="first-name" name="first_name" type="text" value="<?= isset($first_name) ? $first_name : '' ?>" />
    </div>

    <!-- Last Name -->
    <div class="col-lg-6"> 
        <label class="form-label" for="last-name">Last Name</label>
        <input class="form-control" id="last-name" name="last_name" type="text" value="<?= isset($last_name) ? $last_name : '' ?>" />
    </div>

    <!-- Email -->
    <div class="col-lg-6">
        <label class="form-label" for="email1">Email</label>
        <input class="form-control" id="email1" name="email" type="text" value="<?= isset($email) ? $email : '' ?>" />
    </div>

    <!-- Year Level (Static Dropdown) -->
    <div class="col-lg-6">
        <label class="form-label" for="year_level">Year Level</label>
        <select class="form-control" id="year_level" name="year_level">
            <option value="1" <?= isset($year_level) && $year_level == 1 ? 'selected' : '' ?>>1st Year</option>
            <option value="2" <?= isset($year_level) && $year_level == 2 ? 'selected' : '' ?>>2nd Year</option>
            <option value="3" <?= isset($year_level) && $year_level == 3 ? 'selected' : '' ?>>3rd Year</option>
            <option value="4" <?= isset($year_level) && $year_level == 4 ? 'selected' : '' ?>>4th Year</option>
        </select>
    </div>

    <div class="col-12 d-flex justify-content-end"><button class="btn btn-primary" type="submit">Update</button></div>
</form>
    </div>
    </div>
<!-- // ORGANIZATION -->
<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">My Organizations</h5>
    </div>
    <div class="card-body bg-body-tertiary">
        <?php if (!empty($organizations)): ?>
            <?php foreach ($organizations as $index => $organization): ?>
                <div class="d-flex align-items-center <?= $index > 0 ? 'mt-3' : '' ?>"> <!-- Add margin-top for 2nd org onwards -->
                    <!-- Organization Logo -->
                    <div class="avatar avatar-3xl" style="margin-top: -4px;">
                        <div class="avatar-name rounded-circle">
                            <span><?= isset($organization['organization_name']) ? strtoupper($organization['organization_name'][0]) : 'O' ?></span>
                        </div>
                    </div>
                    <!-- Organization Name -->
                    <div class="flex-1 position-relative ps-3">
                        <h6 class="fs-9 mb-0">
                            <a href="#!"><?= $organization['organization_name']; ?>
                                <span data-bs-toggle="tooltip" data-bs-placement="top" title="Verified">
                                    <small class="fa fa-check-circle text-primary" data-fa-transform="shrink-4 down-2"></small>
                                </span>
                            </a>
                        </h6>
                        <div class="border-bottom border-dashed my-3"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No organizations found.</p>
        <?php endif; ?>
    </div>
</div>

<!-- // DEPARTMENT -->
<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">My Department</h5>
    </div>
    <div class="card-body bg-body-tertiary">
        <div class="d-flex align-items-center">
            <!-- Dept Logo -->
            <div class="avatar avatar-3xl" style="margin-top: -4px;">
                <div class="avatar-name rounded-circle">
                    <span><?= isset($department_name) ? strtoupper($department_name[0]) : 'D' ?></span>
                </div>
            </div>
            <!-- Dept Name -->
            <div class="flex-1 position-relative ps-3">
                <h6 class="fs-9 mb-0">
                    <a href="#!"><?= isset($department_name) ? $department_name : 'Department Name' ?>
                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Verified">
                            <small class="fa fa-check-circle text-primary" data-fa-transform="shrink-4 down-2"></small>
                        </span>
                    </a>
                </h6>
                <div class="border-bottom border-dashed my-3"></div>
            </div>
        </div>
    </div>
</div>


</div>

<div class="col-lg-4 ps-lg-2">
    <div class="sticky-sidebar">

   <!-- Change Password Form -->
<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">Change Password</h5>
    </div>
    <div class="card-body bg-body-tertiary">
        <form method="POST" action="<?= site_url('admin/change_password'); ?>">
            <div class="mb-3">
                <label class="form-label" for="old-password">Old Password</label>
                <input class="form-control" id="old-password" name="old_password" type="password" required />
            </div>
            <div class="mb-3">
                <label class="form-label" for="new-password">New Password</label>
                <input class="form-control" id="new-password" name="new_password" type="password" required />
            </div>
            <div class="mb-3">
                <label class="form-label" for="confirm-password">Confirm Password</label>
                <input class="form-control" id="confirm-password" name="confirm_password" type="password" required />
            </div>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error; ?></div>
            <?php endif; ?>
            <button class="btn btn-primary d-block w-100" type="submit">Update Password</button>
        </form>
    </div>
</div>
	
    </div>
</div>
</div>

<!-- ALERTIFY SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Bind to the profile picture form submit
    document.getElementById('profilePicForm').onsubmit = function (e) {
        e.preventDefault(); // Prevent form submission to show the confirmation dialog
        alertify.confirm(
            'Confirm Profile Picture Update', // Dialog title
            'Are you sure you want to update your profile picture?', // Message
            function () {
                document.getElementById('profilePicForm').submit(); // Submit the form after success message
                alertify.success('Profile Picture Updated Successfully'); // Show success message
            },
            function () {
                alertify.error('Profile Picture update cancelled'); // Show cancel message
            }
        );
    };

    // Bind to the profile details form submit
    document.querySelector('form[action="<?php echo site_url('admin/update-profile/'.$student_id); ?>"]').onsubmit = function (e) {
        e.preventDefault(); // Prevent form submission to show the confirmation dialog
        alertify.confirm(
            'Confirm Profile Update', // Dialog title
            'Are you sure you want to update your profile?', // Message
            function () {
                // Show success message first, then submit the form after 3 seconds
                alertify.success('Profile Updated Successfully').delay(3); // Show for 3 seconds

                // Delay the form submission by 3 seconds
                setTimeout(function () {
                    document.querySelector('form[action="<?php echo site_url('admin/update-profile/'.$student_id); ?>"]').submit();
                }, 3000); // 3000ms = 3 seconds
            },
            function () {
                alertify.error('Profile update cancelled'); // Show cancel message
            }
        );
    };
});
</script>


<!-- ALERTIFY SCRIPT END -->