<!DOCTYPE html>
<html data-bs-theme="light" lang="en-US" dir="ltr">

<!-- For HTML in Technologies used -->
<style>
	.text-orange {
		color: #fd7e14;
	}
</style>


<style>
	.afmams-header {
		background: linear-gradient(to bottom, #2196f3, #0d47a1);
		color: #fff;
		overflow: hidden;
		position: relative;
	}

	.afmams-header h1 {
		font-size: 3rem;
	}

	.afmams-header .lead {
		font-size: 1.25rem;
		max-width: 700px;
		margin: 0 auto;
	}

	.floating-icons i {
		animation: float 3s ease-in-out infinite;
	}

	.floating-icons i:nth-child(2) {
		animation-delay: 0.3s;
	}

	.floating-icons i:nth-child(3) {
		animation-delay: 0.6s;
	}

	@keyframes float {

		0%,
		100% {
			transform: translateY(0);
		}

		50% {
			transform: translateY(-8px);
		}
	}






	.icon-circle {
		width: 60px;
		height: 60px;
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 1.5rem;
		box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
		transition: transform 0.3s ease;
	}

	.icon-circle:hover {
		transform: scale(1.1);
	}
</style>



<!-- TEAM MEMBERS STYLE START -->

<style>
	.team-member img {
		width: 120px;
		height: 120px;
		border-radius: 50%;
		transition: transform 0.3s ease-in-out;
	}

	.team-member img:hover {
		transform: scale(1.1);
	}

	.social-icons i {
		transition: color 0.3s ease-in-out;
	}

	.social-icons a:hover i {
		color: #000;
		/* Change to desired hover color */
	}

	.card-body {
		background-color: #fff;
		/* White card background */
		border-radius: 10px;
		/* Optional: rounded corners */
		box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
	}


	.team-section {
		background-color: #fff;
		border-radius: 1rem;
		padding: 2rem;
		box-shadow: 0 0 30px rgba(0, 0, 0, 0.05);
	}

	.glass-card {
		background: rgba(255, 255, 255, 0.15);
		backdrop-filter: blur(10px);
		-webkit-backdrop-filter: blur(10px);
		border: 1px solid rgba(255, 255, 255, 0.2);
		border-radius: 1rem;
		transition: transform 0.3s ease;
	}

	.glass-card:hover {
		transform: translateY(-5px);
	}
</style>

<!-- TEAM MEMBERS STYLE END  -->

<!-- Custom Blue Theme Style -->
<style>
	.btn-primary {
		background-color: #007bff;
		border-color: #007bff;
	}

	.btn-outline-primary {
		color: #007bff;
		border-color: #007bff;
	}

	.btn-outline-primary:hover {
		background-color: #007bff;
		color: #ffffff;
	}

	.features-card {
		background-color: white;
		/* Lighter blue card */
		border-radius: 15px;
		padding: 20px;
		box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);

	}

	.team-container {
		display: flex;
		flex-wrap: wrap;
		justify-content: center;
		gap: 20px;
		/* Space between members */
	}

	.team-member {
		flex: 1 1 200px;
		/* Minimum width of 200px and flexible growth */
		max-width: 250px;
		/* Prevents too-wide members */
		text-align: center;
	}



	.team-member img {
		width: 120px;
		height: 120px;
		border-radius: 50%;
	}

	.team-member h5 {
		margin-top: 10px;
		font-size: 1.1rem;
	}

	.team-member p {
		font-size: 0.9rem;
		color: #555;
	}
</style>
</head>
<!-- Font Awesome CDN for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">



<div class="card mb-3" style="background-color: transparent;border: none;  box-shadow: none;">
	<!-- Main Banner -->
	<header class="afmams-header position-relative text-white">
		<div class="container text-center py-5">
			<div class="floating-icons mb-4">
				<i class="fas fa-user-check fa-2x mx-3 text-white"></i>
				<i class="fas fa-money-check-alt fa-2x mx-3 text-white"></i>
				<i class="fas fa-calendar-alt fa-2x mx-3 text-white"></i>
			</div>
			<h1 class="display-4 fw-bold mb-3">AFMAMS</h1>
			<p class="lead px-3 px-md-5">
				Attendance, Fines, and Activity Management System<br />
				Built to simplify student tracking and event coordination.
			</p>
		</div>

	</header>



	<!-- Features Section 
	<section id="features" class="py-3">
		<div class="card mb-3 mb-lg-0">
			<div class="card-header bg-body-tertiary d-flex justify-content-between">
				<h5 class="mb-0">Our Features</h5>
			</div>
		</div>

		 Space Between Sections 
	<div class="space" style="height: 20px;"></div>  Adds spacing between sections


	<div class="row d-flex align-items-stretch">

		<div class="col-md-4">
			<div class="features-card text-center h-100">
				<i class="fas fa-calendar-check fa-3x mb-3 text-primary"></i>
				<h5>Attendance Monitoring</h5>
				<p>Track attendance with real-time QR code scanning.</p>
			</div>
		</div>
		<div class="col-md-4">
			<div class="features-card text-center h-100">
				<i class="fas fa-money-bill-wave fa-3x mb-3 text-primary"></i>
				<h5>Fines Monitoring</h5>
				<p>Track and manage student fines efficiently in real-time.</p>
			</div>

		</div>
		<div class="col-md-4">
			<div class="features-card text-center h-100">
				<i class="fas fa-calendar-alt fa-3x mb-3 text-primary"></i>
				<h5>Activity Management</h5>
				<p>Efficiently organize and oversee events, schedules, and student activities.</p>
			</div>
		</div>
	</div>

	</section> -->

	<!-- About Section -->

	<!-- About Our Project Card -->
	<div class="card mb-4 mt-4 shadow-sm border-0" style="background: linear-gradient(to right, #f8f9fa, #e9ecef);">
		<div class="card-body px-4 py-5">
			<h2 class="text-center mb-5">
				<i class="fas fa-project-diagram text-primary me-2"></i> About Our Project
			</h2>
			<p class="lead text-center mb-5">
				<strong>AFMAMS</strong> — <em>Attendance, Fines, and Activity Management System</em> — is designed to make student and activity tracking efficient and organized. It allows real-time monitoring of student <strong>attendance</strong> using QR code scanning, ensuring accuracy and accountability. The system also features an automated <strong>fines management</strong> module that calculates and tracks penalties for absences or violations, helping maintain discipline and transparency across all activities.
			</p>

			<div class="row justify-content-center">
				<!-- Feature 1 -->
				<div class="col-12 col-md-4 mb-4">
					<div class="text-center p-4 border rounded shadow-sm bg-white h-100 feature-box">
						<i class="fas fa-calendar-check fa-3x text-success mb-3"></i>
						<h5 class="fw-bold">Attendance</h5>
						<p class="text-muted">Real-time QR-based attendance monitoring</p>
					</div>
				</div>
				<!-- Feature 2 -->
				<div class="col-12 col-md-4 mb-4">
					<div class="text-center p-4 border rounded shadow-sm bg-white h-100 feature-box">
						<i class="fas fa-money-bill-wave fa-3x text-warning mb-3"></i>
						<h5 class="fw-bold">Fines</h5>
						<p class="text-muted">Automated fine tracking and management</p>
					</div>
				</div>
				<!-- Feature 3 -->
				<div class="col-12 col-md-4 mb-4">
					<div class="text-center p-4 border rounded shadow-sm bg-white h-100 feature-box">
						<i class="fas fa-calendar-alt fa-3x text-info mb-3"></i>
						<h5 class="fw-bold">Activities</h5>
						<p class="text-muted">Organize and oversee events & student activities</p>
					</div>
				</div>
			</div>
		</div>
	</div>






	<!-- Technologies Used Card -->
	<div class="card mb-4 shadow-sm border-0">
		<div class="card-body">
			<h3 class="text-center mb-4">
				<i class="fas fa-laptop-code text-success"></i> Technologies Used
			</h3>
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-center align-middle">
					<thead class="table-light">
						<tr>
							<th><i class="fas fa-cogs"></i> Backend</th>
							<th><i class="fas fa-database"></i> Database</th>
							<th><i class="fas fa-desktop"></i> Frontend</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<i class="fab fa-php fa-2x text-primary mb-2"></i><br>
								PHP, CodeIgniter
							</td>
							<td>
								<i class="fas fa-database fa-2x text-danger mb-2"></i><br>
								MySQL
							</td>
							<td>
								<i class="fab fa-bootstrap fa-2x text-info mb-2"></i><br>
								Bootstrap 4
							</td>
						</tr>
						<tr>
							<td>
								<i class="fas fa-code fa-2x text-secondary mb-2"></i><br>
								Backend Logic
							</td>
							<td>
								<i class="fas fa-server fa-2x text-success mb-2"></i><br>
								XAMPP / Apache
							</td>
							<td>
								<i class="fas fa-icons fa-2x text-warning mb-2"></i><br>
								Font Awesome
							</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td>
								<i class="fas fa-code fa-2x text-dark mb-2"></i><br>
								jQuery, HTML/CSS
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>


	<!-- Development Tools Card -->
	<div class="card mb-4 shadow-sm border-0">
		<div class="card-body">
			<h3 class="text-center mb-4">
				<i class="fas fa-tools text-primary"></i> Development Tools
			</h3>
			<div class="row text-center">
				<div class="col-md-4 mb-3">
					<i class="fas fa-code fa-2x text-secondary mb-2"></i>
					<p class="mb-0">VS Code</p>
				</div>
				<div class="col-md-4 mb-3">
					<i class="fab fa-github fa-2x text-dark mb-2"></i>
					<p class="mb-0">Git & GitHub</p>
				</div>
				<div class="col-md-4 mb-3">
					<i class="fas fa-server fa-2x text-success mb-2"></i>
					<p class="mb-0">XAMPP</p>
				</div>
			</div>
		</div>
	</div>




	<!-- Future Plans Card -->
	<div class="card mb-4 shadow-sm">
		<div class="card-body">
			<h3 class="text-center mb-4">
				<i class="fas fa-lightbulb text-warning"></i> Future Plans
			</h3>
			<p class="text-center">
				We plan to enhance the system with more features like advanced reporting, user notifications, and real-time updates for attendance and activity statuses.
			</p>
		</div>
	</div>



	<!-- Team Members Card -->
	<div class="container my-3 team-section">
		<div class="text-center mb-4">
			<h2 class="text-primary"><i class="fas fa-users"></i> Meet Our Team</h2>
			<p class="text-muted">The minds behind AFMAMS</p>
		</div>

		<div class="row justify-content-center">
			<!-- First row: 3 members -->

			<div class="col-md-4 mb-4">
				<div class="card glass-card text-center h-100 shadow-sm">
					<div class="card-body d-flex flex-column">
						<img src="<?= base_url('assets/team/kevin.jpg'); ?>" class="rounded-circle mx-auto mb-3" width="100" height="100" alt="Kevin Gabriel Maranan" style="object-fit: cover;">
						<h5 class="card-title">Kevin Gabriel Maranan</h5>
						<p class="text-muted">Lead Programmer</p>
						<blockquote class="blockquote small fst-italic text-secondary">“Building solutions that scale.”</blockquote>
						<div class="mt-auto">
							<a href="#" class="text-primary mx-1"><i class="fab fa-facebook"></i></a>
							<a href="#" class="text-info mx-1"><i class="fab fa-twitter"></i></a>
							<a href="#" class="text-danger mx-1"><i class="fab fa-google-plus"></i></a>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-4 mb-4">
				<div class="card glass-card text-center h-100 shadow-sm">
					<div class="card-body d-flex flex-column">
						<img src="<?= base_url('assets/team/jarmaine.jpg'); ?>" class="rounded-circle mx-auto mb-3" width="100" height="100" alt="Jarmaine Neil Mojica" style="object-fit: cover;">
						<h5 class="card-title">Jarmaine Neil Mojica</h5>
						<p class="text-muted">Project Manager</p>
						<blockquote class="blockquote small fst-italic text-secondary">“Leading with passion and precision.”</blockquote>
						<div class="mt-auto">
							<a href="#" class="text-primary mx-1"><i class="fab fa-facebook"></i></a>
							<a href="#" class="text-info mx-1"><i class="fab fa-twitter"></i></a>
							<a href="#" class="text-danger mx-1"><i class="fab fa-google-plus"></i></a>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-4 mb-4">
				<div class="card glass-card text-center h-100 shadow-sm">
					<div class="card-body d-flex flex-column">
						<img src="<?= base_url('assets/team/jenah.jpg'); ?>" class="rounded-circle mx-auto mb-3" width="100" height="100" alt="Jenah Marie Rivero" style="object-fit: cover;">
						<h5 class="card-title">Jenah Marie Z. Rivero</h5>
						<p class="text-muted">Programmer</p>
						<blockquote class="blockquote small fst-italic text-secondary">“Turning coffee into clean code.”</blockquote>
						<div class="mt-auto">
							<a href="#" class="text-primary mx-1"><i class="fab fa-facebook"></i></a>
							<a href="#" class="text-info mx-1"><i class="fab fa-twitter"></i></a>
							<a href="#" class="text-danger mx-1"><i class="fab fa-google-plus"></i></a>
						</div>
					</div>
				</div>
			</div>



			<!-- Second row: 2 members centered -->
			<div class="col-md-4 text-center glass-card p-4 m-2">
				<div class="card text-center h-100 shadow-sm border rounded-4">
					<div class="card-body d-flex flex-column">
						<img src="<?= base_url('assets/team/shiann.jpg'); ?>" class="rounded-circle mx-auto mb-3" width="100" height="100" alt="Shian Nichole Marcos" style="object-fit: cover;">
						<h5 class="card-title">Shian Nichole Marcos</h5>
						<p class="text-muted">Documentation</p>
						<blockquote class="blockquote small fst-italic text-secondary">“Details make the difference.”</blockquote>
						<div class="mt-auto">
							<a href="#" class="text-primary mx-1"><i class="fab fa-facebook"></i></a>
							<a href="#" class="text-info mx-1"><i class="fab fa-twitter"></i></a>
							<a href="#" class="text-danger mx-1"><i class="fab fa-google-plus"></i></a>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-4 text-center glass-card p-4 m-2">
				<div class="card text-center h-100 shadow-sm border rounded-4">
					<div class="card-body d-flex flex-column">
						<img src="<?= base_url('assets/team/niwed.jpg'); ?>" class="rounded-circle mx-auto mb-3" width="100" height="100" alt="Niwed Jevett Abad" style="object-fit: cover;">
						<h5 class="card-title">Niwed Jevett Abad</h5>
						<p class="text-muted">Quality Analyst</p>
						<blockquote class="blockquote small fst-italic text-secondary">“Designing with empathy and elegance.”</blockquote>
						<div class="mt-auto">
							<a href="#" class="text-primary mx-1"><i class="fab fa-facebook"></i></a>
							<a href="#" class="text-info mx-1"><i class="fab fa-twitter"></i></a>
							<a href="#" class="text-danger mx-1"><i class="fab fa-google-plus"></i></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>





	<!-- </section> -->

</div>