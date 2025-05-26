<!-- Enhanced Reports.php -->

<style>
	.card {
		border-radius: 12px;
	}

	canvas {
		background: #f8f9fa;
		border-radius: 10px;
		padding: 10px;
	}

	h5,
	h3 {
		font-weight: 600;
	}
</style>


<!-- Chart.js and Plugins -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<!-- FontAwesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="card shadow mb-4">
	<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
		<h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Attendance Reports</h5>
	</div>
</div>

<div class="space mb-3"></div>

<div class="card p-4 shadow-sm">
	<h2 class="text-center mb-3 text-primary"><i class="fas fa-chart-pie me-2"></i>Attendance Analysis Dashboard</h2>
	<p class="text-center text-muted">
		A graphical summary of student attendance including percentages by status, total attendees, and department breakdown.
	</p>

	<!-- Total Attendees Chart -->
	<div class="row mb-5 justify-content-center">
		<div class="col-12 col-md-8">
			<h5 class="text-center text-secondary mb-3"><i class="fas fa-user-check me-1"></i>Total Attendees</h5>
			<canvas id="totalAttendeesChart" height="200"></canvas>
		</div>
	</div>

	<!-- Attendance Status Doughnut Chart -->
	<div class="row justify-content-center mb-4">
		<div class="col-12 col-md-6">
			<h5 class="text-center text-secondary mb-3"><i class="fas fa-clipboard-check me-1"></i>Attendance Status Comparison</h5>
			<canvas id="attendanceStatusChart" height="300"></canvas>
		</div>
	</div>

	<!-- Departments Chart -->
	<div class="row justify-content-center">
		<div class="col-12 col-md-10">
			<h5 class="text-center text-secondary mb-3">Departments with Attending Students</h5>
			<canvas id="departmentsChart" height="200"></canvas>
		</div>
	</div>
</div>

<!-- Chart Scripts -->
<script>
	const totalAttendees = <?= json_encode($total_attendees) ?>;
	const totalExpected = <?= json_encode($total_expected ?? 100) ?>;

	new Chart(document.getElementById('totalAttendeesChart'), {
		type: 'bar',
		data: {
			labels: ['Attendees'],
			datasets: [{
				label: 'Students',
				data: [totalAttendees],
				backgroundColor: '#0d6efd',
				borderRadius: 6
			}]
		},
		options: {
			responsive: true,
			plugins: {
				legend: {
					display: false
				},
				datalabels: {
					anchor: 'end',
					align: 'start',
					color: '#000',
					font: {
						weight: 'bold',
						size: 14
					},
					formatter: (value) => {
						const percent = ((value / totalExpected) * 100).toFixed(1);
						return `${value} (${percent}%)`;
					}
				}
			},
			scales: {
				y: {
					beginAtZero: true,
					ticks: {
						stepSize: 1
					},
					title: {
						display: true,
						text: 'Number of Students',
						color: '#6c757d'
					}
				}
			}
		},
		plugins: [ChartDataLabels]
	});
</script>

<script>
	const statusData = <?= json_encode($status_comparison ?? []) ?>;
	const ctxStatus = document.getElementById('attendanceStatusChart').getContext('2d');

	const labels = Object.keys(statusData);
	const values = Object.values(statusData);
	const total = values.reduce((a, b) => a + b, 0);

	const backgroundColors = {
		Present: '#198754',
		Absent: '#dc3545',
		Incomplete: '#ffc107'
	};

	new Chart(ctxStatus, {
		type: 'doughnut',
		data: {
			labels: labels.map(l => l),
			datasets: [{
				data: values,
				backgroundColor: labels.map(l => backgroundColors[l] || '#6c757d'),
				borderWidth: 2,
			}]
		},
		options: {
			responsive: true,
			plugins: {
				legend: {
					position: 'bottom',
					labels: {
						color: '#343a40',
						font: {
							size: 13
						},
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
									pointStyle: 'circle'
								};
							});
						}
					}
				},
				tooltip: {
					callbacks: {
						label: (ctx) => {
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

<script>
	const byDepartmentRaw = <?= json_encode($by_department ?? []) ?>;
	const byDepartment = byDepartmentRaw.sort((a, b) => b.total - a.total);

	const deptLabels = byDepartment.map(d => d.department);
	const deptCounts = byDepartment.map(d => parseInt(d.total));
	const totalCount = deptCounts.reduce((a, b) => a + b, 0);
	const deptPercentages = deptCounts.map(c => ((c / totalCount) * 100).toFixed(1));

	new Chart(document.getElementById('departmentsChart').getContext('2d'), {
		type: 'bar',
		data: {
			labels: deptLabels,
			datasets: [{
				label: 'Attendees per Department',
				data: deptCounts,
				backgroundColor: '#0d6efd',
				borderRadius: 5
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
						label: (ctx) => {
							const i = ctx.dataIndex;
							return `${ctx.label}: ${deptCounts[i]} (${deptPercentages[i]}%)`;
						}
					}
				},
				datalabels: {
					color: '#fff',
					anchor: 'end',
					align: 'start',
					formatter: (value, context) => {
						const i = context.dataIndex;
						return `${value} (${deptPercentages[i]}%)`;
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
						text: 'Number of Students',
						color: '#6c757d'
					}
				},
				x: {
					title: {
						display: true,
						text: 'Departments',
						color: '#6c757d'
					}
				}
			}
		},
		plugins: [ChartDataLabels]
	});
</script>