        <!-- Include the necessary stylesheets -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />

        <!-- Include the Face API JS -->
        <script defer src="<?php echo base_url(); ?>assets/face_logics/face-api.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="<?php echo site_url(); ?>assets/js/webcam.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <style>
            canvas {
                position: absolute;
            }

            .video-container {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            #video {
                border-radius: 10px;
                box-shadow: #000;
            }
        </style>


        <div class="container">
            <div class="row">
                <!-- QR Scanner & Face Recognition Section -->
                <div class="col-md-7 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Scan with QR or Face</h5>

                            <!-- Video Container -->
                            <div class="video-container position-relative text-center mb-3">
                                <video id="video" class="rounded shadow" width="100%" autoplay muted></video>
                                <canvas id="overlay" class="position-absolute top-0 start-0 w-100 h-100"></canvas>
                            </div>

                            <!-- QR Input Form -->
                            <form class="form-horizontal" id="qrform">
                                <input type="hidden" name="activity_id" id="activity_id" value="<?php echo $activity['activity_id']; ?>">
                                <input type="hidden" name="timeslot_id" id="timeslot_id" value="<?php echo $activity['timeslot_id']; ?>">
                                <div class="form-group">
                                    <label for="text">Scan QR Code</label>
                                    <input type="text" name="student_id" id="text" readonly placeholder="Scan QR Code" class="form-control">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Activity Details Section -->
                <div class="col-md-5 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Activity Details</h5>

                            <div class="mb-3">
                                <h6 class="mb-1">Activity Name:</h6>
                                <p class="mb-0"><?php echo $activity['activity_title']; ?></p>
                            </div>

                            <div class="mb-3">
                                <h6 class="mb-1">Time Now:</h6>
                                <p class="mb-0" id="scan_datetime"></p>
                            </div>

                            <div>
                                <h6 class="mb-1">Scheduled Time Out:</h6>
                                <?php if (!empty($schedule) && isset($schedule[0]['date_time_in'])) : ?>
                                    <p class="mb-0">
                                        <?php
                                        $date = strtotime($schedule[0]['date_time_out']);
                                        echo date('F j, Y | g:i a', $date);
                                        ?>
                                    </p>
                                <?php else : ?>
                                    <p class="mb-0">No scheduled time available</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                Swal.fire({
                    title: 'Loading...',
                    text: 'Please wait while facial recognition initializes.',
                    allowOutsideClick: false,
                    timer: 10000, // 10 seconds in milliseconds
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    willClose: () => {
                        // Optional: add callback logic here after timer ends
                        console.log('Timer ended');
                    }
                });

                var labels = [];
                let detectedFaces = [];
                let sendingData = false;
                let videoStream = null;

                // Initialize webcam
                window.onload = function() {
                    // Show SweetAlert loading
                    Swal.fire({
                        title: 'Loading...',
                        text: 'Please wait while facial recognition initializes.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const video = document.getElementById("video");
                    const videoContainer = document.querySelector(".video-container");
                    let webcamStarted = false;
                    let modelsLoaded = false;

                    // Load face-api.js models
                    Promise.all([
                            faceapi.nets.ssdMobilenetv1.loadFromUri("../../../../assets/models"),
                            faceapi.nets.faceRecognitionNet.loadFromUri("../../../../assets/models"),
                            faceapi.nets.faceLandmark68Net.loadFromUri("../../../../assets/models"),
                        ])
                        .then(() => {
                            modelsLoaded = true;
                            console.log("Models loaded successfully");

                            // Start webcam after models are loaded
                            if (!webcamStarted) {
                                startWebcam();
                                webcamStarted = true;
                                videoContainer.style.display = "flex";
                            }
                        })
                        .catch(() => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Models Not Loaded',
                                text: 'Please check your model folder location.',
                                confirmButtonColor: '#d33'
                            });
                        });

                    // Start webcam feed
                    function startWebcam() {
                        console.log('Starting webcam...');
                        navigator.mediaDevices.getUserMedia({
                                video: true,
                                audio: false,
                            })
                            .then((stream) => {
                                console.log('Webcam started');
                                video.srcObject = stream;
                                videoStream = stream;
                                video.play();
                            })
                            .catch((error) => {
                                console.error("Error accessing webcam: ", error);
                            });
                    }

                    // Get labeled face descriptions from the server
                    async function getLabeledFaceDescriptions() {
                        const response = await fetch("<?= base_url('officer/attendance/get-faces') ?>");
                        const users = await response.json();
                        const labeledDescriptors = [];

                        for (const user of users) {
                            const descriptions = [];
                            if (!user.profile_pic) {
                                console.warn(`No profile picture for ${user.student_id}`);
                                continue;
                            }
                            try {
                                console.log(`Loading image: ${user.profile_pic}`);
                                const img = await faceapi.fetchImage(user.profile_pic);
                                const detections = await faceapi
                                    .detectSingleFace(img)
                                    .withFaceLandmarks()
                                    .withFaceDescriptor();
                                if (detections) {
                                    descriptions.push(detections.descriptor);
                                } else {
                                    console.log(`No face detected for ${user.student_id}`);
                                }
                            } catch (error) {
                                console.error(`Error processing ${user.profile_pic}:`, error);
                            }

                            if (descriptions.length > 0) {
                                labeledDescriptors.push(
                                    new faceapi.LabeledFaceDescriptors(user.student_id, descriptions)
                                );
                            }
                        }

                        return labeledDescriptors;
                    }

                    // Start face recognition
                    video.addEventListener("play", async () => {

                        const labeledFaceDescriptors = await getLabeledFaceDescriptions();
                        const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors);

                        // Create canvas and append to video container
                        const canvas = faceapi.createCanvasFromMedia(video);
                        canvas.classList.add("overlay-canvas");
                        videoContainer.appendChild(canvas);

                        const displaySize = {
                            width: video.offsetWidth,
                            height: video.offsetHeight
                        };
                        canvas.width = displaySize.width;
                        canvas.height = displaySize.height;

                        faceapi.matchDimensions(canvas, displaySize);
                        const ctx = canvas.getContext("2d");

                        let processing = false;

                        setInterval(async () => {
                            if (processing) return; // ⛔ Skip if still processing
                            processing = true;

                            const detections = await faceapi
                                .detectAllFaces(video, new faceapi.SsdMobilenetv1Options())
                                .withFaceLandmarks()
                                .withFaceDescriptors();

                            const resizedDetections = faceapi.resizeResults(detections, displaySize);
                            ctx.clearRect(0, 0, canvas.width, canvas.height);

                            const newDetectedFaces = [];

                            resizedDetections.forEach((detection) => {
                                const bestMatch = faceMatcher.findBestMatch(detection.descriptor);
                                if (!detectedFacesSet.has(bestMatch.label)) {
                                    newDetectedFaces.push(bestMatch.label);

                                    const {
                                        x,
                                        y,
                                        width,
                                        height
                                    } = detection.detection.box;
                                    ctx.strokeStyle = "#00FF00";
                                    ctx.lineWidth = 2;
                                    ctx.strokeRect(x, y, width, height);
                                    ctx.fillStyle = "#00FF00";
                                    ctx.font = "18px Arial";
                                    ctx.fillText(bestMatch.toString(), x, y - 10);
                                }
                            });

                            if (newDetectedFaces.length > 0) {
                                await markAttendance(newDetectedFaces);
                            }

                            processing = false;
                        }, 1000); // ⏱ Slower interval (1s is good)



                    });

                    // Store detected faces to prevent duplicates
                    let detectedFacesSet = new Set();

                    async function markAttendance(detectedFaces) {
                        try {
                            const response = await fetch("<?= base_url('officer/attendance/get-faces') ?>");
                            const studentsData = await response.json();
                            const activityId = <?php echo $activity['activity_id']; ?>;
                            const timeslotId = <?php echo $activity['timeslot_id']; ?>;
                            const now = new Date();
                            const currentTime = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')} ` +
                                `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')}`;

                            for (const detectedFace of detectedFaces) {
                                if (!detectedFacesSet.has(detectedFace)) {
                                    const student = studentsData.find(s => s.student_id === detectedFace);
                                    if (student) {
                                        // ✅ Add to set *before* sending to avoid rapid re-processing
                                        detectedFacesSet.add(detectedFace);

                                        const result = await fetch("<?= base_url('officer/attendance/detect_timeout'); ?>", {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json'
                                            },
                                            body: JSON.stringify({
                                                student_id: student.student_id,
                                                timestamp: currentTime,
                                                activity_id: activityId,
                                                timeslot_id: timeslotId
                                            })
                                        });

                                        const responseJson = await result.json();

                                        if (responseJson.status === "success") {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Attendance Marked',
                                                text: `Student ID ${student.student_id} - ${student.first_name} ${student.last_name} successfully detected and recorded.`,
                                                timer: 5000,
                                                showConfirmButton: true
                                            });
                                        } else if (responseJson.status === 'info') {
                                            Swal.fire({
                                                icon: 'info',
                                                title: 'Info',
                                                text: `Student ID ${student.student_id} - ${student.first_name} ${student.last_name} has already been recorded.`,
                                                timer: 5000,
                                                showConfirmButton: true
                                            });
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Attendance Failed',
                                                text: responseJson.message || 'An unknown error occurred.',
                                                confirmButtonColor: '#d33'
                                            });
                                        }

                                    }
                                }
                            }
                        } catch (error) {
                            console.error('Error fetching student data or updating attendance:', error);

                            Swal.fire({
                                icon: 'error',
                                title: 'Attendance Error',
                                text: 'Failed to mark attendance. Please check the console for more details.',
                                confirmButtonColor: '#d33'
                            });
                        }
                    }
                };

                // QR Code Scanner functionality
                let scanner = new Instascan.Scanner({
                    video: document.getElementById('video') // Using same video element for both functionalities
                });

                Instascan.Camera.getCameras().then(function(cameras) {
                    if (cameras.length > 0) {
                        scanner.start(cameras[0]);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Camera Error',
                            text: 'No cameras found.'
                        });
                    }
                }).catch(function(e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Camera Access Error',
                        text: 'Error accessing cameras: ' + e.message
                    });
                });

                function extractIntegers(text) {
                    const regex = /[\d-]+/g;
                    const integersArray = text.match(regex);
                    if (integersArray && integersArray.length > 0) {
                        return integersArray.join(' ');
                    } else {
                        return null;
                    }
                }

                scanner.addListener('scan', function(content) {
                    let text = extractIntegers(content);
                    document.getElementById('text').value = text;

                    // Send scanned data to server
                    $.ajax({
                        url: "<?php echo site_url('officer/attendance/detect_timeout'); ?>",
                        method: "POST",
                        dataType: "json",
                        data: {
                            student_id: text,
                            activity_id: document.getElementById('activity_id').value,
                            timeslot_id: document.getElementById('timeslot_id').value
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            } else if (response.status === 'info') {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Info',
                                    text: response.message,
                                    timer: 5000,
                                    showConfirmButton: true
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'An error occurred while marking attendance.',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to process the QR scan.'
                            });
                        }
                    });
                });
            </script>

            <div class="row mt-4">
                <div class="card" id="attendanceTable" data-list='{"valueNames":["student-id","student-name","time-in"],"page":5,"pagination":true}'>
                    <div class="card-header border-bottom border-200 px-0 d-flex justify-content-between">
                        <h5 class="mb-0 px-x1"> </h5>
                        <input class="search form-control form-control-sm w-auto me-3" placeholder="Search..." />
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive scrollbar">
                            <table class="table table-hover table-striped overflow-hidden">
                                <thead class="bg-200">
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Time Out</th>
                                    </tr>
                                </thead>
                                <tbody class="list">
                                    <?php foreach ($students as $student): ?>
                                        <tr>
                                            <td class="student-id"><?php echo $student['student_id']; ?></td>
                                            <td class="student-name"><?php echo $student['first_name'] . " " . $student['last_name']; ?></td>
                                            <td class="time-in"><?php echo date('Y-m-d | h:i A', strtotime($student['time_out'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-center">
                        <ul class="pagination mb-0"></ul>
                    </div>
                </div>
            </div>
        </div>


        <script>
            // Function to update the date and time in the desired format
            function updateScanDateTime() {
                var options = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true
                };

                var formattedDate = new Date().toLocaleString('en-US', options);
                document.getElementById("scan_datetime").innerText = formattedDate;
            }

            // Initial update
            updateScanDateTime();

            // Update every second
            setInterval(updateScanDateTime, 1000); // 1000 ms = 1 second
        </script>