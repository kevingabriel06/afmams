<script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>

<div class="card mb-3 mb-lg-0">
	<div class="card-header bg-body-tertiary d-flex justify-content-between">
		<h5 class="mb-0">Attendance History</h5>
	</div>
</div>


<!-- Space Between Sections -->
<div class="space" style="height: 20px;"></div> <!-- Adds spacing between sections -->


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

			<script>
				document.addEventListener("DOMContentLoaded", function() {
					let options = {
						valueNames: ["activity", "organizer", "status"],
						page: 10,
						pagination: true,
					};

					let evalList = new List("attendanceTable", options);

					const searchInput = document.getElementById("searchInput");
					const fallbackMessage = document.getElementById("evaluation-table-fallback");

					searchInput.addEventListener("input", function() {
						evalList.search(this.value);

						const visibleRows = document.querySelectorAll("#attendanceTable tbody tr:not([style*='display: none'])").length;
						fallbackMessage.classList.toggle("d-none", visibleRows > 0);
					});
				});
			</script>

			<div class="card-body p-0">
				<div class="table-responsive scrollbar">
					<table class="table table-hover table-striped overflow-hidden">
						<thead>
							<tr>
								<th scope="col" class="text-nowrap">Activity</th>
								<th scope="col" class="text-nowrap">Organizer</th>
								<th scope="col" class="text-nowrap"></th>
								<th scope="col" class="text-nowrap">Time-in</th>
								<th scope="col" class="text-nowrap">Time-out</th>
								<th scope="col">Status</th>
								<!-- <th scope="col">Action</th> -->
							</tr>
						</thead>
						<tbody class="list">
							<?php foreach ($attendances as $attendance): ?>
								<tr class="align-middle" data-start-date="<?php echo $attendance->start_date; ?>">
									<td class="text-nowrap activity"><?php echo $attendance->activity_title; ?></td>
									<td class="text-nowrap organizer"><?php echo $attendance->organizer; ?></td>
									<td class="text-nowrap"><?php echo $attendance->slot_name; ?></td>
									<td class="text-nowrap">
										<?php echo !empty($attendance->time_in) ? date("M d, Y g:i A", strtotime($attendance->time_in)) : 'No Data'; ?>
									</td>
									<td class="text-nowrap">
										<?php echo !empty($attendance->time_out) ? date("M d, Y g:i A", strtotime($attendance->time_out)) : 'No Data'; ?>
									</td>
									<td class="status">
										<?php
										$status = $attendance->attendance_status;
										switch ($status) {
											case 'Present':
												$badgeClass = 'badge-subtle-success';
												$icon = 'fa-check';
												break;
											case 'Excused':
												$badgeClass = 'badge-subtle-primary';
												$icon = 'fa-user-check';
												break;
											case 'Absent':
												$badgeClass = 'badge-subtle-danger';
												$icon = 'fa-times';
												break;
											case 'Incompleted':
												$badgeClass = 'badge-subtle-warning';
												$icon = 'fa-exclamation';
												break;
											default:
												$badgeClass = 'badge-subtle-secondary';
												$icon = 'fa-question';
										}
										?>
										<span class="badge rounded-pill d-block p-2 <?php echo $badgeClass; ?>">
											<?php echo $status; ?>
											<span class="ms-1 fas <?php echo $icon; ?>" data-fa-transform="shrink-2"></span>
										</span>
									</td>
									<!-- <td class="text-nowrap">
                                        <div class="dropdown font-sans-serif position-static">
                                            <button class="btn btn-link text-600 btn-sm dropdown-toggle btn-reveal" type="button"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="fas fa-ellipsis-h fs-10"></span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end border py-0">
                                                <div class="py-2">
                                                    <a class="dropdown-item" href="#">View Details</a>
                                                </div>
                                            </div>
                                        </div>
                                    </td> -->
								</tr>

							<?php endforeach; ?>
						</tbody>
					</table>
					<!-- ✅ Move fallback outside the table -->
					<div id="evaluation-table-fallback" class="d-none text-center p-3">No results found.</div>
				</div>
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
		const currentMonth = new Date().getMonth(); // 0-based (Jan = 0)
		const startYearDropdown = $('#start-year');
		const endYearDropdown = $('#end-year');
		const semesterDropdown = $('#semester-filter');

		// Populate Start Year dropdown from current year down to 1900
		for (let year = currentYear; year >= 1900; year--) {
			startYearDropdown.append(new Option(year, year));
		}

		// Automatically populate End Year based on selected Start Year
		startYearDropdown.on('change', function() {
			const selectedStartYear = parseInt(this.value);
			endYearDropdown.empty().append(new Option("Select End Year", "", true, true));

			if (selectedStartYear) {
				endYearDropdown.append(new Option(selectedStartYear + 1, selectedStartYear + 1));
			}
		});

		// Function to detect and load current semester
		function loadCurrentSemester() {
			let startYear, endYear, semester;

			if (currentMonth >= 7 && currentMonth <= 11) {
				// August to December → 1st Semester
				startYear = currentYear;
				endYear = currentYear + 1;
				semester = "1st-semester";
			} else if (currentMonth >= 0 && currentMonth <= 6) {
				// January to July → 2nd Semester
				startYear = currentYear - 1;
				endYear = currentYear;
				semester = "2nd-semester";
			}

			// Set selected values
			startYearDropdown.val(startYear).trigger('change');
			setTimeout(() => {
				endYearDropdown.val(endYear);
				semesterDropdown.val(semester);

				// Trigger filtering
				applyFilters();
			}, 200); // Slight delay to ensure end year is populated
		}

		// Main function to apply filters
		window.applyFilters = function() {
			const selectedStartYear = parseInt($('#start-year').val());
			const selectedEndYear = parseInt($('#end-year').val());
			const selectedSemester = $('#semester-filter').val();

			let startDate, endDate;

			// Year range must be exactly 1 year
			if (!selectedStartYear || !selectedEndYear || selectedEndYear - selectedStartYear !== 1) {
				$('#start-year, #end-year').addClass('is-invalid');
				alert("Please select a valid academic year range with a one-year difference.");
				return;
			} else {
				$('#start-year, #end-year').removeClass('is-invalid');
			}

			// Define semester-specific date ranges
			if (selectedSemester === "1st-semester") {
				startDate = new Date(selectedStartYear, 7, 1); // Aug 1
				endDate = new Date(selectedStartYear, 11, 31); // Dec 31
			} else if (selectedSemester === "2nd-semester") {
				startDate = new Date(selectedEndYear, 0, 1); // Jan 1
				endDate = new Date(selectedEndYear, 6, 31); // Jul 31
			} else {
				startDate = new Date(selectedStartYear, 0, 1); // Jan 1
				endDate = new Date(selectedEndYear, 11, 31); // Dec 31
			}

			filterActivitiesByDate(startDate, endDate);
		};

		function filterActivitiesByDate(startDate, endDate) {
			const activities = document.querySelectorAll('tr[data-start-date]');
			let hasVisibleActivity = false;

			activities.forEach(activity => {
				const activityDateStr = activity.getAttribute('data-start-date');
				if (!activityDateStr) return;

				const activityDate = new Date(activityDateStr);
				const matchesDateRange = activityDate >= startDate && activityDate <= endDate;

				if (matchesDateRange) {
					activity.style.display = 'table-row';
					hasVisibleActivity = true;
				} else {
					activity.style.display = 'none';
				}
			});

			toggleNoActivityMessage(hasVisibleActivity);

			// Close modal if open
			const filterModal = document.getElementById('filterModal');
			if (filterModal) {
				const modalInstance = bootstrap.Modal.getInstance(filterModal);
				if (modalInstance) modalInstance.hide();
			}
		}

		function toggleNoActivityMessage(hasVisibleActivity) {
			const fallbackMessage = document.getElementById("evaluation-table-fallback");
			if (hasVisibleActivity) {
				fallbackMessage.classList.add("d-none");
			} else {
				fallbackMessage.classList.remove("d-none");
			}
		}

		// Automatically load the current semester on page load
		loadCurrentSemester();
	});
</script>