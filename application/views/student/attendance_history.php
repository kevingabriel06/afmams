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
							<a href="<?php echo base_url('StudentController/export_attendance_pdf'); ?>" target="_blank" class="btn btn-sm btn-falcon-default ms-2">
								<span class="fas fa-download"></span>
							</a>

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
								<th class="text-nowrap">Activity</th>
								<th class="text-nowrap">Organizer</th>
								<th class="text-nowrap">Status</th>
								<th class="text-nowrap">Action</th>
							</tr>
						</thead>
						<tbody class="list">
							<?php if (empty($attendances)): ?>
								<tr>
									<td colspan="4" class="text-center py-3">No attendance records found.</td>
								</tr>
							<?php else: ?>
								<?php foreach ($attendances as $index => $attendance): ?>
									<tr class="align-middle">
										<td class="text-nowrap"><?php echo $attendance->activity_title; ?></td>
										<td class="text-nowrap"><?php echo $attendance->organizer; ?></td>
										<td class="text-nowrap">
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
												case 'Incomplete':
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
										<td>
											<?php echo '<button class="btn btn-sm border border-primary text-primary bg-transparent rounded-pill px-3"
    style="font-weight: 500;"
    data-bs-toggle="modal"
    data-bs-target="#modal-' . $index . '">
    View Breakdown
</button>'; ?>

										</td>
									</tr>

									<!-- Modal -->
									<div class="modal fade" id="modal-<?php echo $index; ?>" tabindex="-1" aria-labelledby="modalLabel-<?php echo $index; ?>" aria-hidden="true">
										<div class="modal-dialog modal-dialog-centered modal-lg">
											<div class="modal-content" style="border: none; box-shadow: 0 0 10px rgba(0,0,0,0.1); border-radius: 1rem;">
												<div class="modal-header" style="background-color: #e7f1ff; color: #0d6efd; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
													<h5 class="modal-title fw-bold" id="modalLabel-<?php echo $index; ?>">
														Attendance Breakdown - <?php echo htmlspecialchars($attendance->activity_title); ?>
													</h5>

													<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
												</div>
												<div class="modal-body" style="padding: 1.5rem; background-color: #fff;">

													<?php
													// Original data
													$slotNames = explode(',', $attendance->slot_name);
													$timeIns = explode(',', $attendance->all_time_in);
													$timeOuts = explode(',', $attendance->all_time_out);
													$slotCount = max(count($slotNames), count($timeIns), count($timeOuts));

													// Combine and sort
													$combinedSlots = [];
													for ($i = 0; $i < $slotCount; $i++) {
														$name = trim(strtolower($slotNames[$i] ?? "Slot " . ($i + 1)));
														$combinedSlots[] = [
															'original_name' => $slotNames[$i] ?? "Slot " . ($i + 1),
															'sort_order' => ($name === 'morning' ? 1 : ($name === 'afternoon' ? 2 : ($name === 'evening' ? 3 : 4))),
															'time_in' => $timeIns[$i] ?? null,
															'time_out' => $timeOuts[$i] ?? null
														];
													}

													usort($combinedSlots, function ($a, $b) {
														return $a['sort_order'] <=> $b['sort_order'];
													});
													?>

													<?php foreach ($combinedSlots as $slot): ?>
														<div style="border: 1px solid #dee2e6; border-radius: .5rem; padding: 1rem; margin-bottom: 1rem; background-color: #f8f9fa;">
															<h6 style="color: #0d6efd; font-weight: 600; margin-bottom: 1rem;">
																<?php echo htmlspecialchars($slot['original_name']); ?>
															</h6>
															<div style="display: flex; justify-content: space-between;">
																<div style="width: 48%;">
																	<strong>Time-in:</strong><br>
																	<span>
																		<?php echo !empty($slot['time_in'])
																			? date("M d, Y g:i A", strtotime($slot['time_in']))
																			: '<span style="color: #6c757d; font-style: italic;">No Data</span>'; ?>
																	</span>
																</div>
																<div style="width: 48%;">
																	<strong>Time-out:</strong><br>
																	<span>
																		<?php echo !empty($slot['time_out'])
																			? date("M d, Y g:i A", strtotime($slot['time_out']))
																			: '<span style="color: #6c757d; font-style: italic;">No Data</span>'; ?>
																	</span>
																</div>
															</div>
														</div>
													<?php endforeach; ?>

												</div>
												<div class="modal-footer" style="background-color: #f1f1f1; border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem;">
													<button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Close</button>
												</div>
											</div>
										</div>
									</div>

								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>
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