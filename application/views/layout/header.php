<!DOCTYPE html>
<html data-bs-theme="light" lang="en-US" dir="ltr">

<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- ===============================================--><!--    Document Title--><!-- ===============================================-->
	<title>AFMAMS | <?php echo $title ?></title>

	<!-- ===============================================--><!--    Favicons--><!-- ===============================================-->
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url('assets/img/favicons/apple-touch-icon.png'); ?>">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url('assets/img/favicons/favicon-32x32.png'); ?>">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url('assets/img/favicons/favicon-16x16.pn'); ?>g">
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url('assets/img/favicons/favicon.ico'); ?>">
	<link rel="manifest" href="<?php echo base_url('assets/img/favicons/site.webmanifest'); ?>">
	<meta name="msapplication-TileImage" content="<?php echo base_url('assets/img/favicons/mstile-150x150.png'); ?>">
	<meta name="theme-color" content="#ffffff">
	<script src="<?php echo base_url('assets/js/config.js'); ?>"></script>
	<script src="<?php echo base_url('vendors/simplebar/simplebar.min.js'); ?>"></script>

	<!-- ===============================================--><!--    Stylesheets--><!-- ===============================================-->

	<link rel="preconnect" href="https://fonts.gstatic.com/">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
	<link href="<?php echo base_url(); ?>assets/css/theme-rtl.min.css" rel="stylesheet" id="style-rtl">
	<link href="<?php echo base_url(); ?>assets/css/theme.min.css" rel="stylesheet" id="style-default">
	<link href="<?php echo base_url(); ?>assets/css/user-rtl.min.css" rel="stylesheet" id="user-style-rtl">
	<link href="<?php echo base_url(); ?>assets/css/user.min.css" rel="stylesheet" id="user-style-default">

	<!-- QR Scanner Script -->
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/qr1.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/qr2.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/qr3.js"></script>

	<!-- capture-photo -->
	<link href="<?php echo base_url(); ?>assets/css/capture-photo.css" rel="stylesheet" id="user-style-default">

	<!-- JQUERY | SWEETALERT | CALENDAR-->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
	<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

	<!-- responsible for animation -->
	<script>
		var isRTL = JSON.parse(localStorage.getItem('isRTL'));
		if (isRTL) {
			var linkDefault = document.getElementById('style-default');
			var userLinkDefault = document.getElementById('user-style-default');
			linkDefault.setAttribute('disabled', true);
			userLinkDefault.setAttribute('disabled', true);
			document.querySelector('html').setAttribute('dir', 'rtl');
		} else {
			var linkRTL = document.getElementById('style-rtl');
			var userLinkRTL = document.getElementById('user-style-rtl');
			linkRTL.setAttribute('disabled', true);
			userLinkRTL.setAttribute('disabled', true);
		}
	</script>
</head>

<body>
	<!-- ===============================================--><!--    Main Content--><!-- ===============================================-->
	<main class="main" id="top">
		<div class="container" data-layout="container">
			<script>
				var isFluid = JSON.parse(localStorage.getItem('isFluid'));
				if (isFluid) {
					var container = document.querySelector('[data-layout]');
					container.classList.remove('container');
					container.classList.add('container-fluid');
				}
			</script>

			<!-- navigation bar for the admin -->
			<nav class="navbar navbar-light navbar-vertical navbar-expand-xl">
				<script>
					var navbarStyle = localStorage.getItem("navbarStyle");
					if (navbarStyle && navbarStyle !== 'transparent') {
						document.querySelector('.navbar-vertical').classList.add(`navbar-${navbarStyle}`);
					}
				</script>
				<div class="d-flex align-items-center">
					<div class="toggle-icon-wrapper">
						<button class="btn navbar-toggler-humburger-icon navbar-vertical-toggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
					</div><a class="navbar-brand" href="">
						<div class="d-flex align-items-center py-3"><img class="me-2" src="<?php echo base_url('assets/img/pictures/android-chrome-512x512.png'); ?>" alt="" width="50" /><span class="font-sans-serif text-primary">AFMAMS</span></div>
					</a>
				</div>
				<?php if ($users['role'] == 'Student'): ?>
					<!-- student -->
					<div class="collapse navbar-collapse" id="navbarVerticalCollapse">
						<div class="navbar-vertical-content scrollbar">
							<ul class="navbar-nav flex-column mb-3" id="navbarVerticalNav">
								<!-- Home -->
								<a class="nav-link" href="<?php echo site_url('student/home'); ?>" role="button">
									<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-home"></span></span><span class="nav-link-text ps-1">Home</span></div>
								</a>
								<!-- menu options -->
								<li class="nav-item"><!-- label-->
									<div class="row navbar-vertical-label-wrapper mt-3 mb-2">
										<div class="col-auto navbar-vertical-label">Menu</div>
										<div class="col ps-0">
											<hr class="mb-0 navbar-vertical-divider" />
										</div>
									</div><!-- Attendance pages-->
									<a class="nav-link" href="<?php echo site_url('student/attendance-history'); ?>" role="button">
										<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-calendar-alt"></span></span><span class="nav-link-text ps-1">Attendance History</span></div>
									</a><!-- Fines pages-->
									<a class="nav-link" href="<?php echo site_url('student/summary-fines') ?>" role="button">
										<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-coins"></span></span><span class="nav-link-text ps-1">Summary of Fines</span></div>
									</a>
									<!-- Activity management pages-->
									<a class="nav-link dropdown-indicator" href="#activity" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="">
										<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fab fa-buromobelexperte"></span></span><span class="nav-link-text ps-1">Activity</span></div>
									</a>
									<ul class="nav collapse" id="activity">
										<li class="nav-item"><a class="nav-link" href="<?php echo site_url('student/list-activity'); ?>">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1">List of Activity</span></div>
											</a><!-- more inner pages--></li>
										<li class="nav-item"><a class="nav-link" href="<?php echo site_url('student/evaluation-form/list'); ?>">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1">List Evaluation Form</span></div>
											</a><!-- more inner pages--></li>
										<li class="nav-item"><a class="nav-link" href="<?php echo site_url('student/excuse-application/list'); ?>">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1">List Excuse Application</span></div>
											</a><!-- more inner pages--></li>
									</ul><!-- parent pages-->
								</li>
								<!-- about page -->
								<a class="nav-link" href="<?php echo site_url('student/about') ?>" role="button">
									<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-info-circle"></span></span><span class="nav-link-text ps-1">About</span></div>
								</a>
							</ul>
						</div>
					</div>
				<?php elseif ($users['role'] == 'Officer') : ?>
					<div class="collapse navbar-collapse" id="navbarVerticalCollapse">
						<div class="navbar-vertical-content scrollbar">
							<ul class="navbar-nav flex-column mb-3" id="navbarVerticalNav">

								<!-- dashboard -->
								<a class="nav-link" href="<?php echo site_url('officer/dashboard') ?>" role="button">
									<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-chart-pie"></span></span><span class="nav-link-text ps-1">Dashboard</span></div>
								</a>

								<!-- menu options -->
								<li class="nav-item"><!-- label-->
									<div class="row navbar-vertical-label-wrapper mt-3 mb-2">
										<div class="col-auto navbar-vertical-label">Menu</div>
										<div class="col ps-0">
											<hr class="mb-0 navbar-vertical-divider" />
										</div>
									</div><!-- Attendance pages-->
									<a class="nav-link" href="<?php echo site_url('officer/list-activities-attendance'); ?>" role="button">
										<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-calendar-alt"></span></span><span class="nav-link-text ps-1">Attendance</span></div>
									</a><!-- Fines pages-->
									<a class="nav-link" href="<?php echo site_url('officer/list-fines') ?>" role="button">
										<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-coins"></span></span><span class="nav-link-text ps-1">Fines</span></div>
									</a>
									<!-- Activity management pages-->
									<a class="nav-link dropdown-indicator" href="#email" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="">
										<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fab fa-buromobelexperte"></span></span><span class="nav-link-text ps-1">Activity</span></div>
									</a>
									<ul class="nav collapse" id="email">
										<li class="nav-item"><a class="nav-link" href="<?php echo site_url('officer/create-activity'); ?>">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1">Create an Activity</span></div>
											</a><!-- more inner pages--></li>
										<li class="nav-item"><a class="nav-link" href="<?php echo site_url('officer/list-of-activity'); ?>">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1">List of Activity</span></div>
											</a><!-- more inner pages--></li>
										<li class="nav-item"><a class="nav-link" href="<?php echo site_url('officer/list-activity-evaluation'); ?>">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1">List of Evaluation Form</span></div>
											</a><!-- more inner pages--></li>
										<li class="nav-item"><a class="nav-link" href="<?php echo site_url('officer/activity-list'); ?>">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1">List of Excuse Letter</span></div>
											</a><!-- more inner pages--></li>
									</ul><!-- parent pages-->
								</li>

								<!-- community page -->
								<a class="nav-link" href="<?php echo site_url('officer/community'); ?>" role="button">
									<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-comments"></span></span><span class="nav-link-text ps-1">Community</span></div>
								</a>

								<!-- about page -->
								<a class="nav-link" href="<?php echo site_url('officer/about'); ?>" role="button">
									<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-info-circle"></span></span><span class="nav-link-text ps-1">About</span></div>
								</a>

							</ul>

						</div>
					</div>
				<?php else : ?>
					<!-- admin -->
					<div class="collapse navbar-collapse" id="navbarVerticalCollapse">
						<div class="navbar-vertical-content scrollbar">
							<ul class="navbar-nav flex-column mb-3" id="navbarVerticalNav">

								<!-- dashboard -->
								<a class="nav-link" href="<?php echo site_url('admin/dashboard') ?>" role="button">
									<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-chart-pie"></span></span><span class="nav-link-text ps-1">Dashboard</span></div>
								</a>

								<!-- menu options -->
								<li class="nav-item"><!-- label-->
									<div class="row navbar-vertical-label-wrapper mt-3 mb-2">
										<div class="col-auto navbar-vertical-label">Menu</div>
										<div class="col ps-0">
											<hr class="mb-0 navbar-vertical-divider" />
										</div>
									</div><!-- Attendance pages-->
									<a class="nav-link" href="<?php echo site_url('admin/list-activities-attendance'); ?>" role="button">
										<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-calendar-alt"></span></span><span class="nav-link-text ps-1">Attendance</span></div>
									</a><!-- Fines pages-->
									<a class="nav-link" href="<?php echo site_url('admin/list-fines') ?>" role="button">
										<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-coins"></span></span><span class="nav-link-text ps-1">Fines</span></div>
									</a>
									<!-- Activity management pages-->
									<a class="nav-link dropdown-indicator" href="#email" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="">
										<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fab fa-buromobelexperte"></span></span><span class="nav-link-text ps-1">Activity</span></div>
									</a>
									<ul class="nav collapse" id="email">
										<li class="nav-item"><a class="nav-link" href="<?php echo site_url('admin/create-activity'); ?>">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1">Create an Activity</span></div>
											</a><!-- more inner pages--></li>
										<li class="nav-item"><a class="nav-link" href="<?php echo site_url('admin/list-of-activity'); ?>">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1">List of Activity</span></div>
											</a><!-- more inner pages--></li>
										<li class="nav-item"><a class="nav-link" href="<?php echo site_url('admin/list-activity-evaluation'); ?>">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1">List of Evaluation Form</span></div>
											</a><!-- more inner pages--></li>
										<li class="nav-item"><a class="nav-link" href="<?php echo site_url('admin/activity-list'); ?>">
												<div class="d-flex align-items-center"><span class="nav-link-text ps-1">List of Excuse Letter</span></div>
											</a><!-- more inner pages--></li>
									</ul><!-- parent pages-->
								</li>

								<!-- community page -->
								<a class="nav-link" href="<?php echo site_url('admin/community'); ?>" role="button">
									<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-comments"></span></span><span class="nav-link-text ps-1">Community</span></div>
								</a>

								<!-- about page -->
								<a class="nav-link" href="<?php echo site_url('admin/about'); ?>" role="button">
									<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-info-circle"></span></span><span class="nav-link-text ps-1">About</span></div>
								</a>

							</ul>

						</div>
					</div>
				<?php endif; ?>
			</nav>

			<!-- the main content start here! -->
			<div class="content">
				<nav class="navbar navbar-light navbar-glass navbar-top navbar-expand">
					<button class="btn navbar-toggler-humburger-icon navbar-toggler me-1 me-sm-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
					<a class="navbar-brand me-1 me-sm-3" href="../index.html">
						<div class="d-flex align-items-center"><img class="me-2" src="<?php echo base_url('assets/img/pictures/android-chrome-512x512.png'); ?>" alt="" width="50" /><span class="font-sans-serif text-primary fs-6">AFMAMS</span></div>
					</a>

					<ul class="navbar-nav navbar-nav-icons ms-auto flex-row align-items-center">
						<!-- NOTIFICATIONS START -->
						<li class="nav-item dropdown me-2">
							<a class="nav-link position-relative px-0" id="notificationBell" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onclick="markNotificationsAsRead()">
								<i class="fas fa-bell" style="font-size: 24px;"></i>
								<span id="notificationCount"
									class="position-absolute badge rounded-circle bg-danger"
									style="top: 2px; right: -6px; font-size: 0.7rem; padding: 4px 6px;">
									0
									<span class="visually-hidden">unread messages</span>
								</span>
							</a>

							<script>
								function markNotificationsAsRead() {
									$.ajax({
										url: '<?= base_url("Notifications/mark_all_as_read") ?>',
										method: 'POST',
										data: {
											student_id: '<?= $this->session->userdata('student_id') ?>',
											role: '<?= $this->session->userdata('role') ?>'
										},
										success: function(response) {
											// Optionally refresh the notifications after marking them as read
											fetchNotifications();
										},
										error: function(jqXHR, textStatus, errorThrown) {
											console.error("Failed to mark notifications as read:", textStatus);
										}
									});
								}
							</script>

							<div class="dropdown-menu dropdown-menu-end p-0" style="width: 300px; height: 400px; overflow: hidden;">

								<div class="card card-notification shadow-none" style="border: none; margin-bottom: 0; display: flex; flex-direction: column; height: 100%;">

									<div class="card-header" style="padding-bottom: 0; border-bottom: none;">
										<h6 class="card-header-title mb-0">Notifications</h6>
									</div>
									<div style="height: 100%; overflow: hidden;">
										<div id="notificationList"
											class="list-group list-group-flush fw-normal fs-10"
											style="max-height: 360px; overflow-y: auto; padding: 0; margin: 0;">
											<div id="notificationList" class="list-group list-group-flush fw-normal fs-10" style="padding: 0; margin: 0;">

												<!-- Notifications go here -->
											</div>
										</div>
									</div>
								</div>
						</li>



						<!-- NOTIFICATIONS END -->

						<!-- Dynamic Notifications Script -->
						<script>
							function fetchNotifications() {
								$.ajax({
									url: '<?= base_url("Notifications/get_notifications") ?>',
									method: 'GET',
									data: {
										student_id: '<?= $this->session->userdata('student_id') ?>',
										role: '<?= $this->session->userdata('role') ?>'
									},
									dataType: 'json',
									success: function(data) {
										let unreadCount = 0;
										let listHtml = '';

										if (data.length === 0) {
											listHtml = '<div class="text-center text-muted py-3">No notifications</div>';
										} else {
											$('#notificationList').empty();
											data.forEach(notification => {
												if (notification.is_read == 0) unreadCount++;

												const profileImg = '<?= base_url("assets/profile/") ?>' + notification.profile_pic;
												const fullName = notification.first_name + ' ' + notification.last_name;
												const message = notification.message;
												const date = new Date(notification.created_at).toLocaleString('en-US', {
													month: 'long',
													day: 'numeric',
													year: 'numeric',
													hour: 'numeric',
													minute: '2-digit'
												});

												const highlightStyle = notification.is_read == 0 ? 'background-color: #e3f2fd;' : '';

												listHtml += `
                        <a href="${notification.link}" 
                            class="list-group-item list-group-item-action d-flex align-items-center" 
                            style="${highlightStyle}" 
                            data-id="${notification.id}">
                            <img src="${profileImg}" alt="Profile" class="rounded-circle me-2" width="40" height="40">
                            <div>
                                <strong>${fullName}</strong><br>
                                ${message}<br>
                                <small class="text-muted">${date}</small>
                            </div>
                        </a>`;
											});
										}

										if (unreadCount > 0) {
											$('#notificationCount').text(unreadCount).show();
										} else {
											$('#notificationCount').hide();
										}

										$('#notificationList').html(listHtml);

										// Highlight removal after 10 seconds
										$('#notificationList .list-group-item').each(function() {
											if ($(this).css('background-color') === 'rgb(227, 242, 253)') { // #e3f2fd color
												const $notif = $(this);
												const id = $notif.data('id');

												setTimeout(() => {
													$notif.animate({
														backgroundColor: "#ffffff"
													}, 500);

													// After 10 seconds, mark as read
													$.ajax({
														url: '<?= base_url("Notifications/mark_as_read") ?>',
														method: 'POST',
														data: {
															id: id
														}
													});
												}, 10000); // 10 seconds
											}
										});
									},
									error: function(jqXHR, textStatus, errorThrown) {
										console.error("AJAX Error: " + textStatus + " - " + errorThrown);
										$('#notificationList').html('<p class="text-danger">An error occurred while loading notifications. Please try again later.</p>');
									}
								});
							}

							// Call on page load
							fetchNotifications();

							// Optional: Auto-refresh
							setInterval(fetchNotifications, 60000);

							// Mark as read when clicking a notification
							$(document).on('click', '#notificationList a', function(e) {
								e.preventDefault();

								const $notif = $(this);
								const id = $notif.data('id');
								const link = $notif.attr('href');

								$notif.animate({
										backgroundColor: "#ffffff"
									},
									500,
									function() {
										window.location.href = link;
									}
								);

								// Mark as read immediately when clicked (optional behavior)
								$.ajax({
									url: '<?= base_url("Notifications/mark_as_read") ?>',
									method: 'POST',
									data: {
										id: id
									}
								});
							});
						</script>

						<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>


						<!-- //NOTIFICATIONS END -->


						<li class="nav-item dropdown">
							<a class="nav-link pe-0 ps-2" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<div class="avatar avatar-xl">
									<img class="rounded-circle" src="<?= base_url('assets/profile/' . ($users['profile_pic'] ? $users['profile_pic'] : 'default.jpg')) ?>" alt="" />
								</div>
							</a>
							<!-- student -->
							<?php if ($users['role'] == 'Student'): ?>
								<div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end py-0" aria-labelledby="navbarDropdownUser">
									<div class="bg-white dark__bg-1000 rounded-2 py-2">
										<a class="dropdown-item fw-bold text-warning"><span>Student Account</span></a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="<?php echo site_url('student/profile-settings'); ?>">Profile Settings</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="#" onclick="confirmLogout()">Logout</a>
									</div>
								</div>
							<?php elseif ($users['role'] == 'Admin'): ?>
								<!-- admin -->
								<div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end py-0" aria-labelledby="navbarDropdownUser">
									<div class="bg-white dark__bg-1000 rounded-2 py-2">
										<a class="dropdown-item fw-bold text-warning"><span>Admin Account</span></a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="<?php echo site_url('admin/profile-settings'); ?>">Profile Settings</a>
										<a class="dropdown-item" href="<?php echo site_url('admin/general-settings'); ?>">General Settings</a>
										<a class="dropdown-item" href="<?php echo site_url('admin/manage-officers'); ?> ">Manage Officers</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="#" onclick="confirmLogout()">Logout</a>
									</div>
								</div>
							<?php else : ?>
								<div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end py-0" aria-labelledby="navbarDropdownUser">
									<div class="bg-white dark__bg-1000 rounded-2 py-2">
										<a class="dropdown-item fw-bold text-warning"><span>Officer Account</span></a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="<?php echo site_url('officer/profile-settings') ?>">Profile Settings</a>
										<a class="dropdown-item" href="<?php echo site_url('officer/manage-officers'); ?> ">Manage Officers</a>
										<a class="dropdown-item" href="<?php echo site_url('officer/general-settings'); ?>">General Settings</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="#" onclick="confirmLogout()">Logout</a>
									</div>
								</div>
							<?php endif; ?>
						</li>

						<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.6/dist/sweetalert2.min.js"></script>

						<script>
							function confirmLogout() {
								Swal.fire({
									title: 'Are you sure?',
									text: "You will be logged out of your account!",
									icon: 'warning',
									showCancelButton: true,
									confirmButtonText: 'Yes, Log Out',
									cancelButtonText: 'Cancel',
									reverseButtons: true
								}).then((result) => {
									if (result.isConfirmed) {
										// Redirect to logout URL
										window.location.href = '<?php echo site_url('logout'); ?>';
									}
								});
							}
						</script>

					</ul>
				</nav>