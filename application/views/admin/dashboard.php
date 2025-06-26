 <!-- ECharts Library -->
 <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>


 <!-- this section is the for the right side panel which content is be seen -->
 <div class="card bg-100 shadow-none border mb-3">
 	<?php
		// Get mocked current date
		$currentDate = date('Y-m-d');

		// Extract year and month from $currentDate
		$currentYear = (int) date('Y', strtotime($currentDate));
		$currentMonth = (int) date('n', strtotime($currentDate)); // 1-12

		// Determine semester and academic year based on mocked date
		if ($currentMonth >= 8 && $currentMonth <= 12) {
			$semester = '1st Semester';
			$academicYear = "$currentYear - " . ($currentYear + 1);
		} elseif ($currentMonth >= 1 && $currentMonth <= 7) {
			$semester = '2nd Semester';
			$academicYear = ($currentYear - 1) . " - $currentYear";
		} else {
			$semester = 'Unknown';
			$academicYear = 'Unknown';
		}
		?>

 	<div class="d-flex justify-content-between align-items-center w-100">
 		<!-- Left Column -->
 		<div class="d-flex align-items-center">
 			<img class="me-3" src="<?= base_url('assets/img/illustrations/crm-bar-chart.png'); ?>" alt="" width="90" />
 			<div>
 				<h6 class="text-primary fs-10 mb-1">Welcome <?= $users['first_name']; ?> to</h6>
 				<h4 class="text-primary fw-bold mb-0">ADMIN <span class="text-info fw-medium">Account</span></h4>
 			</div>
 		</div>

 		<!-- Right Column -->
 		<div class="text-end d-flex align-items-center">
 			<div class="me-3 text-end">
 				<h4 class="text-primary fw-bold mb-0">
 					<span class="text-info fw-medium">A.Y.</span> <?= $semester_ay->academic_year ?? 'N/A'; ?>
 				</h4>
 				<h6 class="text-primary fs-10 mb-0"><?= $semester_ay->semester ?? 'N/A'; ?></h6>
 			</div>
 			<img class="d-none d-lg-block" src="<?= site_url('assets/img/illustrations/crm-line-chart.png'); ?>" alt="" width="150" />
 		</div>

 	</div>

 </div>


 <div class="row g-3 mb-4">


 	<!-- Upcoming and Ongoing Activities -->
 	<div class="col-md-6 col-xxl-4">
 		<div class="card h-md-100">
 			<div class="card-header pb-0">
 				<h6 class="mb-0 mt-2 d-flex align-items-center">
 					Upcoming & Ongoing Activities
 					<span class="ms-1 text-400" data-bs-toggle="tooltip" title="Sorted by status and time">
 						<span class="far fa-question-circle" data-fa-transform="shrink-1"></span>
 					</span>
 				</h6>
 			</div>
 			<div class="card-body">

 				<!-- Upcoming This Month -->
 				<?php if (!empty($thisMonthActivities)): ?>
 					<h6 class="text-info fw-bold mb-2">Upcoming (This Month)</h6>
 					<ul class="list-group list-group-flush mb-3">
 						<?php foreach ($thisMonthActivities as $activity): ?>
 							<li class="list-group-item d-flex justify-content-between align-items-center">
 								<div>
 									<h6 class="mb-1"><?= $activity['activity_title'] ?> <span class="badge bg-info ms-2">Upcoming</span></h6>
 									<small class="text-muted">
 										<i class="fas fa-calendar-alt me-1"></i><?= date('F j, Y', strtotime($activity['start_date'])) ?>
 									</small>
 								</div>
 								<span class="badge bg-secondary rounded-pill"><?= $activity['attendance'] ?? 0 ?> Expected Attendees</span>
 							</li>
 						<?php endforeach; ?>
 					</ul>
 				<?php endif; ?>

 				<!-- Upcoming Next Month -->
 				<?php if (!empty($nextMonthActivities)): ?>
 					<h6 class="text-primary fw-bold mb-2">Upcoming (Next Month)</h6>
 					<ul class="list-group list-group-flush mb-3">
 						<?php foreach ($nextMonthActivities as $activity): ?>
 							<li class="list-group-item d-flex justify-content-between align-items-center">
 								<div>
 									<h6 class="mb-1"><?= $activity['activity_title'] ?> <span class="badge bg-info ms-2">Upcoming</span></h6>
 									<small class="text-muted">
 										<i class="fas fa-calendar-alt me-1"></i><?= date('F j, Y', strtotime($activity['start_date'])) ?>
 									</small>
 								</div>
 								<span class="badge bg-secondary rounded-pill"><?= $activity['attendance'] ?? 0 ?> Expected Attendees</span>
 							</li>
 						<?php endforeach; ?>
 					</ul>
 				<?php endif; ?>

 				<!-- Ongoing -->
 				<?php if (!empty($ongoingActivities)): ?>
 					<h6 class="text-success fw-bold mb-2">Ongoing Activities</h6>
 					<ul class="list-group list-group-flush">
 						<?php foreach ($ongoingActivities as $activity): ?>
 							<li class="list-group-item">
 								<div class="d-flex justify-content-between align-items-center">
 									<div>
 										<h6 class="mb-1"><?= $activity['activity_title'] ?> <span class="badge bg-success ms-2">Ongoing</span></h6>
 										<div style="display:flex; gap:10px; flex-wrap: wrap;">
 											<div style="padding:5px 10px; background:rgba(255,255,255,0.15); border-radius:10px; backdrop-filter:blur(10px); color:#000; font-size:12px;">
 												<i class="fas fa-calendar-alt me-1"></i><?= $activity['start'] ?>
 											</div>
 											<div style="padding:5px 10px; background:rgba(255,255,255,0.15); border-radius:10px; backdrop-filter:blur(10px); color:#000; font-size:12px;">
 												<i class="fas fa-clock me-1"></i><?= $activity['start_time'] ?> - <?= $activity['end_time'] ?>
 											</div>
 										</div>
 									</div>
 									<span class="badge bg-primary rounded-pill"><?= $activity['attendance'] ?> Attendees</span>
 								</div>
 								<div class="progress mt-2" style="height: 6px;">
 									<div class="progress-bar bg-success" role="progressbar" style="width: <?= $activity['progress'] ?>%;" aria-valuenow="<?= $activity['progress'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
 								</div>
 								<small class="text-muted"><?= $activity['progress'] ?>% Complete</small>
 							</li>
 						<?php endforeach; ?>
 					</ul>
 				<?php endif; ?>


 				<?php if (empty($thisMonthActivities) && empty($nextMonthActivities) && empty($ongoingActivities)): ?>
 					<div class="text-center text-muted py-4">
 						<i class="fas fa-calendar-times fa-2x mb-2"></i>
 						<p class="mb-0">No upcoming or ongoing activities</p>
 					</div>
 				<?php endif; ?>

 			</div>
 		</div>
 	</div>








 	<!-- Total Fines per Activity -->
 	<div class="col-md-6 col-xxl-4">
 		<div class="card h-md-100">
 			<div class="card-header pb-0">
 				<h6 class="mb-0 mt-2 d-flex align-items-center">
 					Total Fines per Activity
 					<span class="ms-1 text-400" data-bs-toggle="tooltip" data-bs-placement="top" title="Fines accumulated for each activity this semester">
 						<span class="far fa-question-circle" data-fa-transform="shrink-1"></span>
 					</span>
 				</h6>
 			</div>
 			<div class="card-body">
 				<?php
					// Prepare the data for fines per activity
					$activityTitles = array_column($fines_per_activity, 'activity_title');
					$totalFines = array_column($fines_per_activity, 'total_fines');

					// Calculate total fines for all activities
					$totalFinesForAll = array_sum($totalFines);
					?>

 				<!-- Total fines display -->
 				<div class="mb-3">
 					<div class="d-flex align-items-end gap-1">
 						<h1 class="text-primary mb-0">Php <?= number_format($totalFinesForAll, 2) ?></h1>
 					</div>
 				</div>

 				<!-- Line Chart -->
 				<div id="finesPerActivityChart" style="height: 250px;"></div>

 				<script>
 					var chartDom = document.getElementById('finesPerActivityChart');
 					var myChart = echarts.init(chartDom);

 					var activityTitles = <?= json_encode($activityTitles) ?>;
 					var totalFines = <?= json_encode($totalFines) ?>;

 					var option = {
 						tooltip: {
 							trigger: 'axis',
 							formatter: '{b0}: {c0}'
 						},
 						xAxis: {
 							type: 'category',
 							data: activityTitles,
 							axisLabel: {
 								show: false // hides the bottom labels
 							},
 							axisTick: {
 								show: false
 							}
 						},
 						yAxis: {
 							type: 'value'
 						},
 						series: [{
 							data: totalFines,
 							type: 'line',
 							smooth: true,
 							lineStyle: {
 								width: 3
 							},
 							itemStyle: {
 								color: '#2196f3'
 							},
 							connectNulls: true
 						}],
 						grid: {
 							top: 10,
 							bottom: 40,
 							left: 40,
 							right: 10
 						}
 					};

 					myChart.setOption(option);
 					window.addEventListener('resize', function() {
 						myChart.resize();
 					});
 				</script>

 			</div>
 		</div>
 	</div>

 	<!-- Department Share -->
 	<div class="col-md-12 col-xxl-3">
 		<div class="card h-md-100">
 			<div class="card-body">
 				<div class="row h-100 justify-content-between g-0">
 					<?php
						$colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-secondary'];
						$total_students = 0;
						foreach ($student_counts as $row) {
							$total_students += (int)$row['student_count'];
						}
						?>

 					<div class="col-5 col-sm-6 col-xxl pe-2">
 						<h6 class="mt-1">Department Share</h6>
 						<div class="fs-11 mt-3">
 							<?php foreach ($student_counts as $index => $row): ?>
 								<div class="d-flex flex-between-center mb-1">
 									<div class="d-flex align-items-center">
 										<span class="dot <?= $colors[$index % count($colors)] ?>"></span>
 										<span class="fw-semi-bold"><?= htmlspecialchars($row['dept_name']) ?></span>
 									</div>
 									<div class="d-xxl-none">
 										<?= round(($row['student_count'] / $total_students) * 100, 2) ?>%
 									</div>
 								</div>
 							<?php endforeach; ?>
 						</div>
 					</div>

 					<div class="col-7 col-sm-6 col-xxl ps-2 align-self-center">
 						<div class="card shadow-sm p-3">
 							<div class="progress" style="height: 30px;">
 								<?php foreach ($student_counts as $index => $row):
										$percentage = $total_students > 0 ? ($row['student_count'] / $total_students) * 100 : 0;
									?>
 									<div class="progress-bar <?= $colors[$index % count($colors)] ?>"
 										role="progressbar"
 										style="width: <?= $percentage ?>%;"
 										aria-valuenow="<?= $percentage ?>"
 										aria-valuemin="0"
 										aria-valuemax="100"
 										data-bs-toggle="tooltip"
 										title="<?= htmlspecialchars($row['dept_name']) . ': ' . $row['student_count'] . ' students (' . round($percentage, 1) . '%)' ?>">
 										<span class="d-none d-md-inline"><?= $row['student_count'] ?></span>
 									</div>
 								<?php endforeach; ?>
 							</div>

 							<div class="text-center mt-3">
 								<strong>Total Students: <?= $total_students ?></strong>
 							</div>
 						</div>
 					</div>
 				</div>
 			</div>
 		</div>
 	</div>

 	<!-- Tooltip Initialization Script -->
 	<script>
 		document.addEventListener('DOMContentLoaded', function() {
 			document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
 				new bootstrap.Tooltip(el);
 			});
 		});
 	</script>
 </div>


 <div class="row g-0">
 	<div class="col-md-12 col-xxl-6 pe-md-2 mb-3 mb-xxl-0">
 		<div class="card h-md-100">
 			<div class="card-header pb-0">
 				<h6 class="mb-0 mt-2 d-flex align-items-center">
 					Attendance per Activity
 					<span class="ms-1 text-400" data-bs-toggle="tooltip" data-bs-placement="top" title="Comparison of expected vs actual attendance this semester">
 						<span class="far fa-question-circle" data-fa-transform="shrink-1"></span>
 					</span>
 				</h6>
 			</div>
 			<div class="card-body">
 				<?php
					// Prepare data
					$activityTitles = array_column($attendance_data, 'activity_title');
					$expectedAttendees = array_column($attendance_data, 'expected_attendees');
					$actualAttendees = array_column($attendance_data, 'actual_attendees');

					// Total actual attendees
					$totalActual = array_sum($actualAttendees);
					?>
 				<!-- Total attendees display -->
 				<div class="mb-3">
 					<div class="d-flex align-items-center gap-3">
 						<h1 class="text-primary mb-0"><?= $attendance_rate ?>%</h1>
 						<span class="badge bg-primary fs-16">
 							Attendance Rate
 						</span>
 					</div>
 				</div>

 				<!-- Line Chart -->
 				<div id="attendancePerActivityChart" style="height: 250px;"></div>

 				<script>
 					var chartDom = document.getElementById('attendancePerActivityChart');
 					var myChart = echarts.init(chartDom);

 					var activityTitles = <?= json_encode($activityTitles) ?>;
 					var expectedAttendees = <?= json_encode($expectedAttendees) ?>;
 					var actualAttendees = <?= json_encode($actualAttendees) ?>;

 					var option = {
 						tooltip: {
 							trigger: 'axis'
 						},
 						legend: {
 							data: ['Expected', 'Actual'],
 							orient: 'horizontal',
 							bottom: 10 // Place legend at the bottom
 						},
 						xAxis: {
 							type: 'category',
 							data: activityTitles,
 							axisLabel: {
 								rotate: 30,
 								interval: 0
 							},
 							axisTick: {
 								show: false
 							}
 						},
 						yAxis: {
 							type: 'value'
 						},
 						series: [{
 								name: 'Expected',
 								type: 'line',
 								smooth: false,
 								symbol: 'circle',
 								symbolSize: 8,
 								data: expectedAttendees,
 								lineStyle: {
 									width: 3,
 									color: '#64b5f6'
 								},
 								itemStyle: {
 									color: '#64b5f6',
 									borderColor: '#64b5f6'
 								},
 								connectNulls: true
 							},
 							{
 								name: 'Actual',
 								type: 'line',
 								smooth: false,
 								symbol: 'circle',
 								symbolSize: 8,
 								data: actualAttendees,
 								lineStyle: {
 									width: 3,
 									color: '#1976d2'
 								},
 								itemStyle: {
 									color: '#1976d2',
 									borderColor: '#1976d2'
 								},
 								connectNulls: true
 							}
 						],
 						grid: {
 							top: 20,
 							bottom: 150, // Increase to avoid overlap with legend
 							left: 40,
 							right: 10
 						}
 					};

 					myChart.setOption(option);
 					window.addEventListener('resize', function() {
 						myChart.resize();
 					});
 				</script>

 			</div>
 		</div>
 	</div>
 </div>

 <div class="row g-0">
 	<div class="col-md-12 col-xxl-6 pe-md-2 mb-3 mb-xxl-0">
 		<div class="card h-md-100">
 			<div class="card-header pb-0">
 				<h6 class="mb-0 mt-2 d-flex align-items-center">
 					Department Attendees per Activity
 					<span class="ms-1 text-400" data-bs-toggle="tooltip" data-bs-placement="top" title="Comparison of attendance across departments for the current semester">
 						<span class="far fa-question-circle" data-fa-transform="shrink-1"></span>
 					</span>
 				</h6>
 			</div>
 			<div class="card-body">
 				<?php
					// Initialize arrays
					$departmentAttendance = [];
					$departmentNames = [];
					$activityTitles = [];

					foreach ($dept_attendance_data as $entry) {
						$activity = $entry->activity_title;
						$department = $entry->dept_name;
						$attendance = $entry->department_attendance;

						$departmentAttendance[$activity][$department] = $attendance;

						$departmentNames[] = $department;
						$activityTitles[] = $activity;
					}

					$chartLabels = array_values(array_unique($departmentNames));
					$activityTitles = array_values(array_unique($activityTitles));

					// Normalize missing values to 0
					foreach ($activityTitles as $activity) {
						foreach ($chartLabels as $dept) {
							$chartData[$activity][$dept] = $departmentAttendance[$activity][$dept] ?? 0;
						}
					}
					?>

 				<!-- Chart Container -->
 				<div id="departmentWiseAttendanceChart" style="height: 350px;"></div>

 				<!-- Chart Script -->
 				<script>
 					var chartDom = document.getElementById('departmentWiseAttendanceChart');
 					var myChart = echarts.init(chartDom);

 					var activityTitles = <?= json_encode($activityTitles) ?>;
 					var departmentLabels = <?= json_encode($chartLabels) ?>;
 					var departmentAttendanceData = <?= json_encode($chartData) ?>;

 					var seriesData = [];

 					departmentLabels.forEach(function(department) {
 						var data = [];
 						activityTitles.forEach(function(activity) {
 							data.push(departmentAttendanceData[activity][department] || 0);
 						});

 						seriesData.push({
 							name: department,
 							type: 'bar',
 							stack: 'stack',
 							emphasis: {
 								focus: 'series'
 							},
 							data: data
 						});
 					});

 					var option = {
 						tooltip: {
 							trigger: 'axis',
 							axisPointer: {
 								type: 'shadow'
 							}
 						},
 						legend: {
 							orient: 'horizontal',
 							bottom: 10, // Fixed bottom placement
 							type: 'plain', // Disable pagination
 							itemGap: 10,
 							data: departmentLabels
 						},
 						grid: {
 							top: 40,
 							bottom: 150, // Increased bottom space to avoid overlap
 							left: 50,
 							right: 10
 						},
 						xAxis: {
 							type: 'category',
 							data: activityTitles,
 							axisLabel: {
 								interval: 0
 							}
 						},
 						yAxis: {
 							type: 'value'
 						},
 						series: seriesData
 					};

 					myChart.setOption(option);
 					window.addEventListener('resize', function() {
 						myChart.resize();
 					});
 				</script>

 			</div>
 		</div>
 	</div>
 </div>