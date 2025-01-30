<!DOCTYPE html>
<html data-bs-theme="light" lang="en-US" dir="ltr">


<!-- Mirrored from prium.github.io/falcon/v3.19.0/pages/authentication/split/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 22 Nov 2023 06:21:13 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ===============================================--><!--    Document Title--><!-- ===============================================-->
    <title>AFMAMS | Login</title>

    <!-- ===============================================--><!--    Favicons--><!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicons/favicon-16x16.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicons/favicon.ico') }}">
    <link rel="manifest" href="{{ asset('assets/img/favicons/manifest.json') }}">
    <meta name="msapplication-TileImage" content="{{ asset('assets/img/favicons/mstile-150x150.png') }}">
    <meta name="theme-color" content="#ffffff">
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <script src="{{ asset('vendors/simplebar/simplebar.min.js') }}"></script>
    {{-- libraries for qrlogin scanner --}}
    <script src="https://reeteshghimire.com.np/wp-content/uploads/2021/05/html5-qrcode.min.js"></script>
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <script src="html5-qrcode.min.js"></script>


{{-- library used for qrlogin scanner --}}
    {{-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.1.10/vue.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/webrtc-adapter/3.3.3/adapter.min.js"></script>
    <script type="text/javascript" src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script> --}}
{{-- library used for qrlogin scanner offline version --}}
    <script type="text/javascript" src="{{ asset('assets/js/qr-login/config.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/qr-login/adapter.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/qr-login/instascan.min.js') }}"></script>






    <!-- ===============================================--><!--    Stylesheets--><!-- ===============================================-->
    <link rel="preconnect" href="https://fonts.gstatic.com/">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
    <link href="{{ asset('vendors/simplebar/simplebar.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/theme-rtl.min.css') }}" rel="stylesheet" id="style-rtl">
    <link href="{{ asset('assets/css/theme.min.css') }}" rel="stylesheet" id="style-default">
    <link href="{{ asset('assets/css/user-rtl.min.css') }}" rel="stylesheet" id="user-style-rtl">
    <link href="{{ asset('assets/css/user.min.css') }}" rel="stylesheet" id="user-style-default">

  </head>

  <body>
    <!-- ===============================================--><!--    Main Content--><!-- ===============================================-->
    <main class="main" id="top">
      <div class="container-fluid">
        <script>
          var isFluid = JSON.parse(localStorage.getItem('isFluid'));
          if (isFluid) {
            var container = document.querySelector('[data-layout]');
            container.classList.remove('container');
            container.classList.add('container-fluid');
          }
        </script>
       
          <div class="col-sm-10 col-md-6 px-sm-0 align-self-center mx-auto py-5">
            <div class="row justify-content-center g-0">
              <div class="col-lg-9 col-xl-8 col-xxl-6">

                @if(session()->has("success"))
              <div class="alert alert-success">
              {{session()->get("success")}}
              </div>
            @endif
            @if(session()->has("error"))
              <div class="alert alert-success">
              {{session()->get("error")}}
              </div>
            @endif

            <div class="card" style="margin-top:70px;">
                  <div class="card-header bg-circle-shape bg-shape text-center p-2"><a class="font-sans-serif fw-bolder fs-5 z-1 position-relative link-light" href="" data-bs-theme="light">AFMAMS</a></div>


                  <div class="card-body p-4">
                    <div class="row flex-between-center">
                      <div class="col-auto" style="margin: 0 auto;">
                    <center>   <h5>Log In through QR Code Scanner</h5> </center>


                       <video id="previews" width="100%"></video>

                       {{-- <input type="text" name="outcome" id="outcome" readonly="" placeholder="Scan QR code" class="form-control"> --}}

                       <input type="text" name="student_id" id="student_id" readonly="" placeholder="Scan QR code" class="form-control">
                       {{-- <input type="text" name="name" id="name" readonly="" placeholder="Scan QR code" class="form-control">
                       <input type="text" name="department" id="department" readonly="" placeholder="Scan QR code" class="form-control"> --}}

                      </div>

                 <!-- <center>     <div>   <button onclick="window.location.href='{{ route('') }}'" class="btn btn-primary" style="margin-top: 20px;">Go to Login Page</button> </div> </center>
                    </div> -->



                 <!-- JavaScript code -->
<script>
    // Initialize the scanner
    let scanner = new Instascan.Scanner({ video: document.getElementById('previews') });

    // Add listener for when a QR code is scanned
    scanner.addListener('scan', function(content) {
        // Extract the student ID from the scanned content
        let studentId = extractStudentID(content);

        // Set the value of the student_id input field to the extracted student ID
        document.getElementById('student_id').value = studentId;

        // Send a POST request to the authentication route
        fetch('{{ route("qr.authenticate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for Laravel
            },
            body: JSON.stringify({ student_id: studentId })
        })
        .then(response => {
            // Handle the response (e.g., redirect to dashboard)
            window.location.href = "{{ route('student.home') }}";
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error authenticating.');
        });
    });

    // Start the scanner
    Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length > 0) {
            scanner.start(cameras[0]);
        } else {
            console.error('No cameras found.');
            alert('No cameras found.');
        }
    }).catch(function (e) {
        console.error(e);
        alert('Error accessing camera.');
    });

    // Function to extract the student ID from the content
    function extractStudentID(content) {
        // Regular expression pattern to match potential student IDs
        let pattern = /\b\d{2}-\d{5}\b/g; // Assuming student ID format like "21-03616"

        // Extract potential student IDs from the content using the pattern
        let studentIDs = content.match(pattern);

        // Return the first potential student ID found, or an empty string if none found
        return studentIDs ? studentIDs[0] : '';
    }
</script>


{{--
                            // let studentIdLength = 7; // Example length for student ID
                            // let nameLength = 20; // Example length for name
                            // let departmentLength = 15; // Example length for department

                            // Assuming the order of data is student ID, name, and department
                                // let studentId = c.substring(0, studentIdLength);
                                // let name = c.substring(studentIdLength, studentIdLength + nameLength);
                                // let department = c.substring(studentIdLength + nameLength);

                                // // Display data in input fields or elsewhere
                                // document.getElementById('student_id').value = studentId;
                                // document.getElementById('name').value = name;
                                // document.getElementById('department').value = department;

                         // Display individual fields
                    //    document.getElementById('student_id').value = studentId;
                    //    document.getElementById('name').value = name;
                    //    document.getElementById('department').value = department;


                        }); --}}




                {{-- //         scanner.addListener('scan', function(c){
                //          // Assuming QR code data is in JSON format like {"student_id": "123456", "name": "John Doe", "department": "Computer Science"}
                //         // let qrData = JSON.parse(c);

                //         let qrData = decodedData;

                //         Try splitting the data by common delimiters or patterns
                //         let dataComponents = qrData.split(',');

                // // Assuming the data is structured as "student_id,name,department"
                //          if (dataComponents.length === 3) {
                //         let studentId = dataComponents[0].trim();
                //         let name = dataComponents[1].trim();
                //         let department = dataComponents[2].trim();

                //        // Access individual fields
                //         // let studentId = qrData.student_id;
                //         // let name = qrData.name;
                //         // let department = qrData.department;

                //        // Display individual fields
                //        document.getElementById('student_id').value = studentId;
                //        document.getElementById('name').value = name;
                //        document.getElementById('department').value = department;

                //          }

                //     });

            //         scanner.addListener('scan', function(c){

            //            // Assuming `decodedData` contains the decoded data from the QR code
            //    let qrData = decodedData;

            // // Try splitting the data by common delimiters or patterns
            // let dataComponents = qrData.split(',');

            //     // Assuming the data is structured as "student_id,name,department"
            // if (dataComponents.length === 3) {
            //  let studentId = dataComponents[0].trim();
            //  let name = dataComponents[1].trim();
            //     let department = dataComponents[2].trim();

            //         // Now you have separated data components, you can store them in the `users` table
            //     // Example: Assuming you have a User model and `users` table with appropriate columns
            //         let user = new User();
            //          user.student_id = studentId;
            //          user.name = name;
            //          user.department = department;
            //           user.save();
            //             } else {
            //               console.log('Unable to separate data into individual components.');
            //                 }

                    // });

                   </script> --}}


{{-- <script type="text/javascript">

function onScanSuccess(data) {
    $.ajax({
        type: "POST",
        cache: false,
        url: "{{ route('qr-code-login.checkUser') }}",
        data:{"_token": "{{ csrf_token()}}",data:data},
        success: function(data) {
            if(data==1) {
                document.getElementById('student_id').innerHTML = '<span class="student_id">' + 'Logged' +'</span>';
                window.location.href = '{{ url('/') }}';
            }
            else {
                alert('There is no user with this QR Code');
            }
        }
    })
}






</script> --}}


                    {{-- <form method="POST" action="{{route("login.post")}}">
                      @csrf

                      <div class="mb-3"><label class="form-label" for="split-login-username">Student ID</label><input class="form-control" id="split-login-username" type="text" name="student_id" />
                        @if ($errors->has('student_id'))
                        <span class="text-danger">
                        {{$errors->first('student-id')}}</span>
                        @endif
                      </div>
                      <div class="mb-3">
                        <div class="d-flex justify-content-between"><label class="form-label" for="split-login-password">Password</label></div><input class="form-control" id="split-login-password" type="password" name="password" required/>
                        @if ($errors->has('password'))
                        <span class="text-danger">
                        {{$errors->first('password')}}</span>
                        @endif
                      </div>
                      <div class="row flex-between-center">
                        <div class="col-auto">
                          <div class="form-check mb-0"><input class="form-check-input" type="checkbox" id="split-checkbox" /><label class="form-check-label mb-0" for="split-checkbox">Remember me</label></div>
                        </div>
                        <div class="col-auto"><a class="fs-10" href="forgot-password.html">Forgot Password?</a></div>
                      </div>
                      <div class="mb-3"><button class="btn btn-primary d-block w-100 mt-3" type="submit" name="submit">Log in</button></div>
                    </form> --}}

                  </div>

                </div>
              </div>
            </div>
          </div>
        {{-- </div> --}}
      </div>
    </main><!-- ===============================================--><!--    End of Main Content--><!-- ===============================================-->


    <!-- ===============================================--><!--    JavaScripts--><!-- ===============================================-->
    <script src="{{ asset('vendors/popper/popper.min.js') }}"></script>
    <script src="{{ asset('vendors/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendors/anchorjs/anchor.min.js') }}"></script>
    <script src="{{ asset('vendors/is/is.min.js') }}"></script>
    <script src="{{ asset('vendors/fontawesome/all.min.js') }}"></script>
    <script src="{{ asset('vendors/lodash/lodash.min.js') }}"></script>
    <script src="{{ asset('polyfill.io/v3/polyfill.min58be.js?features=window.scroll') }}"></script>
    <script src="{{ asset('vendors/list.js/list.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme.js') }}"></script>

  </body>


<!-- Mirrored from prium.github.io/falcon/v3.19.0/pages/authentication/split/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 22 Nov 2023 06:21:13 GMT -->
</html>
