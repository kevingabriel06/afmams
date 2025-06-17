<div class="row gx-3">
	<div class="col-xxl-10 col-xl-12">
		<div class="card" id="ticketsTable"
			data-list='{"valueNames":["activity","status"],"page":11,"pagination":true,"fallback":"tickets-table-fallback"}'>

			<div class="card-header border-bottom border-200 px-0">
				<div class="d-lg-flex justify-content-between">
					<div class="row flex-between-center gy-2 px-x1">
						<div class="col-auto pe-0">
							<h5 class="mb-0">All Excuse Form</h5>
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
								<th class="text-900 px-6 py-2">Activity</th>
								<th class="text-900 px-7 py-2">Status</th>
							</tr>
						</thead>
						<tbody class="list" id="table-ticket-body">
							<?php foreach ($activities as $activity) : ?>
								<tr class="activity-row" data-start-date="<?php echo $activity->start_date; ?>" data-status="<?php echo $activity->status; ?>">
									<td class="align-middle text-nowrap px-6 py-2">
										<div class="d-flex align-items-center">
											<div class="avatar avatar-xl">
												<img class="rounded-circle"
													src="<?php echo !empty($activity->activity_image)
																? base_url('assets/coverEvent/' . $activity->activity_image)
																: base_url('assets/image/OIP.jpg'); ?>" />
											</div>
											<a class="ms-3 text-decoration-none text-dark fw-semibold d-block link-hover"
												href="<?php echo site_url('admin/list-of-excuse-letter/' . $activity->activity_id); ?>">
												<?php echo $activity->activity_title; ?>
											</a>
										</div>
									</td>
									<td class="px-7 py-2">
										<?php if ($activity->status === 'Upcoming'): ?>
											<span class="badge badge rounded-pill d-block p-2 badge-subtle-danger">Upcoming<span class="ms-1 fas fa-redo" data-fa-transform="shrink-2"></span></span>
										<?php elseif ($activity->status === 'Completed'): ?>
											<span class="badge badge rounded-pill d-block p-2 badge-subtle-success">Completed<span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span></span>
										<?php elseif ($activity->status === 'Ongoing'): ?>
											<span class="badge badge rounded-pill d-block p-2 badge-subtle-warning">Ongoing<span class="ms-1 fas fa-stream" data-fa-transform="shrink-2"></span></span>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>

							<!-- "No activities listed" Row -->
							<tr id="no-activity-row" style="display: none;">
								<td colspan="2" class="text-center text-muted fs-8 fw-bold py-2 bg-light">
									<span class="fas fa-calendar-times fa-2x text-muted"></span> <!-- Calendar icon -->
									<h5 class="mt-2 mb-1">No activities listed.</h5>
								</td>
							</tr>
						</tbody>
					</table>
					<div class="text-center d-none" id="tickets-table-fallback">
						<span class="fas fa-calendar-times fa-2x text-muted"></span> <!-- Calendar icon -->
						<p class="fw-bold fs-8 mt-3">No Activity Found</p>
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
					<div class="row">
						<!-- Semester Filter (read-only) -->
						<div class="col-md-6 mb-3">
							<label class="form-label">Semester</label>
							<input type="text" class="form-control" value="<?= htmlspecialchars($active_semester) ?>" readonly>
						</div>

						<!-- Academic Year (read-only) -->
						<div class="col-md-6 mb-3">
							<label class="form-label">Academic Year</label>
							<input type="text" class="form-control" value="<?= htmlspecialchars($active_academic_year) ?>" readonly>
						</div>
					</div>



					<!-- Status -->
					<div class="mb-3">
						<label for="status-filter">Status</label>
						<select id="status-filter" class="form-select">
							<option value="">Select Status</option>
							<option value="Completed">Completed</option>
							<option value="Ongoing">Ongoing</option>
							<option value="Upcoming">Upcoming</option>
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
				valueNames: ["activity", "status"],
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
		window.applyFilters = function() {
			const selectedStatus = document.getElementById('status-filter').value;
			let activities = document.querySelectorAll('.activity-row');
			let hasVisibleActivity = false;

			activities.forEach(activity => {
				const activityStatus = activity.getAttribute('data-status');
				if (selectedStatus === "" || activityStatus === selectedStatus) {
					activity.style.display = "table-row";
					hasVisibleActivity = true;
				} else {
					activity.style.display = "none";
				}
			});

			// Toggle fallback
			const noActivityRow = document.getElementById('no-activity-row');
			if (noActivityRow) {
				noActivityRow.style.display = hasVisibleActivity ? "none" : "table-row";
			}

			// Close modal
			const filterModalEl = document.getElementById('filterModal');
			const modal = bootstrap.Modal.getInstance(filterModalEl);
			if (modal) modal.hide();
		};
	</script>

	<!-- <script>
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
					Swal.fire({
						icon: 'warning',
						title: 'Invalid Academic Year',
						text: 'Please select a valid academic year range with a one-year difference.',
						confirmButtonColor: '#3085d6',
					});
					return;
				} else {
					$('#start-year, #end-year').removeClass('is-invalid');
				}

				// Define the exact date range for 1st and 2nd semesters
				if (selectedSemester === "1st-semester") {
					startDate = new Date(selectedStartYear, 7, 1); // August 1, selected start year
					endDate = new Date(selectedStartYear, 11, 31); // December 31, selected start year
				} else if (selectedSemester === "2nd-semester") {
					startDate = new Date(selectedEndYear, 0, 1); // January 1, selected end year
					endDate = new Date(selectedEndYear, 6, 31); // July 31, selected end year
				} else {
					// Default to the full academic year
					startDate = new Date(selectedStartYear, 0, 1);
					endDate = new Date(selectedEndYear, 11, 31);
				}

				filterActivitiesByDateAndStatus(startDate, endDate, selectedStatus);
			};


			function filterActivitiesByDateAndStatus(startDate, endDate, status) {
				let activities = document.querySelectorAll('.activity-row');
				let hasVisibleActivity = false;

				activities.forEach(activity => {
					let activityDateStr = activity.getAttribute('data-start-date');
					let activityStatus = activity.getAttribute('data-status');
					let activityDate = new Date(activityDateStr + 'T00:00:00'); // safer parsing

					let isInDateRange = activityDate >= startDate && activityDate <= endDate;
					let isStatusMatch = status === "" || activityStatus === status;

					if (isInDateRange && isStatusMatch) {
						activity.style.display = "";
						hasVisibleActivity = true;
					} else {
						activity.style.display = "none";
					}
				});

				// Toggle the "No activities listed" row
				document.getElementById("no-activity-row").style.display = hasVisibleActivity ? "none" : "";
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
	</script> -->


	<script>
		document.addEventListener("DOMContentLoaded", function() {
			displayPastAndNext15DaysActivities();
		});

		function displayPastAndNext15DaysActivities() {
			let today = new Date();

			// Define past 15 days range
			let past15Days = new Date();
			past15Days.setDate(today.getDate() - 15);

			// Define next 15 days range
			let next15Days = new Date();
			next15Days.setDate(today.getDate() + 15);

			let activities = document.querySelectorAll(".activity-row");
			let hasValidActivities = false;

			activities.forEach(function(activity) {
				let startDate = activity.getAttribute("data-start-date");

				if (startDate) {
					let activityDate = new Date(startDate);

					if (activityDate >= past15Days && activityDate <= next15Days) {
						activity.style.display = "table-row";
						hasValidActivities = true;
					} else {
						activity.style.display = "none";
					}
				}
			});

			toggleNoActivityMessage(hasValidActivities);
		}

		function toggleNoActivityMessage(hasActivities) {
			let noActivityRow = document.getElementById('no-activity-row');
			if (noActivityRow) {
				noActivityRow.style.display = hasActivities ? 'none' : 'table-row';
			}
		}
	</script>