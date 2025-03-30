<div class="container">
    <div class="row">
        <!-- Scanner Section -->
        <div class="col-md-6">
            <video id="preview" width="100%"></video>
            <form class="form-horizontal" id="qrform">
                <div class="col-md-6">
                    <label>SCAN QR CODE</label>
                    <!-- Hidden input for activity_id -->
                    <input type="hidden" name="activity_id" id="activity_id" value="<?php echo $activity['activity_id']; ?>">
                    <input type="text" name="student_id" id="text" readonly="" placeholder="Scan QR Code" class="form-control">
                </div>
            </form>

            <!-- Clickable Face Recognition Icon -->
            <a href="<?php echo site_url('admin/activity/face-recognition/' . $activity['activity_id']); ?>">
                <i class="fas fa-user-circle" style="font-size: 50px; color: #007bff;"></i>
            </a>
        </div>

        <!-- Activity Details Section -->
        <div class="col-md-6">
            <h3>Activity Details</h3>
            <div class="activity-info">
                <h4>Activity Name:</h4>
                <p><?php echo $activity['activity_title']; ?></p>
            </div>
            <div class="activity-info">
                <h4>Time Now:</h4>
                <p id="scan_datetime"></p>
            </div>
            <div class="activity-info">
                <h4>Scheduled Time In: </h4>
                <p><?php echo $schedule['date_time_in']; ?></p>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-fill scan date & time
    document.getElementById("scan_datetime").innerText = new Date().toLocaleString();
</script>




<!-- Include the JavaScript libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Ensure jQuery is included -->
<script src="<?php echo site_url(); ?>assets/js/webcam.min.js"></script>

<!-- Include AlertifyCSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

<script>
    // scanner
    let scanner = new Instascan.Scanner({
        video: document.getElementById('preview')
    });
    Instascan.Camera.getCameras().then(function(cameras) {
        if (cameras.length > 0) {
            scanner.start(cameras[0]);
        } else {
            alertify.error('No cameras found.');
        }
    }).catch(function(e) {
        alertify.error('Error accessing cameras: ' + e.message);
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

            // Set Alertify default position to top-right
            alertify.set('notifier', 'position', 'top-right');


            // AJAX request to send the scanned student_id and activity_id to the server
            $.ajax({
                url: "<?php echo site_url('admin/activity/scan-qr-code'); ?>", // Adjust the URL to your backend method
                type: "POST",
                data: {
                    activity_id: $('#activity_id').val(), // Get the activity_id from the hidden input
                    student_id: text // Send the student_id to the server
                },
                success: function(response) {
                    alertify.success(`${text} recorded successfully.`);
                    console.log("Response:", response);
                },
                error: function(xhr, status, error) {
                    alertify.error("AJAX error: " + error);
                    console.error("AJAX error:", status, error);
                }
            });

        } else {
            alertify.error('No QR code detected or no integers extracted.');
        }
    });
</script>