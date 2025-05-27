<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- SweetAlert2 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="card mb-3">
  <div class="card-body">
    <div class="row flex-between-center">
      <div class="col-md">
        <h5 class="mb-2 mb-md-0">Create Evaluation Form</h5>
      </div>
    </div>
  </div>
</div>

<!-- COVER PHOTO SECTION -->
<div class="position-relative text-center" style="max-width: 100%; overflow: hidden;">
  <!-- Cover Photo -->
  <img id="coverPhoto" class="img-fluid w-100 rounded"
    src="<?php echo base_url(); ?>assets/image/OIP.jpg"
    alt="Cover Photo"
    style="height: 250px; object-fit: cover;">

  <!-- Remove Button (Initially Hidden) -->
  <button id="removeCover" class="btn btn-danger position-absolute top-0 end-0 m-2 px-2 py-1 shadow-sm"
    style="display: none; border-radius: 50%; font-size: 16px; line-height: 1;">
    <i class="fas fa-times"></i>
  </button>

  <!-- Hidden File Input -->
  <input type="file" id="coverUpload" accept="image/*" class="d-none" name="coverUpload">

  <!-- Upload Button (Overlay at Top Left) -->
  <label for="coverUpload" class="btn btn-dark position-absolute top-0 start-0 m-3 px-3 py-2 shadow-sm"
    style="border-radius: 8px; font-size: 14px;">
    <i class="fas fa-camera"></i> Change activity photo
  </label>
</div>

<!-- JavaScript for Image Preview & Remove -->
<script>
  document.getElementById("coverUpload").addEventListener("change", function(event) {
    const file = event.target.files[0];
    const preview = document.getElementById("coverPhoto");
    const removeBtn = document.getElementById("removeCover");

    if (file && file.type.startsWith("image/")) {
      const reader = new FileReader();
      reader.onload = function(e) {
        preview.src = e.target.result; // Update image preview
        removeBtn.style.display = "block"; // Show remove button
      };
      reader.readAsDataURL(file);
    } else {
      alert("Invalid file. Please upload an image.");
    }
  });

  // Remove Image Functionality
  document.getElementById("removeCover").addEventListener("click", function() {
    const preview = document.getElementById("coverPhoto");
    preview.src = "<?php echo base_url(); ?>assets/image/OIP.jpg"; // Reset to default image
    document.getElementById("coverUpload").value = ""; // Clear file input
    this.style.display = "none"; // Hide remove button
  });
</script>

<div class="row g-0">
  <div class="card mt-3">
    <div class="card-body bg-body-tertiary">
      <form id="createForm" class="row g-3 needs-validation" novalidate="" data-dropzone="data-dropzone" enctype="multipart/form-data">
        <!-- Select Activity Dropdown -->
        <div class="mb-3">
          <label class="form-label" for="activity">Select Activity</label>
          <div class="input-group has-validation">
            <select class="form-control" id="activity" name="activity_id" required>
              <option value="" disabled selected>Select an activity</option>
              <?php foreach ($activities as $activity): ?>
                <option value="<?= $activity->activity_id ?>">
                  <?= htmlspecialchars($activity->activity_title) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Form Title -->
        <div class="mb-3">
          <label class="form-label" for="formtitle">Form Title</label>
          <div class="input-group has-validation">
            <input class="form-control" id="formtitle" type="text" name="formtitle" placeholder="Untitled Form" required />
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label" for="formdescription">Form Description</label>
          <textarea class="form-control" id="formdescription" name="formdescription" rows="3"></textarea>
        </div>

        <!-- Start Date and End Date (Two Columns) -->
        <div class="row mb-3 g-3">
          <!-- Start Date -->
          <div class="col-md-6">
            <label class="form-label">Date & Time Start:</label>
            <div class="input-group">
              <input class="form-control datetimepicker" id="time_start" type="text"
                placeholder="Select Date & Time" name="time_start" />
              <span class="input-group-text" id="time_end_icon">
                <i class="fas fa-calendar-alt"></i>
              </span>
            </div>
          </div>
          <!-- DATE & TIME END -->
          <div class="col-md-6">
            <label class="form-label">Date & Time End:</label>
            <div class="input-group">
              <input class="form-control datetimepicker" id="time_end" type="text"
                placeholder="Select Date & Time" name="time_end" />
              <span class="input-group-text" id="time_end_icon">
                <i class="fas fa-calendar-alt"></i>
              </span>
            </div>
          </div>
        </div>

        <script>
          document.addEventListener("DOMContentLoaded", function() {
            // Initialize flatpickr for the first set of inputs
            flatpickr("#time_start", {
              enableTime: true,
              noCalendar: false,
              dateFormat: "Y-m-d h:i K",
              time_24hr: false,
              disableMobile: true,
              minDate: new Date(),
            });

            flatpickr("#time_end", {
              enableTime: true,
              noCalendar: false,
              dateFormat: "Y-m-d h:i K",
              time_24hr: false,
              disableMobile: true,
              minDate: new Date(),
            });
          });
        </script>


        <div id="form-fields" class="border-bottom border-dashed my-3"></div>

        <!-- Add Field Button -->
        <div class="d-flex justify-content-center my-3">
          <button
            class="btn btn-primary btn-icon"
            type="button"
            data-bs-toggle="modal"
            data-bs-target="#addFieldModal">
            <i class="fas fa-plus"></i> Add Field
          </button>
        </div>

        <!-- Save and Cancel Buttons -->
        <div class="card-body">
          <div class="row justify-content-between align-items-center">
            <div class="col-md">
              <h5 class="mb-2 mb-md-0">Nice Job! You're almost done</h5>
            </div>
            <div class="col-auto">
              <button
                class="btn btn-danger btn-sm me-2"
                type="button"
                onclick="$('#createForm').get(0).reset()">
                Cancel
              </button>
              <button class="btn btn-falcon-default btn-sm me-2" type="submit">
                Save
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal for adding fields (remains unchanged) -->
<div
  class="modal fade"
  id="addFieldModal"
  tabindex="-1"
  aria-labelledby="addFieldModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addFieldModalLabel">Add Field</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Select the type of field you want to add:</p>
        <div class="d-flex justify-content-around">
          <button class="btn btn-outline-primary" onclick="addField('short')" data-bs-dismiss="modal">
            <i class="fas fa-text-width"></i> Short Answer
          </button>
          <button class="btn btn-outline-primary" onclick="addField('textarea')" data-bs-dismiss="modal">
            <i class="fas fa-align-left"></i> Long Answer
          </button>
          <button class="btn btn-outline-primary" onclick="addField('rating')" data-bs-dismiss="modal">
            <i class="fas fa-star"></i> Star Rating
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  let fieldCount = 0;

  function addField(type) {
    const formFields = document.getElementById("form-fields");
    const fieldId = `field-${fieldCount}`;
    let newField = "";

    if (type === "short") {
      newField = `
      <div class="form-group mb-3 border-bottom border-dashed" id="${fieldId}">
        <div class="d-flex justify-content-between align-items-center">
          <label for="question-${fieldCount}" class="form-label">Question</label>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="required-${fieldCount}" name="required[]" onchange="toggleRequired('${fieldId}', this)" />
            <label class="form-check-label" for="required-${fieldCount}">Required</label>
          </div>
        </div>
        <div class="input-group has-validation">
        <input class="form-control mb-2" id="question-${fieldCount}" type="text" placeholder="Enter your question" name="questions[]" required/>
        </div>
        <input class="form-control mb-2" id="answer-${fieldCount}" type="text" placeholder="Short Answer" name="answers[]" disabled/>
        <input type="hidden" name="type[]" id="type-${fieldCount}" value="short"/>
        <button class="btn btn-sm btn-danger mt-2" type="button" onclick="removeField('${fieldId}')">Remove</button>
      </div>
    `;
    } else if (type === "textarea") {
      newField = `
      <div class="form-group mb-3 border-bottom border-dashed" id="${fieldId}">
        <div class="d-flex justify-content-between align-items-center">
          <label for="question-${fieldCount}" class="form-label">Question</label>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="required-${fieldCount}" name="required[]" onchange="toggleRequired('${fieldId}', this)" />
            <label class="form-check-label" for="required-${fieldCount}">Required</label>
          </div>
        </div>
        <div class="input-group has-validation">
        <input class="form-control mb-2" id="question-${fieldCount}" type="text" placeholder="Enter your question" name="questions[]" required />
        </div>
        <textarea class="form-control mb-2" id="answer-${fieldCount}" rows="3" placeholder="Long Answer" name="answers[]" readonly></textarea>
        <input type="hidden" name="type[]" id="type-${fieldCount}" value="textarea"/>
        <button class="btn btn-sm btn-danger mt-2" type="button" onclick="removeField('${fieldId}')">Remove</button>
      </div>
    `;
    } else if (type === "rating") {
      newField = `
      <div class="form-group mb-3 border-bottom border-dashed" id="${fieldId}">
        <div class="d-flex justify-content-between align-items-center">
          <label for="question-${fieldCount}" class="form-label">Question</label>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="required-${fieldCount}" name="required[]" onchange="toggleRequired('${fieldId}', this)" />
            <label class="form-check-label" for="required-${fieldCount}">Required</label>
          </div>
        </div>
        <div class="input-group has-validation">
        <input class="form-control mb-2" id="question-${fieldCount}" type="text" placeholder="Enter your question" name="questions[]" required/>
        </div>
        <div class="rating-stars mb-2" id="rating-${fieldCount}">
          ${[1, 2, 3, 4]
            .map(
              (i) =>
                `<i class="far fa-star" onclick="setRating(this, ${i})" data-value="${i}"></i>`
            )
            .join("")}
        </div>
        <input type="hidden" name="answers[]" id="rating-value-${fieldCount}" value="" />
        <input type="hidden" name="type[]" id="type-${fieldCount}" value="rating" />
        <button class="btn btn-sm btn-danger mt-2" type="button" onclick="removeField('${fieldId}')">Remove</button>
      </div>
    `;
    }

    formFields.insertAdjacentHTML("beforeend", newField);
    fieldCount++;
  }

  function toggleRequired(fieldId, checkbox) {
    const field = document.getElementById(fieldId);
    const inputs = field.querySelectorAll("input, textarea");
    inputs.forEach((input) => {
      if (checkbox.checked) {
        input.setAttribute("required", "required");
      } else {
        input.removeAttribute("required");
      }
    });
  }

  function removeField(fieldId) {
    const field = document.getElementById(fieldId);
    field.remove();
  }

  function setRating(star, rating) {
    const stars = star.parentElement.children;
    for (let i = 0; i < stars.length; i++) {
      stars[i].className = i < rating ? "fas fa-star" : "far fa-star";
    }
    const ratingInput = star.parentElement.nextElementSibling;
    ratingInput.value = rating;
  }

  $("#createForm").on("submit", function(e) {
    e.preventDefault();

    let isValid = true;
    $(".invalid-feedback").remove(); // Remove previous validation messages
    $("input, select, textarea").removeClass("is-invalid is-valid"); // Reset validation classes

    // Loop through required fields and validate
    $("#form-fields .form-group").each(function() {
      const questionInput = $(this).find('input[name="questions[]"]');
      const typeInput = $(this).find('input[name="type[]"]');

      if (!questionInput.val()) {
        questionInput.addClass("is-invalid").after('<div class="invalid-feedback">Please enter a question.</div>');
        isValid = false;
      } else {
        questionInput.addClass("is-valid");
      }

      if (!typeInput.val()) {
        typeInput.addClass("is-invalid").after('<div class="invalid-feedback">Please enter the type.</div>');
        isValid = false;
      } else {
        typeInput.addClass("is-valid");
      }
    });

    // General form field validation
    const validateField = (selector, message) => {
      const inputField = $(selector);
      if (!inputField.val()) {
        inputField.addClass("is-invalid").after(`<div class="invalid-feedback">${message}</div>`);
        isValid = false;
      } else {
        inputField.addClass("is-valid");
      }
    };

    validateField("#activity", "Please select an activity.");
    validateField("#formtitle", "Please enter the form title.");
    validateField("#formdescription", "Please enter a description.");
    validateField("#time_start", "Please enter a start date.");
    validateField("#time_end", "Please enter an end date.");

    // If form is valid, proceed with submission
    if (isValid) {
      const fields = [];
      $("#form-fields .form-group").each(function() {
        fields.push({
          label: $(this).find('input[name="questions[]"]').val(),
          type: $(this).find('input[name="type[]"]').val(),
          placeholder: $(this).find('input[name="answers[]"]').val() || null,
          required: $(this).find('input[type="checkbox"]').is(":checked") ? 1 : 0,
        });
      });

      // Create FormData object for file handling
      const formData = new FormData();
      formData.append("activity", $("#activity").val());
      formData.append("formtitle", $("#formtitle").val());
      formData.append("formdescription", $("#formdescription").val());
      formData.append("startdate", $("#time_start").val());
      formData.append("enddate", $("#time_end").val());

      // Append fields as JSON string
      formData.append("fields", JSON.stringify(fields));

      // Append cover file (if selected)
      const coverFile = $("#coverUpload")[0]?.files[0]; // Ensure no errors if empty
      if (coverFile) {
        formData.append("coverUpload", coverFile);
      }

      // Trigger confirmation before submitting form
      Swal.fire({
        title: "Are you sure?",
        text: "Do you want to create this evaluation form?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, create it!",
        cancelButtonText: "Cancel"
      }).then((result) => {
        if (result.isConfirmed) {
          // Proceed to submit the form via AJAX
          $.ajax({
            url: "<?php echo site_url('officer/create-evaluation-form/create'); ?>",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
              if (response.success) {
                Swal.fire({
                  icon: "success",
                  title: "Form Created!",
                  text: "Your form has been created successfully.",
                  showConfirmButton: true,
                }).then(() => {
                  $("#createForm")[0].reset();
                  $("#form-fields").empty();

                  if (response.redirect_url) {
                    window.location.href = response.redirect_url;
                  } else {
                    location.reload();
                  }
                });
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Validation Error!",
                  text: response.message || "Please review the form and try again.",
                  showConfirmButton: true,
                });
              }
            },
            error: function() {
              Swal.fire({
                icon: "error",
                title: "Form Creation Failed!",
                text: "An error occurred while creating the form. Please try again.",
                showConfirmButton: true,
              });
            }
          });
        }
      });

    }
  });
</script>