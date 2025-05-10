<form id="privilegeForm">
    <div class="card mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                List of Officers - <?= $this->session->userdata('dept_name') ?: $this->session->userdata('org_name'); ?>
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-nowrap">Name</th>
                            <th class="text-nowrap" scope="col">Manage Fines</th>
                            <th class="text-nowrap" scope="col">Manage Evaluation</th>
                            <th class="text-nowrap" scope="col">Manage Excuse Application</th>
                            <th class="text-nowrap" scope="col">Able to Scan</th>
                            <th class="text-nowrap" scope="col">Able to Create Activity</th>
                            <th class="text-nowrap" scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($privileges as $privilege): ?>
                            <tr>
                                <td class="text-nowrap align-middle">
                                    <?= htmlspecialchars($privilege->first_name . " " . $privilege->last_name); ?>
                                </td>

                                <?php
                                $fields = ['manage_fines', 'manage_evaluation', 'manage_applications', 'able_scan', 'able_create_activity'];
                                foreach ($fields as $field):
                                ?>
                                    <td class="text-center align-middle">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox"
                                                name="privileges[<?= $privilege->privilege_id ?>][<?= $field ?>]"
                                                value="1"
                                                <?= $privilege->$field === 'Yes' ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                <?php endforeach; ?>
                                <td>
                                    <button class="btn btn-sm btn-danger delete-officer" data-id="<?= $privilege->student_id; ?>">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>

                                <script>
                                    $(document).on('click', '.delete-officer', function(e) {
                                        e.preventDefault(); // ✅ Prevent form submission
                                        const officerId = $(this).data('id');

                                        Swal.fire({
                                            title: 'Are you sure?',
                                            text: "This officer will be permanently removed and this account cannot be accessed.",
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonText: 'Yes, delete it!',
                                            cancelButtonText: 'Cancel'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $.ajax({
                                                    url: "<?php echo site_url('officer/manage-officers-department/delete-officer'); ?>",
                                                    type: 'POST',
                                                    data: {
                                                        id: officerId
                                                    },
                                                    dataType: 'json',
                                                    success: function(response) {
                                                        if (response.success) {
                                                            Swal.fire('Deleted!', 'Officer has been removed.', 'success');
                                                            // Optionally remove the row dynamically
                                                            location.reload(); // or $('#officerRow-' + officerId).remove();
                                                        } else {
                                                            Swal.fire('Error', response.message || 'Failed to delete officer.', 'error');
                                                        }
                                                    },
                                                    error: function() {
                                                        Swal.fire('Error', 'Server error occurred.', 'error');
                                                    }
                                                });
                                            }
                                        });
                                    });
                                </script>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('#privilegeForm').on('submit', function(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to save the changes to privileges?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= site_url('officer/manage-officers/update_privileges') ?>",
                        method: "POST",
                        data: $('#privilegeForm').serialize(),
                        dataType: "json",
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Privileges updated successfully.',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    location.reload(); // Reload the page after confirmation
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Update Failed',
                                    text: response.error || 'An error occurred.',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log(xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Server Error',
                                text: 'Please try again later.',
                                footer: '<small>' + error + '</small>',
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                }
            });
        });
    });
</script>