<!DOCTYPE html>
<html data-bs-theme="light" lang="en-US" dir="ltr">

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
  color: #000; /* Change to desired hover color */
}

.card-body {
  background-color: #fff; /* White card background */
  border-radius: 10px; /* Optional: rounded corners */
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
    background-color: white; /* Lighter blue card */
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);


		.team-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 20px; /* Space between members */
}

.team-member {
  flex: 1 1 200px; /* Minimum width of 200px and flexible growth */
  max-width: 250px; /* Prevents too-wide members */
  text-align: center;
}


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
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">



  <!-- Main Banner -->
  <header class="text-center py-5 bg-primary text-white">
    <div class="container">
      <h1 class="display-4">Attendance and Fines Monitoring with Activity Management</h1>
      <p class="lead">Streamlining attendance tracking, fine management, and seamless activity oversight.</p>
      <!-- <a href="#get-started" class="btn btn-light btn-lg">Get Started</a> -->
    </div>
  </header>

  <!-- Features Section -->
  <section id="features" class="py-5">
    <div class="container">
      <h2 class="text-center mb-4">Our Features</h2>
      <div class="row">
        <div class="col-md-4">
          <div class="features-card text-center">
            <i class="fas fa-calendar-check fa-3x mb-3 text-primary"></i>
            <h5>Attendance Monitoring</h5>
            <p>Track attendance with real-time QR code scanning.</p>
          </div>
        </div>
        <div class="col-md-4">
		<div class="features-card text-center">
			<i class="fas fa-money-bill-wave fa-3x mb-3 text-primary"></i>
			<h5>Fines Monitoring</h5>
			<p>Track and manage student fines efficiently in real-time.</p>
		</div>

        </div>
        <div class="col-md-4">
		<div class="features-card text-center">
			<i class="fas fa-calendar-alt fa-3x mb-3 text-primary"></i>
			<h5>Activity Management</h5>
			<p>Efficiently organize and oversee events, schedules, and student activities.</p>
		</div>
        </div>
      </div>
    </div>
  </section>

  <!-- About Section -->

<!-- About Our Project Card -->
<div class="card mb-4 shadow-sm">
  <div class="card-body">
    <h2 class="text-center mb-4">
      <i class="fas fa-project-diagram text-primary"></i> About Our Project
    </h2>
    <p class="lead text-center mb-5">
      AFMAMS (Attendance, Fines, and Activity Management System) is a platform designed to streamline attendance monitoring, activity management, and fines calculation for students and staff.
    </p>
  </div>
</div>

<!-- Technologies Used Card -->
<div class="card mb-4 shadow-sm">
  <div class="card-body">
    <h3 class="text-center mb-4">
      <i class="fas fa-laptop-code text-success"></i> Technologies Used
    </h3>
    <table class="table table-bordered table-striped text-center">
      <thead>
        <tr>
          <th><i class="fas fa-cogs"></i> Technology</th>
          <th><i class="fas fa-cogs"></i> Technology</th>
          <th><i class="fas fa-cogs"></i> Technology</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><i class="fab fa-php fa-3x text-primary"></i> PHP, CodeIgniter</td>
          <td><i class="fas fa-database fa-3x text-danger"></i> MySQL</td>
          <td><i class="fab fa-bootstrap fa-3x text-info"></i> Bootstrap 4</td>
        </tr>
        <tr>
          <td><i class="fas fa-icons fa-3x text-warning"></i> Font Awesome</td>
          <td><i class="fab fa-jquery fa-3x text-dark"></i> jQuery</td>
          <td></td> <!-- Empty cell for layout -->
        </tr>
      </tbody>
    </table>
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
<!-- Team Members Card -->
<div class="team-container card shadow-sm">
  <!-- Header for Team Members -->
  <div class="card-header bg-primary text-white text-center">
    <h3 class="text-white">Meet Our Team</h3>
  </div>
  <div class="card-body">
    <div class="row justify-content-center"> <!-- Centering row -->
      <!-- Team Member 1 -->
      <div class="col-md-2 text-center team-member">
			<img src="<?php echo base_url('assets/team/jarmaine.jpg'); ?>" alt="Team Member" class="img-fluid rounded-circle mb-3">
        <a href="#" class="h5 d-block">Jarmaine Neil Mojica</a>
        <p>Project Manager</p>
        <div class="social-icons">
          <a href="#" class="text-primary mx-2"><i class="fab fa-facebook fa-lg"></i></a>
          <a href="#" class="text-info mx-2"><i class="fab fa-twitter fa-lg"></i></a>
          <a href="#" class="text-danger mx-2"><i class="fab fa-google-plus fa-lg"></i></a>
        </div>
      </div>
      <!-- Team Member 2 -->
      <div class="col-md-2 text-center team-member">
			<img src="<?php echo base_url('assets/team/jenah.jpg'); ?>" alt="Team Member" class="img-fluid rounded-circle mb-3">
        <a href="#" class="h5 d-block">Jenah Marie Rivero</a>
        <p>Programmer</p>
        <div class="social-icons">
          <a href="#" class="text-primary mx-2"><i class="fab fa-facebook fa-lg"></i></a>
          <a href="#" class="text-info mx-2"><i class="fab fa-twitter fa-lg"></i></a>
          <a href="#" class="text-danger mx-2"><i class="fab fa-google-plus fa-lg"></i></a>
        </div>
      </div>
      <!-- Team Member 3 -->
      <div class="col-md-2 text-center team-member">
			<img src="<?php echo base_url('assets/team/kevin.jpg'); ?>" alt="Team Member" class="img-fluid rounded-circle mb-3">
        <a href="#" class="h5 d-block">Kevin Gabriel Maranan</a>
        <p>Programmer</p>
        <div class="social-icons">
          <a href="#" class="text-primary mx-2"><i class="fab fa-facebook fa-lg"></i></a>
          <a href="#" class="text-info mx-2"><i class="fab fa-twitter fa-lg"></i></a>
          <a href="#" class="text-danger mx-2"><i class="fab fa-google-plus fa-lg"></i></a>
        </div>
      </div>
      <!-- Team Member 4 -->
      <div class="col-md-2 text-center team-member">
			<img src="<?php echo base_url('assets/team/shiann.jpg'); ?>" alt="Team Member" class="img-fluid rounded-circle mb-3">
        <a href="#" class="h5 d-block">Shian Nichole Marcos</a>
        <p>Documentation</p>
        <div class="social-icons">
          <a href="#" class="text-primary mx-2"><i class="fab fa-facebook fa-lg"></i></a>
          <a href="#" class="text-info mx-2"><i class="fab fa-twitter fa-lg"></i></a>
          <a href="#" class="text-danger mx-2"><i class="fab fa-google-plus fa-lg"></i></a>
        </div>
      </div>
      <!-- Team Member 5 -->
      <div class="col-md-2 text-center team-member">
			<img src="<?php echo base_url('assets/team/niwed.jpg'); ?>" alt="Team Member" class="img-fluid rounded-circle mb-3">
        <a href="#" class="h5 d-block">Niwed Jevett Abad</a>
        <p>Designer</p>
        <div class="social-icons">
          <a href="#" class="text-primary mx-2"><i class="fab fa-facebook fa-lg"></i></a>
          <a href="#" class="text-info mx-2"><i class="fab fa-twitter fa-lg"></i></a>
          <a href="#" class="text-danger mx-2"><i class="fab fa-google-plus fa-lg"></i></a>
        </div>
      </div>
    </div>
  </div>
</div>



<!-- </section> -->

</div>


