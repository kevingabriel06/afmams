<!DOCTYPE html>
<html data-bs-theme="light" lang="en-US" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AFMAMS | Login </title>

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/img/favicons/apple-touch-icon.png')?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/img/favicons/favicon-32x32.png')?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/img/favicons/favicon-16x16.png')?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('assets/img/favicons/favicon.ico')?>">
    <link rel="manifest" href="<?= base_url('assets/img/favicons/manifest.json')?>">
    <meta name="msapplication-TileImage" content="<?= base_url('assets/img/favicons/mstile-150x150.png')?>">
    <meta name="theme-color" content="#ffffff">

    <!-- Stylesheets -->
    <link rel="preconnect" href="<?= base_url('https://fonts.gstatic.com/')?>">
    <link href="<?= base_url('https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&display=swap')?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendors/simplebar/simplebar.min.css" rel="stylesheet')?>">
    <link href="<?= base_url('assets/css/theme-rtl.min.css')?>" rel="stylesheet" id="style-rtl">
    <link href="<?= base_url('assets/css/theme.min.css')?>" rel="stylesheet" id="style-default">
    <link href="<?= base_url('assets/css/user-rtl.min.css')?>" rel="stylesheet" id="user-style-rtl">
    <link href="<?= base_url('assets/css/user.min.css')?>" rel="stylesheet" id="user-style-default">

</head>

<body>
    <main class="main" id="top">
		
        <div class="container-fluid">
            <div class="row min-vh-100 bg-100">
                <div class="col-6 d-none d-lg-block position-relative">
                    <div class="bg-holder" style="background-image:url(<?= base_url('assets/images/materials/login.png'); ?>); background-position: 50% 20%;"></div>
                </div>
                <div class="col-sm-10 col-md-6 px-sm-0 align-self-center mx-auto py-5">
                    <div class="row justify-content-center g-0">
                        <div class="col-lg-9 col-xl-8 col-xxl-6">
                            <div class="card">
                                <div class="card-header bg-circle-shape bg-shape text-center p-2">
                                    <a class="font-sans-serif fw-bolder fs-5 z-1 position-relative link-light" href="" data-bs-theme="light">AFMAMS</a>
                                </div>
                                <!-- Display error message if it exists -->
                                <?php if ($this->session->flashdata('error')): ?>
                                    <div style="color: red;">
                                        <?= $this->session->flashdata('error'); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body p-4">
                                    <div class="row flex-between-center">
                                        <div class="col-auto">
                                            <h3>Log In</h3>
                                        </div>
                                    </div>
                                    <form method="post" action="<?= site_url('login') ?>">
                                        <div class="mb-3">
                                            <label class="form-label" for="split-login-username">Student ID</label>
                                            <input class="form-control" id="split-login-username" type="text" name="student_id" />
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <label class="form-label" for="split-login-password">Password</label>
                                            </div>
                                            <input class="form-control" id="split-login-password" type="password" name="password" />
                                        </div>
                                        <div class="row flex-between-center">
                                            <div class="col-auto">
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" id="split-checkbox" />
                                                    <label class="form-check-label mb-0" for="split-checkbox">Remember me</label>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <a class="fs-10" href="forgot-password.html">Forgot Password?</a>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <button class="btn btn-primary d-block w-100 mt-3" type="submit" name="submit">Log in</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScripts -->
	<script src="<?= base_url('assets/vendors/popper/popper.min.js'); ?>"></script>
	<script src="<?= base_url('assets/vendors/bootstrap/bootstrap.min.js'); ?>"></script>
	<script src="<?= base_url('assets/vendors/anchorjs/anchor.min.js'); ?>"></script>
	<script src="<?= base_url('assets/vendors/is/is.min.js'); ?>"></script>
	<script src="<?= base_url('assets/vendors/fontawesome/all.min.js'); ?>"></script>
	<script src="<?= base_url('assets/vendors/lodash/lodash.min.js'); ?>"></script>
	<script src="<?= base_url('polyfill.io/v3/polyfill.min58be.js?features=window.scroll'); ?>"></script>
	<script src="<?= base_url('assets/vendors/list.js/list.min.js'); ?>"></script>
	<script src="<?= base_url('assets/js/theme.js'); ?>"></script>


</body>

</html>
