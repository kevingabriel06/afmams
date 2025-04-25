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

			<div class="card-body p-3">
				<div class="table-responsive scrollbar">
					<?php
					$previous_organizer = '';
					$organizer_fines = [];

					foreach ($fines as $fine):
						if ($fine['organizer'] !== $previous_organizer):
							if ($previous_organizer !== ''):
								$total_fines = array_sum(array_column($organizer_fines, 'fines_amount'));
								$last_fine = end($organizer_fines);
								echo '</tbody></table>';
								echo '<div class="bg-light mt-3 p-3 rounded d-flex justify-content-between align-items-center border">';
								echo '<div><span class="fw-bold">Total Fines Imposed:</span><span class="fw-bold text-danger">₱' . number_format($total_fines, 2) . '</span></div>';
								echo '<div>';
								if ($last_fine['fines_status'] !== 'Paid') {
									echo '<button class="btn btn-primary"
								data-bs-toggle="modal"
								data-bs-target="#payNowModal"
								data-total_fines="' . number_format($total_fines, 2) . '"
								data-summary_id="' . $last_fine['summary_id'] . '"
								data-student_id="' . $last_fine['student_id'] . '"
								data-organizer="' . htmlspecialchars($last_fine['organizer'], ENT_QUOTES) . '">Pay Now</button>';
								} else {
									echo '<a href="' . base_url('uploads/fine_receipts/' . $last_fine['generated_receipt']) . '" class="btn btn-success" target="_blank">Download Receipt</a>';
								}
								echo '</div></div>';
							endif;

							// New organizer block
							echo '<table class="table table-hover table-striped table-bordered" style="margin-top: 20px; margin-bottom: 20px;">';
							echo '<thead class="table-light">';
							echo '<tr><th colspan="4" style="text-align: left;">Source of Fine: ' . $fine['organizer'] . '</th></tr>';
							echo '<tr><th>Activity</th><th>Date</th><th>Amount</th></tr>';
							echo '</thead><tbody>';

							$organizer_fines = [];
							$previous_organizer = $fine['organizer'];
						endif;

						$organizer_fines[] = $fine;
					?>
						<tr>
							<td><?php echo $fine['activity_title']; ?></td>
							<td><?php echo date('Y-m-d', strtotime($fine['start_date'])); ?></td>
							<td>₱<?php echo number_format($fine['fines_amount'], 2); ?></td>
						</tr>
					<?php endforeach; ?>

					<!-- Last group -->
					<?php if (!empty($organizer_fines)): ?>
						<?php
						$total_fines = array_sum(array_column($organizer_fines, 'fines_amount'));
						$last_fine = end($organizer_fines);
						?>
						</tbody>
						</table>

						<div class="bg-light mt-3 p-3 rounded d-flex justify-content-between align-items-center border">
							<div>
								<span class="fw-bold">Total Fines Imposed:</span>
								<span class="fw-bold text-danger">₱<?php echo number_format($total_fines, 2); ?></span>
							</div>
							<div>
								<?php
								$status = $last_fine['fines_status'] ?? '';
								$receipt = $last_fine['generated_receipt'] ?? '';
								$receipt_path = FCPATH . 'uploads/fine_receipts/' . $receipt;
								?>

								<?php if ($status === 'Paid' && !empty($receipt) && file_exists($receipt_path)): ?>
									<a href="<?php echo base_url('uploads/fine_receipts/' . $receipt); ?>"
										class="btn btn-success"
										target="_blank"
										download>
										Download Receipt
									</a>

								<?php elseif ($status === 'Pending'): ?>
									<span class="text-warning fw-bold">Waiting for admin approval...</span>

								<?php else: ?>
									<button class="btn btn-primary"
										data-bs-toggle="modal"
										data-bs-target="#payNowModal"
										data-total_fines="<?php echo number_format($total_fines, 2); ?>"
										data-summary_id="<?php echo $last_fine['summary_id']; ?>"
										data-student_id="<?php echo $last_fine['student_id']; ?>"
										data-organizer="<?php echo htmlspecialchars($last_fine['organizer'], ENT_QUOTES); ?>">
										Pay Now
									</button>
								<?php endif; ?>
							</div>

						</div>
					<?php endif; ?>
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