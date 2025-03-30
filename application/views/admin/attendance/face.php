<script defer src="<?php echo base_url();?>assets/face_logics/face-api.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css" rel="stylesheet">
<!-- AlertifyJS CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css"/>

<!-- AlertifyJS Script -->
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

<style>
    canvas {
    position: absolute;
}
.video-container {
    display: flex;
    align-items: center;
    justify-content: center;
}
#video{
    border-radius:10px;
    box-shadow:#000;
}

</style>
<div class="main--content">
  <div class="video-container">
    <!-- Set the width and height of the video here -->
    <video id="video" width="600" height="450" autoplay></video>
    <canvas id="overlay"></canvas>
  </div>
</div>

<script>
  var labels = [];
  let detectedFaces = [];
  let sendingData = false;
  let videoStream = null; // Ensure this is declared globally


  window.onload = function () {
    const video = document.getElementById("video");
    const videoContainer = document.querySelector(".video-container");
    let webcamStarted = false;
    let modelsLoaded = false;

    // Load models
    Promise.all([
      faceapi.nets.ssdMobilenetv1.loadFromUri("../../../assets/models"),
      faceapi.nets.faceRecognitionNet.loadFromUri("../../../assets/models"),
      faceapi.nets.faceLandmark68Net.loadFromUri("../../../assets/models"),
    ])
      .then(() => {
        modelsLoaded = true;
        console.log("Models loaded successfully");

        // Start webcam automatically after models are loaded
        if (!webcamStarted) {
          startWebcam(); // Start webcam feed
          webcamStarted = true;
          videoContainer.style.display = "flex"; // Make video container visible
        }
      })
      .catch(() => {
        alert("Models not loaded, please check your model folder location");
      });

    // Function to start webcam
    function startWebcam() {
      console.log('Starting webcam...');
      navigator.mediaDevices
        .getUserMedia({
          video: true,
          audio: false,
        })
        .then((stream) => {
          console.log('Webcam started');
          video.srcObject = stream;  // Set the video source to the stream
          videoStream = stream;
          video.play();  // Ensure the video starts playing after the stream is assigned
        })
        .catch((error) => {
          console.error("Error accessing webcam: ", error);
        });
    }

    // Function to get labeled face descriptions from the server
    async function getLabeledFaceDescriptions() {
      const response = await fetch("<?= base_url('admin/attendance/get-faces') ?>");
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

    // Start face recognition when the video starts playing
    video.addEventListener("play", async () => {
      const labeledFaceDescriptors = await getLabeledFaceDescriptions();
      const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors);

      // Remove any existing canvas to prevent duplication
      document.querySelectorAll(".overlay-canvas").forEach((c) => c.remove());

      // Create canvas and append to video container
      const canvas = faceapi.createCanvasFromMedia(video);
      canvas.classList.add("overlay-canvas"); // Add a class for easy removal
      videoContainer.appendChild(canvas);

      // Ensure the canvas matches the DISPLAYED size of the video
      const displaySize = { width: video.offsetWidth, height: video.offsetHeight };
      canvas.width = displaySize.width;
      canvas.height = displaySize.height;

      faceapi.matchDimensions(canvas, displaySize);
      const ctx = canvas.getContext("2d");

      setInterval(async () => {
        const detections = await faceapi
          .detectAllFaces(video, new faceapi.SsdMobilenetv1Options())
          .withFaceLandmarks()
          .withFaceDescriptors();

        // Resize detections to match the display size
        const resizedDetections = faceapi.resizeResults(detections, displaySize);

        // Clear previous drawings before adding new boxes
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Draw bounding boxes and labels correctly
        resizedDetections.forEach((detection) => {
          const bestMatch = faceMatcher.findBestMatch(detection.descriptor);
          const { x, y, width, height } = detection.detection.box;

          // Draw rectangle around detected face
          ctx.strokeStyle = "#00FF00"; // Green outline
          ctx.lineWidth = 2;
          ctx.strokeRect(x, y, width, height);

          // Draw label text above the bounding box
          ctx.fillStyle = "#00FF00";
          ctx.font = "18px Arial";
          ctx.fillText(bestMatch.toString(), x, y - 10);
        });

        // Update detected faces and mark attendance
        detectedFaces = resizedDetections.map((detection) =>
          faceMatcher.findBestMatch(detection.descriptor).label
        );
        markAttendance(detectedFaces);
      }, 100);
    });

   // Store detected faces to prevent duplicate entries in the same session
  let detectedFacesSet = new Set();  

  // Function to mark attendance with the current time and check profile_pic from database
  async function markAttendance(detectedFaces) {
      try {
          const response = await fetch("<?= base_url('admin/attendance/get-faces') ?>");
          const studentsData = await response.json();
          
          const activityId = <?php echo $activity['activity_id']; ?>;
          const now = new Date();
          const currentTime = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')} ` +
                    `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')}`;

          detectedFaces.forEach(async (detectedFace) => {
              if (!detectedFacesSet.has(detectedFace)) { // Prevent duplicate entry
                  const student = studentsData.find(s => s.student_id === detectedFace);
                  if (student) {
                      const result = await fetch("<?= base_url('admin/attendance/detect'); ?>", {
                          method: 'POST',
                          headers: { 'Content-Type': 'application/json' },
                          body: JSON.stringify({ 
                              student_id: student.student_id, 
                              timestamp: currentTime, 
                              activityId: activityId 
                          })
                      });

                      const responseJson = await result.json();
                      console.log("Server Response:", responseJson);
                      
                      if (responseJson.status === "success") {
                          alertify.success(`Student ID ${student.student_id} successfully detected. It is recorded.`);
                          detectedFacesSet.add(detectedFace); // Add to Set to prevent duplicate marking
                      } else {
                          alertify.error(`Error: ${responseJson.message}`);
                      }
                  }
              }
          });

      } catch (error) {
          console.error('Error fetching student data or updating attendance:', error);
          alertify.error("Failed to mark attendance. Check console for details.");
      }
  }

  // Reset detected faces on page reload
  window.onload = function () {
      detectedFacesSet.clear();
      console.log("Face detection reset on page load");
  };



  }

</script>
