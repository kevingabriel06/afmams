<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<form id="privilegeForm" action="<?= site_url('officer/manage-officers-department/update_privileges') ?>" method="post">
    <input type="hidden" name="dept_id" value="<?= $dept_id; ?>">

    <div class="card mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <?php foreach ($department as $dept): ?>
                    <?php if ($dept_id == $dept->dept_id): ?>
                        List of Officers - <?= $dept->dept_name; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Manage Fines</th>
                            <th>Manage Evaluation</th>
                            <th>Manage Excuse Application</th>
                            <th>Able to Scan</th>
                            <th>Able to Create Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($privileges as $privilege): ?>
                            <tr>
                                <!-- Full Name -->
                                <td class="text-nowrap align-middle">
                                    <?= $privilege->first_name . " " . $privilege->last_name; ?>
                                </td>

                                <!-- Privileges Checkboxes -->
                                <?php
                                $fields = ['manage_fines', 'manage_evaluation', 'manage_applications', 'able_scan', 'able_create_activity'];
                                foreach ($fields as $field):
                                ?>
                                    <td class="text-center align-middle">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox"
                                                name="privileges[<?= $privilege->privilege_id ?>][<?= $field ?>]"
                                                <?= $privilege->$field === 'Yes' ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                <?php endforeach; ?>
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

            var form = $(this);
            var formData = form.serialize();

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Privileges updated successfully.',
                            confirmButtonColor: '#3085d6'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Update Failed',
                            text: 'An error occurred while updating privileges.',
                            confirmButtonColor: '#d33'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: 'Something went wrong. Please try again.',
                        footer: '<small>' + error + '</small>',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        });
    });
</script>