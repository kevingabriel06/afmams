<div class="d-flex justify-content-end mb-3">
	<a href="<?php echo site_url('student/excuse-application'); ?>" class="btn btn-primary">
		<i class="fas fa-plus"></i> Excuse Application Form
	</a>
</div>

<div class="row gx-3">
	<div class="col-xxl-10 col-xl-12">
		<div class="card" id="ticketsTable"
			data-list='{"valueNames":["activity","status", "subject"],"page":11,"pagination":true,"fallback":"tickets-table-fallback"}'>

			<div class="card-header border-bottom border-200 px-0">
				<div class="d-lg-flex justify-content-between">
					<div class="row flex-between-center gy-2 px-x1">
						<div class="col-auto pe-0">
							<h5 class="mb-0"></h5>
						</div>
					</div>

					<!-- Search Input -->
					<div class="d-flex align-items-center justify-content-between justify-content-lg-end px-x1">
						<div class="d-flex align-items-center" id="table-ticket-replace-element">
							<div class="col-auto">
								<form>
									<div class="input-group input-search-width">
										<input id="searchInput" class="form-control form-control-sm shadow-none search"
											type="search" placeholder="Search by Activity" aria-label="search" />
										<button class="btn btn-sm btn-outline-secondary border-300 hover-border-secondary" type="button">
											<span class="fa fa-search fs-10"></span>
										</button>
									</div>
								</form>
							</div>
							<button class="btn btn-sm btn-falcon-default ms-2" type="button"
								data-bs-toggle="modal" data-bs-target="#filterModal">
								<span class="fas fa-filter"></span>
							</button>
						</div>
					</div>
				</div>
			</div>

			<div class="card-body p-0">
				<div class="table-responsive scrollbar">
					<table class="table table-hover table-striped overflow-hidden">
						<thead class="bg-200">
							<tr>
								<th scope="col">Activity</th>
								<th scope="col">Subject</th>
								<th scope="col">Status</th>
								<th scope="col">Action</th>
							</tr>
						</thead>
						<tbody class="list" id="table-ticket-body">
							<?php foreach ($applications as $excuse) : ?>
								<tr class="activity-row" data-start-date="<?php echo $excuse->start_date; ?>" data-status="<?php echo $excuse->status; ?>" data-content="<?php echo $excuse->content; ?>" data-document="<?php echo $excuse->document; ?>">
									<td class="text-nowrap activity"><?php echo $excuse->activity_title; ?></td>
									<td class="text-nowrap subject"><?php echo $excuse->subject; ?></td>
									<td class="status">
										<?php if ($excuse->exStatus == 'Approved') : ?>
											<span class="badge rounded-pill bg-success-subtle text-success p-2">
												<?= $excuse->exStatus; ?>
												<span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span>
											</span>
										<?php elseif ($excuse->exStatus == 'Disapproved') : ?>
											<span class="badge rounded-pill bg-danger-subtle text-danger p-2">
												<?= $excuse->exStatus; ?>
												<span class="ms-1 fas fa-times" data-fa-transform="shrink-2"></span>
											</span>
										<?php elseif ($excuse->exStatus == 'Pending') : ?>
											<span class="badge rounded-pill bg-warning-subtle text-warning p-2">
												<?= $excuse->exStatus; ?>
												<span class="ms-1 fas fa-hourglass-half" data-fa-transform="shrink-2"></span>
											</span>
										<?php endif; ?>
									</td>
									<td class="text-nowrap">
										<div class="dropdown font-sans-serif position-static">
											<button class="btn btn-link text-600 btn-sm dropdown-toggle btn-reveal" type="button"
												data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<span class="fas fa-ellipsis-h fs-10"></span>
											</button>
											<div class="dropdown-menu dropdown-menu-end border py-0">
												<div class="py-2">
													<button type="button"
														class="dropdown-item btn-view-details"
														data-activity="<?= $excuse->activity_title; ?>"
														data-subject="<?= $excuse->subject; ?>"
														data-status="<?= $excuse->exStatus; ?>"
														data-content="<?= $excuse->content; ?>"
														data-remarks="<?= $excuse->remarks; ?>"
														data-document="<?= $excuse->document; ?>">View Details</button>



													<script>
														$(document).on('click', '.btn-view-details', function() {
															const activity = $(this).data('activity');
															const subject = $(this).data('subject');
															const status = $(this).data('status');
															const remarks = $(this).data('remarks');
															const content = $(this).data('content');
															const documentFile = $(this).data('document');


															$('#detail-activity').text(activity);
															$('#detail-subject').text(subject);
															$('#detail-content').text(content);
															$('#detail-remarks').text(remarks);

															// Set status badge class
															const $statusBadge = $('#detail-status');
															$statusBadge.text(status);
															$statusBadge.removeClass('bg-success bg-warning bg-danger');
															switch (status.toLowerCase()) {
																case 'approved':
																	$statusBadge.addClass('bg-success');
																	break;
																case 'pending':
																	$statusBadge.addClass('bg-warning');
																	break;
																case 'disapproved':
																	$statusBadge.addClass('bg-danger');
																	break;
																default:
																	$statusBadge.addClass('bg-secondary');
															}

															// Set document preview (image only)
															const fileExtension = documentFile.split('.').pop().toLowerCase();
															const baseUrl = '<?php echo base_url(); ?>';
															const filePath = baseUrl + 'assets/excuseFiles/' + documentFile;
															const $imagePreview = $('#imagePreview'); // Match the ID in your HTML

															if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
																$imagePreview.attr('src', filePath).show();
																$imagePreview.parent().attr('href', filePath).attr('target', '_blank');
															} else {
																// Hide or ignore non-image files
																$imagePreview.hide();
																$imagePreview.parent().removeAttr('href target');
															}

															// Show the modal
															const modal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
															modal.show();
														});
													</script>

													<?php if ($excuse->exStatus != 'Approved' && $excuse->exStatus != 'Disapproved') : ?>

														<a class="dropdown-item text-danger btn-cancel-application" href="#" data-excuse-id="<?= $excuse->excuse_id; ?>">Cancel Application</a>
													<?php endif; ?>
												</div>
											</div>

											<script>
												$(document).on('click', '.btn-cancel-application', function(e) {
													e.preventDefault();
													const excuseId = $(this).data('excuse-id');

													Swal.fire({
														title: 'Are you sure?',
														text: 'This will delete the excuse application permanently.',
														icon: 'warning',
														showCancelButton: true,
														confirmButtonColor: '#d33',
														cancelButtonColor: '#6c757d',
														confirmButtonText: 'Yes, cancel it!'
													}).then((result) => {
														if (result.isConfirmed) {
															$.ajax({
																url: '<?= base_url("StudentController/cancel_excuse_application/") ?>' + excuseId,
																type: 'POST',
																success: function(response) {
																	const res = JSON.parse(response);
																	if (res.status === 'success') {
																		Swal.fire({
																			title: 'Cancelled!',
																			text: res.message,
																			icon: 'success',
																			timer: 2000,
																			showConfirmButton: false
																		}).then(() => {
																			location.reload(); // Refresh the page
																		});
																	} else {
																		Swal.fire('Error', res.message, 'error');
																	}
																},
																error: function() {
																	Swal.fire('Error', 'An error occurred while processing your request.', 'error');
																}
															});
														}
													});
												});
											</script>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>


							<!-- View Details Modal -->
							<div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsLabel" aria-hidden="true">
								<div class="modal-dialog modal-dialog-centered modal-lg">
									<div class="modal-content border-0 shadow rounded-4">
										<!-- Modal Header -->
										<div class="modal-header text-white rounded-top" style="background: linear-gradient(135deg, #3b82f6, #60a5fa);">
											<div>
												<h5 class="mb-0 fw-semibold" id="viewDetailsLabel"><i class="bi bi-clipboard-check me-2"></i>Excuse Application Preview</h5>
											</div>
											<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
										</div>

										<!-- Modal Body -->
										<div class="modal-body px-4 py-3" style="background-color: #f0f4ff;">

											<!-- Activity -->
											<div class="mb-3 d-flex align-items-center border-bottom pb-2 gap-2">
												<div class="text-muted d-flex align-items-center">
													<i class="bi bi-calendar-event me-2 text-primary"></i>Activity:
												</div>
												<div class="fw-medium text-dark" id="detail-activity"></div>
											</div>


											<!-- Subject -->
											<div class="mb-3 d-flex align-items-center border-bottom pb-2 gap-2">
												<div class="text-muted d-flex align-items-center">
													<i class="bi bi-book me-2 text-primary"></i>Subject:
												</div>
												<div class="fw-medium text-dark" id="detail-subject"></div>
											</div>

											<!-- Status -->
											<div class="mb-3 d-flex align-items-center border-bottom pb-2 gap-2">
												<div class="text-muted d-flex align-items-center">
													<i class="bi bi-check-circle me-2 text-primary"></i>Status:
												</div>
												<div>
													<span id="detail-status" class="badge px-2 py-1 fs-12 rounded-pill bg-warning text-dark">Pending</span>
												</div>
											</div>


											<!-- Remarks -->
											<div class="mb-3">
												<div class="text-muted mb-1 d-flex align-items-center">
													<i class="bi bi-info-circle me-2 text-primary"></i>Remarks:
												</div>
												<div class="border rounded bg-white p-3 shadow-sm" id="detail-remarks"></div>
											</div>

											<!-- Document Preview -->
											<div>
												<div class="text-muted mb-2">
													<i class="bi bi-file-earmark-image me-2 text-primary"></i>Image Preview:
												</div>

												<!-- Image preview -->
												<img id="imagePreview" src="" alt="Image Preview" class="img-thumbnail rounded shadow-sm border border-primary-subtle" style="max-width: 100%; display: none;" />
											</div>


										</div>

										<!-- Modal Footer -->
										<div class="modal-footer bg-white rounded-bottom">
											<button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Close</button>
										</div>

									</div>
								</div>
							</div>

							<!-- Include Bootstrap Icons -->
							<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

							<!-- "No activities listed" Row -->
							<tr id="no-activity-row" class="d-none">

								<td colspan="4" class="text-center text-muted fs-8 fw-bold py-2 bg-light">
									<span class="fas fa-calendar-times fa-2x text-muted"></span> <!-- Calendar icon -->
									<h5 class="mt-2 mb-1">No excuse application listed.</h5>
								</td>
							</tr>
						</tbody>
					</table>
					<div class="text-center d-none" id="tickets-table-fallback">
						<span class="fas fa-calendar-times fa-2x text-muted"></span> <!-- Calendar icon -->
						<p class="fw-bold fs-8 mt-3">No Excuse Application Found</p>
					</div>
				</div>
			</div>

			<div class="card-footer">
				<div class="d-flex justify-content-center">
					<button class="btn btn-sm btn-falcon-default me-1" type="button" title="Previous" data-list-pagination="prev">
						<span class="fas fa-chevron-left"></span>
					</button>
					<ul class="pagination mb-0"></ul>
					<button class="btn btn-sm btn-falcon-default ms-1" type="button" title="Next" data-list-pagination="next">
						<span class="fas fa-chevron-right"></span>
					</button>
				</div>
			</div>
		</div>
	</div>


	<!-- MODAL FILTER -->
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

					<!-- Status -->
					<div class="mb-3">
						<label for="status-filter">Status</label>
						<select id="status-filter" class="form-select">
							<option value="">Select Status</option>
							<option value="Approved">Approved</option>
							<option value="Pending">Pending</option>
							<option value="Disapproved">Disapproved</option>
						</select>
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
		// FOR SEARCH
		document.addEventListener("DOMContentLoaded", function() {
			var options = {
				valueNames: ["activity", "status", "subject"],
				page: 11,
				pagination: true
			};

			var excuseList = new List("ticketsTable", options);

			document.getElementById("searchInput").addEventListener("keyup", function() {
				excuseList.search(this.value);
			});
		});
	</script>

	<script>
		document.addEventListener("DOMContentLoaded", function() {
			const currentYear = new Date().getFullYear();
			const startYearDropdown = $('#start-year');
			const endYearDropdown = $('#end-year');
			const yearFilter = document.getElementById("year-filter");
			const statusFilter = document.getElementById("status-filter"); // Reference to status filter dropdown

			// Populate Start Year dropdown dynamically from the current year down to 1900
			for (let year = currentYear; year >= 1900; year--) {
				startYearDropdown.append(new Option(year, year));
			}

			// Update End Year based on selected Start Year
			startYearDropdown.on('change', function() {
				const selectedStartYear = parseInt(this.value);
				endYearDropdown.empty().append(new Option("Select End Year", "", true, true)); // Reset options

				if (selectedStartYear) {
					// Automatically set end year as one year after the selected start year
					endYearDropdown.append(new Option(selectedStartYear + 1, selectedStartYear + 1));
				}
			});

			// Apply filters based on semester, academic year, and status
			window.applyFilters = function() {
				const selectedStartYear = parseInt($('#start-year').val());
				const selectedEndYear = parseInt($('#end-year').val());
				const selectedSemester = $('#semester-filter').val();
				const selectedStatus = statusFilter.value; // Get selected status value
				let startDate, endDate;

				// Validate year range (must be exactly a one-year difference)
				if (!selectedStartYear || !selectedEndYear || selectedEndYear - selectedStartYear !== 1) {
					$('#start-year, #end-year').addClass('is-invalid');

					// Display SweetAlert instead of native alert
					Swal.fire({
						icon: 'error',
						title: 'Invalid Year Range',
						text: 'Please select a valid academic year range with a one-year difference.',
						confirmButtonText: 'OK'
					});

					return;
				} else {
					$('#start-year, #end-year').removeClass('is-invalid');
				}

				// Define the exact date range for 1st and 2nd semesters
				if (selectedSemester === "1st-semester") {
					startDate = new Date(selectedStartYear, 7, 1); // August 1, selected start year (e.g., Aug 1, 2024)
					endDate = new Date(selectedStartYear, 11, 31); // December 31, selected start year (e.g., Dec 31, 2024)
				} else if (selectedSemester === "2nd-semester") {
					startDate = new Date(selectedEndYear, 0, 1); // January 1, selected end year (e.g., Jan 1, 2025)
					endDate = new Date(selectedEndYear, 6, 31); // July 31, selected end year (e.g., July 31, 2025)
				} else {
					// Default to the full academic year (Jan 1, start year - Dec 31, end year)
					startDate = new Date(selectedStartYear, 0, 1);
					endDate = new Date(selectedEndYear, 11, 31);
				}

				filterActivitiesByDateAndStatus(startDate, endDate, selectedStatus);
			};

			// Function to filter activities based on the selected date range and status
			function filterActivitiesByDateAndStatus(startDate, endDate, status) {
				let activities = document.querySelectorAll('.activity-row'); // Target the table rows
				let hasVisibleActivity = false;

				activities.forEach(activity => {
					let activityDateStr = activity.getAttribute('data-start-date');
					let activityStatus = activity.getAttribute('data-status'); // Get status from data attribute

					if (!activityDateStr) return; // Skip if no date

					let activityDate = new Date(activityDateStr);

					// Apply date and status filters
					if (
						activityDate >= startDate &&
						activityDate <= endDate &&
						(status === "" || activityStatus === status) // Filter by status only if selected
					) {
						activity.style.display = 'table-row';
						hasVisibleActivity = true;
					} else {
						activity.style.display = 'none';
					}
				});

				toggleNoActivityMessage(hasVisibleActivity);

				// Close the filter modal after applying filters
				let filterModal = document.getElementById('filterModal');
				if (filterModal) {
					let modalInstance = bootstrap.Modal.getInstance(filterModal);
					if (modalInstance) modalInstance.hide();
				}
			}

			function toggleNoActivityMessage(hasVisibleActivity) {
				let fallbackMessage = document.getElementById("tickets-table-fallback");
				if (hasVisibleActivity) {
					fallbackMessage.classList.add("d-none");
				} else {
					fallbackMessage.classList.remove("d-none");
				}
			}
		});
	</script>

	<script>
		document.addEventListener("DOMContentLoaded", function() {
			displayPastAndNext15DaysActivities();
		});

		function displayPastAndNext15DaysActivities() {
			const today = new Date();

			// Define date range
			const past15Days = new Date();
			past15Days.setDate(today.getDate() - 15);

			const next15Days = new Date();
			next15Days.setDate(today.getDate() + 15);

			const activities = document.querySelectorAll(".activity-row");
			let hasValidActivities = false;

			activities.forEach(function(activity) {
				const startDateStr = activity.getAttribute("data-start-date");

				if (startDateStr) {
					const activityDate = parseDate(startDateStr);

					if (activityDate >= past15Days && activityDate <= next15Days) {
						activity.style.display = "table-row";
						hasValidActivities = true;
					} else {
						activity.style.display = "none";
					}
				} else {
					activity.style.display = "none";
				}
			});

			toggleNoActivityMessage(hasValidActivities);
		}

		function toggleNoActivityMessage(hasActivities) {
			const noActivityRow = document.getElementById('no-activity-row');
			if (noActivityRow) {
				noActivityRow.style.display = hasActivities ? 'none' : 'table-row';
			}
		}

		// Helper to parse YYYY-MM-DD formatted dates
		function parseDate(dateStr) {
			const parts = dateStr.split("-");
			return new Date(parts[0], parts[1] - 1, parts[2]);
		}
	</script>