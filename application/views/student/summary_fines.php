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
					<!-- Fines Table -->
					<?php
					$previous_organizer = ''; // Initialize a variable to track the previous organizer
					$organizer_fines = []; // Array to store fines for each organizer
					foreach ($fines as $fine):
						// Check if the organizer is different from the previous one
						if ($fine['organizer'] !== $previous_organizer):
							// If the organizer is different, create a new Source of Fine header
							if ($previous_organizer !== ''):
								echo '</tbody></table>'; // Close the previous table before starting a new one
								// Display total fines for the previous organizer
								$total_fines = array_sum(array_column($organizer_fines, 'fines_amount'));
								echo '<div class="bg-light mt-3 p-3 rounded d-flex justify-content-between align-items-center border">';
								echo '<div><span class="fw-bold">Total Fines Imposed:</span><span class="fw-bold text-danger">₱' . number_format($total_fines, 2) . '</span></div>';
								echo '<div><button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#payNowModal" data-total_fines="' . $total_fines . '">Pay Now</button></div>';
								echo '</div>'; // Close the total section
							endif;
							// Start new table for the new organizer
							echo '<table class="table table-hover table-striped table-bordered" style="margin-top: 20px; margin-bottom: 20px;">';
							echo '<thead class="table-light">';
							echo '<tr>';
							echo '<th colspan="4" style="text-align: left; word-wrap: break-word;">Source of Fine: ' . $fine['organizer'] . '</th>';
							echo '</tr>';
							echo '<tr>';
							echo '<th style="word-wrap: break-word;">Activity</th>';
							echo '<th style="word-wrap: break-word;">Date</th>';
							echo '<th style="word-wrap: break-word;">Amount</th>';
							echo '</tr>';
							echo '</thead>';
							echo '<tbody>';
							// Reset fines for the new organizer
							$organizer_fines = [];
							$previous_organizer = $fine['organizer']; // Update the previous organizer
						endif;

						// Store fines for the current organizer
						$organizer_fines[] = $fine;
					?>
						<tr>
							<td><?php echo $fine['activity_title']; ?></td>
							<td><?php echo date('Y-m-d', strtotime($fine['start_date'])); ?></td>
							<td>₱<?php echo number_format($fine['fines_amount'], 2); ?></td>
						</tr>

					<?php endforeach; ?>

					<!-- Display total fines for the last organizer -->
					<?php if (!empty($organizer_fines)): ?>
						<tfoot>
							<tr>
								<td colspan="2" class="text-end"><strong>Total Fines Imposed:</strong></td>
								<td class="text-danger"><strong>₱<?php echo number_format(array_sum(array_column($organizer_fines, 'fines_amount')), 2); ?></strong></td>
							</tr>
						</tfoot>
					<?php endif; ?>

					</tbody>
					</table>

					<!-- Pay Now Button (Appears after each table) -->
					<div class="bg-light mt-3 p-3 rounded d-flex justify-content-between align-items-center border">
						<div><span class="fw-bold">Total Fines Imposed:</span><span class="fw-bold text-danger">₱<?php echo number_format(array_sum(array_column($organizer_fines, 'fines_amount')), 2); ?></span></div>
						<div><button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#payNowModal">Pay Now</button></div>
					</div>
				</div>
			</div>

			<!-- Pay Now Modal -->
			<div class="modal fade" id="payNowModal" tabindex="-1" aria-labelledby="payNowModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="payNowModalLabel">Pay Fines</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<form id="payFinesForm">
								<!-- Hidden inputs for total fines and student ID -->
								<input type="hidden" id="total_fines" name="total_fines">
								<input type="hidden" id="student_id" name="student_id" value="<?php echo $this->session->userdata('student_id'); ?>">

								<!-- Payment Mode -->
								<div class="mb-3">
									<label for="payment_mode" class="form-label">Payment Mode</label>
									<select class="form-control" id="payment_mode" name="mode_payment" required>
										<option value="Online Payment" selected>Online Payment</option>
									</select>
								</div>

								<!-- Reference Number (Student) -->
								<div class="mb-3">
									<label for="reference_number_students" class="form-label">Student Reference Number</label>
									<input type="text" class="form-control" id="reference_number_students" name="reference_number_students" required>
								</div>

								<!-- Upload Receipt -->
								<div class="mb-3">
									<label for="receipt" class="form-label">Upload Receipt</label>
									<input type="file" class="form-control" id="receipt" name="receipt" accept="image/*">
								</div>

								<!-- Submit Button -->
								<button type="submit" class="btn btn-primary w-100">Submit Payment</button>
							</form>
						</div>
					</div>
				</div>
			</div>

			<script>
				// When the modal is about to be shown, set the total_fines value dynamically
				$('#payNowModal').on('show.bs.modal', function(event) {
					var button = $(event.relatedTarget); // Button that triggered the modal
					var totalFines = button.data('total_fines'); // Extract total fines from data attribute

					// Set the total fines value in the hidden input
					$('#total_fines').val(totalFines);
				});

				// Handle form submission using AJAX
				$('#payFinesForm').submit(function(event) {
					event.preventDefault();

					// Send the form data to the server using AJAX
					$.ajax({
						url: "<?php echo site_url('StudentController/pay_fines'); ?>", // Adjust the URL to your controller method
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
									location.reload(); // Reloads the page
								});
							} else {
								Swal.fire({
									title: 'Payment Failed!',
									text: 'Please try again.',
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

				// Modal for Filter
				$('#filterModal').on('show.bs.modal', function() {
					// You can apply additional logic for filters here if needed
				});
			</script>
		</div>
	</div>
</div>