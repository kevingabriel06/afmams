<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="card mb-3 mb-lg-0">
	<div class="card-header bg-body-tertiary d-flex justify-content-between">
		<h5 class="mb-0">Summary of Fines</h5>
	</div>
</div>

<!-- Space Between Sections -->
<div class="d-flex justify-content-end">
	<div class="space" style="height: 20px;"></div> <!-- Adds spacing between sections -->
</div>

<div class="row gx-3">
	<div class="col-xxl-10 col-xl-12">
		<div class="card" id="attendanceTable">
			<div class="card-header border-bottom border-200 px-0">
				<div class="d-lg-flex justify-content-between">
					<div class="row flex-between-center gy-2 px-x1">

					</div>

					<!-- Search Input -->
					<div class="d-flex align-items-center justify-content-between justify-content-lg-end px-x1">
						<div class="d-flex align-items-center" id="table-ticket-replace-element">
							<div class="col-auto">
								<form>
									<div class="input-group input-search-width">
										<input id="searchInput" class="form-control form-control-sm shadow-none search"
											type="search" placeholder="Search" aria-label="search" />
										<button class="btn btn-sm btn-outline-secondary border-300 hover-border-secondary" type="button">
											<span class="fa fa-search fs-10"></span>
										</button>
									</div>
								</form>
							</div>
							<button class="btn btn-sm btn-falcon-default ms-2" type="button">
								<span class="fas fa-download"></span>
							</button>
							<button class="btn btn-sm btn-falcon-default ms-2" type="button"
								data-bs-toggle="modal" data-bs-target="#filterModal">
								<span class="fas fa-filter"></span>
							</button>
						</div>
					</div>
				</div>
			</div>

			<div class="card-body">
				<div class="table-responsive scrollbar">
					<?php
					$previous_organizer = '';
					$organizer_fines = [];
					$previous_activity = '';

					foreach ($fines as $index => $fine):

						if ($fine['organizer'] !== $previous_organizer):
							if ($previous_organizer !== ''):
								$total_fines = array_sum(array_column($organizer_fines, 'fines_amount'));
								$last_fine = end($organizer_fines);

								echo '</tbody></table>';
								echo '<div class="bg-light mt-3 p-3 rounded d-flex justify-content-between align-items-center border">';
								echo '<div><span class="fw-bold">Total Fines Imposed:</span> <span class="fw-bold text-danger">₱' . number_format($total_fines, 2) . '</span></div>';
								echo '<div>';

								$summary_id = $last_fine['summary_id'];
								$summary_related_fines = array_filter($organizer_fines, function ($f) use ($summary_id) {
									return $f['summary_id'] == $summary_id;
								});

								$hasPending = false;
								$hasPaid = false;
								$receiptFile = '';

								foreach ($summary_related_fines as $f) {
									if ($f['fines_status'] === 'Pending') {
										$hasPending = true;
									}
									if ($f['fines_status'] === 'Paid' && !empty($f['generated_receipt'])) {
										$path = FCPATH . 'uploads/fine_receipts/' . $f['generated_receipt'];
										if (file_exists($path)) {
											$hasPaid = true;
											$receiptFile = $f['generated_receipt'];
										}
									}
								}

								if ($hasPaid && !$hasPending && !empty($receiptFile)) {
									echo '<a href="' . base_url('uploads/fine_receipts/' . $receiptFile) . '" class="btn btn-success" target="_blank" download>Download Receipt</a>';
								} elseif ($hasPending) {
									echo '<span class="text-warning fw-bold">Waiting for admin approval...</span>';
								} else {
									echo '<button class="btn btn-primary"
              data-bs-toggle="modal"
              data-bs-target="#payNowModal"
              data-total_fines="' . number_format($total_fines, 2) . '"
              data-summary_id="' . $last_fine['summary_id'] . '"
              data-student_id="' . $last_fine['student_id'] . '"
              data-organizer="' . htmlspecialchars($last_fine['organizer'], ENT_QUOTES) . '">Pay Now</button>';
								}

								echo '</div></div>';
							endif;

							echo '<table class="table table-hover table-striped table-bordered mt-4 mb-4">';
							echo '<thead class="table-light">';
							echo '<tr><th colspan="4" class="text-start">Source of Fine: ' . htmlspecialchars($fine['organizer']) . '</th></tr>';
							echo '<tr><th>Activity</th><th>Date</th><th>Amount</th><th class="text-center">Action</th></tr>';
							echo '</thead><tbody>';

							$organizer_fines = [];
							$previous_organizer = $fine['organizer'];
							$previous_activity = '';
						endif;

						if ($fine['activity_title'] !== $previous_activity) {
							echo '<tr>';
							echo '<td>' . htmlspecialchars($fine['activity_title']) . '</td>';
							echo '<td>' . date('Y-m-d', strtotime($fine['start_date'])) . '</td>';

							$activity_total = 0;
							foreach ($fines as $f) {
								if ($f['activity_id'] == $fine['activity_id'] && $f['student_id'] == $fine['student_id']) {
									if (!isset($f['time_in']) || $f['time_in'] === null) {
										$activity_total += $f['fines_amount'];
									}
									if (!isset($f['time_out']) || $f['time_out'] === null) {
										$activity_total += $f['fines_amount'];
									}
								}
							}

							echo '<td>₱' . number_format($activity_total, 2) . '</td>';
							echo '<td class="text-center">';
							echo '<button class="btn btn-sm border border-primary text-primary bg-transparent rounded-pill px-3"
          style="font-weight: 500;"
          data-bs-toggle="modal"
          data-bs-target="#breakdownModal-' . $index . '">
          View Breakdown
        </button>';
							echo '</td>';
							echo '</tr>';

							// Modal
							echo '<div class="modal fade" id="breakdownModal-' . $index . '" tabindex="-1" aria-labelledby="breakdownModalLabel-' . $index . '" aria-hidden="true">';
							echo '<div class="modal-dialog modal-dialog-scrollable">';
							echo '<div class="modal-content">';
							echo '<div class="modal-header">';
							echo '<h5 class="modal-title">Fine Breakdown - ' . htmlspecialchars($fine['activity_title']) . '</h5>';
							echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
							echo '</div>';
							echo '<div class="modal-body"><ul class="list-group">';

							$aggregated_fines = [];
							foreach ($fines as $f) {
								if ($f['activity_id'] == $fine['activity_id'] && $f['student_id'] == $fine['student_id']) {
									if (!isset($f['time_in']) || $f['time_in'] === null) {
										$key = $f['slot_name'] . '_in';
										$aggregated_fines[$key] = ($aggregated_fines[$key] ?? 0) + $f['fines_amount'];
									}
									if (!isset($f['time_out']) || $f['time_out'] === null) {
										$key = $f['slot_name'] . '_out';
										$aggregated_fines[$key] = ($aggregated_fines[$key] ?? 0) + $f['fines_amount'];
									}
								}
							}

							$activity_total_breakdown = 0;
							foreach ($aggregated_fines as $label => $amount) {
								echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
								echo '<span>' . htmlspecialchars($label) . '</span>';
								echo '<span>₱' . number_format($amount, 2) . '</span>';
								echo '</li>';
								$activity_total_breakdown += $amount;
							}

							echo '<li class="list-group-item d-flex justify-content-between align-items-center fw-bold">';
							echo '<span>Total</span>';
							echo '<span>₱' . number_format($activity_total_breakdown, 2) . '</span>';
							echo '</li>';

							echo '</ul></div></div></div></div>';

							$previous_activity = $fine['activity_title'];
						}

						$organizer_fines[] = $fine;

					endforeach;

					// Final organizer footer
					if (!empty($organizer_fines)):
						$total_fines = array_sum(array_column($organizer_fines, 'fines_amount'));
						$last_fine = end($organizer_fines);

						echo '</tbody></table>';
						echo '<div class="bg-light mt-3 p-3 rounded d-flex justify-content-between align-items-center border">';
						echo '<div><span class="fw-bold">Total Fines Imposed:</span> <span class="fw-bold text-danger">₱' . number_format($total_fines, 2) . '</span></div>';
						echo '<div>';

						$summary_id = $last_fine['summary_id'];
						$summary_related_fines = array_filter($organizer_fines, function ($f) use ($summary_id) {
							return $f['summary_id'] == $summary_id;
						});

						$hasPending = false;
						$hasPaid = false;
						$receiptFile = '';

						foreach ($summary_related_fines as $f) {
							if ($f['fines_status'] === 'Pending') {
								$hasPending = true;
							}
							if ($f['fines_status'] === 'Paid' && !empty($f['generated_receipt'])) {
								$path = FCPATH . 'uploads/fine_receipts/' . $f['generated_receipt'];
								if (file_exists($path)) {
									$hasPaid = true;
									$receiptFile = $f['generated_receipt'];
								}
							}
						}

						if ($hasPaid && !$hasPending && !empty($receiptFile)) {
							echo '<a href="' . base_url('uploads/fine_receipts/' . $receiptFile) . '" class="btn btn-success" target="_blank" download>Download Receipt</a>';
						} elseif ($hasPending) {
							echo '<span class="text-warning fw-bold">Waiting for admin approval...</span>';
						} else {
							echo '<button class="btn btn-primary"
          data-bs-toggle="modal"
          data-bs-target="#payNowModal"
          data-total_fines="' . number_format($total_fines, 2) . '"
          data-summary_id="' . $last_fine['summary_id'] . '"
          data-student_id="' . $last_fine['student_id'] . '"
          data-organizer="' . htmlspecialchars($last_fine['organizer'], ENT_QUOTES) . '">Pay Now</button>';
						}

						echo '</div></div>';
					endif;
					?>
				</div>
			</div>



			<!-- Modal -->
			<div class="modal fade" id="payNowModal" tabindex="-1" aria-labelledby="payNowModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Pay Fines</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
						</div>
						<div class="modal-body">
							<form id="payFinesForm">
								<input type="hidden" id="organizer" name="organizer">
								<input type="hidden" id="total_fines" name="total_fines">
								<input type="hidden" id="student_id" name="student_id" value="<?php echo $this->session->userdata('student_id'); ?>">

								<div class="mb-3">
									<label class="form-label">Payment Mode</label>
									<select class="form-control" name="mode_payment" required>
										<option value="Online Payment" selected>Online Payment</option>
									</select>
								</div>

								<div class="mb-3">
									<label class="form-label">Student Reference Number</label>
									<input type="text" class="form-control" name="reference_number_students" required>
								</div>

								<div class="mb-3">
									<label class="form-label">Upload Receipt</label>
									<input type="file" class="form-control" name="receipt" accept="image/*">
								</div>

								<button type="submit" class="btn btn-primary w-100">Submit Payment</button>
							</form>
						</div>
					</div>
				</div>
			</div>

			<!-- Script -->
			<script>
				$('#payNowModal').on('show.bs.modal', function(event) {
					var button = $(event.relatedTarget);
					$('#total_fines').val(button.attr('data-total_fines'));
					$('#student_id').val(button.attr('data-student_id'));
					$('#organizer').val(button.attr('data-organizer'));
				});

				$('#payFinesForm').submit(function(event) {
					event.preventDefault();

					$.ajax({
						url: "<?php echo site_url('StudentController/pay_fines'); ?>",
						type: "POST",
						data: new FormData(this),
						processData: false,
						contentType: false,
						success: function(response) {
							var data = JSON.parse(response);
							if (data.status === 'success') {
								Swal.fire({
									title: 'Payment Successful!',
									text: data.message,
									icon: 'success',
									confirmButtonText: 'OK'
								}).then(function() {
									location.reload();
								});
							} else {
								Swal.fire({
									title: 'Payment Failed!',
									text: data.message || 'Please try again.',
									icon: 'error',
									confirmButtonText: 'OK'
								});
							}
						},
						error: function() {
							Swal.fire({
								title: 'Error!',
								text: 'There was an issue with your payment. Please try again.',
								icon: 'error',
								confirmButtonText: 'OK'
							});
						}
					});
				});
			</script>



		</div>
	</div>
</div>