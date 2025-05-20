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

			<!-- Modal for Filter -->
			<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="filterModalLabel">Filter Activities</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<!-- Semester Filter -->
							<div class="mb-3">
								<label for="semester-filter" class="form-label">Semester</label>
								<select id="semester-filter" class="form-select">
									<option value="" selected>Select Semester</option>
									<option value="1st-semester">1st Semester</option>
									<option value="2nd-semester">2nd Semester</option>
								</select>
							</div>

							<!-- Year Picker for Academic Year -->
							<div class="mb-3">
								<label for="year-picker" class="form-label">Academic Year</label>
								<div class="input-group">
									<select id="start-year" class="form-select">
										<option value="" selected>Select Start Year</option>
									</select>
									<span class="input-group-text">-</span>
									<select id="end-year" class="form-select">
										<option value="" selected>Select End Year</option>
									</select>
								</div>
								<div class="invalid-feedback">
									Please select a valid academic year range with a 1-year difference.
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
							<button type="button" class="btn btn-primary" onclick="applyFilters()">Apply Filters</button>
						</div>
					</div>
				</div>
			</div>

			<script>
				document.addEventListener("DOMContentLoaded", function() {
					const currentYear = new Date().getFullYear();
					const startYearDropdown = $('#start-year');
					const endYearDropdown = $('#end-year');

					// Populate Start Year dropdown dynamically from current year down to 1900
					for (let year = currentYear; year >= 1900; year--) {
						startYearDropdown.append(new Option(year, year));
					}

					// When page loads, if start year has a value, populate End Year accordingly
					if (startYearDropdown.val()) {
						const selectedStartYear = parseInt(startYearDropdown.val());
						populateEndYear(selectedStartYear);
					}

					// Update End Year based on selected Start Year
					startYearDropdown.on('change', function() {
						const selectedStartYear = parseInt(this.value);
						populateEndYear(selectedStartYear);
					});

					function populateEndYear(selectedStartYear) {
						endYearDropdown.empty().append(new Option("Select End Year", "", true, true));
						if (selectedStartYear) {
							// Automatically set end year as one year after the selected start year
							endYearDropdown.append(new Option(selectedStartYear + 1, selectedStartYear + 1));
							endYearDropdown.val(selectedStartYear + 1);
						}
					}

					// Apply filters based on semester, academic year, and status
					window.applyFilters = function() {
						const selectedStartYear = parseInt($('#start-year').val());
						const selectedEndYear = parseInt($('#end-year').val());
						const selectedSemester = $('#semester-filter').val();
						let startDate, endDate;

						// Validate year range (must be exactly one year difference)
						if (!selectedStartYear || !selectedEndYear || selectedEndYear - selectedStartYear !== 1) {
							$('#start-year, #end-year').addClass('is-invalid');
							alert("Please select a valid academic year range with a one-year difference.");
							return;
						} else {
							$('#start-year, #end-year').removeClass('is-invalid');
						}

						// Define semester date ranges
						if (selectedSemester === "1st-semester") {
							startDate = new Date(selectedStartYear, 7, 1); // August 1
							endDate = new Date(selectedStartYear, 11, 31); // December 31
						} else if (selectedSemester === "2nd-semester") {
							startDate = new Date(selectedEndYear, 0, 1); // January 1
							endDate = new Date(selectedEndYear, 6, 31); // July 31
						} else {
							// If semester not selected or "all"
							startDate = new Date(selectedStartYear, 0, 1); // January 1
							endDate = new Date(selectedEndYear, 11, 31); // December 31
						}

						filterActivitiesByDate(startDate, endDate);
					};

					// Filters activities by date range
					function filterActivitiesByDate(startDate, endDate) {
						let activities = document.querySelectorAll('.evaluation-row');
						let hasVisibleActivity = false;

						activities.forEach(activity => {
							let activityDateStr = activity.getAttribute('data-start-date');
							if (!activityDateStr) return;

							let activityDate = new Date(activityDateStr);

							if (activityDate >= startDate && activityDate <= endDate) {
								activity.style.display = 'table-row';
								hasVisibleActivity = true;
							} else {
								activity.style.display = 'none';
							}
						});

						// Toggle entire table container visibility
						const tableContainer = document.getElementById("activityTableContainer");
						if (tableContainer) {
							tableContainer.style.display = hasVisibleActivity ? 'block' : 'none';
						}

						toggleNoActivityMessage(hasVisibleActivity);

						// Close modal if open
						let filterModal = document.getElementById('filterModal');
						if (filterModal) {
							let modalInstance = bootstrap.Modal.getInstance(filterModal);
							if (modalInstance) modalInstance.hide();
						}
					}

					// Show or hide fallback message
					function toggleNoActivityMessage(hasVisibleActivity) {
						let fallbackMessage = document.getElementById("evaluation-table-fallback");
						if (hasVisibleActivity) {
							fallbackMessage.classList.add("d-none");
						} else {
							fallbackMessage.classList.remove("d-none");
						}
					}

					// Optional: Auto-apply current semester filter on page load
					(function autoApplyCurrentSemester() {
						const now = new Date();
						const month = now.getMonth();
						const year = now.getFullYear();

						// Set start year and end year dropdowns
						startYearDropdown.val(month >= 7 ? year : year - 1).trigger('change'); // Trigger change to populate end year

						// Set semester filter accordingly
						if (month >= 7) {
							$('#semester-filter').val('1st-semester');
						} else {
							$('#semester-filter').val('2nd-semester');
						}

						// Apply filter
						window.applyFilters();
					})();

				});
			</script>


			<div class="card-body">
				<div class="table-responsive scrollbar" id="activityTableContainer">
					<?php
					$previous_organizer = '';
					$organizer_fines = [];
					$previous_activity = '';

					foreach ($fines as $index => $fine):

						if (trim(strtolower($fine['organizer'])) !== trim(strtolower($previous_organizer))):

							if ($previous_organizer !== ''):
								$total_fines = array_sum(array_column($organizer_fines, 'fines_amount'));
								$last_fine = end($organizer_fines);

								echo '</tbody></table>';
								echo '<div class="bg-light mt-3 p-3 rounded d-flex justify-content-between align-items-center border">';
								echo '<div><span class="fw-bold">Total Fines Imposed:</span> <span class="fw-bold text-danger">₱' . number_format($total_fines, 2) . '</span></div>';
								echo '<div>';

								$summary_id = $last_fine['summary_id'];
								$summary_related_fines = array_filter($organizer_fines, function ($f) use ($summary_id, $previous_organizer) {
									return $f['summary_id'] == $summary_id && $f['organizer'] == $previous_organizer;
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
							echo '<tr class="evaluation-row" data-start-date="' . htmlspecialchars($fine['start_date']) . '">';
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
							$slot_order = ['Morning_in', 'Morning_out', 'Afternoon_in', 'Afternoon_out', 'Evening_in', 'Evening_out'];

							foreach ($slot_order as $label) {
								if (isset($aggregated_fines[$label])) {
									echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
									echo '<span>' . htmlspecialchars($label) . '</span>';
									echo '<span>₱' . number_format($aggregated_fines[$label], 2) . '</span>';
									echo '</li>';
									$activity_total_breakdown += $aggregated_fines[$label];
								}
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
						$last_fine = end($organizer_fines);
						$total_fines = $last_fine['total_fines'] ?? 0;

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
				<div class="fallback text-center my-5 py-5 border rounded shadow-sm bg-light d-none" id="evaluation-table-fallback">
					<div class="mb-3">
						<i class="bi bi-exclamation-circle text-muted" style="font-size: 3rem;"></i>
					</div>
					<h4 class="text-secondary">No data available for this semester</h4>
					<p class="text-muted">Try adjusting your filter or check back later.</p>
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