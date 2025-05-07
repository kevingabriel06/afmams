<!-- reports.php -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<!-- Add FontAwesome (for icons in legend) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">


<div class="card mb-3 mb-lg-0">
	<div class="card-header bg-body-tertiary d-flex justify-content-between">
		<h5 class="mb-0"> Attendance Reports </h5>
	</div>
</div>

<!-- Space Between Sections -->
<div class="space" style="height: 20px;"></div> <!-- Adds spacing between sections -->

<div class="card mb-3 p-4">
	<h2 class="text-center">Attendance Analysis Dashboard Illustrating Absent Student Percentage</h2>
	<p class="text-center text-muted">
		This dashboard shows graphical analysis of student attendance. Attendance % by grade, day of week, and suspensions.
	</p>



	<!-- Total Attendees Chart -->
	<div class="row mb-5 justify-content-center">
		<div class="col-12 col-md-8">
			<canvas id="totalAttendeesChart" width="400" height="200"></canvas>
		</div>
	</div>


	<script>
		const totalAttendees = <?= json_encode($total_attendees) ?>;
		const totalExpected = <?= json_encode($total_expected ?? 100) ?>; // Set 100 or whatever your expected number is

		new Chart(document.getElementById('totalAttendeesChart'), {
			type: 'bar',
			data: {
				labels: ['Total Attendees'],
				datasets: [{
					label: 'Students',
					data: [totalAttendees],
					backgroundColor: '#17a2b8',
					borderColor: '#17a2b8',
					borderWidth: 1
				}]
			},
			options: {
				responsive: true,
				plugins: {
					legend: {
						display: false
					},
					datalabels: {
						anchor: 'end', // moves label outside the bar
						align: 'start', // align at the top of the bar
						color: '#000000', // black color text
						font: {
							weight: 'bold',
							size: 16
						},
						formatter: function(value, context) {
							const percent = ((value / totalExpected) * 100).toFixed(1);
							return `${value} students\n(${percent}%)`;
						}
					},
					tooltip: {
						enabled: true // keep tooltip if you still want hover effect
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						ticks: {
							stepSize: 1
						}
					}
				}
			},
			plugins: [ChartDataLabels]
		});
	</script>



	<!-- Attendance Status Doughnut Chart Section -->
	<div style="max-width: 600px; margin: 0 auto; padding: 1rem;">
		<h3 style="text-align: center; font-weight: bold; margin-bottom: 1rem;">Attendance Status Comparison</h3>
		<canvas id="attendanceStatusChart" height="300"></canvas>
	</div>


	<!-- Legend container -->
	<div class="row justify-content-center">
		<div class="col-10 col-md-8 text-center mb-4">
			<canvas id="attendanceStatusChart"></canvas>
		</div>
	</div>

	<script>
		const statusData = <?= json_encode($status_comparison ?? []) ?>;

		const ctxStatus = document.getElementById('attendanceStatusChart').getContext('2d');

		// Get labels and values
		const labels = Object.keys(statusData);
		const values = Object.values(statusData);

		// Get total for percentage calculation
		const total = values.reduce((a, b) => a + b, 0);

		// Define colors
		const backgroundColors = {
			Present: '#28a745',
			Absent: '#dc3545',
			Incomplete: '#ffc107'
		};

		// Add icons (optional)
		const icons = {
			Present: '\uf00c', // check
			Absent: '\uf00d', // times
			Incomplete: '\uf110' // spinner
		};

		// Build chart
		new Chart(ctxStatus, {
			type: 'doughnut',
			data: {
				labels: labels.map(label => `${label}`),
				datasets: [{
					data: values,
					backgroundColor: labels.map(label => backgroundColors[label] || '#6c757d'),
				}]
			},
			options: {
				responsive: true,
				plugins: {
					legend: {
						position: 'bottom',
						labels: {
							generateLabels: function(chart) {
								const data = chart.data;
								return data.labels.map((label, i) => {
									const value = data.datasets[0].data[i];
									const percent = ((value / total) * 100).toFixed(1);
									return {
										text: `${label} - ${value} (${percent}%)`,
										fillStyle: data.datasets[0].backgroundColor[i],
										strokeStyle: '#fff',
										lineWidth: 2,
										fontColor: '#333',
										pointStyle: 'circle'
									};
								});
							}
						}
					},
					tooltip: {
						callbacks: {
							label: function(ctx) {
								const label = ctx.label || '';
								const value = ctx.raw;
								const percent = ((value / total) * 100).toFixed(1);
								return `${label}: ${value} (${percent}%)`;
							}
						}
					}
				}
			}
		});
	</script>


	<!-- Departments -->

	<div class="row justify-content-center">
		<div class="text-center mb-5" style="max-width: 1000px; width: 90%; margin: auto;">
			<h5><i class="fas fa-building-columns me-2"></i>Departments with Attending Students</h5>
			<canvas id="departmentsChart"></canvas>
		</div>
	</div>

</div>




<script>
	const byDepartmentRaw = <?= json_encode($by_department ?? []) ?>;

	// Sort by total descending
	const byDepartment = byDepartmentRaw.sort((a, b) => b.total - a.total);

	// Extract labels and values
	const deptLabels = byDepartment.map(d => d.department);
	const deptCounts = byDepartment.map(d => parseInt(d.total));
	const totalCount = deptCounts.reduce((a, b) => a + b, 0);
	const deptPercentages = deptCounts.map(c => ((c / totalCount) * 100).toFixed(1));

	// Create bar chart
	const ctxDept = document.getElementById('departmentsChart').getContext('2d');
	new Chart(ctxDept, {
		type: 'bar',
		data: {
			labels: deptLabels,
			datasets: [{
				label: 'Attendees per Department',
				data: deptCounts,
				backgroundColor: '#6610f2'
			}]
		},
		options: {
			responsive: true,
			plugins: {
				legend: {
					display: false
				},
				tooltip: {
					callbacks: {
						label: function(ctx) {
							const index = ctx.dataIndex;
							return `${ctx.label}: ${deptCounts[index]} students (${deptPercentages[index]}%)`;
						}
					}
				},
				datalabels: {
					color: '#fff',
					anchor: 'end',
					align: 'start',
					formatter: function(value, context) {
						const index = context.dataIndex;
						return `${value} (${deptPercentages[index]}%)`;
					},
					font: {
						weight: 'bold'
					}
				}
			},
			scales: {
				y: {
					beginAtZero: true,
					title: {
						display: true,
						text: 'Number of Students'
					}
				},
				x: {
					title: {
						display: true,
						text: 'Departments'
					}
				}
			}
		},
		plugins: [ChartDataLabels]
	});
</script>