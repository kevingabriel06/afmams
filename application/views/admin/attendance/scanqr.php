
<div class="container">
    <div class="row">
        <!-- Scanner Section -->
        <div class="col-md-6">
            <video id="preview" width="100%"></video>
            <form action="{{ route('attendance.scan', ['activity_id' => $activity_id]) }}" method="POST" class="form-horizontal" id="form">
                @csrf
                <div class="col-md-6">
                    <label>SCAN QR CODE</label>
                    <input type="text" name="student_id" id="text" readonly="" placeholder="scan qr code" class="form-control">
                    
                </div>
            </form>
        </div>
        
        <!-- Capture Section -->
        <div class="col-md-6">
            <div id="my_camera"></div>
            <div id="results" style=" visibility:hidden; position:absolute;"></div>
            <form action="{{ route('attendance.scan', ['activity_id' => $activity_id]) }}" method="POST" enctype="multipart/form-data" id="imageForm">
                @csrf
                <input type="hidden" name="image" id="webcamID"> <!-- Hidden input field to hold the base64 image -->
                <button type="button" onclick="saveSnap();" class="btn btn-primary">Capture and Submit</button>
            </form>
        </div>
    </div>
</div>

<!-- Include the Laravel CSRF token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Include the JavaScript libraries -->
<script src="<?php echo site_url() ;?>assets/js/webcam.min.js"></script>

<script>
    // scanner
    let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
    Instascan.Camera.getCameras().then(function(cameras) {
        if (cameras.length > 0) {
            scanner.start(cameras[0]);
        } else {
            alert('No cameras found.');
        }
    }).catch(function(e) {
        console.error(e);
    });

    function extractIntegers(text) {
        // Regular expression to match integers including hyphens
        const regex = /[\d-]+/g;
        const integersArray = text.match(regex);
        if (integersArray && integersArray.length > 0) {
            // Join integers with space
            return integersArray.join(' ');
        } else {
            return null; // No integers found
        }
    }

    scanner.addListener('scan', function(content) {
        let text = extractIntegers(content);
        if (text) {
            document.getElementById('text').value = text;
            // document.getElementById('form').submit();
        } else {
            console.log('No QR code detected or no integers extracted.');
        }
    });

    document.addEventListener("DOMContentLoaded", function(event) {
        configure();
    });

    function configure() {
        Webcam.set({
            width: 480,
            height: 360,
            image_format: 'jpeg',
            jpeg_quality: 90,
            
        });

        Webcam.attach('#my_camera');
    }

    function saveSnap() {
        Webcam.snap(function(data_uri) {
            // Log data URI for debugging
            console.log("Data URI:", data_uri);

            document.getElementById('results').innerHTML = '<img id="webcam" src="' + data_uri + '"> ';
            
            // Extract base64 image data from data URI
            var base64image = data_uri.split(',')[1];
            document.getElementById('webcamID').value = base64image;

            // Submit the form
            document.getElementById('imageForm').submit();
            document.getElementById('form').submit();
        });

        Webcam.reset();
    }

</script>