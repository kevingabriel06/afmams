<!DOCTYPE html>
<html data-bs-theme="light" lang="en-US" dir="ltr">

<!-- Mirrored from prium.github.io/falcon/v3.19.0/pages/landing.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 22 Nov 2023 06:21:16 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ===============================================--><!--    Document Title--><!-- ===============================================-->
    <title>AFMAMS</title>

    <!-- ===============================================--><!--    Favicons--><!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url('assets/img/favicons/apple-touch-icon.png'); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url('assets/img/favicons/favicon-32x32.png'); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url('assets/img/favicons/favicon-16x16.pn'); ?>g">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url('assets/img/favicons/favicon.ico'); ?>">
    <link rel="manifest" href="<?php echo base_url('assets/img/favicons/site.webmanifest'); ?>">
    <meta name="msapplication-TileImage" content="<?php echo base_url('assets/img/favicons/mstile-150x150.png'); ?>">
    <meta name="theme-color" content="#ffffff">
    <script src="../assets/js/config.js"></script>
    <script src="../vendors/simplebar/simplebar.min.js"></script>

    <!-- ===============================================--><!--    Stylesheets--><!-- ===============================================-->
    <link href="<?php echo base_url('vendors/swiper/swiper-bundle.min.css'); ?>" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com/">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
    <link href="<?php echo base_url('vendors/simplebar/simplebar.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/theme-rtl.min.css'); ?>" rel="stylesheet" id="style-rtl">
    <link href="<?php echo base_url('assets/css/theme.min.css'); ?>" rel="stylesheet" id="style-default">
    <link href="<?php echo base_url('assets/css/user-rtl.min.css'); ?>" rel="stylesheet" id="user-style-rtl">
    <link href="<?php echo base_url('assets/css/user.min.css'); ?>" rel="stylesheet" id="user-style-default">

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
        <nav class="navbar navbar-standard navbar-expand-lg fixed-top navbar-dark" data-navbar-darken-on-scroll="data-navbar-darken-on-scroll">
            <div class="container"><a class="navbar-brand" href="../index.html"><span class="text-white dark__text-white">AFMAMS</span></a><button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarStandard" aria-controls="navbarStandard" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse scrollbar" id="navbarStandard">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo site_url('login'); ?>">Login</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- ============================================--><!-- <section> begin ============================-->
        <section class="py-0 overflow-hidden min-vh-100" id="banner" data-bs-theme="light">
            <div class="bg-holder overlay" style="background-image:url(<?php echo base_url('assets/images/materials/background-tech.png'); ?>);background-position: center bottom;"></div><!--/.bg-holder-->
            <div class="container">
                <div class="row align-items-center pt-8 pt-lg-10 pb-lg-9 pb-xl-0">
                    <div class="col-md-12 col-lg-6 col-xl-5 text-center text-xl-start">
                        <h1 class="text-white fw-light">Monitor <span class="typed-text fw-bold" data-typed-text='["activities","attendance","fines"]'></span><br />with ease and efficiency</h1>
                        <p class="lead text-white opacity-75">Manage your activities, track attendance, and monitor fines — all in one streamlined system designed to keep things simple and organized.</p><a class="btn btn-outline-light border-2 rounded-pill btn-lg mt-4 fs-9 py-2" href="<?php echo site_url('login'); ?>">Explore the system<span class="fas fa-play ms-2" data-fa-transform="shrink-6 down-1"></span></a>
                    </div>
                    <!-- <div class="col-xl-7 offset-xl-0 mt-4 mt-xl-0 text-center align-self-end"><a class="img-landing-banner rounded" href=""><img class="img-fluid d-dark-none" src="<//?php echo base_url('assets/img/generic/dashboard-alt.jpg'); ?>" alt="" /><img class="img-fluid d-light-none" src="<//?php echo base_url('assets/img/generic/dashboard-alt-dark.png'); ?>" alt="" /></a></div> -->
                </div>
            </div><!-- end of .container-->
        </section><!-- <section> close ============================--><!-- ============================================-->


        <!-- ============================================--><!-- <section> begin ============================-->
        <section>
            <div class="container">
                <div class="row justify-content-center text-center">
                    <div class="col-lg-8 col-xl-7 col-xxl-6">
                        <h1 class="fs-7 fs-sm-5 fs-md-4">Manage Attendance, Monitor Fines, and Organize Activities with Ease</h1>
                        <p class="lead">A powerful web-based system designed to streamline your organization's events, ensure attendance tracking, and monitor compliance through fines — all in one place.</p>
                    </div>
                </div>

                <!-- PLAN -->
                <div class="row flex-center mt-8">
                    <div class="col-md col-lg-5 col-xl-4 ps-lg-6">
                        <img class="img-fluid px-6 px-md-0" src="<?php echo base_url('assets/images/icons/planning.png'); ?>" alt="Planning Icon" />
                    </div>
                    <div class="col-md col-lg-5 col-xl-4 mt-4 mt-md-0">
                        <h5 class="text-danger"><span class="far fa-calendar-check me-2"></span>PLAN</h5>
                        <h3>Activity Scheduling & Setup</h3>
                        <p>Plan and set up your events or meetings in advance. Assign departments, set schedules, and prepare attendance forms with built-in tools to keep things on track from the start.</p>
                    </div>
                </div>

                <!-- BUILD -->
                <div class="row flex-center mt-7">
                    <div class="col-md col-lg-5 col-xl-4 pe-lg-6 order-md-2">
                        <img class="img-fluid px-6 px-md-0" src="<?php echo base_url('assets/images/icons/monitoring.png'); ?>" alt="Monitoring Icon" />
                    </div>
                    <div class="col-md col-lg-5 col-xl-4 mt-4 mt-md-0">
                        <h5 class="text-info"><span class="fas fa-user-check me-2"></span>TRACK</h5>
                        <h3>Real-time Attendance & Fines Monitoring</h3>
                        <p>Monitor attendance effortlessly with integrated tools like QR scanning and facial recognition. Automatically log absences, tardiness, and generate fines based on your organization's policies.</p>
                    </div>
                </div>

                <!-- DEPLOY -->
                <div class="row flex-center mt-7">
                    <div class="col-md col-lg-5 col-xl-4 ps-lg-6">
                        <img class="img-fluid px-6 px-md-0" src="<?php echo base_url('assets/images/icons/summary-report.png'); ?>" alt="Summary Icon" />
                    </div>
                    <div class="col-md col-lg-5 col-xl-4 mt-4 mt-md-0">
                        <h5 class="text-success"><span class="fas fa-clipboard-list me-2"></span>REVIEW</h5>
                        <h3>Generate Reports & Evaluate</h3>
                        <p>Analyze attendance data, fines issued, and activity participation. Export detailed reports and get insights that help improve engagement and compliance in your organization.</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- <section> close ============================--><!-- ============================================-->



        <!-- ============================================--><!-- <section> begin ============================-->
        <section class="bg-body-tertiary dark__bg-opacity-50 text-center">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <h1 class="fs-7 fs-sm-5 fs-md-4">Meet Our Team</h1>
                        <p class="lead">Meet the amazing team behind the Attendance, Fines Monitoring, and Activity Management System.</p>
                    </div>
                </div>

                <div class="row mt-6 mb-3">
                    <div class="col-lg-4">
                        <div class="card card-span h-100">
                            <img class="card-span-img" src="<?php echo base_url('assets/images/profile/6c15e20e-79ad-47dd-82e0-60d587397ba6.jpg'); ?>" class="img-fluid rounded-circle" style="width: 100px; height: 100px; object-fit: cover;" />
                            <div class="card-body pt-6 pb-4">
                                <h5 class="mb-2">Kevin Gabriel L. Maranan</h5>
                                <h6 class="mb-3 fs-italic">Lead Programmer</h6>
                                <p>Hi, Kevin here. Please enjoy our system, it is the reason why I'm always busy no chat with my babe but I'm so grateful for her. She's always with me. Mwah. Kung may makita kayo error wag nyo na pansinin please lang.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card card-span h-100">
                            <img class="card-span-img" src="<?php echo base_url('assets/images/profile/491001449_668866715738020_5069744685178282391_n.jpg'); ?>" class="img-fluid rounded-circle" style="width: 100px; height: 100px; object-fit: cover;" />
                            <div class="card-body pt-6 pb-4">
                                <h5 class="mb-2">Jarmaine Neil R. Mojica</h5>
                                <h6 class="mb-3 fs-italic">Project Manager</h6>
                                <p>Hi, I am Neil. Please enjoy using the system as it serves functional and entertaining purposes. Message ne if youre single. Please</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card card-span h-100">
                            <img class="card-span-img" src="<?php echo base_url('assets/images/profile/491149224_1732092327375779_4061073706986534511_n.png'); ?>" class="img-fluid rounded-circle" style="width: 100px; height: 100px; object-fit: cover;" />
                            <div class="card-body pt-6 pb-4">
                                <h5 class="mb-2">Jenah Marie Z. Rivero</h5>
                                <h6 class="mb-3 fs-italic">Programmer</h6>
                                <p>Hi, I’m Jenah — the programmer.
                                    Our system runs on logic, caffeine, and pure determination (with occasional screaming at the screen).
                                    I’ve poured my effort and code into this — so please enjoy it. And if you find an error… just smile, refresh, and pretend it’s a “feature.”
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-6 justify-content-center">
                    <div class="col-lg-4">
                        <div class="card card-span h-100">
                            <img class="card-span-img" src="<?php echo base_url('assets/profile/default.jpg'); ?>" class="img-fluid rounded-circle" style="width: 100px; height: 100px; object-fit: cover;" />
                            <div class="card-body pt-6 pb-4">
                                <h5 class="mb-2">Niwed Jevett C. Abad</h5>
                                <h6 class="mb-3 fs-italic">Quality Analyst</h6>
                                <p>Envisioning a quality system that promotes accountability, transparency, and seamless user experience, through precise testingin mo'ko.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card card-span h-100">
                            <img class="card-span-img" src="<?php echo base_url('assets/profile/default.jpg'); ?>" class="img-fluid rounded-circle" style="width: 100px; height: 100px; object-fit: cover;" />
                            <div class="card-body pt-6 pb-4">
                                <h5 class="mb-2">Shiann Nicole Marcos</h5>
                                <h6 class="mb-3 fs-italic">Documentation</h6>
                                <p>Build your webapp with the world's most popular front-end component library along with Falcon's 32 sets of carefully designed elements.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- end of .container-->
        </section><!-- <section> close ============================--><!-- ============================================-->


        <!-- ============================================--><!-- <section> begin ============================-->
        <section class="bg-dark pt-8 pb-4" data-bs-theme="light">
            <div class="container">
                <div class="position-absolute btn-back-to-top bg-dark"><a class="text-600" href="#banner" data-bs-offset-top="0"><span class="fas fa-chevron-up" data-fa-transform="rotate-45"></span></a></div>
            </div><!-- end of .container-->
        </section><!-- <section> close ============================--><!-- ============================================-->



        <!-- ============================================--><!-- <section> begin ============================-->
        <section class="py-0 bg-dark" data-bs-theme="light">
            <div>
                <hr class="my-0 text-600 opacity-25" />
                <div class="container py-3">
                    <div class="row justify-content-between fs-10">
                        <div class="col-12 col-sm-auto text-center">
                            <p class="mb-0 text-600 opacity-85"> All rights reserved <span class="d-none d-sm-inline-block">| </span><br class="d-sm-none" /> 2025 &copy; <span class="fw-bold">Bachelor of Science in Information Systems</span></p>
                        </div>
                    </div>
                </div>
            </div><!-- end of .container-->
        </section><!-- <section> close ============================--><!-- ============================================-->

    </main><!-- ===============================================--><!--    End of Main Content--><!-- ===============================================-->

    <!-- ===============================================--><!--    JavaScripts--><!-- ===============================================-->
    <script src="<?php echo base_url('vendors/popper/popper.min.js'); ?>"></script>
    <script src="<?php echo base_url('vendors/bootstrap/bootstrap.min.js'); ?>"></script>
    <script src="<?php echo base_url('vendors/anchorjs/anchor.min.js'); ?>"></script>
    <script src="<?php echo base_url('vendors/is/is.min.js'); ?>"></script>
    <script src="<?php echo base_url('vendors/swiper/swiper-bundle.min.js'); ?>"></script>
    <script src="<?php echo base_url('vendors/typed.js/typed.js'); ?>"></script>
    <script src="<?php echo base_url('vendors/fontawesome/all.min.js'); ?>"></script>
    <script src="<?php echo base_url('vendors/lodash/lodash.min.js'); ?>"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
    <script src="<?php echo base_url('vendors/list.js/list.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/theme.js'); ?>"></script>

</body>


<!-- Mirrored from prium.github.io/falcon/v3.19.0/pages/landing.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 22 Nov 2023 06:21:22 GMT -->

</html>