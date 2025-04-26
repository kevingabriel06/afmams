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
            <li class="nav-item dropdown">
              <a class="nav-link notification-indicator notification-indicator-primary px-0 fa-icon-wait" id="navbarDropdownNotification" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-hide-on-body-scroll="data-hide-on-body-scroll"><span class="fas fa-bell" data-fa-transform="shrink-6" style="font-size: 33px;"></span></a>
              <div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end dropdown-menu-card dropdown-menu-notification dropdown-caret-bg" aria-labelledby="navbarDropdownNotification">
                <div class="card card-notification shadow-none">
                  <div class="card-header">
                    <div class="row justify-content-between align-items-center">
                      <div class="col-auto">
                        <h6 class="card-header-title mb-0">Notifications</h6>
                      </div>
                      <div class="col-auto ps-0 ps-sm-3"><a class="card-link fw-normal" href="#">Mark all as read</a></div>
                    </div>
                  </div>
                  <div class="scrollbar-overlay" style="max-height:19rem">
                    <div class="list-group list-group-flush fw-normal fs-10">
                      <div class="list-group-title border-bottom">NEW</div>
                      <div class="list-group-item">
                        <a class="notification notification-flush notification-unread" href="#!">
                          <div class="notification-avatar">
                            <div class="avatar avatar-2xl me-3">
                              <img class="rounded-circle" src="../assets/img/team/1-thumb.png" alt="" />
                            </div>
                          </div>
                          <div class="notification-body">
                            <p class="mb-1"><strong>Emma Watson</strong> replied to your comment : "Hello world 😍"</p>
                            <span class="notification-time"><span class="me-2" role="img" aria-label="Emoji">💬</span>Just now</span>
                          </div>
                        </a>
                      </div>
                      <div class="list-group-item">
                        <a class="notification notification-flush notification-unread" href="#!">
                          <div class="notification-avatar">
                            <div class="avatar avatar-2xl me-3">
                              <div class="avatar-name rounded-circle"><span>AB</span></div>
                            </div>
                          </div>
                          <div class="notification-body">
                            <p class="mb-1"><strong>Albert Brooks</strong> reacted to <strong>Mia Khalifa's</strong> status</p>
                            <span class="notification-time"><span class="me-2 fab fa-gratipay text-danger"></span>9hr</span>
                          </div>
                        </a>
                      </div>
                      <div class="list-group-title border-bottom">EARLIER</div>
                      <div class="list-group-item">
                        <a class="notification notification-flush" href="#!">
                          <div class="notification-avatar">
                            <div class="avatar avatar-2xl me-3">
                              <img class="rounded-circle" src="../assets/img/icons/weather-sm.jpg" alt="" />
                            </div>
                          </div>
                          <div class="notification-body">
                            <p class="mb-1">The forecast today shows a low of 20&#8451; in California. See today's weather.</p>
                            <span class="notification-time"><span class="me-2" role="img" aria-label="Emoji">🌤️</span>1d</span>
                          </div>
                        </a>
                      </div>
                      <div class="list-group-item">
                        <a class="border-bottom-0 notification-unread  notification notification-flush" href="#!">
                          <div class="notification-avatar">
                            <div class="avatar avatar-xl me-3">
                              <img class="rounded-circle" src="../assets/img/logos/oxford.png" alt="" />
                            </div>
                          </div>
                          <div class="notification-body">
                            <p class="mb-1"><strong>University of Oxford</strong> created an event : "Causal Inference Hilary 2019"</p>
                            <span class="notification-time"><span class="me-2" role="img" aria-label="Emoji">✌️</span>1w</span>
                          </div>
                        </a>
                      </div>
                      <div class="list-group-item">
                        <a class="border-bottom-0 notification notification-flush" href="#!">
                          <div class="notification-avatar">
                            <div class="avatar avatar-xl me-3">
                              <img class="rounded-circle" src="../assets/img/team/10.jpg" alt="" />
                            </div>
                          </div>
                          <div class="notification-body">
                            <p class="mb-1"><strong>James Cameron</strong> invited to join the group: United Nations International Children's Fund</p>
                            <span class="notification-time"><span class="me-2" role="img" aria-label="Emoji">🙋‍</span>2d</span>
                          </div>
                        </a>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer text-center border-top"><a class="card-link d-block" href="../app/social/notifications.html">View all</a></div>
                </div>
              </div>
            </li>
            <li class="nav-item dropdown"><a class="nav-link pe-0 ps-2" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                    <a class="dropdown-item" href="<?php echo site_url('logout'); ?>">Logout</a>
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
                    <a class="dropdown-item" href="<?php echo site_url('logout'); ?>">Logout</a>
                  </div>
                </div>
              <?php else : ?>
                <div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end py-0" aria-labelledby="navbarDropdownUser">
                  <div class="bg-white dark__bg-1000 rounded-2 py-2">
                    <a class="dropdown-item fw-bold text-warning"><span>Officer Account</span></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo site_url('officer/profile-settings') ?>">Profile Settings</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo site_url('logout'); ?>">Logout</a>
                  </div>
                </div>
              <?php endif; ?>
            </li>
          </ul>
        </nav>