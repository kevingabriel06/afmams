<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<div class="card mb-3">
	<div class="card shadow-sm border-0 rounded-3">
		<div class="card-header bg-primary text-white">
			<h4 class="mb-0 text-white">🧾 Record Cash Payment</h4>
		</div>
		<div class="card-body p-4">

			<form action="<?= base_url('admin/cash-payment/submit') ?>" method="post">
				<div class="row g-3">
					<div class="col-md-6">
						<label class="form-label fw-semibold">Student ID</label>
						<input type="text" class="form-control" name="student_id" required>
					</div>
					<div class="col-md-6">
						<label class="form-label fw-semibold">Select Activity</label>
						<select class="form-select" name="activity_id" required>
							<option value="" disabled selected>Choose an Activity</option>
							<?php foreach ($activities as $activity): ?>
								<option value="<?= $activity->activity_id ?>">
									<?= htmlspecialchars($activity->activity_title) ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>


					<div class="col-md-6">
						<label class="form-label fw-semibold">Amount Paid</label>
						<input type="number" step="0.01" class="form-control" name="amount_paid" required>
					</div>
					<div class="col-md-12">
						<label class="form-label fw-semibold">Remark</label>
						<textarea class="form-control" name="remark" rows="3" placeholder="Optional notes about the payment..."></textarea>
					</div>
				</div>

				<div class="mt-4 d-flex justify-content-end">
					<button type="submit" class="btn btn-primary px-4">
						<i class="fas fa-check-circle me-1"></i> Submit Payment
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
	<?php if ($this->session->flashdata('success')): ?>
		Swal.fire({
			icon: 'success',
			title: 'Success!',
			text: "<?= $this->session->flashdata('success') ?>",
			confirmButtonColor: '#3085d6',
			confirmButtonText: 'OK'
		});
	<?php endif; ?>
</script>

<script>
	$(document).on('submit', 'form[action="<?= base_url('admin/cash-payment/submit') ?>"]', function(e) {
		e.preventDefault(); // prevent the default form submission
		let form = this;

		Swal.fire({
			title: 'Are you sure?',
			text: 'Do you want to record this cash payment?',
			icon: 'question',
			showCancelButton: true,
			confirmButtonText: 'Yes, submit it!',
			cancelButtonText: 'Cancel',
			confirmButtonColor: '#3085d6'
		}).then((result) => {
			if (result.isConfirmed) {
				form.submit(); // Proceed to submit after confirmation
			}
		});
	});
</script>